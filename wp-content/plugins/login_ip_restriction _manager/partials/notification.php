<?php
global $wpdb;
$table_agqa_report_system = $wpdb->prefix . 'faq_report_system';
$class_active = '';
$pending_response_count = $wpdb->get_var("
                SELECT COUNT(*) 
                FROM $table_agqa_report_system
                WHERE status = 'Pending Response'
            ");


$pending_response_read = $wpdb->get_var("
                SELECT COUNT(*) 
                FROM $table_agqa_report_system
                WHERE read_report = ''
            ");
if ($pending_response_read > 0) {
    $class_active = 'active';
}


// Get current user ID
$current_user_id = get_current_user_id();

// Build SQL
$sql = $wpdb->prepare("
    SELECT
        id,
        user_id,
        user_read_report,
        create_time
    FROM {$wpdb->prefix}faq_report_system
    WHERE user_id = %d
    ORDER BY id DESC
    LIMIT 1
", $current_user_id);

// Get result
$last_report = $wpdb->get_row($sql); // get_row returns only one row



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


?>
<div class="notification-ctn">
    <div class="notification-button">
        <div class="notification-button-counting">2</div>
        <div class="notification-button-icon">
            <img src="<?php echo URIP_URL ?>assets/image/notification-icon.svg" alt="Notification Icon">
        </div>
    </div>
    <div class="notification-popup">
        <div class="notification-popup-head">
            <div class="notification-tags">
                <div class="notification-tag-card">
                    <span class="notification-dot <?php echo $class_active; ?>"></span>
                    <span>Pending Reports</span>
                    <a href="#" class="notification-count active"><?php echo esc_html($pending_response_count); ?></a>
                </div>
                <div class="notification-tag-card">
                    <span class="notification-dot"></span>
                    <span>Pending Review</span>
                    <a href="#" class="notification-count">0</a>
                </div>
            </div>
            <div class="notification-list-heading"><strong>Notification</strong></div>
            <div class="notification-lists-ctn">
                <?php if ($last_report && $last_report->user_read_report !== "read") { ?>
                    <div class="notification-list">
                        <span class="notification-list-dot"></span>
                        <div class="notification-list-title">
                            Unread report responses available
                        </div>
                        <div class="notification-list-date">
                            <?php echo date('Y/m/d', strtotime($last_report->create_time)); ?>
                        </div>

                    </div>
                <?php } ?>
                <div class="notification-list">
                    <span class="notification-list-dot"></span>
                    <div class="notification-list-title">
                        Please set up your profile
                    </div>
                    <div class="notification-list-date"> <?php echo date('Y/m/d', strtotime($add_manage_users_data->created_at)); ?></div>
                </div>
            </div>
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
        jQuery('.notification-count.active').on('click', function(e) {
            e.preventDefault();
            var nonce = agqa_ajax.nonce;
            jQuery.ajax({
                url: agqa_ajax.ajax_url,
                type: "POST",
                data: {
                    action: "handle_faq_read_report",
                    nonce: nonce, // Nonce for security
                },
                success: function(response) {
                    // If deletion is successful, hide the popup and remove the FAQ from the DOM
                    window.location.href = "/report-system/?status=pending";

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

    });
</script>