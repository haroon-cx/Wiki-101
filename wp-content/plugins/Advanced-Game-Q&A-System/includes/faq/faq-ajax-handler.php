<?php

// AJAX Handler: Insert new FAQ
function agqa_insert_review_faq()
{
    global $wpdb;

    // Verify nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'agqa_nonce')) {
        die('Permission Denied');
    }
    parse_str($_POST['form_data'], $data); // Parse serialized form data
    // echo $data['faq-question'];
    // wp_die();

    // Get data from AJAX request
    $question = sanitize_text_field($data['faq-question']);
    $answer = wp_kses_post($data['faq-answer']);
    $answer = preg_replace('/<p[^>]*data-f-id="pbf"[^>]*>.*?Powered by.*?Froala Editor.*?<\/p>/is', '', $answer);
    $verified_answer = 0;
    $faq_category = sanitize_text_field($data['faq-category']);

    // Insert the FAQ into the database
    $current_user_id = get_current_user_id();
    $wpdb->insert(
        "{$wpdb->prefix}agqa_approval_review_page",
        array(
            'faq_id' => 0,
            'question' => $question,
            'answer' => $answer,
            'verified_answer' => $verified_answer,
            'faq_category' => $faq_category,
            'status' => 'Pending',
            'type_name' => 'FAQ Add',
            'user_id' => $current_user_id,
            'created_at' => current_time('mysql'),

        ),
        array(
            '%s',
            '%s',
            '%s',
            '%s'
        )
    );

    // If everything went well, return success
    $response['status']  = 'Success';
    $response['message'] = 'Success: Provider data updated!';
    echo json_encode($response);
    wp_die();
}
add_action('wp_ajax_agqa_insert_review_faq', 'agqa_insert_review_faq'); // For logged-in users
add_action('wp_ajax_nopriv_agqa_insert_review_faq', 'agqa_insert_review_faq'); // For non-logged-in users


/**
 * FAQ Approvel handler
 */

function handle_faq_review_approval()
{
    global $wpdb;

    // Ensure nonce is valid for security
    if (! isset($_POST['nonce']) || ! wp_verify_nonce($_POST['nonce'], 'agqa_nonce')) {
        die('Permission Denied');
    }

    parse_str($_POST['form_data'], $data); // Parse serialized form data


    // Get posted data (faq_id, status)
    $faq_id = intval($data['faq-id']);
    $status = sanitize_text_field($data['status']);
    $review_id = intval($data['review-id']);
    $question = sanitize_text_field($data['faq-question']);
    $answer = wp_kses_post($data['faq-answer']);
    $answer = preg_replace('/<p[^>]*data-f-id="pbf"[^>]*>.*?Powered by.*?Froala Editor.*?<\/p>/is', '', $answer);
    $faq_category = sanitize_text_field($data['faq-category']);

    // Check if the review status is "approved"
    if ($status == 'approve') {

        // If faq_id is not 0, check if the FAQ already exists in agqa_faq
        if ($faq_id > 0) {
            // Retrieve the FAQ data from agqa_faq
            $faq_data = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}agqa_faq WHERE id = %d", $faq_id));

            if ($faq_data) {
                // Insert the review into agqa_faq_history table
                $inserted = $wpdb->insert(
                    "{$wpdb->prefix}agqa_faq_history",
                    array(
                        'faq_id' => $faq_id,
                        'question' => $faq_data->question,
                        'answer' => $faq_data->answer,
                        'verified_answer' => $faq_data->verified_answer,
                        'faq_category' => $faq_data->faq_category,
                        'user_id' => get_current_user_id(), // Use current user ID
                    )
                );

                if ($inserted) {
                    // Now update the agqa_faq table with the new data
                    $updated_faq = $wpdb->update(
                        "{$wpdb->prefix}agqa_faq",
                        array(
                            'question' => $question,
                            'answer' => $answer,
                            'verified_answer' => 1,
                            'faq_category' => $faq_category,
                            'user_id' => get_current_user_id(),

                        ),
                        array('id' => $faq_id) // Update the FAQ where the ID matches
                    );

                    // Check if FAQ was updated successfully
                    if ($updated_faq !== false) {
                        // Update the FAQ review status to 'approved'
                        $update_status = $wpdb->update(
                            "{$wpdb->prefix}agqa_approval_review_page",
                            array(
                                'status' => 'approved',  // Set the status to approved
                            ),
                            array('id' => $review_id) // Update the review based on its ID
                        );

                        if ($update_status !== false) {
                            $response['status']  = 'Success';
                            $response['message'] = 'Successfully Submitted';
                            echo json_encode($response);
                        } else {
                            echo 'Failed to update the review status.';
                        }
                    } else {
                        echo 'Failed to update the FAQ.';
                    }
                } else {
                    echo 'Failed to insert into FAQ history.';
                }
            }
        } else {

            // Insert new FAQ into agqa_faq table
            $wpdb->insert(
                "{$wpdb->prefix}agqa_faq",
                array(
                    'question' => $question,
                    'answer' => $answer,
                    'verified_answer' => 1,
                    'faq_category' => $faq_category,
                    'user_id' => get_current_user_id(), // Current user ID
                )
            );
            $faq_id = $wpdb->insert_id; // Get the ID of the newly inserted FAQ

            // Update the FAQ review status to 'approved' and set the faq_id in the review
            $wpdb->update(
                "{$wpdb->prefix}agqa_approval_review_page",
                array(
                    'status' => 'approved', // Set status to approved
                    'faq_id' => $faq_id, // Set the newly inserted faq_id in the review
                ),
                array('id' => $review_id) // Update the specific review ID
            );

            // If everything went well, return success
            $response['status']  = 'Success';
            $response['message'] = 'Successfully Submitted';
            echo json_encode($response);
        }
    } else {
        echo 'Invalid status or no action taken.';
    }

    wp_die(); // End AJAX request
}

