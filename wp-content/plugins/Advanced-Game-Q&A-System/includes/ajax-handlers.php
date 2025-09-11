<?php
// includes/ajax-handlers.php

add_action('wp_ajax_nopriv_agqa_get_categories', 'agqa_get_categories');
add_action('wp_ajax_agqa_get_categories', 'agqa_get_categories');
function agqa_get_categories()
{
    check_ajax_referer('agqa_nonce', 'nonce');
    global $wpdb;
    $results = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}agqa_categories ORDER BY name ASC");
    wp_send_json_success($results);
}

add_action('wp_ajax_agqa_add_category', 'agqa_add_category');
function agqa_add_category()
{
    check_ajax_referer('agqa_nonce', 'nonce');
    if (! current_user_can('administrator')) {
        wp_send_json_error('Not allowed');
    }

    global $wpdb;
    $name = sanitize_text_field($_POST['name']);
    $wpdb->insert("{$wpdb->prefix}agqa_categories", ['name' => $name]);
    wp_send_json_success(['id' => $wpdb->insert_id]);
}

add_action('wp_ajax_nopriv_agqa_get_posts', 'agqa_get_posts');
add_action('wp_ajax_agqa_get_posts', 'agqa_get_posts');
function agqa_get_posts()
{
    check_ajax_referer('agqa_nonce', 'nonce');
    global $wpdb;
    if (current_user_can('administrator') || current_user_can('editor') || current_user_can('contributor')) {
        $results = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}agqa_posts ORDER BY created_at DESC");
    } else {
        $results = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}agqa_posts WHERE visible = 1 AND status IN ('approve') ORDER BY created_at DESC");
    }
    wp_send_json_success($results);
}

add_action('wp_ajax_agqa_add_post', 'agqa_add_post');
function agqa_add_post()
{
    check_ajax_referer('agqa_nonce', 'nonce');

    if (! current_user_can('administrator') && ! current_user_can('editor') && ! current_user_can('contributor')) {
        wp_send_json_error('Not allowed');
    }

    global $wpdb;
    $message = [];
    // Determine status based on user role
    if (current_user_can('administrator')) {
        // Administrator can set status from POST or default to 'approve'
        $status  = isset($_POST['status']) ? sanitize_text_field($_POST['status']) : 'approve';
        $message = 'Add New Game Successfully.';
    } elseif (current_user_can('contributor')) {
        // Editor's posts go to pending automatically
        $status  = 'pending';
        $message = 'New game has been created successfully and is currently pending approval.';
    } else {
        // Other users not allowed (just in case)
        wp_send_json_error('Not allowed');
    }

    $inserted = $wpdb->insert("{$wpdb->prefix}agqa_posts", [
        'category_id' => intval($_POST['category_id']),
        'title'       => sanitize_text_field($_POST['title']),
        'content'     => sanitize_textarea_field($_POST['content']),
        'image_url'   => esc_url_raw($_POST['image_url']),
        'status'      => $status,
    ]);

    if ($inserted) {
        wp_send_json_success([
            'message' => $message,
            'status'  => 'success',
            'id'      => $wpdb->insert_id,
        ]);
    } else {
        wp_send_json_error('Insert failed');
    }
}

add_action('wp_ajax_nopriv_agqa_get_questions', 'agqa_get_questions');
add_action('wp_ajax_agqa_get_questions', 'agqa_get_questions');
function agqa_get_questions()
{
    check_ajax_referer('agqa_nonce', 'nonce');
    global $wpdb;
    $post_id   = intval($_POST['post_id']);
    $questions = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->prefix}agqa_questions WHERE post_id = %d", $post_id));
    foreach ($questions as &$q) {
        $q->featured = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}agqa_answers WHERE question_id = %d AND is_featured = 1", $q->id));
    }
    wp_send_json_success($questions);
}

add_action('wp_ajax_agqa_add_question', 'agqa_add_question');
function agqa_add_question()
{
    check_ajax_referer('agqa_nonce', 'nonce');
    if (! current_user_can('administrator')) {
        wp_send_json_error('Not allowed');
    }

    global $wpdb;
    $wpdb->insert("{$wpdb->prefix}agqa_questions", [
        'post_id'    => intval($_POST['post_id']),
        'question'   => sanitize_text_field($_POST['question']),
        'created_by' => get_current_user_id(),
    ]);
    wp_send_json_success(['id' => $wpdb->insert_id]);
}

add_action('wp_ajax_nopriv_agqa_get_answers', 'agqa_get_answers');
add_action('wp_ajax_agqa_get_answers', 'agqa_get_answers');
function agqa_get_answers()
{
    check_ajax_referer('agqa_nonce', 'nonce');
    global $wpdb;
    $question_id = intval($_POST['question_id']);
    $answers     = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->prefix}agqa_answers WHERE question_id = %d AND is_featured = 0 ORDER BY created_at ASC", $question_id));
    wp_send_json_success($answers);
}

add_action('wp_ajax_agqa_submit_answer', 'agqa_submit_answer');
function agqa_submit_answer()
{
    check_ajax_referer('agqa_nonce', 'nonce');
    global $wpdb;
    $wpdb->insert("{$wpdb->prefix}agqa_answers", [
        'question_id' => intval($_POST['question_id']),
        'user_id'     => get_current_user_id(),
        'content'     => sanitize_textarea_field($_POST['content']),
    ]);
    wp_send_json_success(['message' => 'Answer submitted.']);
}

// add_action('wp_ajax_agqa_submit_complaint', 'agqa_submit_complaint');
// function agqa_submit_complaint() {
//     check_ajax_referer('agqa_nonce', 'nonce');
//     global $wpdb;
//     $wpdb->insert("{$wpdb->prefix}agqa_complaints", [
//         'answer_id' => intval($_POST['answer_id']),
//         'user_id' => get_current_user_id(),
//         'reason' => sanitize_textarea_field($_POST['reason'])
//     ]);
//     wp_send_json_success(['message' => 'Complaint submitted.']);
// }

// Handle Reporting for Answers
add_action('wp_ajax_agqa_submit_complaint', 'agqa_submit_complaint');
add_action('wp_ajax_nopriv_agqa_submit_complaint', 'agqa_submit_complaint');

function agqa_submit_complaint()
{
    global $wpdb;

    // Verify nonce for security
    if (! isset($_POST['nonce']) || ! wp_verify_nonce($_POST['nonce'], 'agqa_nonce')) {
        wp_send_json_error(['message' => 'Invalid nonce']);
    }

    $answer_id = intval($_POST['answer_id']);
    $reason    = sanitize_text_field($_POST['reason']);
    $note      = sanitize_textarea_field($_POST['note']);
    $user_id   = get_current_user_id(); // If the user is logged in

    // Insert complaint into the answer complaints table
    $table_name = $wpdb->prefix . 'agqa_complaints';
    $wpdb->insert(
        $table_name,
        [
            'answer_id' => $answer_id,
            'user_id'   => $user_id,
            'reason'    => $reason,
            'note'      => $note,
            'status'    => 'pending',
        ]
    );

    wp_send_json_success();
}

// Handle Reporting for Questions
add_action('wp_ajax_agqa_submit_question_complaint', 'agqa_submit_question_complaint');
add_action('wp_ajax_nopriv_agqa_submit_question_complaint', 'agqa_submit_question_complaint');

