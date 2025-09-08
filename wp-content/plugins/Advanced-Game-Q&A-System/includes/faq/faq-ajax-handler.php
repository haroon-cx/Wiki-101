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
        "{$wpdb->prefix}agqa_faq_review",
        array(
            'faq_id' => 0,
            'question' => $question,
            'answer' => $answer,
            'verified_answer' => $verified_answer,
            'faq_category' => $faq_category,
            'status' => 'Pending',
            'user_id' => $current_user_id,
            'time' => current_time('mysql'),

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
                            "{$wpdb->prefix}agqa_faq_review",
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
                "{$wpdb->prefix}agqa_faq_review",
                array(
                    'status' => 'approve', // Set status to approved
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

    // Insert the FAQ into the database
    $wpdb->insert(
        "{$wpdb->prefix}agqa_faq_review",
        array(
            'faq_id' => $faq_id,
            'question' => $question,
            'answer' => $answer,
            'verified_answer' => $verified_answer,
            'faq_category' => $faq_category
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
 * FAQ like & dislike handler
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
        // Record exists, update it (like or dislike)
        $wpdb->update(
            $table,
            ['action_type' => $action_type],
            ['faq_id' => $faq_id, 'user_id' => $user_id],
            ['%d'],
            ['%d', '%d']
        );
        $message = 'Your preference has been updated.';
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

    $response['status']  = 'Success';
    $response['message'] = 'Successfully Submitted';
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
    echo $faq_id;
    // wp_die();

    // Delete the FAQ from the agqa_faq table
    $table_faq = $wpdb->prefix . 'agqa_faq';
    $table_history = $wpdb->prefix . 'agqa_faq_history'; // If you want to also delete from history

    // Delete FAQ from both tables (history and FAQ)
    $wpdb->delete($table_faq, array('id' => $faq_id));
    $wpdb->delete($table_history, array('faq_id' => $faq_id));

    $response['status']  = 'Success';
    $response['message'] = 'Successfully Submitted';
    echo json_encode($response);


    wp_die(); // End the AJAX request
}

// Hook to handle the deletion
add_action('wp_ajax_delete_faq', 'handle_faq_deletion');
add_action('wp_ajax_nopriv_delete_faq', 'handle_delete_faq');
