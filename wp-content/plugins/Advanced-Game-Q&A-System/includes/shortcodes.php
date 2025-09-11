<?php
// includes/shortcodes.php
add_shortcode('agqa_interface', 'agqa_render_interface');

function agqa_render_interface()
{
    ob_start();
    ?>
<div id="agqa-interface" class="p-6 max-w-7xl mx-auto mt">
    <!-- 🔍 Global Search Box (Commented Out) -->
    <!--
    <div id="agqa-search-box">
        <input type="text" id="agqa-search-input" placeholder="Search all questions and answers...">
        <div id="agqa-search-results"></div>
    </div>
    -->

    <?php if (current_user_can('administrator') || current_user_can('contributor')): ?>
        <!-- Admin Tools Section -->
        <div class="admin-tools-wrapper">
            <!-- Heading -->
            <h3 class="admin-heading fy">Manage Games</h3>
            <!-- Horizontal Rule -->
            <hr class="heading-divider" />
            <!-- New Games Heading and Button on Same Line -->
            <div class="heading-button-flex">
                <h2 class="new-games-heading">New Games</h2>
                <?php
                $user_id = get_current_user_id();
                if (get_user_meta($user_id, 'cuim_viewer_mode', true) == '0') { ?>
                    <div class="button-bar">
                        <button class="add-category-button" onclick="document.getElementById('agqa-add-game-modal').style.display='block'">+ Add New Game</button>
                    </div>
                <?php } ?>
            </div>

            <!-- Modal Content -->
           <div id="agqa-modal-overlay"
            style="display:none; position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background-color: rgba(0,0,0,0.5); z-index: 9999;">

            <!-- Modal -->
            <div id="agqa-add-game-modal"
                style="position: absolute; top: 20%;  left: 51%; transform: translate(-50%, -50%); background: white; border-radius: 8px; z-index: 10000;">
                <div class="AG">Add new Game</div>

                <input type="text" id="agqa-admin-post-title" placeholder="Game Title..."
                    style="width:100%; margin-bottom:10px;">

                <textarea id="agqa-admin-post-content" placeholder="Game Description..."
                    style="width:100%; margin-bottom:10px;"></textarea>

                <input type="text" id="agqa-admin-post-image" placeholder="Click to select image"
                    style="width:100%; margin-bottom:10px; cursor: pointer;" readonly>

                <div style="text-align: right">
                    <button id="agqa-admin-add-post">Add Game</button>
                    <button id="agqa-admin-add-post-close">Close</button>
                </div>
            </div>
        </div>
        <script>
        // Open overlay + modal
        document.querySelector('.add-category-button').addEventListener('click', function() {
            document.getElementById('agqa-modal-overlay').style.display = 'block';
        });

        // Close overlay + modal
        document.getElementById('agqa-admin-add-post-close').addEventListener('click', function() {
            document.getElementById('agqa-modal-overlay').style.display = 'none';
        });

        // Optional: close if clicked outside modal
        document.getElementById('agqa-modal-overlay').addEventListener('click', function(e) {
            if (e.target === this) {
                this.style.display = 'none';
            }
        });
        </script>
        </div>
    <?php else: ?>
        <!-- Public New Games Heading for Non-Admins -->
        <h2 class="new-games-heading">New Games</h2>
    <?php endif; ?>

    <!-- 🟦 Public Game List -->
    <div id="agqa-post-list"></div>
</div>
<?php
    return ob_get_clean();
}