function agqa_submit_question_complaint()
{
    global $wpdb;

    // Verify nonce for security
    if (! isset($_POST['nonce']) || ! wp_verify_nonce($_POST['nonce'], 'agqa_nonce')) {
        wp_send_json_error(['message' => 'Invalid nonce']);
    }

    $question_id = intval($_POST['question_id']);
    $reason      = sanitize_text_field($_POST['reason']);
    $note        = sanitize_textarea_field($_POST['note']);
    $user_id     = get_current_user_id(); // If the user is logged in

    // Insert complaint into the question complaints table
    $table_name = $wpdb->prefix . 'agqa_complaints_questions';
    $wpdb->insert(
        $table_name,
        [
            'question_id' => $question_id,
            'user_id'     => $user_id,
            'reason'      => $reason,
            'note'        => $note,
            'status'      => 'pending',
        ]
    );

    wp_send_json_success();
}

// END

add_action('wp_ajax_agqa_get_complaints', 'agqa_get_complaints');
function agqa_get_complaints()
{
    check_ajax_referer('agqa_nonce', 'nonce');
    if (! current_user_can('administrator')) {
        wp_send_json_error('Unauthorized');
    }

    global $wpdb;
    $q = "SELECT c.*, a.content AS answer_text FROM {$wpdb->prefix}agqa_complaints c
          JOIN {$wpdb->prefix}agqa_answers a ON c.answer_id = a.id
          WHERE c.status = 'pending' ORDER BY c.created_at DESC";
    $results = $wpdb->get_results($q);
    wp_send_json_success($results);
}

add_action('wp_ajax_agqa_moderate_complaint', 'agqa_moderate_complaint');
function agqa_moderate_complaint()
{
    check_ajax_referer('agqa_nonce', 'nonce');
    if (! current_user_can('administrator')) {
        wp_send_json_error('Unauthorized');
    }

    global $wpdb;
    $id        = intval($_POST['complaint_id']);
    $decision  = sanitize_text_field($_POST['decision']);
    $note      = sanitize_textarea_field($_POST['note']);
    $complaint = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}agqa_complaints WHERE id = %d", $id));

    if ($decision === 'approved') {
        $wpdb->update("{$wpdb->prefix}agqa_answers", ['is_featured' => 1], ['id' => $complaint->answer_id]);
    }

    $wpdb->update("{$wpdb->prefix}agqa_complaints", [
        'status'     => $decision,
        'admin_note' => $note,
    ], ['id' => $id]);

    $user = get_user_by('id', $complaint->user_id);
    if ($user && $decision === 'rejected') {
        wp_mail($user->user_email, 'Your Complaint was Rejected', $note);
    }

    wp_send_json_success(['message' => 'Updated.']);
}
add_action('wp_ajax_nopriv_agqa_search_all', 'agqa_search_all');
add_action('wp_ajax_agqa_search_all', 'agqa_search_all');
function agqa_search_all()
{
    check_ajax_referer('agqa_nonce', 'nonce');
    global $wpdb;
    $term = '%' . $wpdb->esc_like($_POST['term']) . '%';

    // 🔄 Updated: now also returns question_id always
    $query = $wpdb->prepare(
        "SELECT 'question' AS type, q.id AS question_id, q.question AS content, p.title AS post_title
         FROM {$wpdb->prefix}agqa_questions q
         JOIN {$wpdb->prefix}agqa_posts p ON q.post_id = p.id
         WHERE q.question LIKE %s
         UNION
         SELECT 'answer' AS type, q.id AS question_id, a.content, p.title
         FROM {$wpdb->prefix}agqa_answers a
         JOIN {$wpdb->prefix}agqa_questions q ON a.question_id = q.id
         JOIN {$wpdb->prefix}agqa_posts p ON q.post_id = p.id
         WHERE a.content LIKE %s
         ORDER BY post_title ASC",
        $term,
        $term
    );

    $results = $wpdb->get_results($query);
    wp_send_json_success($results);
}

add_action('wp_ajax_agqa_edit_game_full', 'agqa_edit_game_full');

function agqa_edit_game_full()
{
    check_ajax_referer('agqa_nonce', 'nonce');

    $game_id         = intval($_POST['game_id']);
    $new_title       = sanitize_text_field($_POST['new_title']);
    $new_image       = esc_url_raw($_POST['new_image']);
    $new_description = sanitize_textarea_field($_POST['new_description']);

    global $wpdb;
    $updated = $wpdb->update(
        "{$wpdb->prefix}agqa_posts", // ✅ change if your table name is different
        [
            'title'     => $new_title,
            'image_url' => $new_image,
            'content'   => $new_description,
        ],
        ['id' => $game_id]
    );

    if ($updated !== false) {
        if ($updated === 0) {
            wp_send_json_success(['message' => 'No changes made (values are the same).']);
        } else {
            wp_send_json_success(['message' => 'Game updated successfully.']);
        }
    } else {
        wp_send_json_error(['message' => 'Database update failed.', 'error' => $wpdb->last_error]);
    }
}
add_action('wp_ajax_agqa_toggle_game_visibility', 'agqa_toggle_game_visibility');

function agqa_toggle_game_visibility()
{
    check_ajax_referer('agqa_nonce', 'nonce');

    $game_id = intval($_POST['game_id']);
    $status  = ($_POST['status'] === 'hide') ? 0 : 1;

    global $wpdb;
    $updated = $wpdb->update(
        "{$wpdb->prefix}agqa_posts", // ✅ change if needed
        ['visible' => $status],
        ['id' => $game_id]
    );

    if ($updated !== false) {
        wp_send_json_success();
    } else {
        wp_send_json_error();
    }
}
add_action('wp_ajax_agqa_update_status', 'agqa_update_status');

function agqa_update_status()
{
    check_ajax_referer('agqa_nonce', 'nonce');

    $game_id = intval($_POST['game_id']);
    $status  = sanitize_text_field($_POST['status']);

    $allowed_status = ['pending', 'reject', 'approve'];
    if (! in_array($status, $allowed_status, true)) {
        wp_send_json_error(['message' => 'Invalid status']);
        wp_die();
    }

    global $wpdb;
    // Update the main post status
    $updated = $wpdb->update(
        $wpdb->prefix . 'agqa_posts',
        ['status' => $status],
        ['id' => $game_id],
        ['%s'], // format for status (string)
        ['%d']  // format for id (integer)
    );

    // Update related questions status
    $questions_updated = $wpdb->update(
        $wpdb->prefix . 'agqa_questions',
        ['status' => $status],
        ['post_id' => $game_id],
        ['%s'],
        ['%d']
    );

    $questions = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT id FROM {$wpdb->prefix}agqa_questions WHERE post_id = %d",
            $game_id
        )
    );

    foreach ($questions as $question) {
        $question_id = $question->id;
        // Update related answers status
        $answers_updated = $wpdb->update(
            $wpdb->prefix . 'agqa_answers',
            ['status' => $status],
            ['question_id' => $question_id],
            ['%s'],
            ['%d']
        );
    }

    if ($updated !== false) {
        wp_send_json_success();
    } else {
        wp_send_json_error(['message' => 'Database update failed']);
    }
    wp_die();
}

add_action('wp_ajax_agqa_edit_question', 'agqa_edit_question');

function agqa_edit_question()
{
    check_ajax_referer('agqa_nonce', 'nonce');

    if (! current_user_can('administrator') && ! current_user_can('contributor')) {
        wp_send_json_error('Permission denied');
    }

    $question_id  = intval($_POST['question_id']);
    $new_question = sanitize_text_field($_POST['new_question']);

    if (! $question_id || empty($new_question)) {
        wp_send_json_error('Invalid data');
    }

    global $wpdb;

    // By default keep status as is
    $status_to_set = null;

    // If editor, force status to pending
    if (current_user_can('contributor')) {
        $status_to_set = 'pending';
    }

    $data = ['question' => $new_question];
    if ($status_to_set !== null) {
        $data['status'] = $status_to_set;
    }

    $updated = $wpdb->update(
        "{$wpdb->prefix}agqa_questions",
        $data,
        ['id' => $question_id]
    );

    if ($updated !== false) {
        wp_send_json_success();
    } else {
        wp_send_json_error('Update failed');
    }
}

