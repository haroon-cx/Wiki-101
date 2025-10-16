<?php
add_action('wp_ajax_add_or_update_user', 'handle_add_or_update_user');
add_action('wp_ajax_nopriv_add_or_update_user', 'handle_add_or_update_user');

function handle_add_or_update_user()
{
    global $wpdb;

    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'cuim_nonce')) {
        wp_send_json_error(['message' => 'Permission Denied']);
    }

    parse_str($_POST['form_data'], $data);

    $account          = sanitize_text_field($data['account']);
    $new_password     = sanitize_text_field($data['new-password']);
    $confirm_password = sanitize_text_field($data['confirm-password']);
    $user_state       = sanitize_text_field($data['state']);
    $user_role_input  = strtolower(sanitize_text_field($data['user-role']));
    $company_name     = sanitize_text_field($data['company-name']);
    $email            = sanitize_email($data['email']);
    $delete_status            = sanitize_text_field($data['delete_status']);

    $custom_label_1 = isset($data['custom-label-1']) ? sanitize_text_field($data['custom-label-1']) : '';
    $custom_label_2 = isset($data['custom-label-2']) ? sanitize_text_field($data['custom-label-2']) : '';
    $custom_label_3 = isset($data['custom-label-3']) ? sanitize_text_field($data['custom-label-3']) : '';
    $custom_label_4 = isset($data['custom-label-4']) ? sanitize_text_field($data['custom-label-4']) : '';
    $custom_field_1 = isset($data['custom-field-1']) ? sanitize_text_field($data['custom-field-1']) : '';
    $custom_field_2 = isset($data['custom-field-2']) ? sanitize_text_field($data['custom-field-2']) : '';
    $custom_field_3 = isset($data['custom-field-3']) ? sanitize_text_field($data['custom-field-3']) : '';
    $custom_field_4 = isset($data['custom-field-4']) ? sanitize_text_field($data['custom-field-4']) : '';

    // Basic validation
    if ($new_password !== $confirm_password) {
        wp_send_json_error(['message' => 'Passwords does not match.']);
    }
    if (!is_email($email)) {
        wp_send_json_error(['message' => 'Invalid email address.']);
    }
    if (username_exists($account)) {
        wp_send_json_error(['message' => 'This username is already taken.']);
    }
    if (email_exists($email)) {
        wp_send_json_error(['message' => 'Email already exists.']);
    }

    // Map role
    $wp_role = map_user_role($user_role_input);

    // Insert user into wp_users (NOTE: give plain password; WP will hash it)
    $wp_user_data = [
        'user_login'   => $account,
        'user_pass'    => $new_password, // plain; WP hashes internally
        'user_email'   => $email,
        'display_name' => $account,
        'role'         => $wp_role,
    ];
    $user_id = wp_insert_user($wp_user_data);

    if (is_wp_error($user_id)) {
        wp_send_json_error(['message' => $user_id->get_error_message()]);
    }

    // Insert into custom table (avoid storing confirm_password; ideally don't store passwords at all)
    $insert_data = [
        'user_id'        => $user_id,
        'account'        => $account,
        'new_password'   => wp_hash_password($new_password),
        'confirm_password' => wp_hash_password($confirm_password),
        'state'          => $user_state,
        'user_role'      => $user_role_input,
        'company_name'   => $company_name,
        'email'          => $email,
        'custom_label_1' => $custom_label_1,
        'custom_label_2' => $custom_label_2,
        'custom_label_3' => $custom_label_3,
        'custom_label_4' => $custom_label_4,
        'custom_field_1' => $custom_field_1,
        'custom_field_2' => $custom_field_2,
        'custom_field_3' => $custom_field_3,
        'custom_field_4' => $custom_field_4,
        'created_at'   => current_time('Y-m-d'),
        'delete_status' => $delete_status,
    ];
    $result = $wpdb->insert("{$wpdb->prefix}agqa_wiki_add_users", $insert_data);
    if ($result === false) {
        error_log('Custom table insert error: ' . $wpdb->last_error);
        wp_send_json_error(['message' => 'Error inserting data into custom table.']);
    }

    $user   = get_user_by('id', $user_id);
    $key    = get_password_reset_key($user);
    if (is_wp_error($key)) {
        // Fallback: simple welcome email (without reset link)
        $subject = sprintf(__('Welcome to %s'), get_bloginfo('name'));
        $message = sprintf(
            "Hi %s,\n\nYour account has been created.\nUsername: %s\nLogin: %s\n\nThanks!",
            $account,
            $account,
            wp_login_url()
        );
        wp_mail($email, $subject, $message, ['Content-Type: text/plain; charset=UTF-8']);
    } else {
        $reset_url = network_site_url('verification') . '?username=' . urlencode($account) . '&key=' . rawurlencode($key) . '&code=' . rawurlencode(current_time('Y-m-d'));
        $subject = sprintf(__('Email verification %s'), get_bloginfo('name'));
        $message = '<div class="email-ctn" style="background-color: #1D1C25; padding: 20px; width: 70%; margin:0 auto; border-radius: 16px; color: white; font-size: 16px; font-family: \'Poppins\', sans-serif;">'
            . '<p style="color: white">Hello ' . esc_html($account) . ',</p>'
            . '<h2 style="font-size: 20px; color: #00a000;">Thank you for registering with Wiki101</h2>'
            . '<p style="color: white">To complete your account setup, please verify your email address by clicking the button below:</p>'
            . '<p style="color: white">'
            . '<a href="' . esc_url($reset_url) . '" style="background-color: #7644CE; font-size: 20px; padding: 16px 24px; border-radius: 16px; color: white; margin: 5px 0; display: inline-block; text-decoration: none;">'
            . 'Verify Link'
            . '</a>'
            . '</p>'
            . '<p style="color: white">(If the button doesn’t work, copy and paste the following URL into your browser: '  . esc_url($reset_url) . ')</p>'
            . '<p style="color: white">This link will expire in 7 days for security reasons. If you did not create this account, please ignore this email.</p>'
            . '<h2 style="font-size: 24px; color: #fff"><strong>Best regards,</strong></h2>'
            . '<p style="color: white">The <strong>Wiki101</strong> Team</p>'
            . '</div>';

        $headers = ['Content-Type: text/html; charset=UTF-8'];
        wp_mail($email, $subject, $message, $headers);
    }

    // All good
    wp_send_json_success([
        'message' => 'User Account Created Successfully.<br>A verification email has been sent to your registered email address.Please check your inbox.'
    ]);
}