add_shortcode('agqa_post_page', 'agqa_post_page_shortcode');
function agqa_post_page_shortcode()
{
    if (!isset($_GET['id']))
        return '<p>No post ID provided.</p>';
    global $wpdb;
    $post_id = intval($_GET['id']);

    $post = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}agqa_posts WHERE id = %d", $post_id));
    if (!$post)
        return '<p>Post not found.</p>';


    // Handle add question
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_question'])) {
        function get_user_answer_status()
        {
            if (current_user_can('contributor')) {
                return 'pending';
            }
            // Admin or others
            return 'approve'; // or 'approved' as default for admin
        }
        $question = sanitize_text_field($_POST['question_text']);
        $status = get_user_answer_status();
        $wpdb->insert("{$wpdb->prefix}agqa_questions", [
            'post_id' => $post_id,
            'question' => $question,
            'status' => $status,
        ]);
        $redirect = home_url("/post/?id=$post_id");
        if (!headers_sent()) {
            wp_redirect($redirect);
            exit;
        } else {
            echo "<script>window.location.href='$redirect';</script>";
            exit;
        }
    }

    if (current_user_can('administrator') || current_user_can('editor') || current_user_can('contributor')) {
        $questions = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->prefix}agqa_questions WHERE post_id = %d", $post_id));
    } else {
        $questions = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->prefix}agqa_questions WHERE visible = 1 AND status IN ('approve') AND post_id = %d", $post_id));
    }
    ob_start();
    ?>
    <div class="agqa-post-detail">
        <?php
            $user_id = get_current_user_id();
            if (get_user_meta($user_id, 'cuim_viewer_mode', true) == '0') { ?>

        <?php if (current_user_can('administrator') || current_user_can('editor') || current_user_can('contributor')): ?>
        <img src="<?php echo esc_url($post->image_url); ?>" style="max-width:100%;height:auto;">
        <h2><?php echo esc_html($post->title); ?></h2>
        <p><?php echo esc_html($post->content); ?></p>

        <div class="agqa-game-actions">
            <button type="button" class="button agqa-edit-game-btn">Edit Game</button>
            <?php if (current_user_can('administrator') || current_user_can('editor')) { ?>
            <?php if ($post->visible == '1'): ?>
            <button type="button" class="button agqa-hide-game-btn">Hide Game</button>
            <?php elseif ($post->visible == '0'): ?>
            <button type="button" class="button agqa-show-game-btn">Show Game</button>
            <?php endif; ?>


            <h3>
                Update Status:
            </h3>
            <select id="agqa-status-dropdown-<?php echo esc_attr($post->id); ?>" class="agqa-status-dropdown"
                data-game-id="<?php echo esc_attr($post->id); ?>">
                <option value="pending" <?php selected($post->status, 'pending'); ?>>Pending</option>
                <option value="reject" <?php selected($post->status, 'reject'); ?>>Reject</option>
                <option value="approve" <?php selected($post->status, 'approve'); ?>>Approve</option>
            </select>
            <?php } ?>
        </div>

        <!-- 🔧 Edit Game Modal -->
        <div id="agqa-edit-game-modal">
            <h4 style="margin-bottom:15px;">✏️ Edit Game</h4>

            <input type="text" id="agqa-edit-game-title" placeholder="Game Title"
                value="<?php echo esc_attr($post->title); ?>">

            <textarea id="agqa-edit-game-description"
                placeholder="Game Description"><?php echo esc_textarea($post->content); ?></textarea>

            <input type="text" id="agqa-admin-post-image" placeholder="Click to select image"
                value="<?php echo esc_url($post->image_url); ?>" readonly>


            <div style="text-align:right; margin-top:15px;">
                <button id="agqa-save-game-button" class="button">Save</button>
                <button onclick="jQuery('#agqa-edit-game-modal').hide()" class="button">
                    Cancel</button>
            </div>
        </div>


        <?php if (current_user_can('administrator') || current_user_can('contributor')) { ?>

        <h3>Add Question to this Game</h3>
        <form method="post">
            <textarea name="question_text" placeholder="Type your question..." style="width:100%;"
                required="required"></textarea>
            <input type="hidden" name="add_question" value="1">
            <button type="submit">Add Question</button>
        </form>
        <?php } ?>
        <?php endif; ?>

        <?php } ?>
        <h3>Questions</h3>
        <ul>
            <?php foreach ($questions as $q):?>
            <?php $featured = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}agqa_answers WHERE question_id = %d AND is_featured = 1", $q->id)); ?>
            <li style="position: relative; <?php echo ($q->status == 'pending' && get_user_meta($user_id, 'cuim_viewer_mode', true)) ? ' display:none;' : ''; ?>"
                class="<?php echo ($q->status == 'pending' ? 'pending_active' : '') ?> ">
                <a href="/question/?id=<?php echo $q->id; ?>">
                    <?php echo esc_html($q->question); ?>
                </a>

                <div class="agqa-question-interactions" data-question-id="<?php echo esc_attr($q->id); ?>">
                    <div class="copy-btn" data-text="<?php echo esc_html($q->question); ?>">
                        <i class="fa-regular fa-clone"></i>
                    </div>
                    <div class="like-btn"><i class="fa-regular fa-thumbs-up"></i></div>
                    <div class="dislike-btn"><i class="fa-regular fa-thumbs-down"></i></div>
                    <div class="report-btn">
                        <img src="https://wiki.101.games/wp-content/uploads/2025/07/Alert-triangle.png" alt="Report"
                            width="18" />
                    </div>

                    <?php if (!empty($featured->is_featured)) { ?>
                    <span class="trx_addons_icon_selector icon-checkbox"> Verified Question</span>
                    <?php } ?>
                </div>


                <!-- Edit and Delete Buttons -->
                <?php
                        if (get_user_meta($user_id, 'cuim_viewer_mode', true) == '0') { ?>

                <?php if (current_user_can('administrator') || current_user_can('editor') || current_user_can('contributor')) { ?>
                <div class="" style="position:absolute; top:10px; right:10px;">
                    <!-- Edit Button -->
                    <?php if (current_user_can('administrator') || current_user_can('contributor')) { ?>
                    <button class="button agqa-edit-question-btn" data-question-id="<?php echo esc_attr($q->id); ?>"
                        style="background: transparent !important; padding: 0; padding-left: 9px;"><i
                            class="fas fa-pencil-alt"></i></button>
                    <?php } ?>
                    <!-- Eye Button for Show/Hide -->

                    <!-- Custom Status Dropdown Menu -->
                    <?php if (current_user_can('administrator') || current_user_can('editor')) { ?>
                    <button class="button agqa-toggle-visibility-btn" data-question-id="<?php echo esc_attr($q->id); ?>"
                        title="<?php echo ($q->visible ? 'Hide Question' : 'Show Question'); ?>"
                        style="background: transparent !important; padding: 0; padding-left: 9px;">
                        <?php echo ($q->visible ? '<i class="far fa-eye"></i>' : '<i class="far fa-eye-slash"></i>'); ?>
                    </button>
                    <div class="agqa-question-status" style="display: inline-block; position: relative;">
                        <button class="button agqa-status-toggle"
                            style="background: transparent !important; padding: 0; padding-left: 10px;"><i
                                class="fas fa-chevron-down"></i></button>
                        <ul class="agqa-status-menu" data-question-id="<?php echo esc_attr($q->id); ?>"
                            style="display:none; position: absolute; top: 100%; right: 0; background: rgb(7, 16, 33); border: 1px solid #586380; list-style: none; padding: 10px 8px; margin: 0; min-width: 160px; z-index: 999; border-radius: 8px">
                            <li class="agqa-status-item" data-status="pending"
                                style="padding: 5px 15px; cursor: pointer; <?php echo ($q->status == 'pending' ? ' border-bottom:2px solid #a8b1ff;' : '') ?>">
                                Pending</li>
                            <li class="agqa-status-item" data-status="approve"
                                style="padding: 5px 15px; cursor: pointer; <?php echo ($q->status == 'approve' ? ' border-bottom:2px solid #a8b1ff;' : '') ?>">
                                Approve</li>
                            <li class="agqa-status-item" data-status="reject"
                                style="padding: 5px 15px; cursor: pointer; <?php echo ($q->status == 'reject' ? ' border-bottom:2px solid #a8b1ff;' : '') ?>">
                                Reject</li>
                        </ul>
                    </div>
                    <?php } ?>

                </div>

                <div id="agqa-edit-question-modal">
                    <h3>Edit Question</h3>
                    <textarea id="agqa-edit-question-text"></textarea>
                    <input type="hidden" id="agqa-edit-question-id">
                    <div style="text-align:right;">
                        <button id="agqa-save-question-btn" class="button">Save</button>
                        <button onclick="jQuery('#agqa-edit-question-modal').hide()" class="button">Cancel</button>
                    </div>
                </div>
                <?php } ?>
                <?php } ?>
                <?php if (get_user_meta($user_id, 'cuim_viewer_mode', true) || current_user_can('subscriber')) { ?>
                <!-- <div class="report-question-button" data-question-id="<?php echo esc_attr($q->id); ?>"
                    onclick="document.getElementById('report-question-popup-<?php echo $q->id; ?>').style.display='block'">
                    🚩 Report
                </div> -->
                <?php } ?>
                <div id="report-question-popup-<?php echo $q->id; ?>" class="agqa-question-popup" style="display:none;">
                    <form method="post">
                        <h1 class="heading-1"><strong>Report this question:</strong><br></h1>
                        <input type="hidden" id="agqa-complaint-question-list" name="complaint_question_id"
                            value="<?php echo $q->id; ?>"> <!-- Hidden input for question ID -->
                        <label><input type="radio" name="complaint_reason" value="Inappropriate Content" required>
                            Inappropriate Content</label><br>
                        <label><input type="radio" name="complaint_reason" value="Scam" required> Scam</label><br>
                        <label><input type="radio" name="complaint_reason" value="Spam" required> Spam</label><br>
                        <label><input type="radio" name="complaint_reason" value="Technical Error" required> Technical
                            Error</label><br>
                        <textarea name="note" placeholder="Additional note (optional)..."
                            style="width:100%; height:60px; margin-top:10px;"></textarea><br>
                        <div style="text-align: right">
                            <button type="submit" id="submit-report-question">Submit Report</button>
                            <button type="button"
                                onclick="document.getElementById('report-question-popup-<?php echo $q->id; ?>').style.display='none'">Cancel</button>
                        </div>
                    </form>
                </div>
                <!-- Report Popup -->
                <div id="agqa-report-popup" class="agqa-popup" style="display: none">
                    <div class="agqa-popup-overlay"></div>
                    <div class="agqa-popup-content">
                        <div class="agqa-popup-header">
                            <h2>Report</h2>
                            <div class="agqa-popup-close">&times;</div>
                        </div>

                        <form id="agqa-report-form">
                            <input type="hidden" name="answer_id" id="report_answer_id" value="">

                            <label for="problem-type">What Problem Did You Encounter?</label>
                            <select name="problem_type" id="problem-type" required>
                                <option value="">Select Role</option>
                                <option value="abuse">Abusive Content</option>
                                <option value="spam">Spam</option>
                                <option value="misleading">Misleading Information</option>
                                <option value="other">Other</option>
                            </select>

                            <label for="report-description">Detailed Description</label>
                            <textarea name="description" id="report-description" placeholder="Typing..."
                                required></textarea>

                            <label for="report-attachment">Upload Attachments</label>
                            <!-- <input type="file" name="attachment" id="report-attachment"> -->
                            <div class="custom-file-upload">
                                <label for="report-attachment">
                                    <div class="upload-box">
                                        <div class="upload-icon">+</div>
                                        <div class="upload-label">Upload</div>
                                    </div>
                                </label>
                                <input type="file" name="attachment" id="report-attachment" />
                            </div>
                            <button type="submit" class="report-submit-btn">Sent</button>
                        </form>
                    </div>
                </div>
                <!-- Dislike Popup -->
                <div id="agqa-dislike-popup" class="agqa-popup agqa-dislike-popup" style="display: none;">
                    <div class="popup-overlay"></div>
                    <div class="popup-container">
                        <div class="popup-header">
                            <h2>Dislike</h2>
                            <div class="popup-close">&times;</div>
                        </div>

                        <form id="agqa-dislike-form">
                            <input type="hidden" name="answer_id" id="dislike_answer_id" value="">

                            <div class="popup-question">Why Don’t You Like This Game?</div>

                            <div class="popup-checkbox-group">
                                <label class="custom-checkbox">
                                    <input type="checkbox" name="reasons[]" value="not_fun">
                                    <div class="checkmark"></div>
                                    This game is not fun
                                </label>

                                <label class="custom-checkbox">
                                    <input type="checkbox" name="reasons[]" value="too_slow">
                                    <div class="checkmark"></div>
                                    The game is too slow
                                </label>

                                <label class="custom-checkbox">
                                    <input type="checkbox" name="reasons[]" value="bad_graphics">
                                    <div class="checkmark"></div>
                                    The graphics are not pretty
                                </label>

                                <label class="custom-checkbox">
                                    <input type="checkbox" name="reasons[]" value="not_intuitive">
                                    <div class="checkmark"></div>
                                    The game is not intuitive
                                </label>

                                <label class="custom-checkbox">
                                    <input type="checkbox" name="reasons[]" value="other" id="dislike-other-checkbox">
                                    <div class="checkmark"></div>
                                    Other
                                </label>
                            </div>

                            <textarea name="other_reason" id="dislike-other-text" placeholder="Description"></textarea>

                            <button type="submit" class="popup-submit">Sent</button>
                        </form>
                    </div>
                </div>
            </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php
        return ob_get_clean();
    }