// Question like/dislike Function
add_action('wp_ajax_agqa_like_question', 'agqa_like_question');
function agqa_like_question()
{
    check_ajax_referer('agqa_nonce', 'nonce');

    $question_id = isset($_POST['question_id']) ? intval($_POST['question_id']) : 0;
    $user_id     = get_current_user_id();

    if ($question_id > 0 && $user_id) {
        global $wpdb;

        $existing_like = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$wpdb->prefix}agqa_user_question_likes_dislikes WHERE user_id = %d AND question_id = %d AND action_type = %s",
            $user_id, $question_id, 'like'
        ));

        if ($existing_like > 0) {
            wp_send_json_error(['message' => 'You already liked this question']);
        }

        $wpdb->insert("{$wpdb->prefix}agqa_user_question_likes_dislikes", [
            'user_id'     => $user_id,
            'question_id' => $question_id,
            'action_type' => 'like',
        ]);

        wp_send_json_success(['message' => 'Liked']);
    }

    wp_send_json_error(['message' => 'Invalid request']);
}

// END

add_action('wp_ajax_agqa_toggle_question_visibility', 'agqa_toggle_question_visibility');

function agqa_toggle_question_visibility()
{
    check_ajax_referer('agqa_nonce', 'nonce');

    $question_id = intval($_POST['question_id']);
    $status      = sanitize_text_field($_POST['status']);

    if (! in_array($status, ['show', 'hide'], true)) {
        wp_send_json_error('Invalid status');
    }

    $visible = ($status === 'show') ? 1 : 0;

    global $wpdb;

    // By default keep status as is
    $status_to_set = null;

    // If editor, force status to pending
    if (current_user_can('editor') && ! current_user_can('administrator')) {
        $status_to_set = 'pending';
    }

    $data = ['visible' => $visible];
    if ($status_to_set !== null) {
        $data['status'] = $status_to_set;
    }

    $updated = $wpdb->update(
        "{$wpdb->prefix}agqa_questions",
        $data,
        ['id' => $question_id]
    );

    if ($updated !== false) {
        wp_send_json_success();
    } else {
        wp_send_json_error('Update failed');
    }
}

add_action('wp_ajax_agqa_update_question_status', 'agqa_update_question_status');

function agqa_update_question_status()
{
    check_ajax_referer('agqa_nonce', 'nonce');

    $question_id = intval($_POST['question_id']);
    $status      = sanitize_text_field($_POST['status']);

    $allowed_status = ['pending', 'approve', 'reject'];
    if (! in_array($status, $allowed_status, true)) {
        wp_send_json_error('Invalid status');
    }

    global $wpdb;
    $updated = $wpdb->update(
        "{$wpdb->prefix}agqa_questions",
        ['status' => $status],
        ['id' => $question_id]
    );

    if ($updated !== false) {
        wp_send_json_success();
    } else {
        wp_send_json_error('Database update failed');
    }
}

add_action('wp_ajax_agqa_dropdown_action', 'agqa_dropdown_action');
function agqa_dropdown_action()
{
    check_ajax_referer('agqa_nonce', 'nonce');

    if (! current_user_can('administrator')) {
        wp_send_json_error('Permission denied');
    }

    $answer_id = intval($_POST['answer_id']);
    $action    = sanitize_text_field($_POST['dropdown_action']);

    if (! $answer_id || empty($action)) {
        wp_send_json_error('Invalid data');
    }

    // Example: update status column in DB
    global $wpdb;
    $updated = $wpdb->update(
        "{$wpdb->prefix}agqa_answers",
        ['status' => $action],
        ['id' => $answer_id]
    );

    if ($updated !== false) {
        wp_send_json_success();
    } else {
        wp_send_json_error('Database update failed');
    }
}

add_action('wp_ajax_agqa_like_answer', 'agqa_like_answer');
function agqa_like_answer()
{
    check_ajax_referer('agqa_nonce', 'nonce');

    // Ensure answer_id is set and valid
    $answer_id = isset($_POST['answer_id']) ? intval($_POST['answer_id']) : 0;
    $user_id   = get_current_user_id();

    if ($answer_id > 0) {
        global $wpdb;

        // Check if the user has already liked this answer
        $existing_like = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*) FROM {$wpdb->prefix}agqa_user_likes_dislikes WHERE user_id = %d AND answer_id = %d AND action_type = %s",
                $user_id,
                $answer_id,
                'like'
            )
        );

        if ($existing_like > 0) {
            wp_send_json_error(['message' => 'You have already liked this answer']);
            return;
        }

        // Add like to the database
        $wpdb->insert("{$wpdb->prefix}agqa_user_likes_dislikes", [
            'user_id'     => $user_id,
            'answer_id'   => $answer_id,
            'action_type' => 'like',
        ]);

        // Increment the like_count for the answer
        $wpdb->query(
            $wpdb->prepare(
                "UPDATE {$wpdb->prefix}agqa_answers SET like_count = like_count + 1 WHERE id = %d",
                $answer_id
            )
        );

        wp_send_json_success(['message' => 'Liked successfully']);
    } else {
        wp_send_json_error(['message' => 'Invalid answer ID']);
    }
}

add_action('wp_ajax_agqa_dislike_answer', 'agqa_dislike_answer');
function agqa_dislike_answer()
{
    check_ajax_referer('agqa_nonce', 'nonce');

    // Ensure answer_id is set and valid
    $answer_id = isset($_POST['answer_id']) ? intval($_POST['answer_id']) : 0;
    $user_id   = get_current_user_id();

    if ($answer_id > 0) {
        global $wpdb;

        // Check if the user has already disliked this answer
        $existing_dislike = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*) FROM {$wpdb->prefix}agqa_user_likes_dislikes WHERE user_id = %d AND answer_id = %d AND action_type = %s",
                $user_id,
                $answer_id,
                'dislike'
            )
        );

        if ($existing_dislike > 0) {
            wp_send_json_error(['message' => 'You have already disliked this answer']);
            return;
        }

        // Add dislike to the database
        $wpdb->insert("{$wpdb->prefix}agqa_user_likes_dislikes", [
            'user_id'     => $user_id,
            'answer_id'   => $answer_id,
            'action_type' => 'dislike',
        ]);

        // Increment the dislike_count for the answer
        $wpdb->query(
            $wpdb->prepare(
                "UPDATE {$wpdb->prefix}agqa_answers SET dislike_count = dislike_count + 1 WHERE id = %d",
                $answer_id
            )
        );

        wp_send_json_success(['message' => 'Disliked successfully']);
    } else {
        wp_send_json_error(['message' => 'Invalid answer ID']);
    }
}
add_action('wp_ajax_agqa_submit_feedback', 'agqa_submit_feedback');
function agqa_submit_feedback()
{

    check_ajax_referer('agqa_nonce', 'nonce');

    global $wpdb;

    $user_id    = get_current_user_id();
    $answer_id  = intval($_POST['answer_id']);
    $type       = sanitize_text_field($_POST['type']);
    $reason     = sanitize_text_field($_POST['reason'] ?? '');
    $details    = sanitize_textarea_field($_POST['details'] ?? '');
    $attachment = '';

    // Handle file upload
    if (! empty($_FILES['attachment']) && ! empty($_FILES['attachment']['name'])) {
        require_once ABSPATH . 'wp-admin/includes/file.php';
        $uploaded = media_handle_upload('attachment', 0);
        if (is_wp_error($uploaded)) {
            wp_send_json_error(['message' => 'File upload failed.']);
        } else {
            $attachment = wp_get_attachment_url($uploaded);
        }
    }

    $wpdb->insert("{$wpdb->prefix}agqa_feedback", [
        'user_id'        => $user_id,
        'answer_id'      => $answer_id,
        'type'           => $type,
        'reason'         => $reason,
        'details'        => $details,
        'attachment_url' => $attachment,
    ]);

    // 💌 Send email to admin
    $admin_email = get_option('admin_email');
    $subject     = "New {$type} submitted on answer #$answer_id";
    $message     = "Reason: $reason\n\nDetails:\n$details\n\nAttachment: $attachment";
    wp_mail($admin_email, $subject, $message);

    wp_send_json_success(['message' => ucfirst($type) . ' submitted successfully']);
}
add_action('wp_ajax_save_api_revenue_entry', 'save_api_revenue_entry');
add_action('wp_ajax_nopriv_save_api_revenue_entry', 'save_api_revenue_entry');