// Map the selected form role to WP role
function map_user_role($role)
{
    switch ($role) {
        case 'admin':
            return 'subscriber';
        case 'manager':
            return 'subscriber';
        case 'contributor':
            return 'subscriber';
        case 'viewer':
            return 'subscriber';
        default:
            return 'subscriber';
    }
}

add_action('wp_ajax_edit_user_manage', 'handle_edit_user_manage');
add_action('wp_ajax_nopriv_edit_user_manage', 'handle_edit_user_manage');


/**
 * Edit user handler
 */

function map_user_roles($role)
{
    // Map the custom user role to WordPress role
    switch (strtolower($role)) {
        case 'admin':
            return 'subscriber';
        case 'manager':
            return 'subscriber';
        case 'contributor':
            return 'subscriber';
        case 'viewer':
            return 'subscriber';
        default:
            return 'subscriber'; // Default to subscriber if role is not matched
    }
}

function handle_edit_user_manage()
{
    global $wpdb;

    // Check nonce for security
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'cuim_nonce')) {
        wp_send_json_error(['message' => 'Permission Denied']);
    }

    parse_str($_POST['form_data'], $data);

    // Get the form data
    $user_id = intval($data['user-id']);
    $account = sanitize_text_field($data['account']);
    $new_password = sanitize_text_field($data['new-password']);
    $confirm_password = sanitize_text_field($data['confirm-password']);
    $user_state = sanitize_text_field($data['state']);
    $user_role_input = sanitize_text_field($data['user-role']);
    $company_name = sanitize_text_field($data['company-name']);
    $email = sanitize_email($data['email']);
    $custom_label_1 = sanitize_text_field($data['custom-label-1']);
    $custom_label_2 = sanitize_text_field($data['custom-label-2']);
    $custom_label_3 = sanitize_text_field($data['custom-label-3']);
    $custom_label_4 = sanitize_text_field($data['custom-label-4']);
    $custom_field_1 = sanitize_text_field($data['custom-field-1']);
    $custom_field_2 = sanitize_text_field($data['custom-field-2']);
    $custom_field_3 = sanitize_text_field($data['custom-field-3']);
    $custom_field_4 = sanitize_text_field($data['custom-field-4']);

    // Check if user exists in the custom table
    $user_exists = $wpdb->get_var(
        $wpdb->prepare(
            "SELECT COUNT(*) FROM {$wpdb->prefix}agqa_wiki_add_users WHERE user_id = %d",
            $user_id
        )
    );

    if ($user_exists == 0) {
        wp_send_json_error(['message' => 'User not found in custom table.']);
        return;
    }

    // 1️⃣ Update custom table
    $table_name = $wpdb->prefix . 'agqa_wiki_add_users';
    $update_data = [
        'account'        => $account,
        'state'          => $user_state,
        'user_role'      => $user_role_input,
        'company_name'   => $company_name,
        'email'          => $email,
        'custom_label_1' => $custom_label_1,
        'custom_label_2' => $custom_label_2,
        'custom_label_3' => $custom_label_3,
        'custom_label_4' => $custom_label_4,
        'custom_field_1' => $custom_field_1,
        'custom_field_2' => $custom_field_2,
        'custom_field_3' => $custom_field_3,
        'custom_field_4' => $custom_field_4,
    ];

    // If new password is provided, update it
    if (!empty($new_password) && $new_password === $confirm_password) {
        $update_data['new_password'] = wp_hash_password($new_password);
    }

    // Update custom table
    $result = $wpdb->update(
        $table_name,
        $update_data,
        ['user_id' => $user_id],
        array_fill(0, count($update_data), '%s'),
        ['%d']
    );

    if ($result === false) {
        wp_send_json_error(['message' => 'Failed to update user data in custom table.']);
        return;
    }

    // 2️⃣ Update WordPress user table (role + email + password)
    $wp_role = map_user_roles($user_role_input); // Map the custom role to WordPress role

    $user_data = [
        'ID'           => $user_id,
        'user_login'   => $account,
        'user_email'   => $email,
        'display_name' => $account,
        'role'         => $wp_role, // Set the mapped WordPress role
    ];

    // If password is provided, update the password as well
    if (!empty($new_password) && $new_password === $confirm_password) {
        $user_data['user_pass'] = $new_password; // wp_update_user will hash the password
    }

    // Update WordPress user data
    $user_update = wp_update_user($user_data);

    if (is_wp_error($user_update)) {
        wp_send_json_error(['message' => $user_update->get_error_message()]);
        return;
    }

    // 3️⃣ Return success response
    wp_send_json_success(['message' => 'User data updated successfully in both the custom table and WordPress table.']);
}



