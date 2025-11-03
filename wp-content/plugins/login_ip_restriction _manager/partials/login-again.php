<div class="successfull-message-ctn">
    <div class="successfull-message-ctn-content">
        <div class="successfull-message-icon">
            <img src="<?php echo URIP_URL ?>assets/image/successfull-message-icon.svg" alt="Success Icon">
        </div>
        <div class="successfull-message-text">
            <h2>Password reset successful.Please log in with your new password.  Redirecting in 3 seconds.If not, click
                below.</h2>
            <div class="login-button center-align"><a href="<?php echo get_home_url(); ?>" class=button>Log In Again
                </a></div>
        </div>
    </div>
</div>
<script>
    jQuery(document).ready(function() {
        setTimeout(function() {
            window.location.href = '<?php echo get_home_url(); ?>'; // Redirect after alert
        }, 3000);


    });
</script>