function save_api_revenue_entry()
{
    check_ajax_referer('agqa_nonce', 'nonce');
    global $wpdb;

    $table = $wpdb->prefix . 'agqa_api_entries';

    // Step 1: Validate required fields
    foreach (['selling_price', 'api_cost', 'provider_name'] as $field) {
        if (empty($_POST[$field])) {
            wp_send_json_error(['message' => "Missing required field: $field"]);
        }
    }

    // Step 2: Sanitize inputs
    $data = [
        'selling_price' => sanitize_text_field($_POST['selling_price']),
        'api_cost'      => sanitize_text_field($_POST['api_cost']),
        'provider_name' => sanitize_text_field($_POST['provider_name']),
        'game_category' => sanitize_text_field($_POST['game_category']),
        'api_type'      => sanitize_text_field($_POST['api_type']),
        'game_name'     => sanitize_text_field($_POST['game_name']),
        'telegram'      => sanitize_text_field($_POST['telegram']),
        'website'       => esc_url_raw($_POST['website']),
        'notes'         => sanitize_textarea_field($_POST['notes']),
        'submitted_at'  => current_time('mysql'),
    ];

    // Step 3: Insert into database
    $inserted = $wpdb->insert($table, $data);

    if ($inserted) {
        $data['id'] = $wpdb->insert_id;
        wp_send_json_success([
            'message' => 'Saved successfully',
            'entry'   => $data,
        ]);
    } else {
        error_log("DB Insert Error: " . $wpdb->last_error);
        wp_send_json_error([
            'message' => 'Insert failed',
            'error'   => $wpdb->last_error,
        ]);
    }
}
add_action('wp_ajax_get_api_revenue_entries', 'get_api_revenue_entries');
add_action('wp_ajax_nopriv_get_api_revenue_entries', 'get_api_revenue_entries');

function get_api_revenue_entries()
{
    global $wpdb;
    $results = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}agqa_api_entries ORDER BY submitted_at DESC");
    wp_send_json_success($results);
}
/**
 * Insert Revenue
 */
function handle_insert_provider_data()
{
    // Verify nonce
    if (! isset($_POST['nonce']) || ! wp_verify_nonce($_POST['nonce'], 'agqa_nonce')) {
        echo 'Error: Invalid nonce!';
        wp_die();
    }

    // Check for form data
    if (! isset($_POST['form_data'])) {
        echo 'Error: Invalid data';
        wp_die();
    }

    parse_str($_POST['form_data'], $data);
    global $wpdb;

    // Sanitize basic values
    $provider_name = sanitize_text_field($data['provider-name']);
    $game_types    = explode(',', $data['select-role']);
    $budget        = sanitize_text_field($data['budget']);
    $image_url     = isset($data['imageurl']) ? esc_url_raw($data['imageurl']) : ''; // Get the image URL from the form data

    foreach ($game_types as $game_type_name) {
        $game_type_slug = sanitize_title($game_type_name);

        // Get game_type ID and game_category_id from wp_game_type
        $type = $wpdb->get_row($wpdb->prepare(
            "SELECT id, game_category_id FROM {$wpdb->prefix}game_type WHERE slug = %s",
            $game_type_slug
        ));

        if (! $type) {
            continue; // Skip if type not found
        }

        // Check for existing record
        $existing_record = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM {$wpdb->prefix}agqa_revenu WHERE provider_name = %s AND game_type_id = %d",
            $provider_name,
            $type->id
        ));

        if ($existing_record) {
            // Show error message if data already exists
            echo 'Error: The game provider already exist.';
            wp_die();
        }

        // Prepare data for insertion
        $insert_data = [
            'provider_name'               => $provider_name,
            'state'                       => sanitize_text_field($data['state']),
            'game_category_id'            => $type->game_category_id,
            'game_type_id'                => $type->id,
            'selling_price'               => isset($data['selling-price']) ? floatval($data['selling-price']) : 0,
            'api_cost'                    => isset($data['api-cost']) ? floatval($data['api-cost']) : 0,
            'api_type'                    => sanitize_text_field($data['api-type']),
            'game_info_website'           => esc_url_raw($data['game-info-website']),
            'game_demo_website'           => esc_url_raw($data['game-demo-website']),
            'representative_contact_info' => sanitize_text_field($data['representative-contact']),
            'representative_telegram'     => sanitize_text_field($data['representative-telegram']),
            'notes'                       => sanitize_textarea_field($data['notes']),
            'contract_filename'           => sanitize_file_name($data['upload-file']),
            'url_update_date'             => sanitize_text_field($data['url-update-date']),
            'image_url'                   => $image_url, // Save the image URL
        ];

        // Determine table: revenue or sales
        $table_name = $budget === 'Sale' ? $wpdb->prefix . 'agqa_sales' : $wpdb->prefix . 'agqa_revenu';

        // Insert data
        $wpdb->insert($table_name, $insert_data);
    }

    echo 'Success: Provider data inserted!';
    wp_die();
}

add_action('wp_ajax_insert_provider_data', 'handle_insert_provider_data');
add_action('wp_ajax_nopriv_insert_provider_data', 'handle_insert_provider_data');

/**
 * Add Sale Data
 */
function handle_insert_provider_sale_data()
{
    // Verify nonce
    if (! isset($_POST['nonce']) || ! wp_verify_nonce($_POST['nonce'], 'agqa_nonce')) {
        echo 'Error: Invalid nonce!';
        wp_die();
    }

    // Check for form data
    if (! isset($_POST['form_data'])) {
        echo 'Error: Invalid data';
        wp_die();
    }

    parse_str($_POST['form_data'], $data);
    global $wpdb;

    // Sanitize basic values
    $provider_name = sanitize_text_field($data['provider-name']);
    $game_types    = explode(',', $data['select-role']);
    $budget        = sanitize_text_field($data['business-model']);
    $image_url     = isset($data['imageurl']) ? esc_url_raw($data['imageurl']) : ''; // Get the image URL from the form data

    foreach ($game_types as $game_type_name) {
        $game_type_slug = sanitize_title($game_type_name);

        // Get game_type ID and game_category_id from wp_game_type
        $type = $wpdb->get_row($wpdb->prepare(
            "SELECT id, game_category_id FROM {$wpdb->prefix}game_type WHERE slug = %s",
            $game_type_slug
        ));

        if (! $type) {
            continue; // Skip if type not found
        }

        // Check for existing record
        $existing_record = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM {$wpdb->prefix}agqa_sales WHERE provider_name = %s AND game_type_id = %d",
            $provider_name,
            $type->id
        ));

        if ($existing_record) {
            // Show error message if data already exists
            echo 'Error: The game provider already exist.';
            wp_die();
        }

        // Prepare data for insertion
        $insert_data = [
            'provider_name'    => $provider_name,
            'game_category_id' => $type->game_category_id,
            'game_type_id'     => $type->id,
            'image_url'        => $image_url,
        ];
        // Determine table: revenue or sales
        $table_name = $wpdb->prefix . 'agqa_sales';
        $wpdb->insert($table_name, $insert_data);

    }
    echo 'Success: Provider data inserted!';
    wp_die();
}