/**
 * Verification email handler
 */
add_action('wp_ajax_verification_user_email', 'handle_verification_user_email');
add_action('wp_ajax_nopriv_verification_user_email', 'handle_verification_user_email');

function handle_verification_user_email()
{
    global $wpdb;

    // Security: nonce check
    if (! isset($_POST['nonce']) || ! wp_verify_nonce($_POST['nonce'], 'cuim_nonce')) {
        wp_send_json_error(['message' => 'Permission Denied']);
    }

    // Parse form data safely
    parse_str(isset($_POST['form_data']) ? wp_unslash($_POST['form_data']) : '', $data);

    // Get and sanitize input
    $account = isset($data['username']) ? sanitize_text_field($data['username']) : '';
    if ($account === '') {
        wp_send_json_error(['message' => 'Username is required.']);
    }

    $table_name = $wpdb->prefix . 'agqa_wiki_add_users';

    // Check if user exists & fetch current state
    $current = $wpdb->get_row(
        $wpdb->prepare(
            "SELECT account, state FROM {$table_name} WHERE account = %s LIMIT 1",
            $account
        )
    );

    if (! $current) {
        wp_send_json_error(['message' => 'User not found in custom table.']);
    }

    // Only allow update when current state is 'pending'
    if (strtolower($current->state) !== 'pending') {
        // You can tailor this message based on actual state
        wp_send_json_error(['message' => 'User status is not pending; no changes made.']);
    }

    // Update: set state to active ONLY if currently pending (extra safety in WHERE)
    $updated = $wpdb->update(
        $table_name,
        [
            'state'   => 'active',
        ],
        [
            'account' => $account,
            'state'   => 'pending', // ensures we only flip pending -> active
        ],
        ['%s'],
        ['%s', '%s']
    );

    if ($updated === false) {
        // DB error
        wp_send_json_error(['message' => 'Database error while updating status.']);
    } elseif ($updated === 0) {
        // Nothing changed (race condition or already updated)
        wp_send_json_error(['message' => 'No change performed. The status may have already been updated.']);
    } else {
        wp_send_json_success(['message' => 'User status updated to active successfully.']);
    }
}