add_shortcode('agqa_question_page', 'agqa_question_page_shortcode');
function agqa_question_page_shortcode()
{
    if (!isset($_GET['id']))
        return '<p>No question ID provided.</p>';
    global $wpdb;
    $qid = intval($_GET['id']);

    $question = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}agqa_questions WHERE id = %d", $qid));
    if (!$question)
        return '<p>Question not found.</p>';

    $featured = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}agqa_answers WHERE question_id = %d AND is_featured = 1", $qid));

    if (current_user_can('administrator') || current_user_can('editor') || current_user_can('contributor')) {
        $answers = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->prefix}agqa_answers WHERE question_id = %d AND is_featured = 0 ORDER BY created_at ASC", $qid));
    } else {
        $answers = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->prefix}agqa_answers WHERE question_id = %d AND is_featured = 0 AND status IN ('approve') ORDER BY created_at ASC", $qid));
    }

    $user_id = get_current_user_id();
    ob_start();
    ?>
    <div class="agqa-question-detail">
        <h2>Question:</h2>
        <p><?php echo esc_html($question->question); ?></p>
        <?php if ($featured): ?>
        <div class="agqa-featured">
            <!-- <strong>✅ Verified Answer by <?php echo esc_html($featured->display_name); ?>:</strong> -->
            <div style="margin:8px 0;"><?php echo esc_html($featured->content); ?></div>

            <div class="agqa-featured-content" data-answer-id="<?php echo $featured->id; ?>">
                <?php echo esc_html($featured->content); ?>

                <div class="agqa-interactions">
                    <div class="copy-btn sc_button_hover_slide_left" data="<?php echo esc_html($featured->content); ?>">
                        <i class="fa-regular fa-clone"></i>
                    </div>
                    <div class="like-btn" data-answer-id="<?php echo $featured->id; ?>">
                        <i class="fa-regular fa-thumbs-up"></i>
                    </div>
                    <div class="dislike-btn" data-answer-id="<?php echo $featured->id; ?>">
                        <i class="fa-regular fa-thumbs-down"></i>
                    </div>

                    <div class="report-btn" data-answer-id="<?php echo $featured->id; ?>" title="Report">
                        <img src="https://wiki.101.games/wp-content/uploads/2025/07/Alert-triangle.png" alt="Report"
                            style="width: 20px; height: 20px;">
                    </div>
                    <?php if (!empty($featured->is_featured)) { ?>
                    <span class="trx_addons_icon_selector icon-checkbox"> Verified answer</span>
                    <?php } ?>
                    <!-- Report Popup -->
                    <div id="agqa-report-popup" class="agqa-popup" style="display: none">
                        <div class="agqa-popup-overlay"></div>
                        <div class="agqa-popup-content">
                            <div class="agqa-popup-header">
                                <h2>Report</h2>
                                <span class="agqa-popup-close">&times;</span>
                            </div>

                            <form id="agqa-report-form">
                                <input type="hidden" name="answer_id" id="report_answer_id" value="">

                                <label for="problem-type">What Problem Did You Encounter?</label>
                                <select name="problem_type" id="problem-type" required>
                                    <option value="">Select Role</option>
                                    <option value="abuse">Abusive Content</option>
                                    <option value="spam">Spam</option>
                                    <option value="misleading">Misleading Information</option>
                                    <option value="other">Other</option>
                                </select>

                                <label for="report-description">Detailed Description</label>
                                <textarea name="description" id="report-description" placeholder="Typing..."
                                    required></textarea>

                                <label for="report-attachment">Upload Attachments</label>
                                <!-- <input type="file" name="attachment" id="report-attachment"> -->
                                <div class="custom-file-upload">
                                    <label for="report-attachment">
                                        <div class="upload-box">
                                            <div class="upload-icon">+</div>
                                            <div class="upload-label">Upload</div>
                                        </div>
                                    </label>
                                    <input type="file" name="attachment" id="report-attachment" />
                                </div>
                                <button type="submit" class="report-submit-btn">Sent</button>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- Dislike Popup -->
                <!-- <div id="agqa-dislike-popup" class="agqa-popup" style="display: none">
                        <div class="agqa-popup-overlay"></div>
                            <div class="agqa-popup-content">
                                <div class="agqa-popup-header">
                                <h2>Dislike</h2>
                                <span class="agqa-popup-close">&times;</span>
                                </div>

                                <form id="agqa-dislike-form">
                                <input type="hidden" name="answer_id" id="dislike_answer_id" value="">

                                <label>Why Don’t You Like This Game?</label>
                                <div class="checkbox-group">
                                    <label><input type="checkbox" name="reasons[]" value="not_fun"> This game is not fun</label>
                                    <label><input type="checkbox" name="reasons[]" value="too_slow"> The game is too slow</label>
                                    <label><input type="checkbox" name="reasons[]" value="bad_graphics"> The graphics are not pretty</label>
                                    <label><input type="checkbox" name="reasons[]" value="not_intuitive"> The game is not intuitive</label>
                                    <label><input type="checkbox" name="reasons[]" value="other" id="dislike-other-checkbox"> Other</label>
                                </div>

                                <textarea name="other_reason" id="dislike-other-text" placeholder="Description" style="display: none;"></textarea>

                                <button type="submit" class="dislike-submit-btn">Sent</button>
                                </form>
                            </div>
                        </div> -->
                <!-- Dislike Popup -->
                <div id="agqa-dislike-popup" class="agqa-popup agqa-dislike-popup" style="display: none;">
                    <div class="popup-overlay"></div>
                    <div class="popup-container">
                        <div class="popup-header">
                            <h2>Dislike</h2>
                            <span class="popup-close">&times;</span>
                        </div>

                        <form id="agqa-dislike-form">
                            <input type="hidden" name="answer_id" id="dislike_answer_id" value="">

                            <div class="popup-question">Why Don’t You Like This Game?</div>

                            <div class="popup-checkbox-group">
                                <label class="custom-checkbox">
                                    <input type="checkbox" name="reasons[]" value="not_fun">
                                    <span class="checkmark"></span>
                                    This game is not fun
                                </label>

                                <label class="custom-checkbox">
                                    <input type="checkbox" name="reasons[]" value="too_slow">
                                    <span class="checkmark"></span>
                                    The game is too slow
                                </label>

                                <label class="custom-checkbox">
                                    <input type="checkbox" name="reasons[]" value="bad_graphics">
                                    <span class="checkmark"></span>
                                    The graphics are not pretty
                                </label>

                                <label class="custom-checkbox">
                                    <input type="checkbox" name="reasons[]" value="not_intuitive">
                                    <span class="checkmark"></span>
                                    The game is not intuitive
                                </label>

                                <label class="custom-checkbox">
                                    <input type="checkbox" name="reasons[]" value="other" id="dislike-other-checkbox">
                                    <span class="checkmark"></span>
                                    Other
                                </label>
                            </div>

                            <textarea name="other_reason" id="dislike-other-text" placeholder="Description"></textarea>

                            <button type="submit" class="popup-submit">Sent</button>
                        </form>
                    </div>
                </div>
            </div>


            <!-- Admin Edit/Delete Buttons -->
            <?php if (get_user_meta($user_id, 'cuim_viewer_mode', true) == '0') { ?>
            <?php if (current_user_can('administrator')): ?>
            <div class="agqa-admin-tools-box" style="position:absolute; top:10px; right:10px;">
                <form method="post" style="display:inline;">
                    <input type="hidden" name="edit_answer_id" value="<?php echo $featured->id; ?>">
                    <button onclick="document.getElementById('edit-featured-popup').style.display='block'" type="button"
                        title="Edit"> <i class="fas fa-pencil-alt"></i>
                    </button>
                </form>
            </div>

            <!-- Edit Popup for Featured -->
            <div id="edit-featured-popup" class="agqa-admin-edit-box">
                <form method="post">
                    <input type="hidden" name="edit_answer_id" value="<?php echo $featured->id; ?>">
                    <input type="text" name="edit_answer_name" value="<?php echo esc_attr($featured->display_name); ?>"
                        style="width:100%; margin-bottom:10px;">
                    <textarea name="edit_answer_content"
                        style="width:100%; height:80px;"><?php echo esc_textarea($featured->content); ?></textarea><br>
                    <div style="text-align: right">
                        <button type="submit">Update</button>
                        <button type="button"
                            onclick="document.getElementById('edit-featured-popup').style.display='none'">Cancel
                        </button>
                    </div>
                </form>
            </div>
            <?php endif; ?>
            <?php } ?>
            <!-- Report Button (All Users) -->
            <?php if (get_user_meta($user_id, 'cuim_viewer_mode', true) || current_user_can('subscriber')) { ?>
            <!-- <button class="egqa-reporet-button"
                onclick="document.getElementById('report-featured-popup').style.display='block'"
                style="margin-top:10px; color:#fff;">🚩 Report
            </button> -->
            <?php } ?>
            <div id="report-featured-popup" class="agqa-question-popup">
                <form method="post">
                    <h1 class="heading-1"><strong>Report this answer:</strong><br></h1>
                    <input type="hidden" id="agqa-complaint-answer-list" name="complaint_answer_id"
                        value="<?php echo $featured->id; ?>">
                    <label><input type="radio" name="complaint_reason" value="Inappropriate Content" required>
                        Inappropriate Content</label><br>
                    <label><input type="radio" name="complaint_reason" value="Scam" required> Scam</label><br>
                    <label><input type="radio" name="complaint_reason" value="Spam" required> Spam</label><br>
                    <label><input type="radio" name="complaint_reason" value="Technical Error" required> Technical
                        Error</label><br>
                    <textarea name="note" placeholder="Additional note (optional)..." class="note-input"
                        style="width:100%; height:60px; margin-top:10px;">            </textarea>

                    <br>
                    <div style="text-align: right">
                        <button type="submit" id="agqa-submit-complaint">Submit Report</button>
                        <button type="button"
                            onclick="document.getElementById('report-featured-popup').style.display='none'">Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <?php endif; ?>

        <?php if (get_user_meta($user_id, 'cuim_viewer_mode', true) == '0') { ?>
        <?php if (current_user_can('administrator') || current_user_can('contributor')): ?>
        <h3>Add Answer</h3>
        <form method="post">
            <input type="text" name="admin_answer_name" placeholder="Answerer's Name"
                style="width:100%; margin-bottom:8px;">
            <br>
            <br>
            <textarea name="admin_answer" placeholder="Write an answer..." style="width:100%; height:100px;"></textarea><br>
            <br>
            <button type="submit">Submit Answer</button>
            <br>
            <br>

        </form>
        <?php endif; ?>
        <?php } ?>

        <?php foreach ($answers as $ans): ?>
        <div class="agqa-answer-item <?php echo ($ans->status == 'pending' ? 'pending_active' : '') ?>"
            style="position:relative;  <?php echo ($ans->status == 'pending' && get_user_meta($user_id, 'cuim_viewer_mode', true)) ? ' display:none;' : ''; ?>">
            <div style="font-weight:bold;"><?php echo esc_html($ans->display_name); ?></div>
            <div style="margin-top:6px;"><?php echo esc_html($ans->content); ?></div>

            <?php if (current_user_can('administrator') || current_user_can('editor') || current_user_can('contributor')): ?>
            <!-- Edit & Admin Tools -->
            <?php if (get_user_meta($user_id, 'cuim_viewer_mode', true) == '0') { ?>
            <div class="agqa-admin-tools-box" style="position:absolute; top:10px; right:10px;">
                <?php if (current_user_can('administrator') || current_user_can('editor')) { ?>
                <form method="post" style="display:inline;">
                    <input type="hidden" name="make_featured" value="1">
                    <input type="hidden" name="answer_id" value="<?php echo $ans->id; ?>">
                    <button type="submit" title="Make Featured"><i class="fas fa-star"></i></button>
                </form>
                <?php } ?>
                <?php if (current_user_can('administrator') || current_user_can('contributor')) { ?>
                <button onclick="document.getElementById('edit-popup-<?php echo $ans->id; ?>').style.display='block'"
                    title="Edit">
                    <i class="fas fa-pencil-alt"></i>
                </button>
                <?php } ?>
                <?php if (current_user_can('administrator') || current_user_can('editor')) { ?>
                <!-- Dropdown Menu -->
                <div class="agqa-admin-dropdown" style="position:relative; display:inline-block;">
                    <button class="agqa-dropdown-toggle" type="button"><i class="fas fa-chevron-down"></i></button>
                    <ul class="agqa-dropdown-menu"
                        style="display:none; position: absolute; top: 100%; right: 0; background: rgb(7, 16, 33); border: 1px solid #586380; list-style: none; padding: 10px 8px; margin: 0; min-width: 160px; z-index: 999; border-radius: 8px">
                        <input type="hidden" name="answer_id" value="<?php echo $ans->id; ?>">
                        <li class="agqa-dropdown-item" data-action="pending"
                            style="padding: 5px 15px; cursor: pointer; <?php echo ($ans->status == 'pending' ? ' border-bottom:2px solid #a8b1ff;' : '') ?>">
                            Pending</li>
                        <li class="agqa-dropdown-item" data-action="approve"
                            style="padding: 5px 15px; cursor: pointer; <?php echo ($ans->status == 'approve' ? ' border-bottom:2px solid #a8b1ff;' : '') ?>">
                            Approved</li>
                        <li class="agqa-dropdown-item" data-action="reject"
                            style="padding: 5px 15px; cursor: pointer; <?php echo ($ans->status == 'reject' ? ' border-bottom:2px solid #a8b1ff;' : '') ?>">
                            Reject</li>
                    </ul>
                </div>
                <?php } ?>
            </div>

            <!-- Edit Popup -->
            <div id="edit-popup-<?php echo $ans->id; ?>" class="agqa-admin-edit-box">
                <form method="post">
                    <input type="hidden" name="edit_answer_id" value="<?php echo $ans->id; ?>">
                    <input type="text" name="edit_answer_name" value="<?php echo esc_attr($ans->display_name); ?>"
                        style="width:100%; margin-bottom:10px;">
                    <textarea name="edit_answer_content"
                        style="width:100%; height:80px;"><?php echo esc_textarea($ans->content); ?></textarea><br>
                    <div style="text-align: right">
                        <button type="submit">Update</button>
                        <button type="button"
                            onclick="document.getElementById('edit-popup-<?php echo $ans->id; ?>').style.display='none'">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
            <?php } ?>
            <?php endif; ?>

            <!-- Report Button -->
            <!-- <button onclick="document.getElementById('report-popup-<?php echo $ans->id; ?>').style.display='block'" style="margin-top:10px;">🚩 Report</button>
                    <div id="report-popup-<?php echo $ans->id; ?>" style="display:none; background:#fff; border:1px solid #aaa; padding:15px; position:fixed; top:20%; left:50%; transform:translateX(-50%); z-index:999; width:90%; max-width:400px;">
                        <form method="post">
                            <strong>Report this answer:</strong><br>
                            <input type="hidden" name="complaint_answer_id" value="<?php echo $ans->id; ?>">
                            <label><input type="radio" name="complaint_reason" value="Inappropriate Content" required> Inappropriate Content</label><br>
                            <label><input type="radio" name="complaint_reason" value="Scam" required> Scam</label><br>
                            <label><input type="radio" name="complaint_reason" value="Spam" required> Spam</label><br>
                            <label><input type="radio" name="complaint_reason" value="Technical Error" required> Technical Error</label><br>
                            <textarea name="note" placeholder="Additional note (optional)..." style="width:100%; height:60px; margin-top:10px;"></textarea>
                            <br><button type="submit">Submit Report</button>
                            <button type="button" onclick="document.getElementById('report-popup-<?php echo $ans->id; ?>').style.display='none'">Cancel</button>
                        </form>
                    </div> -->
        </div>
        <?php endforeach; ?>


        <?php

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                global $wpdb;

                // Helper function to get user status based on role
                function get_user_answer_status()
                {
                    if (current_user_can('contributor')) {
                        return 'pending';
                    }
                    // Admin or others
                    return 'approve'; // or 'approved' as default for admin
                }

                // 1. Add new answer (admin or editor)
                if ((current_user_can('administrator') || current_user_can('contributor')) && isset($_POST['admin_answer'])) {
                    $answer_content = sanitize_text_field($_POST['admin_answer']);
                    $answer_name = sanitize_text_field($_POST['admin_answer_name']);
                    $status = get_user_answer_status();

                    $wpdb->insert("{$wpdb->prefix}agqa_answers", [
                        'question_id' => $qid,
                        'user_id' => get_current_user_id(),
                        'content' => $answer_content,
                        'display_name' => $answer_name,
                        'status' => $status,
                        'created_at' => current_time('mysql')
                    ]);

                    echo '<script>alert("Answer added and marked as ' . esc_js($status) . '"); location.href=location.href;</script>';
                    exit;
                }

                // 2. Edit answer (any user allowed to edit)
                if (isset($_POST['edit_answer_id'], $_POST['edit_answer_content'], $_POST['edit_answer_name'])) {
                    $aid = intval($_POST['edit_answer_id']);
                    $new_content = sanitize_text_field($_POST['edit_answer_content']);
                    $new_name = sanitize_text_field($_POST['edit_answer_name']);
                    $status = get_user_answer_status();

                    $data = [
                        'content' => $new_content,
                        'display_name' => $new_name,
                        'status' => $status
                    ];

                    $wpdb->update("{$wpdb->prefix}agqa_answers", $data, ['id' => $aid]);

                    echo '<script>alert("Answer updated and marked as ' . esc_js($status) . '"); location.href=location.href;</script>';
                    exit;
                }

                // 3. Mark answer as featured (admin or editor)
                if (isset($_POST['make_featured'], $_POST['answer_id'])) {
                    $aid = intval($_POST['answer_id']);
                    $status = get_user_answer_status();

                    // Clear previous featured answer for this question
                    $wpdb->query("UPDATE {$wpdb->prefix}agqa_answers SET is_featured = 0 WHERE question_id = $qid");

                    // Mark this as featured, also update status if editor
                    $data = ['is_featured' => 1];
                    if ($status === 'pending') {
                        $data['status'] = 'pending';
                    }

                    $wpdb->update("{$wpdb->prefix}agqa_answers", $data, ['id' => $aid]);

                    echo '<script>alert("Answer marked as featured and status updated."); location.href=location.href;</script>';
                    exit;
                }
            }


            ?>
    </div>
    <?php
        return ob_get_clean();
    }


