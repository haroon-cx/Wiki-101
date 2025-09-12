<?php
// Action hook for AJAX requests
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
//     $state = sanitize_text_field($data['state']);
//     $user_role = sanitize_text_field($data['user-role']);
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

//     // Prepare user data for wp_users table
//     $wp_user_data = array(
//         'user_login' => $account,
//         'user_pass' => wp_hash_password($new_password),  // Hash the password
//         'user_email' => $email,
//         'display_name' => $account,
//         'role' => get_user_role_from_admin_selection($user_role)  // Get the role based on the selection
//     );

//     // Insert user into wp_users table
//     $user_id = wp_insert_user($wp_user_data);

//     // Check for errors during user insertion
//     if (is_wp_error($user_id)) {
//         wp_send_json_error(array('message' => $user_id->get_error_message()));
//     }

//     // Now insert the user into agqa_wiki_add_users table
//     $user_data = array(
//         'user_id' => $user_id,
//         'account' => $account,
//         'new_password' => wp_hash_password($new_password),  // Hash the password for security
//         'confirm_password' => wp_hash_password($confirm_password),
//         'state' => $state,
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
//         // If insert failed, log the error for debugging purposes
//         error_log('Error inserting user into agqa_wiki_add_users table');
//         wp_send_json_error(array('message' => 'Error inserting data into custom table.'));
//     }

//     // Send success response
//     wp_send_json_success(array('message' => 'User added/updated successfully.'));
// }

// // Function to map user role from the admin's selection
// function get_user_role_from_admin_selection($role)
// {
//     // Map the selected role to a WordPress role
//     switch ($role) {
//         case 'administrator':
//             return 'administrator';
//         case 'manager':
//             return 'editor'; // Assuming manager is an editor
//         case 'author':
//             return 'author';
//         case 'contributor':
//             return 'contributor';
//         case 'subscriber':
//             return 'subscriber';
//         default:
//             return 'subscriber'; // Default role
//     }
// }
add_action('wp_ajax_add_or_update_user', 'handle_add_or_update_user');
add_action('wp_ajax_nopriv_add_or_update_user', 'handle_add_or_update_user');

function handle_add_or_update_user()
{
    global $wpdb;

    // Check nonce for security
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'cuim_nonce')) {
        die('Permission Denied');
    }

    // Parse the form data
    parse_str($_POST['form_data'], $data);

    // Sanitize and assign data
    $account = sanitize_text_field($data['account']);
    $new_password = sanitize_text_field($data['new-password']);
    $confirm_password = sanitize_text_field($data['confirm-password']);
    $user_state = sanitize_text_field($data['state']);
    $user_role = sanitize_text_field($data['user-role']);  // The role selected from the form
    $user_role = strtolower($user_role);  // Convert the role to lowercase
    $company_name = sanitize_text_field($data['company-name']);
    $email = sanitize_email($data['email']);

    // Handle custom labels and fields
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
        wp_send_json_error(array('message' => 'Passwords do not match.'));
    }

    // Check if email is valid
    if (!is_email($email)) {
        wp_send_json_error(array('message' => 'Invalid email address.'));
    }

    // Check if the user already exists in wp_users
    $user = get_user_by('email', $email);

    if ($user) {
        wp_send_json_error(array('message' => 'User already exists.'));
    }

    // Map the selected role to a valid WordPress role
    $wp_role = map_user_role($user_role);  // Get WordPress role based on the selected role

    // Prepare user data for wp_users table
    $wp_user_data = array(
        'user_login' => $account,
        'user_pass' => wp_hash_password($new_password),  // Hash the password
        'user_email' => $email,
        'display_name' => $account,
        'role' => $wp_role  // Correctly assign the role in wp_users table
    );

    // Insert user into wp_users table
    $user_id = wp_insert_user($wp_user_data);

    // Check for errors during user insertion
    if (is_wp_error($user_id)) {
        wp_send_json_error(array('message' => $user_id->get_error_message()));
    }

    // Now insert the user into agqa_wiki_add_users table with user_id
    $user_data = array(
        'user_id' => $user_id,  // Insert the user_id here
        'account' => $account,
        'new_password' => wp_hash_password($new_password),  // Hash the password for security
        'confirm_password' => wp_hash_password($confirm_password),
        'state' => $user_state,
        'user_role' => $user_role,
        'company_name' => $company_name,
        'email' => $email,
        'custom_label_1' => $custom_label_1,
        'custom_label_2' => $custom_label_2,
        'custom_label_3' => $custom_label_3,
        'custom_label_4' => $custom_label_4,
        'custom_field_1' => $custom_field_1,
        'custom_field_2' => $custom_field_2,
        'custom_field_3' => $custom_field_3,
        'custom_field_4' => $custom_field_4
    );

    // Insert user data into agqa_wiki_add_users table
    $insert_result = $wpdb->insert("{$wpdb->prefix}agqa_wiki_add_users", $user_data);

    // Check for errors during agqa_wiki_add_users insertion
    if ($insert_result === false) {
        // Log detailed error information
        error_log('Error inserting user into agqa_wiki_add_users table. MySQL Error: ' . $wpdb->last_error);
        wp_send_json_error(array('message' => 'Error inserting data into custom table.'));
    }

    // Now insert user role into wp_usermeta table manually if not already done
    update_user_meta($user_id, '_wp_capabilities', array($wp_role => true));

    // Send success response
    wp_send_json_success(array('message' => 'User added/updated successfully.'));
}

// Function to map the user role selected in the form to a WordPress role
function map_user_role($role)
{
    // Map the selected role to a WordPress role
    switch ($role) {
        case 'admin':
            return 'administrator';  // 'admin' in form maps to WordPress 'administrator'
        case 'manager':
            return 'editor';  // 'manager' in form maps to WordPress 'editor'
        case 'contributor':
            return 'contributor';  // 'contributor' in form maps to WordPress 'contributor'
        case 'viewer':
            return 'subscriber';  // 'subscriber' in form maps to WordPress 'subscriber'
        default:
            return 'subscriber';  // Default role in case of an invalid selection
    }
}