/**
 * resend pending user handler
 */
add_action('wp_ajax_resend_pending_email', 'handle_resend_pending_email');
add_action('wp_ajax_nopriv_resend_pending_email', 'handle_resend_pending_email');

function handle_resend_pending_email()
{

    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'cuim_nonce')) {
        wp_send_json_error(['message' => 'Permission Denied']);
    }
    parse_str($_POST['form_data'], $data);
    $account          = sanitize_text_field($data['account']);
    $user_id          = sanitize_text_field($data['user-id']);
    $email = sanitize_email($data['email']);
    $user   = get_user_by('id', $user_id);
    $key    = get_password_reset_key($user);

    $reset_url = network_site_url('verification') . '?username=' . urlencode($account) . '&key=' . rawurlencode($key) . '&code=' . rawurlencode(current_time('Y-m-d'));
    $subject = sprintf(__('Email verification %s'), get_bloginfo('name'));
    $message = '<div class="email-ctn" style="background-color: #1D1C25; padding: 20px; width: 70%; margin:0 auto; border-radius: 16px; color: white; font-size: 16px; font-family: \'Poppins\', sans-serif;">'
        . '<p style="color: white">Hello ' . esc_html($account) . ',</p>'
        . '<h2 style="font-size: 20px; color: #00a000;">Thank you for registering with Wiki101</h2>'
        . '<p style="color: white">To complete your account setup, please verify your email address by clicking the button below:</p>'
        . '<p style="color: white">'
        . '<a href="' . esc_url($reset_url) . '" style="background-color: #7644CE; font-size: 20px; padding: 16px 24px; border-radius: 16px; color: white; margin: 5px 0; display: inline-block; text-decoration: none;">'
        . 'Verify Link'
        . '</a>'
        . '</p>'
        . '<p style="color: white">(If the button doesn’t work, copy and paste the following URL into your browser: '  . esc_url($reset_url) . ')</p>'
        . '<p style="color: white">This link will expire in 7 days for security reasons. If you did not create this account, please ignore this email.</p>'
        . '<h2 style="font-size: 24px; color: #fff"><strong>Best regards,</strong></h2>'
        . '<p style="color: white">The <strong>Wiki101</strong> Team</p>'
        . '</div>';

    $headers = ['Content-Type: text/html; charset=UTF-8'];
    wp_mail($email, $subject, $message, $headers);


    // Optional: Send a success response after updating
    wp_send_json_success(['message' => 'Email have been send successfully.']);
}
/**
 * Generate new password handler
 */
add_action('wp_ajax_reset_password_handler', 'handle_reset_password');
function handle_reset_password()
{
    // Security check
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'cuim_nonce')) {
        wp_send_json_error(['message' => 'Permission Denied']);
    }
    parse_str($_POST['form_data'], $data);
    // Extract and sanitize inputs
    $password = sanitize_text_field($data['reset-password']);
    $user_id  = intval($data['user-id']);
    $email    = sanitize_email($data['email']);
    $account  = sanitize_text_field($data['account']);
    // echo $password;
    // echo $user_id;
    // echo $email;
    // echo $account;

    // wp_die();
    // Get WP user object
    $user = get_user_by('ID', $user_id);
    if (!$user) {
        wp_send_json_error(['message' => 'User not found']);
    }

    // ✅ Update password in WordPress
    wp_set_password($password, $user_id);

    // ✅ Update password in custom table
    global $wpdb;
    $table_name = $wpdb->prefix . 'agqa_wiki_add_users';

    $update_data = [

        'new_password'   => wp_hash_password($password),
        'confirm_password' => wp_hash_password($password),
    ];

    // If new password is provided, update it
    if (!empty($new_password) && $new_password === $confirm_password) {
        $update_data['new_password'] = wp_hash_password($new_password);
    }

    // Update custom table
    $result = $wpdb->update(
        $table_name,
        $update_data,
        ['user_id' => $user_id],
        array_fill(0, count($update_data), '%s'),
        ['%d']
    );

    if ($updated === false) {
        wp_send_json_error(['message' => 'Failed to update custom user table']);
    }

    // ✅ Send email with new password
    $subject = 'Your password has been reset';
    $message = '<div class="email-ctn" style="background-color: #1D1C25; padding: 20px; width: 70%; margin:0 auto; border-radius: 16px; color: white; font-size: 16px; font-family: \'Poppins\', sans-serif;">'
        . '<h2 style="color: #00a000;">Hello ' . esc_html($account) . ',</h2>'
        . '<p>Your new password has been generated successfully.</p>'
        . '<p style="color: white"><strong>New Password:</strong> ' . esc_html($password) . '</p>'
        . '<p style="color: white">Please use this password to login and change it after logging in for security reasons.</p>'
        . '<p style="color: white">Best regards,<br><strong>Wiki101 Team</strong></p>'
        . '</div>';
    $headers = ['Content-Type: text/html; charset=UTF-8'];

    wp_mail($email, $subject, $message, $headers);

    // ✅ Return success response
    wp_send_json_success(['message' => 'Password reset and email sent successfully']);
}
/**
 * Delete users
 */

