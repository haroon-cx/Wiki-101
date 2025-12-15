<div class="successfull-message-ctn">
    <div class="successfull-message-ctn-content">
        <div class="successfull-message-icon">
            <img src="<?php echo URIP_URL ?>assets/image/successfull-message-icon.svg" alt="Success Icon">
        </div>
        <!-- <div class="successfull-message-text">
            <h2>Password reset successful.Please log in with your new password.  Redirecting in 3 seconds.If not, click
                below.</h2>
            <div class="login-button center-align"><a href="<?php echo get_home_url(); ?>" class=button>Log In Again
                </a></div>
        </div> -->
        <?php
        $is_zh = strpos($_SERVER['REQUEST_URI'], '/zh') !== false;
        ?>

        <div class="successfull-message-text">
            <h2>
                <?php if ($is_zh): ?>
                    密碼重設成功。請使用新密碼登入。<br>
                    3 秒後將重新導向。如未自動跳轉，請點擊下方按鈕
                <?php else: ?>
                    Password reset successful. Please log in with your new password.<br>
                    Redirecting in 3 seconds. If not, click below.
                <?php endif; ?>
            </h2>
            <div class="login-button center-align">
                <a href="<?php echo esc_url(get_home_url()); ?>" class="button">
                    <?php echo $is_zh ? '重新登入' : 'Log In Again'; ?>
                </a>
            </div>
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