add_action('wp_ajax_insert_provider_sale_data', 'handle_insert_provider_sale_data');
add_action('wp_ajax_nopriv_insert_provider_sale_data', 'handle_insert_provider_sale_data');

/**
 * ADD Revenue
 */
function handle_add_revenue_provider_data()
{
    // Verify nonce
    if (! isset($_POST['nonce']) || ! wp_verify_nonce($_POST['nonce'], 'agqa_nonce')) {
        echo 'Error: Invalid nonce!';
        wp_die();
    }
    // Check for form data
    if (! isset($_POST['form_data'])) {
        echo 'Error: Invalid data';
        wp_die();
    }
    parse_str($_POST['form_data'], $data);
    global $wpdb;
    // Sanitize basic values
    $provider_name   = sanitize_text_field($data['provider-name']);
    $game_type_input = explode(',', $data['select-game-type-id']);
    $budget          = sanitize_text_field($data['budget']);
    $provider_id     = isset($data['provider-id']) ? intval($data['provider-id']) : 0;
    $image_url_query = $wpdb->prepare(
        "SELECT image_url FROM {$wpdb->prefix}agqa_revenu WHERE id = %s LIMIT 1",
        $provider_id
    );
     $image_pdf_query = $wpdb->prepare(
        "SELECT contract_filename FROM {$wpdb->prefix}agqa_revenu WHERE id = %s LIMIT 1",
        $provider_id
    );
// Execute the query and fetch the image_url
    $image_url = $wpdb->get_var($image_url_query);
    $image_pdf_url = $wpdb->get_var($image_pdf_query);

// Split cat_id and type_id into arrays based on commas
    $cat_ids       = ! empty($data['select-game-category']) ? explode(',', $data['select-game-category']) : [];
    $game_type_ids = ! empty($data['select-game-type-id']) ? explode(',', $data['select-game-type-id']) : [];
    $provider_name = isset($data['provider-name']) ? sanitize_text_field($data['provider-name']) : '';
    foreach ($cat_ids as $cat_id) {
        foreach ($game_type_ids as $game_type_id) {

            // Prepare the query to check for existing combinations
            $query = $wpdb->prepare(
                "SELECT COUNT(*) FROM {$wpdb->prefix}agqa_revenu
             WHERE provider_name = %s AND game_category_id = %d AND game_type_id = %d",
                $provider_name,
                intval($cat_id),
                intval($game_type_id)
            );

            $exist = $wpdb->get_var($query);

            // Check if combination already exists
            if (intval($exist) > 0) {
                $query = $wpdb->prepare(
                    "SELECT name FROM {$wpdb->prefix}game_type WHERE id = %d LIMIT 1",
                    $game_type_id
                );
                $game_type_name = $wpdb->get_var($query);
                echo "Provider Name: $provider_name,  Game type: $game_type_name<br>";
                echo 'The combination of provider and game type already exists.';
                wp_die(); // Stop execution if any combination exists
            }
        }
    }

    foreach ($game_type_ids as $game_type_id) {
        // Get the game type details from wp_game_type using game_type_id
        $type = $wpdb->get_row($wpdb->prepare(
            "SELECT id, game_category_id FROM {$wpdb->prefix}game_type WHERE id = %d",
            $game_type_id
        ));

        if (! $type) {
            continue;
        }

        // Check for existing record to prevent duplication
        $existing_record = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM {$wpdb->prefix}agqa_revenu WHERE provider_name = %s AND game_type_id = %d",
            $provider_name,
            $type->id// Use the game_type_id from the fetched type
        ));

        if ($existing_record) {
            // Show error message if data already exists
            echo 'Error: The game provider already exists.';
            wp_die();
        }

        // Prepare the data for insertion
        $insert_data = [
            'provider_name'               => $provider_name,
            'state'                       => ! empty($data['state']) ? sanitize_text_field($data['state']) : 0,
            'game_category_id'            => $type->game_category_id,
            'game_type_id'                => $type->id,
            'selling_price'               => isset($data['selling-price']) && is_numeric($data['selling-price']) ? floatval($data['selling-price']) : null,
            'api_cost'                    => isset($data['api-cost']) && is_numeric($data['api-cost']) ? floatval($data['api-cost']) : null,
            'api_type'                    => ! empty($data['api-type']) ? sanitize_text_field($data['api-type']) : null,
            'game_info_website'           => ! empty($data['game-info-website']) ? ($data['game-info-website']) : null,
            'game_demo_website'           => ! empty($data['game-demo-website']) ? ($data['game-demo-website']) : 'none',
            'representative_contact_info' => ! empty($data['representative-name']) ? sanitize_text_field($data['representative-name']) : null,
            'representative_telegram'     => ! empty($data['representative-telegram']) ? sanitize_text_field($data['representative-telegram']) : 'none',
            'custom_label_1'              => ! empty($data['custom-label-1']) ? sanitize_text_field($data['custom-label-1']) : null,
            'custom_label_2'              => ! empty($data['custom-label-2']) ? sanitize_text_field($data['custom-label-2']) : null,
            'custom_label_3'              => ! empty($data['custom-label-3']) ? sanitize_text_field($data['custom-label-3']) : null,
            'custom_label_4'              => ! empty($data['custom-label-4']) ? sanitize_text_field($data['custom-label-4']) : null,
            'custom_field_1'              => ! empty($data['custom-field-1']) ? sanitize_text_field($data['custom-field-1']) : null,
            'custom_field_2'              => ! empty($data['custom-field-2']) ? sanitize_text_field($data['custom-field-2']) : null,
            'custom_field_3'              => ! empty($data['custom-field-3']) ? sanitize_text_field($data['custom-field-3']) : null,
            'custom_field_4'              => ! empty($data['custom-field-4']) ? sanitize_text_field($data['custom-field-4']) : null,
            'notes'                       => ! empty($data['notes-detail']) ? sanitize_textarea_field($data['notes-detail']) : null,
            'image_url'                   => ! empty($image_url) ? $image_url : null,
            'contract_filename' => ! empty(trim($data['imageurls']))  ? esc_url_raw($data['imageurls'])  : ( ! empty($image_pdf_url) ? $image_pdf_url : 'none' ),
            'contract_upload_date'        => current_time('mysql'), // Set this to current time
            'url_update_date'             => ! empty($data['url-update-date']) ? sanitize_text_field($data['url-update-date']) : '',

        ];

        // Insert into the database and check for errors
        $insert_result = $wpdb->insert("{$wpdb->prefix}agqa_revenu", $insert_data);

        if ($insert_result === false) {
            // Output error if insert fails
            echo "Error: Data insertion failed. Check your database setup.<br>";
            wp_die(); // Stop execution if insert fails
        } else {
            echo "Data successfully inserted!<br>";
        }
    }

    echo 'Success: Provider data inserted!';
    wp_die();
}

add_action('wp_ajax_add_revenue_provider_data', 'handle_add_revenue_provider_data');
add_action('wp_ajax_nopriv_add_revenue_provider_data', 'handle_add_revenue_provider_data');

// END

/**
 * Sale Add Form Script
 */

