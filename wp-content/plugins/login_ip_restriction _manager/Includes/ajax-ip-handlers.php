<?php
// Save IP
// add_action('wp_ajax_cuim_save_ip', function () {
//     check_ajax_referer('cuim_nonce', 'security');
//     if (!current_user_can('administrator')) wp_send_json_error(__('Permission denied.', 'custom-user-ip-manager'));

//     $uid = intval($_POST['user_id'] ?? 0);
//     $ip  = sanitize_text_field($_POST['ip'] ?? '');

//     if ($uid <= 0) wp_send_json_error(__('Invalid user.', 'custom-user-ip-manager'));

//     update_user_meta($uid, 'allowed_ip', $ip);
//     wp_send_json_success(__('IP updated.', 'custom-user-ip-manager'));
// });

// add_action('wp_ajax_cuim_get_ip_list', function () {
//     check_ajax_referer('cuim_nonce', 'security');

//     if (!current_user_can('administrator') && !current_user_can('editor')) {
//         wp_send_json_error('Permission denied.');
//     }

//     $users = get_users(['role__not_in' => ['administrator']]);
//     $result = [];

//     foreach ($users as $user) {
//         $result[] = [
//             'id'    => $user->ID,
//             'email' => $user->user_email,
//             'ip'    => get_user_meta($user->ID, 'allowed_ip', true),
//         ];
//     }


//     wp_send_json_success($result);
// });


// // Delete IP
// add_action('wp_ajax_cuim_delete_ip', function () {
//     check_ajax_referer('cuim_nonce', 'security');
//     if (!current_user_can('administrator')) wp_send_json_error('Permission denied.');

//     $uid = intval($_POST['user_id'] ?? 0);
//     if ($uid <= 0) wp_send_json_error('Invalid user.');

//     delete_user_meta($uid, 'allowed_ip');
//     wp_send_json_success('IP deleted.');
// });

/**
 * Ajax Comparision (Check Account for IP to User)
 */

// add_action('wp_ajax_check_user_account', 'handle_check_user_account');
// add_action('wp_ajax_nopriv_check_user_account', 'handle_check_user_account');

// function handle_check_user_account()
// {
//     global $wpdb;

//     // Check nonce for security
//     if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'cuim_nonce')) {
//         wp_send_json_error(['message' => 'Permission Denied']);
//     }

//     parse_str($_POST['form_data'], $data);

//     // Get the form data
//     $account = sanitize_text_field($data['account-name']);

//     // Check if user exists in the custom table
//     $user_exists = $wpdb->get_var(
//         $wpdb->prepare(
//             "SELECT COUNT(*) FROM {$wpdb->prefix}agqa_wiki_add_users WHERE account = %s",  // Use %s for strings
//             $account
//         )
//     );

//     // Check if user doesn't exist
//     if ($user_exists == 0) {
//         wp_send_json_error(['message' => 'Account not found']);
//         return;
//     }

//     // Check if user exists in the custom table
//     $user_exists = $wpdb->get_var(
//         $wpdb->prepare(
//             "SELECT COUNT(*) FROM {$wpdb->prefix}agqa_wiki_add_ip WHERE account = %s",  // Use %s for strings
//             $account
//         )
//     );


//     // Check if user doesn't exist
//     if ($user_exists == 1) {
//         wp_send_json_error(['message' => 'The IP whitelist record already exists for this member']);
//         return;
//     }

//     wp_send_json_success(['message' => 'Account success']);

//     wp_die(); // End the AJAX request
// }

add_action('wp_ajax_check_user_account', 'handle_check_user_account');
add_action('wp_ajax_nopriv_check_user_account', 'handle_check_user_account');

