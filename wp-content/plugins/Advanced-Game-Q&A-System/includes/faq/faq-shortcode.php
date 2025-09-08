<?php
function custom_faq_shortcode()
{

    $add_faq_new  = isset($_GET['add']) ? intval($_GET['add']) : 0;
    $add_faq_edit  = isset($_GET['edit']) ? intval($_GET['edit']) : 0;
    $add_faq_history = isset($_GET['history']) ? intval($_GET['history']) : 0;
    $add_faq_review = isset($_GET['review']) ? intval($_GET['review']) : 0;
    $edit_faq_review = isset($_GET['edit-review']) ? intval($_GET['edit-review']) : 0;
    // echo $edit_faq_review;


    global $wpdb;
    $table_agqa_faq = $wpdb->prefix . 'agqa_faq';
    $table_agqa_faq_like = $wpdb->prefix . 'agqa_faq_likes_dislikes';


    if ($add_faq_edit == 0 && $add_faq_new == 0) {
        $faq_data = $wpdb->get_results("
            SELECT
                id,
                question,
                answer,
                faq_category,
                verified_answer
            FROM $table_agqa_faq
            ORDER BY id DESC
        ");

        $table_data_like = $wpdb->get_results("
            SELECT
                id,
                faq_id,
                user_id,
                action_type
            FROM $table_agqa_faq_like
            ORDER BY id DESC
        ");
    }

    if ($add_faq_edit !== 0) {
        $faq_data = $wpdb->get_results($wpdb->prepare("
        SELECT
            id,
            question,
            answer,
            faq_category,
            verified_answer
        FROM $table_agqa_faq
        WHERE id = %d
        ORDER BY id DESC
    ", $add_faq_edit));
    }


    ob_start();
    include AGQA_PATH . 'includes/faq/faq-report.php';

    if ($add_faq_new !== 0) {
        include AGQA_PATH . 'includes/faq/add-form-faq.php';
    }

    if ($add_faq_edit !== 0) {
        include AGQA_PATH . 'includes/faq/edit-form-faq.php';
    }
    if ($add_faq_history !== 0) {
        include AGQA_PATH . 'includes/faq/faq-history.php';
    }
    if ($add_faq_review !== 0) {
        include AGQA_PATH . 'includes/faq/faq-review.php';
    }
    if ($edit_faq_review !== 0) {
        include AGQA_PATH . 'includes/faq/faq-edit-review.php';
    }
?>
    <?php if ($add_faq_new == 0 && $add_faq_edit == 0 && $add_faq_review == 0 && $add_faq_history == 0 && $edit_faq_review == 0) { ?>
        <div class="faq-template">
            <div id="page-content">
                <!-- Content will be dynamically updated based on pagination -->
            </div>
            <div class="template-title">
                <h1>FAQ</h1>
            </div>
            <!-- filter Start -->
            <div class="filter-container">
                <div class="filter-area">
                    <form action="#" autocomplete="off">
                        <input type="search" name="filter-search" id="filter-search" placeholder="description">
                        <div class="filter-select">
                            <input type="hidden" name="filter-select-hidden" class="agqa-filter-select-hidden">
                            <button class="filter-select-title">
                                <span class="filter-default-text">select Role</span>
                                <span class="filter-selected-text"></span>
                            </button>
                            <div class="filter-select-list">
                                <ul>
                                    <li>All</li>
                                    <li>Account & Access Management</li>
                                    <li>System Features & Workflow</li>
                                    <li>Reports & Data Queries</li>
                                    <li>Errors & Troubleshooting</li>
                                    <li>Notifications & Alerts</li>
                                    <li>Performance & Compatibility</li>
                                    <li>System Settings & Customization</li>
                                    <li>Customer Support & Contact</li>
                                    <li>Other</li>
                                </ul>
                            </div>
                        </div>
                        <button type="submit" class="filter-select-button" id="agqa-game-filter"><span>Search</span></button>
                    </form>
                </div>
                <div class="filter-right-area">
                    <div class="add-button-ctn">
                        <a href="<?php echo esc_url(home_url('faq/') . '?add=1'); ?>" class="add-button">
                            <img src="<?php echo AGQA_URL ?>assets/images/plus-icon.svg" alt="Plus Icon">Add FAQ
                        </a>
                    </div>
                </div>
            </div>
            <!-- filter End -->

            <!-- Main Content Start -->
            <div class="faq-main-content">

                <div class="faq-accordions">
                    <?php foreach ($faq_data as $faq_value) {
                        // Initialize like and dislike counts for this FAQ
                        $likes_data = 0;
                        $unlikes_data = 0;

                        // Track the user's action (like or dislike) for this FAQ
                        $user_action = ''; // empty, 'liked', or 'disliked'

                        foreach ($table_data_like as $aga_like) {
                            // Fetch like count for the specific faq_id and user_id
                            if ($aga_like->faq_id == $faq_value->id) {
                                if ($aga_like->action_type == '1') {
                                    $likes_data++; // Increment like count for this faq_id
                                } elseif ($aga_like->action_type == '0') {
                                    $unlikes_data++; // Increment dislike count for this faq_id
                                }

                                // Check if the current user has liked or disliked this FAQ
                                if ($aga_like->user_id == get_current_user_id()) {
                                    if ($aga_like->action_type == '1') {
                                        $user_action = 'liked'; // User has liked this FAQ
                                    } elseif ($aga_like->action_type == '0') {
                                        $user_action = 'disliked'; // User has disliked this FAQ
                                    }
                                }
                            }
                        }

                        // Default like and dislike counts
                        $like_count = $likes_data;
                        $unlike_count = $unlikes_data;
                    ?>

                        <div class="faq-accordion" data-id="<?php echo $faq_value->id ?>">
                            <div class="faq-accodion-status"><?php echo $faq_value->faq_category; ?></div>
                            <div class="faq-accordion-head">
                                <h2><?php echo esc_html($faq_value->question); ?></h2>
                                <button class="button agqa-status-toggle"
                                    style="background: transparent !important; padding: 0; padding-left: 10px;">
                                    <img src="<?php echo esc_url(AGQA_URL . 'assets/images/accordian-arrow.svg'); ?>" alt="Arrow">
                                </button>
                            </div>

                            <div class="faq-accordion-body">
                                <?php if ($faq_value->answer) { ?>
                                    <?php echo ($faq_value->answer); ?>
                                <?php } ?>
                            </div>

                            <div class="faq-accordion-bottom">
                                <button
                                    class="faq-accordion-button like-button <?php echo ($user_action == 'liked') ? 'active' : ''; ?>"
                                    name="action-type">
                                    <div class="faq-accordion-icon">
                                        <input type="hidden" class="agqa-like" name="faq-id"
                                            value="<?php echo esc_attr($faq_value->id); ?>">
                                        <img src="<?php echo esc_url(AGQA_URL . 'assets/images/like-icon.svg'); ?>" alt="Like Icon">
                                    </div>
                                    <span class="like-coounting"><?php echo esc_html($like_count); ?></span>
                                </button>

                                <!-- Unlike Button -->
                                <button
                                    class="faq-accordion-button unlike-button <?php echo ($user_action == 'disliked') ? 'active' : ''; ?>"
                                    name="action-type">
                                    <div class="faq-accordion-icon">
                                        <input type="hidden" class="agqa-dislike" name="faq-id"
                                            value="<?php echo esc_attr($faq_value->id); ?>">
                                        <img src="<?php echo esc_url(AGQA_URL . 'assets/images/unlike-icon.svg'); ?>"
                                            alt="Un Like Icon">
                                    </div>
                                    <span class="unlike-coounting"><?php echo esc_html($unlike_count); ?></span>
                                </button>

                                <!-- Copy, Edit, Delete and Report buttons (not modified) -->
                                <button class="faq-accordion-button copy-button">
                                    <div class="faq-accordion-icon">
                                        <img src="<?php echo esc_url(AGQA_URL . 'assets/images/copy-text-icon.svg'); ?>"
                                            alt="Copy Icon">
                                    </div>
                                    <span>Copy</span>
                                </button>

                                <a href="<?php echo esc_url(home_url('faq/') . '?edit=' . $faq_value->id); ?>"
                                    class="faq-accordion-button edit-button">
                                    <div class="faq-accordion-icon">
                                        <img src="<?php echo esc_url(AGQA_URL . 'assets/images/edit-icon.svg'); ?>" alt="Edit Icon">
                                    </div>
                                    Edit
                                </a>
                                <div id="custom-faq-field-popup">
                                    <div id="custom-faq-field-popup-inner">
                                        <h2>Delete</h2>
                                        <div class="popup-form-cross-icon"></div>
                                        <div class="form-message">Are you sure you want to Delete?</div>
                                        <div class="agqa-popup-form-buttons d-flex">
                                            <button class="no-cancel" type="button">No</button>
                                            <button id="yes-cancel" type="submit" value="<?php echo $faq_value->id; ?>">Yes</button>
                                        </div>
                                    </div>
                                </div>
                                <button class="faq-accordion-button delete-button">
                                    <div class="faq-accordion-icon">
                                        <input type="hidden" class="agqa-dell" name="faq-id"
                                            value="<?php echo esc_attr($faq_value->id); ?>">
                                        <img src="<?php echo esc_url(AGQA_URL . 'assets/images/delete-icon.svg'); ?>"
                                            alt="Delete Icon">
                                    </div>
                                    <span>Delete</span>
                                </button>

                                <button class="faq-accordion-button report-button">
                                    <div class="faq-accordion-icon">
                                        <img src="<?php echo esc_url(AGQA_URL . 'assets/images/alert-icon.svg'); ?>"
                                            alt="Report Icon">
                                    </div>
                                    <span>Report</span>
                                </button>

                                <button class="faq-accordion-button verified-button">
                                    <div class="faq-accordion-icon">
                                        <img src="<?php echo esc_url(AGQA_URL . 'assets/images/verified-icon.svg'); ?>"
                                            alt="Verified Answer Icon">
                                    </div>
                                    <span>Verified answer</span>
                                </button>
                            </div>
                        </div>
                    <?php } ?>
                    <div class="section-found">
                        <div class="no-found-ctn">
                            <div class="search-no-found">
                                <div class="search-no-found-icon">
                                    <img src="<?php echo AGQA_URL ?>assets/images/search-forund-icon.svg" alt="Search Icon">
                                </div>
                                <div class="search-no-found-text">
                                    <h2>Nothing matched your search</h2>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="pagination-ctn">
                <div id="pagination-demo"></div>
            </div>
            <!-- Main Content End -->
        </div>
        <script>
            jQuery(document).ready(function($) {
                var itemsPerPage = 15;
                var totalItems = jQuery('.faq-accordion').length;

                // Only show pagination if more than 16 items exist
                if (totalItems > 16) {
                    var totalPages = Math.ceil(totalItems / itemsPerPage);
                    jQuery('#pagination-demo').twbsPagination({
                        totalPages: totalPages, // Total pages
                        visiblePages: 3, // Number of visible pages
                        onPageClick: function(event, page) {
                            jQuery('.faq-accordion').hide(); // Hide all items initially

                            jQuery('.faq-accordion').each(function(index) {
                                var pageIndex = Math.floor(index / itemsPerPage) + 1;
                                if (pageIndex === page) {
                                    jQuery(this).show();
                                }
                            });
                        }
                    });
                    jQuery('.faq-accordion').each(function(index) {
                        var pageIndex = Math.floor(index / itemsPerPage) + 1;
                        jQuery(this).attr('data-page', pageIndex);
                    });
                } else {
                    jQuery('#pagination-demo').hide(); // Hide pagination if there are 16 or fewer items
                }
            });
        </script>

    <?php } ?>
<?php
    return ob_get_clean();
} ?>
<?php add_shortcode('custom_faq', 'custom_faq_shortcode'); ?>