// Hook the action to an AJAX call
add_action('wp_ajax_approve_faq_review', 'handle_faq_review_approval');
add_action('wp_ajax_nopriv_approve_faq_review', 'handle_faq_review_approval'); // For non-logged-in users



/**
 * handle_reject_faq_review_approval
 */

function handle_reject_faq_review_approval()
{
    global $wpdb;

    // Ensure nonce is valid for security
    if (! isset($_POST['nonce']) || ! wp_verify_nonce($_POST['nonce'], 'agqa_nonce')) {
        die('Permission Denied');
    }

    parse_str($_POST['form_data'], $data); // Parse serialized form data

    $review_id = intval($data['review-id']);
    $wpdb->update(
        "{$wpdb->prefix}agqa_approval_review_page",
        array(
            'status' => 'rejected',
        ),
        array('id' => $review_id) // Update the specific review ID
    );

    // If everything went well, return success
    $response['status']  = 'Success';
    $response['message'] = 'Successfully Submitted';
    echo json_encode($response);



    wp_die(); // End AJAX request
}

// Hook the action to an AJAX call
add_action('wp_ajax_handle_reject_faq_review_approval', 'handle_reject_faq_review_approval');
add_action('wp_ajax_nopriv_handle_reject_faq_review_approval', 'handle_reject_faq_review_approval');


/**
 * Edit form FAQ handler
 */

function agqa_edit_faq()
{
    global $wpdb;

    // Verify nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'agqa_nonce')) {
        die('Permission Denied');
    }
    parse_str($_POST['form_data'], $data); // Parse serialized form data

    // Get data from AJAX request
    $faq_id = sanitize_text_field($data['faq-id']);
    $question = sanitize_text_field($data['faq-question']);
    $answer = sanitize_textarea_field($data['faq-answer']);

    $verified_answer = 0;
    $faq_category = sanitize_text_field($data['faq-category']);

    // First remove the "Powered by Froala Editor" text (case-insensitive)
    $answer = preg_replace('/Powered by.*?Froala Editor.*?/is', '', $answer);

    // Remove any empty <p> or <span> tags
    $answer = preg_replace('/<p[^>]*>\s*<\/p>/is', '', $answer);
    $answer = preg_replace('/<span[^>]*>\s*<\/span>/is', '', $answer);

    // Trim any leading or trailing spaces
    $answer = trim($answer);
    $user_id = get_current_user_id();
    // Insert the FAQ into the database
    $wpdb->insert(
        "{$wpdb->prefix}agqa_approval_review_page",
        array(
            'faq_id' => $faq_id,
            'question' => $question,
            'answer' => $answer,
            'type_name' => 'FAQ Edit',
            'verified_answer' => $verified_answer,
            'faq_category' => $faq_category,
            'user_id' => $user_id,
        ),
        array(
            '%s', // question
            '%s', // answer
            '%s', // verified_answer
        )
    );

    // If everything went well, return success
    $response['status']  = 'Success';
    $response['message'] = 'Successfully Submitted';
    echo json_encode($response);
    wp_die();
}
add_action('wp_ajax_agqa_edit_faq', 'agqa_edit_faq');
add_action('wp_ajax_nopriv_agqa_edit_faq', 'agqa_edit_faq');