function handle_check_user_account()
{
    global $wpdb;

    // Check nonce for security
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'cuim_nonce')) {
        wp_send_json_error(['message' => 'Permission Denied']);
    }

    parse_str($_POST['form_data'], $data);

    // Get the form data
    $account = sanitize_text_field($data['account-name']);

    // Check if user exists in users table
    $user_exists = $wpdb->get_var(
        $wpdb->prepare(
            "SELECT COUNT(*) FROM {$wpdb->prefix}agqa_wiki_add_users WHERE account = %s",
            $account
        )
    );

    if ($user_exists == 0) {
        wp_send_json_error(['message' => 'Account not found']);
        return;
    }

    // Fetch last IP whitelist record for the user
    $last_ip_record = $wpdb->get_row(
        $wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}agqa_wiki_add_ip WHERE account = %s ORDER BY id DESC LIMIT 1",
            $account
        )
    );

    if ($last_ip_record) {
        // Agar delete_status table-body-disabled hai to allow karo
        if ($last_ip_record->delete_status === 'table-body-disabled') {
            wp_send_json_success(['message' => 'IP whitelist record can be added again (last was disabled).']);
            return;
        } else {
            // Agar active record already hai to error bhejo
            wp_send_json_error(['message' => 'The IP whitelist record already exists for this member']);
            return;
        }
    }

    // Agar koi IP record nahi mila, to allow karo
    wp_send_json_success(['message' => 'Account success']);
    wp_die();
}

/**
 * handle_add_user_ip
 */

add_action('wp_ajax_handle_add_user_ip', 'handle_add_user_ip');
add_action('wp_ajax_nopriv_handle_add_user_ip', 'handle_add_user_ip');

function handle_add_user_ip()
{
    global $wpdb;

    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'cuim_nonce')) {
        wp_send_json_error(['message' => 'Permission Denied']);
    }

    parse_str($_POST['form_data'], $data);
    $user_id       = get_current_user_id();
    $account       = sanitize_text_field($data['account-name']);
    $ip_ipv4       = sanitize_text_field($data['ip-ipv4']);
    $ip_ipv6       = sanitize_text_field($data['ip-ipv6']);

    // Insert into custom table (avoid storing confirm_password; ideally don't store passwords at all)
    $insert_data = [
        'user_id'        => $user_id,
        'account'        => $account,
        'ipv4'        => $ip_ipv4,
        'ipv6'        => $ip_ipv6,
        'created_at'   => current_time('Y-m-d')
    ];
    $result = $wpdb->insert("{$wpdb->prefix}agqa_wiki_add_ip", $insert_data);
    if ($result === false) {
        error_log('Custom table insert error: ' . $wpdb->last_error);
        wp_send_json_error(['message' => 'Error inserting data into custom table.']);
    }


    // All good
    wp_send_json_success([
        'message' => 'Submit Successful'
    ]);
}


/**
 * handle_edit_user_ip_update
 */

add_action('wp_ajax_handle_edit_user_ip_update', 'handle_edit_user_ip_update');
add_action('wp_ajax_nopriv_handle_edit_user_ip_update', 'handle_edit_user_ip_update');

function handle_edit_user_ip_update()
{
    global $wpdb;

    // Check nonce for security
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'cuim_nonce')) {
        wp_send_json_error(['message' => 'Permission Denied']);
    }

    // Parse form data
    parse_str($_POST['form_data'], $data);
    $id       = sanitize_text_field($data['ip-edit-id']);
    $ip_ipv4  = sanitize_text_field($data['ip-ipv4']);
    $ip_ipv6  = sanitize_text_field($data['ip-ipv6']);

    // Validate ID (ensure it's numeric and exists)
    if (!is_numeric($id)) {
        wp_send_json_error(['message' => 'Invalid ID']);
    }

    // Prepare data for updating
    $update_data = [
        'ipv4' => $ip_ipv4,
        'ipv6' => $ip_ipv6,
    ];

    // Update the record in the custom table
    $result = $wpdb->update(
        "{$wpdb->prefix}agqa_wiki_add_ip",  // Table name
        $update_data,                      // Data to update
        ['id' => $id],                     // Condition: update record with the specified ID
        ['%s', '%s'],                      // Format for the fields (IPv4 and IPv6 are strings)
        ['%d']                             // Format for the ID (integer)
    );

    // Check for success or failure
    if ($result === false) {
        // Log error with query and last error
        error_log('Error updating IP data: ' . $wpdb->last_error);
        wp_send_json_error(['message' => 'Error updating data.']);
    }

    // Success response
    wp_send_json_success(['message' => 'Successfully Updated']);
}
