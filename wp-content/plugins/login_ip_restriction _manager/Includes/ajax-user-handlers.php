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
        wp_send_json_error(['message' => 'Passwords do not match.']);
    }
    if (!is_email($email)) {
        wp_send_json_error(['message' => 'Invalid email address.']);
    }
    if (username_exists($account)) {
        wp_send_json_error(['message' => 'This username is already taken.']);
    }
    if (email_exists($email)) {
        wp_send_json_error(['message' => 'User already exists.']);
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
        // If you must store, store hash; better: store NULL and drop these columns later.
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
        // If you added a DATE column:
        // 'created_at'   => current_time('Y-m-d'),
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
        $reset_url = network_site_url('wp-login.php?action=rp&key=' . rawurlencode($key) . '&login=' . rawurlencode($user->user_login), 'login');

        $subject = sprintf(__('Welcome to %s'), get_bloginfo('name'));
        $message = '<p>Hi ' . esc_html($account) . ',</p>'
            . '<p>Your account has been created successfully.</p>'
            . '<p><strong>Username:</strong> ' . esc_html($account) . '</p>'
            . '<p>Click the link below to set your password:</p>'
            . '<p><a href="' . esc_url($reset_url) . '">' . esc_html($reset_url) . '</a></p>'
            . '<p>Login page: <a href="' . esc_url(wp_login_url()) . '">' . esc_html(wp_login_url()) . '</a></p>'
            . '<p>Thanks,<br>' . esc_html(get_bloginfo('name')) . '</p>';

        $headers = ['Content-Type: text/html; charset=UTF-8'];
        wp_mail($email, $subject, $message, $headers);
    }

    // All good
    wp_send_json_success(['message' => 'User added and email sent.']);
}

// Map the selected form role to WP role
function map_user_role($role)
{
    switch ($role) {
        case 'admin':
            return 'administrator';
        case 'manager':
            return 'editor';
        case 'contributor':
            return 'contributor';
        case 'viewer':
            return 'subscriber';
        default:
            return 'subscriber';
    }
}
/**
 * Edit user handler
 */
// add_action('wp_ajax_edit_user_manage', 'handle_edit_user_manage');
// add_action('wp_ajax_nopriv_edit_user_manage', 'handle_edit_user_manage');

// function handle_edit_user_manage() {
//     global $wpdb;
//     // Check nonce for security
//     if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'cuim_nonce')) {
//         wp_send_json_error(['message' => 'Permission Denied']);
//     }

//     parse_str($_POST['form_data'], $data);
//     // echo '<pre>' . print_r($data, true) . '</pre>';

//     // Get the form data
//     $user_id = intval($data['user-id']);
//     $account = sanitize_text_field($data['account']);
//     $new_password = sanitize_text_field($data['new-password']);
//     $confirm_password = sanitize_text_field($data['confirm-password']);
//     $user_state = sanitize_text_field($data['state']);
//     $user_role_input = sanitize_text_field($data['user-role']);
//     $company_name = sanitize_text_field($data['company-name']);
//     $email = sanitize_email($data['email']);
//     $custom_label_1 = sanitize_text_field($data['custom-label-1']);
//     $custom_label_2 = sanitize_text_field($data['custom-label-2']);
//     $custom_label_3 = sanitize_text_field($data['custom-label-3']);
//     $custom_label_4 = sanitize_text_field($data['custom-label-4']);
//     $custom_field_1 = sanitize_text_field($data['custom-field-1']);
//     $custom_field_2 = sanitize_text_field($data['custom-field-2']);
//     $custom_field_3 = sanitize_text_field($data['custom-field-3']);
//     $custom_field_4 = sanitize_text_field($data['custom-field-4']);
  
//         $user_exists = $wpdb->get_var(
//             $wpdb->prepare(
//                 "SELECT COUNT(*) FROM {$wpdb->prefix}agqa_wiki_add_users WHERE user_id = %d", 
//                 $user_id
//             )
//         );
//     //   echo $user_id;
//     //   wp_die();


//     if ($user_exists == 0) {
//         wp_send_json_error(['message' => 'User not found in custom table.']);
//         return;
//     }

//     // Prepare the data to update in the custom table
//   $table_name = $wpdb->prefix . 'agqa_wiki_add_users';

// // Prepare the data to update
// $update_data = [
//     'account'        => $account,
//     'state'          => $user_state,
//     'user_role'      => $user_role_input,
//     'company_name'   => $company_name,
//     'email'          => $email,
//     'custom_label_1' => $custom_label_1,
//     'custom_label_2' => $custom_label_2,
//     'custom_label_3' => $custom_label_3,
//     'custom_label_4' => $custom_label_4,
//     'custom_field_1' => $custom_field_1,
//     'custom_field_2' => $custom_field_2,
//     'custom_field_3' => $custom_field_3,
//     'custom_field_4' => $custom_field_4,
// ];

//     // If new password is provided, update it
//     if (!empty($new_password) && $new_password === $confirm_password) {
//         $update_data['new_password'] = wp_hash_password($new_password);
//     }

// // Always proceed with the update, no need to check if data has changed
//     $result = $wpdb->update(
//         $table_name, 
//         $update_data,
//         array('user_id' => $user_id),  // Condition: where user_id = $user_id
//         array('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s'),  // Format for fields
//         array('%d')  // Format for user_id
//     );

//   // Check if the update was successful
//     if ($result === false) {
//         wp_send_json_error(['message' => 'Failed to update user data in custom table.']);
//         return;
//     }

//     wp_send_json_success(['message' => 'User data updated successfully in the custom table.']);

//     // Update the record in the default WordPress user table using wp_update_user
//     $user_data = [
//         'ID' => $user_id,
//         'user_login' => $account,
//         'user_email' => $email,
//         'display_name' => $account,
//         'role' => $user_role_input,
//     ];

//     // If password is provided, update the password as well
//     if (!empty($new_password) && $new_password === $confirm_password) {
//         $user_data['user_pass'] = wp_hash_password($new_password);
//     }

//     // Update WordPress user data
//     $user_update = wp_update_user($user_data);

//     if (is_wp_error($user_update)) {
//         wp_send_json_error(['message' => $user_update->get_error_message()]);
//         return;
//     }

//     // Send success response
//     $response['status']  = 'Success';
//     $response['message'] = 'Successfully Submitted';
//     echo json_encode($response);
//   wp_die(); // End the AJAX request
// }
add_action('wp_ajax_edit_user_manage', 'handle_edit_user_manage');
add_action('wp_ajax_nopriv_edit_user_manage', 'handle_edit_user_manage');

function map_user_roles($role)
{
    // Map the custom user role to WordPress role
    switch (strtolower($role)) {
        case 'admin':
            return 'administrator';
        case 'manager':
            return 'editor';
        case 'contributor':
            return 'contributor';
        case 'viewer':
            return 'subscriber';
        default:
            return 'subscriber'; // Default to subscriber if role is not matched
    }
}

function handle_edit_user_manage() {
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