/**
 * delete_faq
 */

function delete_faq()
{
    global $wpdb;
    // Verify nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'agqa_nonce')) {
        die('Permission Denied');
    }
    parse_str($_POST['form_data'], $data);

    // Get the form data
    $faqID = sanitize_text_field($data['faq_id']);
    $current_user = wp_get_current_user();
    $current_user_id = get_current_user_id();


    // Check if user exists in the custom table
    $user_exists = $wpdb->get_var(
        $wpdb->prepare(
            "SELECT COUNT(*) FROM {$wpdb->prefix}agqa_faq WHERE id = %s",  // Use %s for strings
            $faqID
        )
    );

    // Check if user doesn't exist
    if ($user_exists == 0) {
        wp_send_json_error(['message' => 'User not found in custom table.']);
        return;
    }

    // 1️⃣ Update custom table: set delete_status to 'deleted'
    $table_name = $wpdb->prefix . 'agqa_faq';
    $update_data = [
        'delete_status' => 'table-body-disabled', // Set delete status to 'deleted'
        'delete_user_name' => $current_user->user_login, // Set delete status to 'deleted'
        'delete_user_id' => $current_user_id, // Set delete status to 'deleted'
        'delete_user_date' => current_time('mysql'), // Set delete status to 'deleted'
    ];

    // Update custom table
    $updated = $wpdb->update(
        $table_name,
        $update_data,
        ['id' => $faqID], // Where condition
        ['%s'], // Format for delete_status
        ['%s']  // Format for account
    );

    // Respond with success message
    $response['status']  = 'Success';
    $response['message'] = 'Successfully Deleted.';
    echo json_encode($response);

    wp_die(); // End the AJAX request
}
add_action('wp_ajax_delete_faq', 'delete_faq');
add_action('wp_ajax_nopriv_delete_faq', 'delete_faq');

/**
 * like dislike handler
 */


function handle_like_dislike_action()
{
    // Check nonce for security
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'agqa_nonce')) {
        wp_send_json_error(['message' => 'Permission denied.']);
    }

    // Parse the form_data from AJAX request
    parse_str($_POST['form_data'], $data);
    $faq_id = intval($data['faq-id']);
    $action_type = sanitize_text_field($data['like']); // '1' = like, '0' = dislike
    $user_id = get_current_user_id();

    if (!$user_id) {
        wp_send_json_error(['message' => 'You must be logged in to like/dislike.']);
    }

    global $wpdb;
    $table = $wpdb->prefix . 'agqa_faq_likes_dislikes';

    // Check if a record already exists
    $existing_action = $wpdb->get_var($wpdb->prepare("
        SELECT action_type 
        FROM $table 
        WHERE faq_id = %d AND user_id = %d
    ", $faq_id, $user_id));

    if ($existing_action !== null) {
        if ($existing_action == 1 && $action_type == 1) {
            // User already liked, so delete the like record
            $wpdb->delete(
                $table,
                ['faq_id' => $faq_id, 'user_id' => $user_id],
                ['%d', '%d']
            );
            $message = 'Your like has been removed.';
        } elseif ($existing_action == 0 && $action_type == 0) {
            // User already disliked, so delete the dislike record
            $wpdb->delete(
                $table,
                ['faq_id' => $faq_id, 'user_id' => $user_id],
                ['%d', '%d']
            );
            $message = 'Your dislike has been removed.';
        } else {
            // User wants to change the action (like -> dislike or dislike -> like)
            $wpdb->update(
                $table,
                ['action_type' => $action_type],
                ['faq_id' => $faq_id, 'user_id' => $user_id],
                ['%d'],
                ['%d', '%d']
            );
            $message = 'Your preference has been updated.';
        }
    } else {
        // No record exists, insert new
        $wpdb->insert(
            $table,
            [
                'faq_id' => $faq_id,
                'user_id' => $user_id,
                'action_type' => $action_type
            ],
            ['%d', '%d', '%d']
        );
        $message = 'Your preference has been saved.';
    }

    // Optionally, return updated counts
    $like_count = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM $table WHERE faq_id = %d AND action_type = 1",
        $faq_id
    ));
    $dislike_count = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM $table WHERE faq_id = %d AND action_type = 0",
        $faq_id
    ));

    $response['status'] = 'Success';
    $response['message'] = $message;
    $response['like_count'] = $like_count;
    $response['dislike_count'] = $dislike_count;

    echo json_encode($response);

    wp_die(); // End the request
}

