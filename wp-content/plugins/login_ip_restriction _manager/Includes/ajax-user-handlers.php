<?php
// Action hook for AJAX requests
add_action('wp_ajax_add_or_update_user', 'handle_add_or_update_user');
add_action('wp_ajax_nopriv_add_or_update_user', 'handle_add_or_update_user');

function handle_add_or_update_user()
{
    global $wpdb;

    // Check nonce for security (optional)
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'cuim_nonce')) {
        die('Permission Denied');
    }
    parse_str($_POST['form_data'], $data);
    // Get the data from the request
    $account = sanitize_text_field($data['account']);
    $new_password = sanitize_text_field($data['new_password']);
    $confirm_password = sanitize_text_field($data['confirm_password']);
    $state = sanitize_text_field($data['state']);
    $user_role = sanitize_text_field($data['user_role']);
    $company_name = sanitize_text_field($data['company_name']);
    $email = sanitize_email($data['email']);
    $custom_labels = array_map('sanitize_text_field', $data['custom_labels']);
    $custom_fields = array_map('sanitize_text_field', $data['custom_fields']);

    // Basic validation
    if ($new_password !== $confirm_password) {
        wp_send_json_error(array('message' => 'Passwords do not match.'));
    }

    // Prepare the data
    $data = array(
        'account' => $account,
        'new_password' => wp_hash_password($new_password),  // Hash password
        'confirm_password' => wp_hash_password($confirm_password),
        'state' => $state,
        'user_role' => $user_role,
        'company_name' => $company_name,
        'email' => $email,
        'custom_label_1' => $custom_labels[0],
        'custom_label_2' => $custom_labels[1],
        'custom_label_3' => $custom_labels[2],
        'custom_label_4' => $custom_labels[3],
        'custom_field_1' => $custom_fields[0],
        'custom_field_2' => $custom_fields[1],
        'custom_field_3' => $custom_fields[2],
        'custom_field_4' => $custom_fields[3]
    );

    // Determine if it's an insert or update
    $user_id = isset($data['user_id']) ? intval($data['user_id']) : 0;
    if ($user_id) {
        // Update the existing user
        $wpdb->update(
            "{$wpdb->prefix}agqa_wiki_add_users",
            $data,
            array('id' => $user_id)
        );
    } else {
        // Insert a new user
        $wpdb->insert("{$wpdb->prefix}agqa_wiki_add_users", $data);
    }

    // Send a success response
    wp_send_json_success(array('message' => 'User added/updated successfully.'));
}