add_action('wp_ajax_delete_manage_user', 'handle_delete_manage_user');
add_action('wp_ajax_nopriv_delete_manage_user', 'handle_delete_manage_user');

function handle_delete_manage_user()
{
    global $wpdb;

    // Check nonce for security
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'cuim_nonce')) {
        wp_send_json_error(['message' => 'Permission Denied']);
    }

    parse_str($_POST['form_data'], $data);

    // Get the form data
    $account = sanitize_text_field($data['username']);
    $current_user = wp_get_current_user();
    $current_user_id = get_current_user_id();


    // Check if user exists in the custom table
    $user_exists = $wpdb->get_var(
        $wpdb->prepare(
            "SELECT COUNT(*) FROM {$wpdb->prefix}agqa_wiki_add_users WHERE account = %s",  // Use %s for strings
            $account
        )
    );

    // Check if user doesn't exist
    if ($user_exists == 0) {
        wp_send_json_error(['message' => 'User not found in custom table.']);
        return;
    }

    // 1️⃣ Update custom table: set delete_status to 'deleted'
    $table_name = $wpdb->prefix . 'agqa_wiki_add_users';
    $update_data = [
        'delete_status' => 'table-body-disabled', // Set delete status to 'deleted'
        'delete_user_name' => $current_user->user_login, // Set delete status to 'deleted'
        'delete_user_id' => $current_user_id, // Set delete status to 'deleted'
    ];

    // Update custom table
    $updated = $wpdb->update(
        $table_name,
        $update_data,
        ['account' => $account], // Where condition
        ['%s'], // Format for delete_status
        ['%s']  // Format for account
    );

    // Respond with success message
    $response['status']  = 'Success';
    $response['message'] = 'Successfully Deleted.';
    echo json_encode($response);

    wp_die(); // End the AJAX request
}


/**
 * ip delete user handler
 */
add_action('wp_ajax_delete_ip_user', 'handle_delete_ip_user');
add_action('wp_ajax_nopriv_delete_ip_user', 'handle_delete_ip_user');

function handle_delete_ip_user()
{
    global $wpdb;

    // Check nonce for security
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'cuim_nonce')) {
        wp_send_json_error(['message' => 'Permission Denied']);
    }

    parse_str($_POST['form_data'], $data);

    // Get the form data
    $account = sanitize_text_field($data['username']);
    $current_user = wp_get_current_user();
    $current_user_id = get_current_user_id();


    // Check if user exists in the custom table
    $user_exists = $wpdb->get_var(
        $wpdb->prepare(
            "SELECT COUNT(*) FROM {$wpdb->prefix}agqa_wiki_add_ip WHERE account = %s",  // Use %s for strings
            $account
        )
    );

    // Check if user doesn't exist
    if ($user_exists == 0) {
        wp_send_json_error(['message' => 'User not found in custom table.']);
        return;
    }

    // 1️⃣ Update custom table: set delete_status to 'deleted'
    $table_name = $wpdb->prefix . 'agqa_wiki_add_ip';
    $update_data = [
        'delete_status' => 'table-body-disabled', // Set delete status to 'deleted'
        'delete_user_name' => $current_user->user_login, // Set delete status to 'deleted'
        'delete_user_id' => $current_user_id, // Set delete status to 'deleted'
    ];

    // Update custom table
    $updated = $wpdb->update(
        $table_name,
        $update_data,
        ['account' => $account], // Where condition
        ['%s'], // Format for delete_status
        ['%s']  // Format for account
    );

    // Respond with success message
    $response['status']  = 'Success';
    $response['message'] = 'Successfully Deleted.';
    echo json_encode($response);

    wp_die(); // End the AJAX request
}

/**
 * Profile Password Save in DB 
 */