add_action('wp_ajax_like_dislike_action', 'handle_like_dislike_action');
add_action('wp_ajax_nopriv_like_dislike_action', 'handle_like_dislike_action');

/**
 * FAQ Delete Table handler
 */
function handle_faq_deletion()
{
    // Verify nonce for security
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'agqa_nonce')) {
        die('Permission Denied');
    }

    parse_str($_POST['form_data'], $data);

    // Check if faq_id is set and valid
    global $wpdb;
    $faq_id = intval($data['faq_id']); // Get the FAQ ID from the request

    // Delete the FAQ from the agqa_faq table
    $table_faq = $wpdb->prefix . 'agqa_faq';
    // $table_history = $wpdb->prefix . 'agqa_faq_history'; // If you want to also delete from history

    // Delete FAQ from agqa_faq table
    $wpdb->delete($table_faq, array('id' => $faq_id));

    // Update corresponding record in wp_agqa_approval_review_page table
    $table_faq_review = $wpdb->prefix . 'agqa_approval_review_page';

    // Update the record in wp_agqa_approval_review_page table: set faq_id to 0 and status to 'Pending'
    $wpdb->update(
        $table_faq_review,
        array(
            'faq_id' => 0, // Set faq_id to 0
            'status' => 'Pending' // Set status to 'Pending'
        ),
        array('faq_id' => $faq_id) // Condition to match the faq_id
    );

    // Respond with success message
    $response['status']  = 'Success';
    $response['message'] = 'Successfully Deleted.';
    echo json_encode($response);

    wp_die(); // End the AJAX request
}

/**
 * Faqs report system
 */
// Hook to handle the deletion
add_action('wp_ajax_faq_report_system', 'handle_faq_report_system');
add_action('wp_ajax_nopriv_faq_report_system', 'handle_faq_report_system');

function handle_faq_report_system()
{

    // Verify nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'agqa_nonce')) {
        die('Permission Denied');
    }

    parse_str($_POST['form_data'], $data); // Parse serialized form data

    global $wpdb;

    $user_id = get_current_user_id();    // Get data from AJAX request
    $user = get_userdata($user_id); // false if not found

    $faq_report_type = sanitize_text_field($data['faq-report-type']);
    $faq_report_answer = sanitize_text_field($data['faq-report-answer']);
    $faq_image_url = sanitize_text_field($data['imageUrl']);
    $reporter = $user ? $user->display_name : '';
    $reply_time = sanitize_text_field($data['reply_time']);
    //  echo $reporter;
    // wp_die();
    // Insert the FAQ into the database
    $wpdb->insert(
        "{$wpdb->prefix}faq_report_system",
        [
            'user_id'            => $user_id,
            'report_type'        => $faq_report_type,
            'status'             => 'Pending Response',
            'issue_detail'      => $faq_report_answer,
            'upload_attachments' => $faq_image_url,
            'reporter' => $reporter,
            'reply_time' => '--',
        ],
        [
            '%d', // user_id
            '%s', // report_type
            '%s', // status
            '%s', // issue_detail	
            '%s', // upload_attachments
            '%s', // reporter
            '%s', // reply time
        ]
    );

    // If everything went well, return success
    $response['status']  = 'Success';
    $response['message'] = 'Successfully Submitted';
    echo json_encode($response);
    wp_die();
}





/**
 * report_image_system_upload
 */