function handle_add_sale_provider_data()
{
    // Verify nonce
    if (! isset($_POST['nonce']) || ! wp_verify_nonce($_POST['nonce'], 'agqa_nonce')) {
        echo 'Error: Invalid nonce!';
        wp_die();
    }
    // Check for form data
    if (! isset($_POST['form_data'])) {
        echo 'Error: Invalid data';
        wp_die();
    }
    parse_str($_POST['form_data'], $data);
    global $wpdb;
    // Sanitize basic values
    $provider_name   = sanitize_text_field($data['provider-name']);
    $game_type_input = explode(',', $data['select-game-type-id']);
    $budget          = sanitize_text_field($data['budget']);
    $provider_id     = isset($data['provider-id']) ? intval($data['provider-id']) : 0;
    $image_url_query = $wpdb->prepare(
        "SELECT image_url FROM {$wpdb->prefix}agqa_sales WHERE id = %s LIMIT 1",
        $provider_id
    );
    $image_pdf_query = $wpdb->prepare(
        "SELECT contract_filename FROM {$wpdb->prefix}agqa_sales WHERE id = %s LIMIT 1",
        $provider_id
    );

// Execute the query and fetch the image_url
    $image_url = $wpdb->get_var($image_url_query);
    $image_pdf_url = $wpdb->get_var($image_pdf_query);

// Split cat_id and type_id into arrays based on commas
    $cat_ids       = ! empty($data['select-game-category']) ? explode(',', $data['select-game-category']) : [];
    $game_type_ids = ! empty($data['select-game-type-id']) ? explode(',', $data['select-game-type-id']) : [];
    $provider_name = isset($data['provider-name']) ? sanitize_text_field($data['provider-name']) : '';

    foreach ($cat_ids as $cat_id) {
        foreach ($game_type_ids as $game_type_id) {

            // Prepare the query to check for existing combinations
            $query = $wpdb->prepare(
                "SELECT COUNT(*) FROM {$wpdb->prefix}agqa_sales
             WHERE provider_name = %s AND game_category_id = %d AND game_type_id = %d",
                $provider_name,
                intval($cat_id),
                intval($game_type_id)
            );

            $exist = $wpdb->get_var($query);

            // Check if combination already exists
            if (intval($exist) > 0) {
                $query = $wpdb->prepare(
                    "SELECT name FROM {$wpdb->prefix}game_type WHERE id = %d LIMIT 1",
                    $game_type_id
                );
                $game_type_name = $wpdb->get_var($query);
                echo "Provider Name: $provider_name,  Game type: $game_type_name<br>";
                echo 'The combination of provider and game type already exists.';
                wp_die(); // Stop execution if any combination exists
            }
        }
    }

    foreach ($game_type_ids as $game_type_id) {
        // Get the game type details from wp_game_type using game_type_id
        $type = $wpdb->get_row($wpdb->prepare(
            "SELECT id, game_category_id FROM {$wpdb->prefix}game_type WHERE id = %d",
            $game_type_id
        ));

        if (! $type) {
            continue;
        }

        // Check for existing record to prevent duplication
        $existing_record = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM {$wpdb->prefix}agqa_sales WHERE provider_name = %s AND game_type_id = %d",
            $provider_name,
            $type->id// Use the game_type_id from the fetched type
        ));

        if ($existing_record) {
            // Show error message if data already exists
            echo 'Error: The game provider already exists.';
            wp_die();
        }

        // Prepare the data for insertion
        $insert_data = [
            'provider_name'           => $provider_name,
            'state'                   => ! empty($data['state']) ? sanitize_text_field($data['state']) : 0,
            'game_category_id'        => $type->game_category_id,
            'game_type_id'            => $type->id,
            'min_revenue_share'       => isset($data['selling-price']) && is_numeric($data['selling-price']) ? floatval($data['selling-price']) : null,
            'max_resale_share'        => isset($data['api-cost']) && is_numeric($data['api-cost']) ? floatval($data['api-cost']) : null,
            'api_type'                => ! empty($data['api-type']) ? sanitize_text_field($data['api-type']) : null,
            'game_info_website'       => ! empty($data['game-info-website']) ? ($data['game-info-website']) : null,
            'game_demo_website'       => ! empty($data['game-demo-website']) ? ($data['game-demo-website']) : 'none',
            'representative_name'     => ! empty($data['representative-name']) ? sanitize_text_field($data['representative-name']) : 'none',
            'representative_telegram' => ! empty($data['representative-telegram']) ? sanitize_text_field($data['representative-telegram']) : 'none',
            'custom_label_1'          => ! empty($data['custom-label-1']) ? sanitize_text_field($data['custom-label-1']) : null,
            'custom_label_2'          => ! empty($data['custom-label-2']) ? sanitize_text_field($data['custom-label-2']) : null,
            'custom_label_3'          => ! empty($data['custom-label-3']) ? sanitize_text_field($data['custom-label-3']) : null,
            'custom_label_4'          => ! empty($data['custom-label-4']) ? sanitize_text_field($data['custom-label-4']) : null,
            'custom_field_1'          => ! empty($data['custom-field-1']) ? sanitize_text_field($data['custom-field-1']) : null,
            'custom_field_2'          => ! empty($data['custom-field-2']) ? sanitize_text_field($data['custom-field-2']) : null,
            'custom_field_3'          => ! empty($data['custom-field-3']) ? sanitize_text_field($data['custom-field-3']) : null,
            'custom_field_4'          => ! empty($data['custom-field-4']) ? sanitize_text_field($data['custom-field-4']) : null,
            'notes'                   => ! empty($data['notes-detail']) ? sanitize_textarea_field($data['notes-detail']) : null,
            'image_url'               => ! empty($image_url) ? $image_url : null,
            'contract_filename' => ! empty(trim($data['imageurls']))  ? esc_url_raw($data['imageurls']) : ( ! empty($image_pdf_url) ? $image_pdf_url : 'none' ),
            'contract_upload_date'    => current_time('mysql'), // Set this to current time
            'url_update_date'         => ! empty($data['url-update-date']) ? sanitize_text_field($data['url-update-date']) : '',

        ];

        // Insert into the database and check for errors
        $insert_result = $wpdb->insert("{$wpdb->prefix}agqa_sales", $insert_data);

        if ($insert_result === false) {
            // Output error if insert fails
            echo "Error: Data insertion failed. Check your database setup.<br>";
            wp_die(); // Stop execution if insert fails
        } else {
            echo "Data successfully inserted!<br>";
        }
    }

    echo 'Success: Provider data inserted!';
    wp_die();
}

add_action('wp_ajax_add_sale_provider_data', 'handle_add_sale_provider_data');
add_action('wp_ajax_nopriv_add_sale_provider_data', 'handle_add_sale_provider_data');

// END

/**
 * Edit Revenue Form Handler
 */