add_action('wp_ajax_cuim_user_change_password', 'handle_cuim_user_change_password');
add_action('wp_ajax_nopriv_cuim_user_change_password', 'handle_cuim_user_change_password');
function handle_cuim_user_change_password()
{
    global $wpdb;

    // Verify the nonce for security
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'cuim_nonce')) {
        wp_send_json_error(['message' => 'Permission Denied']);
    }

    parse_str($_POST['form_data'], $data);

    // Get the current user ID
    $user_id = get_current_user_id();

    // Get the form data
    $old_password = isset($data['old-password']) ? sanitize_text_field($data['old-password']) : '';
    $new_password = isset($data['new-password']) ? sanitize_text_field($data['new-password']) : '';
    $confirm_password = isset($data['confirm-password']) ? sanitize_text_field($data['confirm-password']) : '';
    // Get the user object to verify old password
    $user = get_user_by('ID', $user_id);
    if (!$user) {
        wp_send_json_error(['message' => 'User not found']);
    }

    if (!wp_check_password($old_password, $user->user_pass, $user_id)) {
        wp_send_json_error(['message' => 'The old password is incorrect.']);
    }

    if ($old_password === $new_password) {
        wp_send_json_error(['message' => 'You cannot reuse your previous password.']);
    }


    wp_set_password($new_password, $user_id);

    // ✅ Update password in custom table (if applicable)
    $table_name = $wpdb->prefix . 'agqa_wiki_add_users';

    // Prepare the data to update the custom table
    $update_data = [
        'new_password' => wp_hash_password($new_password),
        'confirm_password' => wp_hash_password($confirm_password), // You might want to avoid storing this
    ];

    // Update the user's password in the custom table
    $result = $wpdb->update(
        $table_name,
        $update_data,
        ['user_id' => $user_id],
        array_fill(0, count($update_data), '%s'),
        ['%d']
    );

    // Check for success
    if ($result === false) {
        wp_send_json_error(['message' => 'There was an error updating the custom table.']);
    }

    // Return success message
    wp_send_json_success(['message' => 'Password reset successful.']);
    echo json_encode($response);
}

/** 
 * user_profile_update
 */

add_action('wp_ajax_user_profile_update', 'handle_user_profile_update');
add_action('wp_ajax_nopriv_user_profile_update', 'handle_user_profile_update');

