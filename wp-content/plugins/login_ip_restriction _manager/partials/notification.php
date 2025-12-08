<?php

$getUserRole = get_user_role_simple();

global $wpdb;
$table_agqa_report_system = $wpdb->prefix . 'faq_report_system';
$table_agqa_approval_sysetm = $wpdb->prefix . 'agqa_approval_review_page';
$class_active = '';
$class_approval_active = '';
$pending_response_count = $wpdb->get_var("
SELECT COUNT(*) 
FROM $table_agqa_report_system
WHERE status = 'Pending Response'
");


$approval_pending_count = $wpdb->get_var("
SELECT COUNT(*) 
FROM $table_agqa_approval_sysetm
WHERE status = 'Pending'
");


$pending_response_read = $wpdb->get_var("
SELECT COUNT(*) 
FROM $table_agqa_report_system
WHERE read_report = ''
");
if ($pending_response_read > 0) {
    $class_active = 'active';
}


$pending_approval_read = $wpdb->get_var("
SELECT COUNT(*) 
FROM $table_agqa_approval_sysetm
WHERE read_report = ''
");
if ($pending_approval_read > 0) {
    $class_approval_active = 'active';
}


// Get current user ID
$current_user_id = get_current_user_id();

// Build SQL
$sql_read_report_user = $wpdb->prepare("
    SELECT
        id,
        user_id,
        status,
        user_read_report,
        create_time
    FROM {$wpdb->prefix}faq_report_system
    WHERE user_id = %d
    AND status != 'pending response'  -- Exclude 'pending response' status
    AND (user_read_report IS NULL OR user_read_report = '')  -- Ensure user_read_report is empty or NULL
    ORDER BY id DESC
    LIMIT 1
", $current_user_id);

// Get result
$last_report = $wpdb->get_row($sql_read_report_user); // get_row returns only one row



$sql_review_read_user = $wpdb->prepare("
    SELECT
        id,
        user_id,
        user_read_report,
       created_at
    FROM {$wpdb->prefix}agqa_approval_review_page
    WHERE user_id = %d
    ORDER BY id DESC
    LIMIT 1
", $current_user_id);

// Get result
$last_review_report = $wpdb->get_row($sql_review_read_user);
// Table name
$table_agqa_manage_user = "{$wpdb->prefix}agqa_wiki_add_users";

// Query: Get current user's data
$add_manage_users_data = $wpdb->get_row(
    $wpdb->prepare("
        SELECT
        created_at
        FROM $table_agqa_manage_user
        WHERE user_id = %d
        LIMIT 1
    ", $current_user_id)
);


// Table name
$table_name_profile = "{$wpdb->prefix}agqa_wiki_read_user_profile";

// Query: Get user_id from table where it matches current user
$found_user_id = $wpdb->get_var(
    $wpdb->prepare("SELECT user_id FROM $table_name_profile WHERE user_id = %d", $current_user_id)
);
?>
<div class="notification-ctn">
    <div class="notification-button">
        <div class="notification-button-counting">0</div>
        <div class="notification-button-icon">
            <img src="<?php echo URIP_URL ?>assets/image/notification-icon.svg" alt="Notification Icon">
        </div>
    </div>
    <div class="notification-popup <?php echo $disabledActionClassFreeze; ?>">
        <div class="notification-popup-head">
            <?php if ($getUserRole !== 'viewer') { ?>
                <div class="notification-tags">
                    <div class="notification-tag-card">
                        <span class="notification-dot <?php echo $class_active; ?>"></span>
                        <span>Pending Reports</span>
                        <a href="#"
                            class="notification-count active cuim-response-review-count"><?php echo $pending_response_count; ?></a>
                    </div>
                    <?php if ($getUserRole !== 'contributor') { ?>
                        <div class="notification-tag-card">
                            <span class="notification-dot <?php echo $class_approval_active; ?>"></span>
                            <span>Pending Review</span>
                            <?php if ($approval_pending_count > 0) { ?>
                                <a href="#"
                                    class="notification-count active cuim-review-pending-count"><?php echo $approval_pending_count; ?></a>
                            <?php } else { ?>
                                <a href="#" class="notification-count"><?php echo $approval_pending_count; ?></a>
                            <?php  } ?>
                        </div>
                    <?php  } ?>
                </div>
            <?php } ?>
            <?php if (($last_report && $last_report->user_read_report !== "read" && strtolower($last_report->status) !== 'pending response') || $current_user_id !== (int) $found_user_id) { ?>
                <div class="notification-list-heading"><strong>Notification</strong></div>
                <div class="notification-lists-ctn">
                    <?php if ($last_review_report && $last_review_report->user_read_report !== "read") { ?>
                        <!-- <div class="notification-list cuim-user-review-report" style="cursor: pointer;">
                            <span class="notification-list-dot"></span>
                            <div class="notification-list-title">
                                Unread review responses available
                            </div>
                            <div class="notification-list-date">
                                <?php echo date('Y/m/d', strtotime($last_review_report->created_at)); ?>
                            </div>

                        </div> -->
                    <?php } ?>
                    <?php if ($last_report && $last_report->user_read_report !== "read" && strtolower($last_report->status) !== 'pending response') { ?>
                        <div class="notification-list cuim-user-faq-report" style="cursor: pointer;">
                            <span class="notification-list-dot"></span>
                            <div class="notification-list-title">
                                Unread report responses available
                            </div>
                            <div class="notification-list-date">
                                <?php echo date('Y/m/d', strtotime($last_report->create_time)); ?>
                            </div>

                        </div>
                    <?php } ?>
                    <?php if ($current_user_id !== (int) $found_user_id) { ?>

                        <div class="notification-list cuim-user-profile-note" style="cursor: pointer;">
                            <span class=" notification-list-dot"></span>
                            <div class="notification-list-title">
                                Please set up your profile
                            </div>
                            <?php if ($add_manage_users_data) { ?>
                                <div class="notification-list-date">
                                    <?php echo date('Y/m/d', strtotime($add_manage_users_data->created_at)); ?></div>
                            <?php } else {

                                echo '<div class="notification-list-date">2025/09/01</div>';
                            } ?>
                        </div>
                    <?php } ?>
                </div>
            <?php } ?>
        </div>
    </div>
</div>
<script>
    jQuery(document).ready(function() {
        // setTimeout(function() {
        //     var $btn = jQuery('.filter-pending-responses');
        //     const btn = $btn.get(0);
        //     // Create and dispatch mouse events
        //     ['mousedown', 'mouseup', 'click'].forEach(eventType => {
        //         btn.dispatchEvent(
        //             new MouseEvent(eventType, {
        //                 view: window,
        //                 bubbles: true,
        //                 cancelable: true
        //             })
        //         );
        //     });

        // }, 2000);
        jQuery('.cuim-response-review-count').on('click', function(e) {
            e.preventDefault();
            var nonce = cuim_ajax.nonce;
            jQuery.ajax({
                url: cuim_ajax.ajax_url,
                type: "POST",
                data: {
                    action: "handle_faq_read_report",
                    nonce: nonce, // Nonce for security
                },
                success: function(response) {
                    // If deletion is successful, hide the popup and remove the FAQ from the DOM
                    if (window.location.pathname.startsWith("/zh")) {
                        window.location.href = "/zh/report-system/?status=pending";
                    } else {
                        window.location.href = "/report-system/?status=pending";
                    }

                    if (response.includes("Success")) {
                        // $(".faq-accordion[data-id='" + del + "']").remove();
                    } else {
                        alert(response);
                    }
                },
                error: function() {
                    alert("An error occurred while deleting the FAQ.");
                },
            });
        });

        jQuery('.cuim-review-pending-count').on('click', function(e) {
            e.preventDefault();
            var nonce = cuim_ajax.nonce;
            jQuery.ajax({
                url: cuim_ajax.ajax_url,
                type: "POST",
                data: {
                    action: "handle_approval_read_report",
                    nonce: nonce, // Nonce for security
                },
                success: function(response) {
                    // If deletion is successful, hide the popup and remove the FAQ from the DOM
                    if (window.location.pathname.startsWith("/zh")) {
                        window.location.href = "/zh/approval-page";
                    } else {
                        window.location.href = "/approval-page/";
                    }

                    if (response.includes("Success")) {
                        // $(".faq-accordion[data-id='" + del + "']").remove();
                    } else {
                        alert(response);
                    }
                },
                error: function() {
                    alert("An error occurred while deleting the FAQ.");
                },
            });
        });


        jQuery('.cuim-user-faq-report').on('click', function(e) {
            e.preventDefault();
            var nonce = cuim_ajax.nonce;
            jQuery.ajax({
                url: cuim_ajax.ajax_url,
                type: "POST",
                data: {
                    action: "handle_faq_user_read_report",
                    nonce: nonce, // Nonce for security
                },
                success: function(response) {
                    // If deletion is successful, hide the popup and remove the FAQ from the DOM
                    if (window.location.pathname.startsWith("/zh")) {
                        window.location.href = "/zh/report-system/";
                    } else {
                        window.location.href = "/report-system/";
                    }

                    if (response.includes("Success")) {
                        // $(".faq-accordion[data-id='" + del + "']").remove();
                    } else {
                        alert(response);
                    }
                },
                error: function() {
                    alert("An error occurred while deleting the FAQ.");
                },
            });
        });


        jQuery('.cuim-user-review-report').on('click', function(e) {
            e.preventDefault();
            var nonce = cuim_ajax.nonce;
            jQuery.ajax({
                url: cuim_ajax.ajax_url,
                type: "POST",
                data: {
                    action: "handle_approval_user_review_report",
                    nonce: nonce, // Nonce for security
                },
                success: function(response) {
                    // If deletion is successful, hide the popup and remove the FAQ from the DOM
                    window.location.href = "/approval-page/";

                    if (response.includes("Success")) {
                        // $(".faq-accordion[data-id='" + del + "']").remove();
                    } else {
                        alert(response);
                    }
                },
                error: function() {
                    alert("An error occurred while deleting the FAQ.");
                },
            });
        });

        jQuery('.cuim-user-profile-note').on('click', function(e) {
            e.preventDefault();
            // $notificationCounting = jQuery('.notification-button-counting').text();
            var $el = jQuery('.notification-button-counting');
            var count = parseInt($el.text().trim(), 10) || 0;

            if (count > 1) {
                $el.text(count - 1);
            } else if (count == 1) {
                $el.text(count - 1);
                jQuery('.notification-list-heading').remove();
                jQuery('.notification-lists-ctn').remove();
            }
            var nonce = cuim_ajax.nonce;
            jQuery.ajax({
                url: cuim_ajax.ajax_url,
                type: "POST",
                data: {
                    action: "handle_user_profile_notification",
                    nonce: nonce, // Nonce for security
                },
                success: function(response) {
                    // If deletion is successful, hide the popup and remove the FAQ from the DOM
                    jQuery('.cuim-profile-form-wrapper').addClass('active');
                    jQuery('.notification-popup').removeClass('active');
                    jQuery('.cuim-user-profile-note').hide();
                    if (response.includes("Success")) {
                        // $(".faq-accordion[data-id='" + del + "']").remove();
                    } else {
                        console.log(response);
                    }
                },
                error: function() {
                    alert("An error occurred while deleting the FAQ.");
                },
            });
        });
        // Count the number of elements with class 'notification-list'
        var count = jQuery('.notification-list').length;

        // Show count in console (ya jahan chaho use kar sakte ho)
        console.log("Total .notification-list elements:", count);

        // Agar aap count ko kisi element mein show karna chahte ho, for example:
        jQuery('.notification-button-counting').text(count);
    });
</script>