add_shortcode('agqa_complaints_admin', 'agqa_render_complaint_dashboard');
function agqa_render_complaint_dashboard()
{
    if (!current_user_can('administrator'))
        return '<p>You do not have access to this section.</p>';
    global $wpdb;

    $table = $wpdb->prefix . 'agqa_complaints';
    $table_questions = $wpdb->prefix . 'agqa_complaints_questions'; // Table for question complaints
    $answers = $wpdb->prefix . 'agqa_answers';
    $questions = $wpdb->prefix . 'agqa_questions';
    $users = $wpdb->users;

    // Fetching complaints related to answers
    $answer_complaints = $wpdb->get_results("
        SELECT c.*, 
               a.content AS answer_content, 
               a.display_name AS answer_name, 
               u.display_name AS user_name 
        FROM {$table} c
        LEFT JOIN {$answers} a ON c.answer_id = a.id
        LEFT JOIN {$users} u ON c.user_id = u.ID
        ORDER BY c.created_at DESC
    ");

    // Fetching complaints related to questions
    $question_complaints = $wpdb->get_results("
        SELECT c.*, 
               q.question AS question_content, 
               u.display_name AS user_name 
        FROM {$table_questions} c
        LEFT JOIN {$questions} q ON c.question_id = q.id
        LEFT JOIN {$users} u ON c.user_id = u.ID
        ORDER BY c.created_at DESC
    ");

    ob_start();
    echo '<h2 class="eu"> Complaint Moderation Panel</h2>
    <hr class="heading-divider-mc" />';

    // Answer complaints table
  echo '<h3>Answer Complaints</h3>';
  echo '<table class="answer-table" style="width:100%;border-collapse:collapse;">
        <thead class="t-answer">
            <tr>
                <th style="width: 50%;">Answer</th>
                <th style="width: 10%;">Answer By</th>
                <th style="width: 10%;">Reported By</th>
                <th style="width: 10%;">Reason</th>
                <th style="width: 10%;">Note</th>
                <th style="width: 5%;">Status</th>
                <th style="width: 5%;">Actions</th>
            </tr>
        </thead>
        <tbody>';
    foreach ($answer_complaints as $c) {
        $approveLabel = in_array($c->reason, ['Inappropriate Content', 'Technical Error']) ? '✅' : '✅';
        $approveLabel_status = in_array($c->reason, ['Inappropriate Content', 'Technical Error']) ? 'marked' : 'approve';
        
        echo '<tr>
                <td>' . esc_html($c->answer_content) . '</td>
                <td>' . esc_html($c->answer_name) . '</td>
                <td>' . esc_html($c->user_name) . '</td>
                <td>' . esc_html($c->reason) . '</td>
                <td>' . esc_html($c->note) . '</td>
                <td>' . esc_html(strtolower($c->status) === '' ? 'Marked' : ucfirst($c->status)) . '</td>
                <td style="text-align: center;">
                    <!-- Dropdown Menu with Font Awesome Chevron Icon -->
                    <div class="dropdown">
                        <button class="dropbtn">
                            <i class="fas fa-chevron-down"></i> <!-- Font Awesome Icon -->
                        </button>
                        <div class="dropdown-content">
                            <form method="post">
                                <input type="hidden" name="complaint_id" value="' . intval($c->id) . '">
                                <input type="hidden" name="decision" value="approve">
                                <button type="submit" class="approve-btn">Approve</button>
                            </form>
                            <form method="post">
                                <input type="hidden" name="complaint_id" value="' . intval($c->id) . '"> 
                                <input type="hidden" name="decision" value="rejected">
                                <button type="submit" class="reject-button">Reject</button>
                            </form>
                        </div>
                    </div>
                </td>
            </tr>';
    }
    echo '</tbody></table>';

  echo '
    <script>
        // Toggle Dropdown on Button Click
        document.addEventListener("DOMContentLoaded", function() {
            var dropdownBtns = document.querySelectorAll(".dropbtn");
            
            dropdownBtns.forEach(function(btn) {
                btn.addEventListener("click", function() {
                    var dropdownContent = this.nextElementSibling;
                    dropdownContent.style.display = dropdownContent.style.display === "block" ? "none" : "block";
                });
            });
        });
    </script>

<style>
    /* Dropdown Menu */
    .dropdown {
        position: relative;
        display: inline-block;
    }

    .dropbtn {
        background-color: #3f3f4d;
        color: white;
            padding: 8px 11px 10px 3px;
        border: none;
        cursor: pointer;
        border-radius: 8px;
        font-size: 16px;
        display: flex;
        align-items: center;
        font-weight: 500;
    }

    .dropdown-content {
        display: none;
        position: absolute;
        background-color: #6B4EBD;
        min-width: 160px;
        box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.2);
        z-index: 1;
        border-radius: 8px;
        // margin-top: 5px;
        right: 18px;
        padding: 10px !important;
    }

    .dropdown-content form {
        display: flex;
        flex-direction: column;
    }

    .dropdown-content button {
        padding: 10px;
        background-color: #4b4b5d;
        border-radius: 8px;
        color: white;
        border: none;
        width: 100%;
        margin: 5px 0;
        text-align: center;
        cursor: pointer;
        font-weight: 400;
    }

    .dropdown-content button:hover {
        background-color: #6f6f7f;
    }

    .dropdown-content button:focus {
        outline: none;
    }

    /* Font Awesome Icon */
    .dropbtn i {
        margin-left: 8px;
    }
    .approve-btn,
    .reject-button{
        background-color: #6B4EBD !important;
    }
</style>
';




    // Question complaints table
    echo '<h3>Question Complaints</h3>';
    echo '<table class="question-table" style="width:100%;border-collapse:collapse;">
            <thead class="t-question">
                <tr>
                    <th>Questions</th>
                    <th>Reported By</th>
                    <th>Reason</th>
                    <th>Note</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>';
foreach ($question_complaints as $complaint) {
    $approveLabel = in_array($complaint->reason, ['Inappropriate Content', 'Technical Error']) ? '✅' : '✅';
    $approveLabel_status = in_array($complaint->reason, ['Inappropriate Content', 'Technical Error']) ? 'marked' : 'approve';

    // Check if the status is empty, if so, default it to 'approve'
    $final_status = empty($complaint->status) ? 'approve' : $complaint->status;

    echo '<tr>
            <td>' . esc_html($complaint->question_content) . '</td>
            <td>' . esc_html($complaint->user_name) . '</td>
            <td>' . esc_html($complaint->reason) . '</td>
            <td>' . esc_html($complaint->note) . '</td>
            <td>' . esc_html(strtolower($complaint->status) === '' ? 'Marked' : ucfirst($complaint->status)) . '</td>
            <td style="text-align: center;">
                <!-- Dropdown Menu with Font Awesome Chevron Icon for Question Complaints -->
                <div class="dropdown">
                    <button class="question-dropbtn" data-id="' . intval($complaint->id) . '">
                        <i class="fas fa-chevron-down"></i> <!-- Font Awesome Icon -->
                    </button>
                    <div class="dropdown-content" id="dropdown-content-' . intval($complaint->id) . '">
                        <form method="post">
                            <input type="hidden" name="q_complaint_id" value="' . intval($complaint->id) . '">
                            <input type="hidden" name="decision" value="approve">
                            <button type="submit" class="approve-btn">Approve</button>
                        </form>
                        <form method="post">
                            <input type="hidden" name="q_complaint_id" value="' . intval($complaint->id) . '">
                            <input type="hidden" name="decision" value="rejected">
                            <button type="submit" class="reject-button">Reject</button>
                        </form>
                    </div>
                </div>
            </td>
          </tr>';
}
echo '</tbody></table>';

echo '

<script>
    // Toggle Dropdown on Button Click for Question Complaints Only
    document.addEventListener("DOMContentLoaded", function() {
        // Select all dropdown buttons for question complaints
        var dropdownBtns = document.querySelectorAll(".question-dropbtn");
        
        dropdownBtns.forEach(function(btn) {
            btn.addEventListener("click", function() {
                // Use data-id attribute to target the correct dropdown content
                var id = btn.getAttribute("data-id");
                var dropdownContent = document.getElementById("dropdown-content-" + id);
                
                // Toggle the display of the corresponding dropdown content
                dropdownContent.style.display = dropdownContent.style.display === "block" ? "none" : "block";
            });
        });
    });
</script>

<style>
    /* Dropdown Menu */
    .dropdown {
        position: relative;
        display: inline-block;
    }

    .question-dropbtn {
        background-color: #3f3f4d;
        color: white;
        padding: 8px 20px;
        border: none;
        cursor: pointer;
        border-radius: 8px;
        font-size: 16px;
        display: flex;
        align-items: center;
        font-weight: 500;
    }

    .dropdown-content {
        display: none;
        position: absolute;
        background-color: #3f3f4d;
        min-width: 160px;
        box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.2);
        z-index: 1;
        border-radius: 8px;
        right: 18px; /* Right-aligned */
    }

    .dropdown-content form {
        display: flex;
        flex-direction: column;
    }

    .dropdown-content button {
        padding: 10px;
        background-color: #4b4b5d;
        border-radius: 8px;
        color: white;
        border: none;
        width: 100%;
        margin: 5px 0;
        text-align: center;
        cursor: pointer;
        font-weight: 400;
    }

    .dropdown-content button:hover {
        background-color: #6f6f7f;
    }

    .dropdown-content button:focus {
        outline: none;
    }

    /* Font Awesome Icon */
    .question-dropbtn i {
        margin-left: 8px;
    }
</style>
';


    // Handle form action for answer complaints
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['complaint_id'], $_POST['decision'])) {
        $id = intval($_POST['complaint_id']);
        $decision = sanitize_text_field($_POST['decision']);
        $note = isset($_POST['note']) ? sanitize_text_field($_POST['note']) : '';

        $complaint = $wpdb->get_row("SELECT * FROM {$table} WHERE id = {$id}");

        if ($complaint && in_array($decision, ['approve', 'marked', 'rejected'])) {
            $final_status = $decision;

            $wpdb->update($table, [
                'status' => $final_status,
                'note' => $note,
                'updated_at' => current_time('mysql')
            ], ['id' => $id]);

            echo '<script>location.href=location.href;</script>';
        }
    }

    // Handle form action for question complaints
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['q_complaint_id'], $_POST['decision'])) {
        $id = intval($_POST['q_complaint_id']);
        $decision = sanitize_text_field($_POST['decision']);
        $note = isset($_POST['note']) ? sanitize_text_field($_POST['note']) : '';

        $complaint = $wpdb->get_row("SELECT * FROM {$table_questions} WHERE id = {$id}");

        if ($complaint && in_array($decision, ['approve', 'marked', 'rejected'])) {
            $final_status = $decision;

            $wpdb->update($table_questions, [
                'status' => $final_status,
                'note' => $note,
                'updated_at' => current_time('mysql')
            ], ['id' => $id]);

            echo '<script>location.href=location.href;</script>';
        }
    }

    return ob_get_clean();
}