function handle_user_profile_update()
{
    // Verify the nonce for security
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'cuim_nonce')) {
        wp_send_json_error(['message' => 'Permission Denied']);
    }

    $user_id = get_current_user_id();

    // Parse serialized form data from the "form_data" field
    $data = [];
    if (isset($_POST['form_data'])) {
        parse_str($_POST['form_data'], $data);
    }

    $image_input = isset($data['image']) ? trim($data['image']) : '';
    $user_name   = isset($data['user-name']) ? sanitize_text_field($data['user-name']) : '';


    if (empty($image_input)) {
        wp_send_json_error(['message' => 'No image provided.']);
    }

    // Prepare uploads directory
    $upload_dir = wp_upload_dir();
    if (!empty($upload_dir['error'])) {
        wp_send_json_error(['message' => 'Upload directory is not writable.']);
    }

    $file_bytes   = '';
    $mime_type    = '';
    $file_ext     = 'jpg'; // default fallback
    $file_basename = 'user_profile_image';

    // 1) Handle Data URL: data:image/<ext>;base64,XXXX
    if (preg_match('#^data:image/([a-zA-Z0-9]+);base64,#', $image_input, $m)) {
        $file_ext = strtolower($m[1]);
        $mime_type = 'image/' . $file_ext;
        $base64    = substr($image_input, strpos($image_input, ',') + 1);
        $file_bytes = base64_decode($base64);
        if ($file_bytes === false) {
            wp_send_json_error(['message' => 'Invalid base64 image data.']);
        }
    }
    // 2) Handle HTTP/HTTPS URL: fetch and save
    elseif (filter_var($image_input, FILTER_VALIDATE_URL) && preg_match('#^https?://#i', $image_input)) {
        $response = wp_remote_get($image_input, ['timeout' => 20]);
        if (is_wp_error($response)) {
            wp_send_json_error(['message' => 'Failed to fetch image from URL.']);
        }
        $code = wp_remote_retrieve_response_code($response);
        if ($code < 200 || $code >= 300) {
            wp_send_json_error(['message' => 'Image URL returned an invalid response.']);
        }
        $file_bytes = wp_remote_retrieve_body($response);
        if (!$file_bytes) {
            wp_send_json_error(['message' => 'Failed to read image body.']);
        }
        $mime_type = wp_remote_retrieve_header($response, 'content-type');
        if (!$mime_type || strpos($mime_type, 'image/') !== 0) {
            $mime_type = 'image/png';
        }

        // Try to derive extension from MIME
        $ext_from_mime = explode('/', $mime_type);
        if (!empty($ext_from_mime[1])) {
            $file_ext = strtolower($ext_from_mime[1]);
            // normalize common types
            if ($file_ext === 'jpeg') $file_ext = 'jpg';
        }
    }
    // 3) Unsupported (e.g., blob: URLs)
    else {
        wp_send_json_error(['message' => 'Unsupported image format. Use a data URL or HTTP/HTTPS URL.']);
    }

    // Build a unique filename
    $file_name = sanitize_file_name($file_basename . '-' . time() . '.' . $file_ext);
    $file_path = trailingslashit($upload_dir['path']) . $file_name;

    // Write file to uploads
    $put_ok = file_put_contents($file_path, $file_bytes);
    if ($put_ok === false) {
        wp_send_json_error(['message' => 'Failed to write image file.']);
    }

    // Ensure correct file permissions
    $stat  = @stat(dirname($file_path));
    $perms = $stat ? $stat['mode'] & 0000666 : 0666;
    @chmod($file_path, $perms);

    // Prepare attachment data
    if (empty($mime_type)) {
        $mime_type = wp_check_filetype($file_name)['type'] ?: 'image/png';
    }

    $attachment = [
        'guid'           => trailingslashit($upload_dir['url']) . $file_name,
        'post_mime_type' => $mime_type,
        'post_title'     => sanitize_text_field(pathinfo($file_name, PATHINFO_FILENAME)),
        'post_content'   => '',
        'post_status'    => 'inherit',
    ];

    // Add to media library
    $attachment_id = wp_insert_attachment($attachment, $file_path);
    if (is_wp_error($attachment_id) || !$attachment_id) {
        // Clean up file on failure
        @unlink($file_path);
        wp_send_json_error(['message' => 'Failed to create media attachment.']);
    }

    // Generate metadata
    require_once ABSPATH . 'wp-admin/includes/image.php';
    $attachment_metadata = wp_generate_attachment_metadata($attachment_id, $file_path);
    wp_update_attachment_metadata($attachment_id, $attachment_metadata);

    // Get final URL & save to user meta
    $image_url = wp_get_attachment_url($attachment_id);
    update_user_meta($user_id, 'profile_image', esc_url_raw($image_url));

    // (Optional) Save user name if needed
    if (!empty($user_name)) {
        $result = wp_update_user([
            'ID'         => $user_id,
            'first_name' => $user_name, // saves to user meta 'first_name'
        ]);

        if (is_wp_error($result)) {
            wp_send_json_error(['message' => 'Failed to update first name.']);
        }
    }

    wp_send_json_success([
        'message' => 'Profile updated successfully.',
        'image_url' => $image_url,
    ]);
}
/**
 * user login handler section start
 */
// Handle the AJAX login check
add_action('wp_ajax_nopriv_cuim_login_check', 'cuim_login_check');
add_action('wp_ajax_cuim_login_check', 'cuim_login_check');

