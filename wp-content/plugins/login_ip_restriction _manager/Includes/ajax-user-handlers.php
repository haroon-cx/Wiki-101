<?php
// add_action('wp_ajax_add_or_update_user', 'handle_add_or_update_user');
// add_action('wp_ajax_nopriv_add_or_update_user', 'handle_add_or_update_user');

// function handle_add_or_update_user()
// {
//     global $wpdb;

//     // Check nonce for security
//     if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'cuim_nonce')) {
//         die('Permission Denied');
//     }

//     // Parse the form data
//     parse_str($_POST['form_data'], $data);

//     // Sanitize and assign data
//     $account = sanitize_text_field($data['account']);
//     $new_password = sanitize_text_field($data['new-password']);
//     $confirm_password = sanitize_text_field($data['confirm-password']);
//     $user_state = sanitize_text_field($data['state']);
//     $user_role = sanitize_text_field($data['user-role']);  // The role selected from the form
//     $user_role = strtolower($user_role);  // Convert the role to lowercase
//     $company_name = sanitize_text_field($data['company-name']);
//     $email = sanitize_email($data['email']);

//     // Handle custom labels and fields
//     $custom_label_1 = isset($data['custom-label-1']) ? sanitize_text_field($data['custom-label-1']) : '';
//     $custom_label_2 = isset($data['custom-label-2']) ? sanitize_text_field($data['custom-label-2']) : '';
//     $custom_label_3 = isset($data['custom-label-3']) ? sanitize_text_field($data['custom-label-3']) : '';
//     $custom_label_4 = isset($data['custom-label-4']) ? sanitize_text_field($data['custom-label-4']) : '';
//     $custom_field_1 = isset($data['custom-field-1']) ? sanitize_text_field($data['custom-field-1']) : '';
//     $custom_field_2 = isset($data['custom-field-2']) ? sanitize_text_field($data['custom-field-2']) : '';
//     $custom_field_3 = isset($data['custom-field-3']) ? sanitize_text_field($data['custom-field-3']) : '';
//     $custom_field_4 = isset($data['custom-field-4']) ? sanitize_text_field($data['custom-field-4']) : '';

//     // Basic validation
//     if ($new_password !== $confirm_password) {
//         wp_send_json_error(array('message' => 'Passwords do not match.'));
//     }

//     // Check if email is valid
//     if (!is_email($email)) {
//         wp_send_json_error(array('message' => 'Invalid email address.'));
//     }

//     // Check if the user already exists in wp_users
//     $user = get_user_by('email', $email);

//     if ($user) {
//         wp_send_json_error(array('message' => 'User already exists.'));
//     }

//     // Map the selected role to a valid WordPress role
//     $wp_role = map_user_role($user_role);  // Get WordPress role based on the selected role

//     // Prepare user data for wp_users table
//     $wp_user_data = array(
//         'user_login' => $account,
//         'user_pass' => wp_hash_password($new_password),  // Hash the password
//         'user_email' => $email,
//         'display_name' => $account,
//         'role' => $wp_role  // Correctly assign the role in wp_users table
//     );

//     // Insert user into wp_users table
//     $user_id = wp_insert_user($wp_user_data);

//     // Check for errors during user insertion
//     if (is_wp_error($user_id)) {
//         wp_send_json_error(array('message' => $user_id->get_error_message()));
//     }

//     // Now insert the user into agqa_wiki_add_users table with user_id
//     $user_data = array(
//         'user_id' => $user_id,  // Insert the user_id here
//         'account' => $account,
//         'new_password' => wp_hash_password($new_password),  // Hash the password for security
//         'confirm_password' => wp_hash_password($confirm_password),
//         'state' => $user_state,
//         'user_role' => $user_role,
//         'company_name' => $company_name,
//         'email' => $email,
//         'custom_label_1' => $custom_label_1,
//         'custom_label_2' => $custom_label_2,
//         'custom_label_3' => $custom_label_3,
//         'custom_label_4' => $custom_label_4,
//         'custom_field_1' => $custom_field_1,
//         'custom_field_2' => $custom_field_2,
//         'custom_field_3' => $custom_field_3,
//         'custom_field_4' => $custom_field_4
//     );

//     // Insert user data into agqa_wiki_add_users table
//     $insert_result = $wpdb->insert("{$wpdb->prefix}agqa_wiki_add_users", $user_data);

//     // Check for errors during agqa_wiki_add_users insertion
//     if ($insert_result === false) {
//         // Log detailed error information
//         error_log('Error inserting user into agqa_wiki_add_users table. MySQL Error: ' . $wpdb->last_error);
//         wp_send_json_error(array('message' => 'Error inserting data into custom table.'));
//     }

//     // Now insert user role into wp_usermeta table manually if not already done
//     update_user_meta($user_id, '_wp_capabilities', array($wp_role => true));

//     // Send success response
//     wp_send_json_success(array('message' => 'User added/updated successfully.'));
// }

// // Function to map the user role selected in the form to a WordPress role
// function map_user_role($role)
// {
//     // Map the selected role to a WordPress role
//     switch ($role) {
//         case 'admin':
//             return 'administrator';  // 'admin' in form maps to WordPress 'administrator'
//         case 'manager':
//             return 'editor';  // 'manager' in form maps to WordPress 'editor'
//         case 'contributor':
//             return 'contributor';  // 'contributor' in form maps to WordPress 'contributor'
//         case 'viewer':
//             return 'subscriber';  // 'subscriber' in form maps to WordPress 'subscriber'
//         default:
//             return 'subscriber';  // Default role in case of an invalid selection
//     }
// }
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

    // ✅ Send welcome email with password reset link (safer than emailing password)
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
add_action('wp_ajax_edit_user_manage', 'handle_edit_user_manage');
add_action('wp_ajax_nopriv_edit_user_manage', 'handle_edit_user_manage');

function handle_edit_user_manage()
{
    // Check nonce for security
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'cuim_nonce')) {
        wp_send_json_error(['message' => 'Permission Denied']);
    }
    parse_str($_POST['form_data'], $data);
    // Get the form data
    $user_id = intval($data['user_id']);
    $account = sanitize_text_field($data['account']);
    $password = sanitize_text_field($data['password']);
    $email = sanitize_email($data['email']);
    $state = sanitize_text_field($data['state']);
    $user_role = sanitize_text_field($data['user_role']);
    $company_name = sanitize_text_field($data['company_name']);

    // Ensure the user exists
    $user = get_user_by('id', $user_id);
    if (!$user) {
        wp_send_json_error(array('message' => 'User not found.'));
        return;
    }

    // Update user data
    $user_data = array(
        'ID' => $user_id,
        'user_login' => $account,
        'user_email' => $email,
        'display_name' => $account,
        'role' => $user_role,
    );

    if (!empty($password)) {
        $user_data['user_pass'] = wp_hash_password($password);  // Set password if provided
    }

    $user_update = wp_update_user($user_data);

    if (is_wp_error($user_update)) {
        wp_send_json_error(array('message' => $user_update->get_error_message()));
        return;
    }

    // Update user meta (for custom fields, user state, etc.)
    update_user_meta($user_id, 'company_name', $company_name);
    update_user_meta($user_id, 'state', $state);

    // Send success response
    wp_send_json_success(array('message' => 'User data updated successfully.'));
}