function handle_edit_revnue_form()
{
    // Initialize response array
    $response = [
        'status'  => 'error', // Default status
        'message' => '',
    ];
    // Verify nonce
    if (! isset($_POST['nonce']) || ! wp_verify_nonce($_POST['nonce'], 'agqa_nonce')) {
        $response['message'] = 'Error: Invalid nonce!';
        echo json_encode($response);
        wp_die();
    }
    // Check for form data
    if (! isset($_POST['form_data'])) {
        $response['message'] = 'Error: Invalid data';
        echo json_encode($response);
        wp_die();
    }
    parse_str($_POST['form_data'], $data); // Parse serialized form data
    global $wpdb;
    // Get provider-id from the form data
    $provider_id = isset($data['provider-id']) ? intval($data['provider-id']) : 0;

    if ($provider_id <= 0) {
        $response['message'] = 'Error: Invalid provider ID';
        echo json_encode($response);
        wp_die();
    }

    // Sanitize other form data
    // $provider_name = sanitize_text_field($data['provider-name']);
    $image_url   = isset($data['imageurl']) ? esc_url_raw($data['imageurl']) : '';
    $image_urls = isset($data['imageurls']) && !empty($data['imageurls']) 
    ? esc_url_raw($data['imageurls']) 
    : (isset($data['already-upload-contract']) && !empty($data['already-upload-contract']) ? esc_url_raw($data['already-upload-contract']) : '');
    $insert_data = [
        // 'provider_name' => !empty($provider_name) ? sanitize_text_field($provider_name) : null,
        'state'                       => ! empty($data['state']) ? sanitize_text_field($data['state']) : null,
        'game_category_id'            => ! empty($data['select-game-category-id']) ? sanitize_text_field($data['select-game-category-id']) : null,
        'game_type_id'                => ! empty($data['select-game-type-id']) ? sanitize_text_field($data['select-game-type-id']) : null,
        'selling_price'               => isset($data['selling-price']) && is_numeric($data['selling-price']) ? floatval($data['selling-price']) : null,
        'api_cost'                    => isset($data['api-cost']) && is_numeric($data['api-cost']) ? floatval($data['api-cost']) : null,
        'api_type'                    => ! empty($data['api-type']) ? sanitize_text_field($data['api-type']) : null,
        'game_info_website'           => ! empty($data['game-info-website']) ? ($data['game-info-website']) : null,
        'game_demo_website'           => ! empty($data['game-demo-website']) ? ($data['game-demo-website']) : 'none',
        'representative_contact_info' => ! empty($data['representative-name']) ? sanitize_text_field($data['representative-name']) : null,
        'representative_telegram'     => ! empty($data['representative-telegram']) ? sanitize_text_field($data['representative-telegram']) : 'none',
        'custom_label_1'              => ! empty($data['custom-label-1']) ? sanitize_text_field($data['custom-label-1']) : null,
        'custom_label_2'              => ! empty($data['custom-label-2']) ? sanitize_text_field($data['custom-label-2']) : null,
        'custom_label_3'              => ! empty($data['custom-label-3']) ? sanitize_text_field($data['custom-label-3']) : null,
        'custom_label_4'              => ! empty($data['custom-label-4']) ? sanitize_text_field($data['custom-label-4']) : null,
        'custom_field_1'              => ! empty($data['custom-field-1']) ? sanitize_text_field($data['custom-field-1']) : null,
        'custom_field_2'              => ! empty($data['custom-field-2']) ? sanitize_text_field($data['custom-field-2']) : null,
        'custom_field_3'              => ! empty($data['custom-field-3']) ? sanitize_text_field($data['custom-field-3']) : null,
        'custom_field_4'              => ! empty($data['custom-field-4']) ? sanitize_text_field($data['custom-field-4']) : null,
        'notes'                       => ! empty($data['notes-detail']) ? sanitize_textarea_field($data['notes-detail']) : null,
        // 'image_url' => !empty($image_url) ? $image_url : null,
        'contract_filename'           => $image_urls,
        // 'contract_upload_date'        => current_time('mysql'), // Set this to current time
        'url_update_date'             => ! empty($data['url-update-date']) ? sanitize_text_field($data['url-update-date']) : null,
    ];

// 1) Inputs normalize
    $provider_name = isset($data['provider-game-name']) ? sanitize_text_field($data['provider-game-name']) : '';
    $cat_id        = ! empty($data['select-game-category-id']) ? intval($data['select-game-category-id']) : 0;
    $type_id       = ! empty($data['select-game-type-id']) ? intval($data['select-game-type-id']) : 0;

// 2) Guard (optional)
    if ($provider_name !== '' && $cat_id > 0 && $type_id > 0) {

        // 3) Check same combo in ANY OTHER row (id != current)
        $exists = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*)
             FROM {$wpdb->prefix}agqa_revenu
             WHERE provider_name = %s
               AND game_category_id = %d
               AND game_type_id = %d
               AND id != %d",
                $provider_name,
                $cat_id,
                $type_id,
                $provider_id // current editing row's id
            )
        );

        // 4) If found, block update with JSON
        if (intval($exists) > 0) {
            // $response['status']  = 'error';
            // $response['message'] = 'This is already exists';
            echo json_encode('This is already exists');
            wp_die();
        }
    }
    // Set the WHERE condition to update the correct record by provider-id
    $where = [
        'id' => $provider_id, // Use provider_id to identify the record
    ];

    // Update the record in the database
    $update_result = $wpdb->update($wpdb->prefix . 'agqa_revenu', $insert_data, $where);

    // Check if the update was successful
    if ($update_result === false) {
        $response['message'] = 'Error: Update failed! ' . $wpdb->last_error;
        echo json_encode($response);
        wp_die();
    }

    // If everything went well, return success
    $response['status']  = 'success';
    $response['message'] = 'Success: Provider data updated!';
    echo json_encode($response);
    wp_die();
}
add_action('wp_ajax_handle_edit_revnue_form', 'handle_edit_revnue_form');
add_action('wp_ajax_nopriv_handle_edit_revnue_form', 'handle_edit_revnue_form');

/**
 * Edit Sales Handler
 */