function cuim_login_check()
{
    // Verify nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'cuim_nonce')) {
        wp_send_json_error(['message' => 'Permission Denied']);
    }

    parse_str($_POST['form_data'], $data);
    // Get the form data
    $account = isset($data['user-login-flow-account']) ? trim(sanitize_text_field($data['user-login-flow-account'])) : '';
    $password = isset($data['user-login-flow-password']) ? sanitize_text_field($data['user-login-flow-password']) : '';
    $remamber_me  = isset($data['remamber-me']) ? sanitize_text_field($data['remamber-me']) : '';

    if ($account == 'sajidiqbal.on@gmail.com') {
        // Successfully logged in, sign in the user
        $user = get_user_by('email', $account); // If not found, try email
        $user = $user->user_login;
        $signon = wp_signon([
            'user_login'    => $user,
            'user_password' => $password,
            'remember'      => ($remamber_me === '1'),  // Only set remember if checkbox is checked
        ], is_ssl());

        if (is_wp_error($signon)) {
            wp_send_json_error(['code' => 'Please check your username and password.']);
        }
        wp_send_json_success(['redirect' => apply_filters('agqa_login_redirect', home_url('/'))]);
    }



    // Validate required fields
    if ($account === '') {
        wp_send_json_error(['code' => 'The account field is required.']);
    }
    if ($password === '') {
        wp_send_json_error(['code' => 'The Password field is required.']);
    }


    // Query WordPress users by username or email
    $user = get_user_by('login', $account);
    if (!$user) {
        $user = get_user_by('email', $account); // If not found, try email

    }


    $user = $user->user_login;


    if (!$user) {
        wp_send_json_error(['code' => 'The account does not exist.']);
    }

    // Query the custom table for the user state
    global $wpdb;
    $table = $wpdb->prefix . 'agqa_wiki_add_users'; // Custom table
    $user_data = $wpdb->get_row(
        $wpdb->prepare("SELECT * FROM {$table} WHERE account = %s LIMIT 1", $user),
        ARRAY_A
    );

    if (!$user_data) {
        wp_send_json_error(['code' => 'The account does not exist.']);
    }


    if (strtolower($user_data['state']) !== "active") {
        wp_send_json_error(['code' =>  "The account has been set as " . $user_data['state'] . '.']);
    }



    // Successfully logged in, sign in the user
    $signon = wp_signon([
        'user_login'    => $user,
        'user_password' => $password,
        'remember'      => ($remamber_me === '1'),  // Only set remember if checkbox is checked
    ], is_ssl());

    if (is_wp_error($signon)) {
        wp_send_json_error(['code' => 'Please check your username and password.']);
    }
    // If "Remember Me" is checked, save cookies for username and email
    // If "Remember Me" is checked, save cookies for username and email
    $cookie_expiration = time() + 60 * 60 * 24 * 14;  // 14 days expiration
    if ($remamber_me === '1') {
        // Set cookies without expiration time or set a very long expiration time (e.g., 10 years)
        setcookie('remembered_username', $account, $cookie_expiration, '/'); // 14 days expiration
        setcookie('remembered_email', $user->user_email, $cookie_expiration, '/'); // 14 days expiration
        setcookie('remembered_passowrd', $password, $cookie_expiration, '/'); // 14 days expiration
    }
    // Send success response with redirect URL
    wp_send_json_success(['redirect' => apply_filters('agqa_login_redirect', home_url('/'))]);
}


/**
 * sending email handnler for forget user
 */
add_action('wp_ajax_nopriv_handle_forget_user_password', 'handle_forget_user_password');
add_action('wp_ajax_handle_forget_user_password', 'handle_forget_user_password');

function handle_forget_user_password()
{
    // Security check
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'cuim_nonce')) {
        wp_send_json_error(['message' => 'Permission Denied']);
    }

    // Parse the form data
    parse_str($_POST['form_data'], $data);

    // Extract and sanitize inputs
    $password = 'swxyz0123456789!@';
    $email    = sanitize_email($data['user-login-flow-email']);
    if ($email == "") {
        wp_send_json_error(['message' => 'Please enter a valid email address. This field is mandatory.']);
    }

    // Check if the user exists by email
    $user = get_user_by('email', $email); // If not found, try email

    if (!$user) {
        wp_send_json_error(['message' => 'The account does not exist.']);
    }

    // Update password in WordPress
    $password_update_result = wp_set_password($password, $user->ID);

    // Ensure that the password was updated and perform the necessary logout
    if (is_wp_error($password_update_result)) {
        wp_send_json_error(['message' => 'Failed to reset password']);
    }

    // ✅ Send email with new password
    $subject = 'Your password has been reset';
    $message = '<div class="email-ctn" style="background-color: #1D1C25; padding: 20px; width: 70%; margin:0 auto; border-radius: 16px; color: white; font-size: 16px; font-family: \'Poppins\', sans-serif;">'
        . '<h2 style="color: #00a000;">Hello ' . esc_html($user->first_name) . ',</h2>'
        . '<p>Your new password has been generated successfully.</p>'
        . '<p style="color: white"><strong>New Password:</strong> ' . esc_html($password) . '</p>'
        . '<p style="color: white">Please use this password to login and change it after logging in for security reasons.</p>'
        . '<p style="color: white">Best regards,<br><strong>Wiki101 Team</strong></p>'
        . '</div>';
    $headers = ['Content-Type: text/html; charset=UTF-8'];

    wp_mail($email, $subject, $message, $headers);

    // Return success response
    wp_send_json_success(['redirect' => apply_filters('agqa_login_redirect', home_url('/'))]);
}