function report_image_system_upload()
{
    // Make sure you localized this same handle when creating the nonce in JS
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'agqa_nonce')) {
        wp_send_json_error(['message' => 'Nonce verification failed.'], 403);
    }

    // Accept either 'file' or 'attachments'
    if (empty($_FILES['file']) && empty($_FILES['attachments'])) {
        wp_send_json_success([
            'message' => 'Files uploaded.',
        ]);
    }

    // Normalize into an array of file items
    $files_post = !empty($_FILES['file']) ? $_FILES['file'] : $_FILES['attachments'];
    $items = [];

    if (is_array($files_post['name'])) {
        $count = count($files_post['name']);
        for ($i = 0; $i < $count; $i++) {
            $items[] = [
                'name'     => $files_post['name'][$i],
                'type'     => $files_post['type'][$i],
                'tmp_name' => $files_post['tmp_name'][$i],
                'error'    => $files_post['error'][$i],
                'size'     => $files_post['size'][$i],
            ];
        }
    } else {
        $items[] = $files_post;
    }

    require_once ABSPATH . 'wp-admin/includes/file.php';

    $urls = [];
    // Optional: restrict mimes
    $overrides = [
        'test_form' => false,
        'mimes'     => [
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
        ],
    ];

    // (Optional) put uploads under /uploads/agqa-reports
    $uploads = wp_upload_dir();
    add_filter('upload_dir', function ($dirs) use ($uploads) {
        $dirs['path']   = $uploads['basedir'] . '/agqa-reports';
        $dirs['url']    = $uploads['baseurl'] . '/agqa-reports';
        $dirs['subdir'] = '/agqa-reports';
        return $dirs;
    });

    foreach ($items as $f) {
        if (!empty($f['error'])) {
            remove_all_filters('upload_dir');
            wp_send_json_error(['message' => 'Upload error code: ' . $f['error']], 400);
        }
        $res = wp_handle_upload($f, $overrides);
        if (isset($res['error'])) {
            remove_all_filters('upload_dir');
            wp_send_json_error(['message' => 'Upload failed: ' . $res['error']], 500);
        }
        $urls[] = esc_url_raw($res['url']);
    }

    remove_all_filters('upload_dir');

    wp_send_json_success([
        'message' => 'Files uploaded.',
        'url'     => count($urls) === 1 ? $urls[0] : $urls,
    ]);
}

add_action('wp_ajax_report_image_system_upload', 'report_image_system_upload');
add_action('wp_ajax_nopriv_report_image_system_upload', 'report_image_system_upload');

/**
 * FAQs fetch answer handler
 */
function fetch_faq_answer()
{
    // Verify nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'agqa_nonce')) {
        wp_send_json_error(['message' => 'Nonce verification failed.'], 403);
    }

    // Check if FAQ ID is set
    if (isset($_POST['faq_id'])) {
        global $wpdb;
        $faq_id = intval($_POST['faq_id']);

        // Get the answer for the selected FAQ
        $faq_answer = $wpdb->get_var($wpdb->prepare("
            SELECT answer 
            FROM {$wpdb->prefix}agqa_faq
            WHERE id = %d
        ", $faq_id));

        if ($faq_answer) {
            // Return the answer as a successful response
            wp_send_json_success(['answer' => $faq_answer]);
        } else {
            // Return error if answer is not found
            wp_send_json_error(['message' => 'Answer not found.']);
        }
    }

    // Always die in the end of the AJAX function to return a response
    wp_die();
}


// Register AJAX action for logged-in users
add_action('wp_ajax_fetch_faq_answer', 'fetch_faq_answer');
add_action('wp_ajax_nopriv_fetch_faq_answer', 'fetch_faq_answer');
/**
 * 
 */
function agqa_report_reply_system()
{
    global $wpdb;

    // Verify nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'agqa_nonce')) {
        die('Permission Denied');
    }
    parse_str($_POST['form_data'], $data);
    $user_id = get_current_user_id();    // Get data from AJAX request
    $user = get_userdata($user_id); // false if not found
    // Get data from AJAX request
    $report_id = sanitize_text_field($data['id']);
    $respond_status = sanitize_text_field($data['respond-status-type']);
    $respond_textarea = sanitize_text_field($data['respond-detail-textarea']);
    $reply_user = $user ? $user->display_name : '';
    // echo $report_id;
    // wp_die();
    // Assuming these are your updated values
    $updated_status = $respond_status;  // Example updated status
    $updated_answer = $respond_textarea; // Example updated answer

    if (!empty($updated_answer)) {
        $updated_status = 'Responded';
    } else {
        $updated_status = $respond_status;
    }

    // Update the data in the table based on the report_id
    $wpdb->update(
        "{$wpdb->prefix}faq_report_system",  // Table name
        array(
            'status' => $updated_status,  // Updated status
            'issue_detail_reply' => $updated_answer,  // Updated answer
            'answer' => $reply_user,
            'reply_time' => current_time('mysql'),
        ),
        array('id' => $report_id), // Condition to match the row by report_id
        array('%s', '%s'),  // Format of the updated values
        array('%d')  // Format of the condition (report_id is an integer)
    );


    // If everything went well, return success
    $response['status']  = 'Success';
    $response['message'] = 'Change the status to “Responded” and submit.';
    echo json_encode($response);
    wp_die();
}
add_action('wp_ajax_agqa_report_reply_system', 'agqa_report_reply_system');
add_action('wp_ajax_nopriv_agqa_report_reply_system', 'agqa_report_reply_system');