function handle_edit_sales_form()
{
    // Initialize response array
    $response = [
        'status'  => 'error', // Default status
        'message' => '',
    ];
    // Verify nonce
    if (! isset($_POST['nonce']) || ! wp_verify_nonce($_POST['nonce'], 'agqa_nonce')) {
        $response['message'] = 'Error: Invalid nonce!';
        echo json_encode($response);
        wp_die();
    }
    // Check for form data
    if (! isset($_POST['form_data'])) {
        $response['message'] = 'Error: Invalid data';
        echo json_encode($response);
        wp_die();
    }
    parse_str($_POST['form_data'], $data); // Parse serialized form data
    global $wpdb;
    // Get provider-id from the form data
    $provider_id = isset($data['provider-id']) ? intval($data['provider-id']) : 0;

    if ($provider_id <= 0) {
        $response['message'] = 'Error: Invalid provider ID';
        echo json_encode($response);
        wp_die();
    }

    // Sanitize other form data
    // $provider_name = sanitize_text_field($data['provider-name']);
    $image_url   = isset($data['imageurl']) ? esc_url_raw($data['imageurl']) : '';
     $image_urls = isset($data['imageurls']) && !empty($data['imageurls']) 
    ? esc_url_raw($data['imageurls']) 
    : (isset($data['already-upload-contract']) && !empty($data['already-upload-contract']) ? esc_url_raw($data['already-upload-contract']) : ''); // Get the image URL from the form data
    $insert_data = [
        // 'provider_name' => !empty($provider_name) ? sanitize_text_field($provider_name) : null,
        'state'                   => ! empty($data['state']) ? sanitize_text_field($data['state']) : null,
        'game_category_id'        => ! empty($data['select-game-category-id']) ? sanitize_text_field($data['select-game-category-id']) : null,
        'game_type_id'            => ! empty($data['select-game-type-id']) ? sanitize_text_field($data['select-game-type-id']) : null,
        'min_revenue_share'       => isset($data['selling-price']) && is_numeric($data['selling-price']) ? floatval($data['selling-price']) : null,
        'max_resale_share'        => isset($data['api-cost']) && is_numeric($data['api-cost']) ? floatval($data['api-cost']) : null,
        'api_type'                => ! empty($data['api-type']) ? sanitize_text_field($data['api-type']) : null,
        'game_info_website'       => ! empty($data['game-info-website']) ? ($data['game-info-website']) : null,
        'game_demo_website'       => ! empty($data['game-demo-website']) ? ($data['game-demo-website']) : 'none',
        'representative_name'     => ! empty($data['representative-name']) ? sanitize_text_field($data['representative-name']) : null,
        'representative_telegram' => ! empty($data['representative-telegram']) ? sanitize_text_field($data['representative-telegram']) : 'none',
        'custom_label_1'          => ! empty($data['custom-label-1']) ? sanitize_text_field($data['custom-label-1']) : null,
        'custom_label_2'          => ! empty($data['custom-label-2']) ? sanitize_text_field($data['custom-label-2']) : null,
        'custom_label_3'          => ! empty($data['custom-label-3']) ? sanitize_text_field($data['custom-label-3']) : null,
        'custom_label_4'          => ! empty($data['custom-label-4']) ? sanitize_text_field($data['custom-label-4']) : null,
        'custom_field_1'          => ! empty($data['custom-field-1']) ? sanitize_text_field($data['custom-field-1']) : null,
        'custom_field_2'          => ! empty($data['custom-field-2']) ? sanitize_text_field($data['custom-field-2']) : null,
        'custom_field_3'          => ! empty($data['custom-field-3']) ? sanitize_text_field($data['custom-field-3']) : null,
        'custom_field_4'          => ! empty($data['custom-field-4']) ? sanitize_text_field($data['custom-field-4']) : null,
        'notes'                   => ! empty($data['notes-detail']) ? sanitize_textarea_field($data['notes-detail']) : null,
        // 'image_url' => !empty($image_url) ? $image_url : null,
        'contract_filename'       => $image_urls,
        // 'contract_upload_date'    => current_time('mysql'), // Set this to current time
        'url_update_date'         => ! empty($data['url-update-date']) ? sanitize_text_field($data['url-update-date']) : null,
    ];

// 1) Inputs normalize
    $provider_name = isset($data['provider-game-name']) ? sanitize_text_field($data['provider-game-name']) : '';
    $cat_id        = ! empty($data['select-game-category-id']) ? intval($data['select-game-category-id']) : 0;
    $type_id       = ! empty($data['select-game-type-id']) ? intval($data['select-game-type-id']) : 0;

// 2) Guard (optional)
    if ($provider_name !== '' && $cat_id > 0 && $type_id > 0) {

        // 3) Check same combo in ANY OTHER row (id != current)
        $exists = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*)
             FROM {$wpdb->prefix}agqa_sales
             WHERE provider_name = %s
               AND game_category_id = %d
               AND game_type_id = %d
               AND id != %d",
                $provider_name,
                $cat_id,
                $type_id,
                $provider_id // current editing row's id
            )
        );

        // 4) If found, block update with JSON
        if (intval($exists) > 0) {
            // $response['status']  = 'error';
            // $response['message'] = 'This is already exists';
            echo json_encode('This is already exists');
            wp_die();
        }
    }
    // Set the WHERE condition to update the correct record by provider-id
    $where = [
        'id' => $provider_id, // Use provider_id to identify the record
    ];

    // Update the record in the database
    $update_result = $wpdb->update($wpdb->prefix . 'agqa_sales', $insert_data, $where);

    // Check if the update was successful
    if ($update_result === false) {
        $response['message'] = 'Error: Update failed! ' . $wpdb->last_error;
        echo json_encode($response);
        wp_die();
    }

    // If everything went well, return success
    $response['status']  = 'success';
    $response['message'] = 'Success: Provider data updated!';
    echo json_encode($response);
    wp_die();
}
add_action('wp_ajax_handle_edit_sales_form', 'handle_edit_sales_form');
add_action('wp_ajax_nopriv_handle_edit_sales_form', 'handle_edit_sales_form');

// END

/**
 * Upload file in media handler
 */

function ddmu_handle_upload()
{

    if (! isset($_POST['nonce']) || ! wp_verify_nonce($_POST['nonce'], 'agqa_nonce')) {
        echo json_encode(['status' => 'error', 'message' => 'Nonce verification failed.']);
        wp_die();
    }

    if (! empty($_FILES['file'])) {
        $uploaded_file = $_FILES['file'];

        $upload = wp_handle_upload($uploaded_file, ['test_form' => false]);

        if (isset($upload['url'])) {
            echo json_encode(['status' => 'success', 'url' => $upload['url']]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Upload failed.']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No file uploaded.']);
    }

    wp_die();
}

add_action('wp_ajax_ddmu_handle_upload', 'ddmu_handle_upload');
add_action('wp_ajax_nopriv_ddmu_handle_upload', 'ddmu_handle_upload');

// End Sales Handler

/**
 * Revenue Reorder Handle
 */


add_action('wp_ajax_save_user_revenue_sort_order', 'save_user_revenue_sort_order_handler');

function save_user_revenue_sort_order_handler() {
    if (!is_user_logged_in()) {
        wp_send_json_error('You must be logged in.');
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'reorder_revenue';

    $user_id = get_current_user_id();

    if (!isset($_POST['form_data'])) {
        wp_send_json_error('No form data received.');
    }

    parse_str($_POST['form_data'], $data); 

    if (!isset($data['sort-by-revenue'])) {
        wp_send_json_error('Missing sort order.');
    }

    $sort_order = sanitize_text_field($data['sort-by-revenue']);

    // Check if user already has a record
    $existing = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM $table_name WHERE user_id = %d",
        $user_id
    ));

    if ($existing > 0) {
        // Update existing record
        $updated = $wpdb->update(
            $table_name,
            ['sort_order' => $sort_order],
            ['user_id' => $user_id],
            ['%s'],
            ['%d']
        );

        if ($updated === false) {
            wp_send_json_error('Database update error.');
        }

        wp_send_json_success('Sort order updated successfully.');
    } else {
        // Insert new record
        $inserted = $wpdb->insert(
            $table_name,
            [
                'user_id'    => $user_id,
                'sort_order' => $sort_order,
            ],
            [
                '%d',
                '%s'
            ]
        );

        if ($inserted === false) {
            wp_send_json_error('Database insert error.');
        }

        wp_send_json_success('Sort order inserted successfully.');
    }

    wp_die(); 
}


// END


/**
 * Sale reorder handler
 */

add_action('wp_ajax_save_user_sales_sort_order', 'save_user_sales_sort_order_handler');

function save_user_sales_sort_order_handler() {
    if (!is_user_logged_in()) {
        wp_send_json_error('You must be logged in.');
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'reorder_sales';

    $user_id = get_current_user_id();

    if (!isset($_POST['form_data'])) {
        wp_send_json_error('No form data received.');
    }

    parse_str($_POST['form_data'], $data); 

    if (!isset($data['sort-by-sales'])) {
        wp_send_json_error('Missing sort order.');
    }

    $sort_order = sanitize_text_field($data['sort-by-sales']);

    // Check if user already has a record
    $existing = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM $table_name WHERE user_id = %d",
        $user_id
    ));

    if ($existing > 0) {
        // Update existing record
        $updated = $wpdb->update(
            $table_name,
            ['sort_order' => $sort_order],
            ['user_id' => $user_id],
            ['%s'],
            ['%d']
        );

        if ($updated === false) {
            wp_send_json_error('Database update error.');
        }

        wp_send_json_success('Sort order updated successfully.');
    } else {
        // Insert new record
        $inserted = $wpdb->insert(
            $table_name,
            [
                'user_id'    => $user_id,
                'sort_order' => $sort_order,
            ],
            [
                '%d',
                '%s'
            ]
        );

        if ($inserted === false) {
            wp_send_json_error('Database insert error.');
        }

        wp_send_json_success('Sort order inserted successfully.');
    }

    wp_die(); 
}


// END