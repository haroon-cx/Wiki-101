<?php
$username = isset($_GET['username']) ? sanitize_text_field($_GET['username']) : '';
$code = isset($_GET['code']) ? sanitize_text_field($_GET['code']) : '2025-01-01';
$date_diff = 10;
$user_info = "";
global $wpdb;
$table_agqa_manage_user = $wpdb->prefix . 'agqa_wiki_add_users';
if ($code) {
    $code_date = DateTime::createFromFormat('Y-m-d', $code); // Convert 'code' to DateTime object
    $current_date = new DateTime(); // Current date
    $date_diff = $current_date->diff($code_date)->days; // Calculate the difference in days
}

// Fetch the user record from the database
if ($username) {
    $user_info = $wpdb->get_row($wpdb->prepare("
        SELECT
            id,
            user_id,
            account,
            new_password,
            confirm_password,
            state,
            user_role,
            company_name,
            email,
            custom_label_1,
            custom_label_2,
            custom_label_3,
            custom_label_4,
            custom_field_1,
            custom_field_2,
            custom_field_3,
            custom_field_4,
            created_at
        FROM $table_agqa_manage_user
        WHERE account = %s
        ", $username));
    // Check if the user exists
}
?>

<style>
    .sidebar.widget_area.left.sidebar_below.sidebar_default {
        display: none;
    }

    .page_content_wrap .content_wrap, .page_content_wrap .content_container, .content_wrap, .content_container {
        width: 100% !important;
        max-width: 100%;
    }
</style>


<?php if ($date_diff > 7) { ?>

    <div class="successfull-message-ctn">
        <div class="successfull-message-ctn-content">
            <div class="successfull-message-icon">
                <img src="<?php echo URIP_URL ?>assets/image/successfull-message-icon.svg" alt="Success Icon">
            </div>
            <div class="successfull-message-text">
                <div class="error-message"><h2> The link has expired. Please request a new one. </h2></div>
            </div>
        </div>
    </div>

    <?php exit;
} ?>

<?php if (!$user_info) { ?>
    <div class="successfull-message-ctn">
        <div class="successfull-message-ctn-content">
            <div class="successfull-message-icon">
                <img src="<?php echo URIP_URL ?>assets/image/successfull-message-icon.svg" alt="Success Icon">
            </div>
            <div class="successfull-message-text">
                <div class="error-message"><h2> User not found. </h2></div>
            </div>
        </div>
    </div>
    <?php exit;
} ?>


<div class="successfull-message-ctn">
    <div class="successfull-message-ctn-content">
        <div class="successfull-message-icon">
            <img src="<?php echo URIP_URL ?>assets/image/successfull-message-icon.svg" alt="Success Icon">
        </div>
        <div class="successfull-message-text">
            <h2>Password reset successful. Please log in with your new password. Redirecting in 3 seconds. If not, click
                below.</h2>
        </div>
        <input type="hidden" class="username" value="<?php echo $username ?>">
    </div>
</div>

<script>
    jQuery(document).ready(function ($) {
        var formData =  "username=" + jQuery('.username').val();
        // var formData = $form.serialize();


        var nonce = cuim_ajax.nonce; // Nonce for security

        // Send the AJAX request
        $.ajax({
            url: cuim_ajax.ajax_url,
            type: "POST",
            data: {
                action: "verification_user_email",
                form_data: formData, // Pass the form data to the server
                nonce: nonce,
            },
            success: function (response) {
                if (response.success) {
                    // Success message
                } else {
                    // Failure message
                }
            },
            error: function (response) {
                // Error message if AJAX fails
                alert("An error occurred.");
            },
        });
    });
</script>

<!--<div class="email-ctn" style="background-color: #1D1C25;  padding: 20px; width: 70%; margin:0 auto; border-radius: 16px; color: white; font-size: 16px; font-family: 'Poppins', sans-serif;">-->
<!--    <p style="color: white">Hello [User Name]</p>-->
<!--    <h2 style="font-size: 20px; color: #00a000;'">Thank you for registering with [Platform Name]</h2>-->
<!--    <p style="color: white">To complete your account setup, please verify your email address by clicking the button below:</p>-->
<!--    <p style="color: white">-->
<!--        <a href="#" style="background-color: #7644CE; font-size: 20px; padding: 16px 24px; border-radius: 16px; color: white; margin: 5px 0; display: inline-block; text-decoration: none;">-->
<!--            Verify Link-->
<!--        </a>-->
<!--    </p>-->
<!--    <p style="color: white">This link will expire in 7 days for security reasons. If you did not create this account, please ignore this email.</p>-->
<!--    <h2 style="font-size: 24px; color: #fff"><strong>Best regards,</strong></h2>-->
<!--    <p style="color: white">The <strong>[Platform Name]</strong> Team</p>-->
<!--</div>-->