<?php
// Get remembered username and email cookies
$remembered_username = isset($_COOKIE['remembered_username']) ? $_COOKIE['remembered_username'] : '';
$remembered_email = isset($_COOKIE['remembered_email']) ? $_COOKIE['remembered_email'] : '';

// Check if username is set, otherwise use the email
if (empty($remembered_username) && !empty($remembered_email)) {
    $remembered_username = $remembered_email; // Use email as username if username is not set
}
$remembered_password = isset($_COOKIE['remembered_passowrd']) ? $_COOKIE['remembered_passowrd'] : '';


?>

<style>
    /* (Start) This Below style specific for this Page */

    header.header {
        display: none;
    }

    .sidebar.widget_area.left.sidebar_below.sidebar_default {
        display: none;
    }

    .page_content_wrap .content_wrap,
    .page_content_wrap .content_container,
    .content_wrap,
    .content_container {
        width: 100% !important;
        max-width: 100%;
    }

    body.body_style_wide:not(.expand_content) .page_content_wrap .content_wrap>.content,
    body.body_style_wide:not(.expand_content) .content_wrap>.content {
        width: 100% !important;
    }

    .page_content_wrap {
        padding: 0 !important;
    }

    /* (End) This Above style specific for this Page */

    .user-login-flow-container {
        height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .user-login-flow-form {
        background: linear-gradient(0deg, rgba(0, 0, 0, 0.2), rgba(0, 0, 0, 0.2)), linear-gradient(91.48deg, #252031 31.31%, #7644CE 135.46%);
        border-radius: 16px;
        padding: 88px 24px 60px;
    }

    .user-login-flow-logo {
        line-height: .6;
        margin-bottom: 36px;
    }

    .user-login-flow-logo img {
        max-width: 373px;
    }

    .user-login-flow-logo,
    .user-login-flow-form-heading {
        text-align: center;
    }

    .user-login-flow-form-heading {
        margin-bottom: 32px;
    }

    .user-login-flow-form-field input:not([type="submit"]) {
        height: 56px;
        padding-left: 56px !important;
        background-repeat: no-repeat;
        background-position: left 20px center;
    }

    .user-login-flow-password {
        position: relative;
    }

    .user-login-flow-password .toggle-password {
        top: 17px;
        border-radius: 50%;
    }

    #user-login-flow-account {
        background-image: url('<?php echo URIP_URL; ?>assets/image/user-login-icon.svg');
        background-size: 20px;
    }

    #user-login-flow-password {
        background-image: url('<?php echo URIP_URL; ?>assets/image/passoword-lock-icon.svg');
        background-size: 16px;
    }

    #user-login-flow-email {
        background-image: url('<?php echo URIP_URL; ?>assets/image/user-login-email.svg');
        background-size: 20px;
    }

    .user-login-flow-form-field:nth-of-type(2) {
        margin-top: 20px;
    }

    .user-login-flow-form-bottom {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 40px;
        gap: 20px;
        padding: 0 16px;
    }

    .user-login-flow-forget-password {
        color: #45AFFF;
        text-decoration: underline;
        cursor: pointer;
    }

    input[type="radio"]+label:before,
    input[type="checkbox"]+label:before,
    input[type="checkbox"]+.description:before {
        border-color: #8C8C8C !important;
        width: 16px;
        height: 16px;
        top: 3px;
        background-color: #fff;
    }

    input[type="radio"]+label,
    input[type="checkbox"]+label,
    input[type="checkbox"]+.description {
        padding-left: 24px !important;
        font-size: 16px !important;
        color: white !important;
        cursor: pointer;
    }

    .user-login-flow-login-button {
        text-align: center;
    }

    #user-login-submit {
        margin-bottom: 0;
        width: max-content;
        min-width: 240px;
        font-size: 20px !important;
    }

    .user-login-flow-box-inner {
        position: relative;
        box-sizing: border-box;
        width: 585px;
    }

    .user-login-flow-box-inner::before {
        content: '';
        position: absolute;
        top: -1px;
        left: -1px;
        right: -1px;
        bottom: -1px;
        z-index: -1;
        background: linear-gradient(270deg, #624491, #EE36FF, #624491);
        background-size: 600% 600%;
        animation: gradientBorderAnimation 8s ease infinite;
        border-radius: 16px;
    }

    @keyframes gradientBorderAnimation {
        0% {
            background-position: 0% 50%;
        }

        50% {
            background-position: 100% 50%;
        }

        100% {
            background-position: 0% 50%;
        }
    }

    .forget-password-box {
        display: none;
    }
</style>

<div class="user-login-flow-container">
    <div class="user-login-flow-box">
        <div class="user-login-flow-box-inner login-box">
            <div class="user-login-flow-form">
                <div class="user-login-flow-logo">
                    <a href="#"><img src="<?php echo URIP_URL ?>assets/image/site-logo-wiki.png" alt="Site Logo"></a>
                </div>
                <div class="user-login-flow-form-heading">
                    <h2>Log In</h2>
                </div>
                <div class="user-login-flow-form-inner">
                    <form action="#" id="cuim-user-login-form" autocomplete="off" data-inited-validation="1">
                        <div class="user-login-flow-form-field user-login-flow-account">
                            <input type="text" name="user-login-flow-account" id="user-login-flow-account" placeholder="Please enter your account" value="<?php echo $remembered_username ?>">
                        </div>
                        <div class="error-message"></div>
                        <div class="user-login-flow-form-field user-login-flow-password">
                            <div class="toggle-password"></div>
                            <input type="password" name="user-login-flow-password" id="user-login-flow-password" placeholder="Please enter your password" value="<?php echo $remembered_password; ?>">
                        </div>
                        <div class="error-message cuim-user-login-error"></div>

                        <div class="user-login-flow-form-field user-login-flow-form-bottom">
                            <div class="user-login-flow-remamber-me">
                                <input type="checkbox" id="remamber-me" name="remamber-me" value="1">
                                <label for="remamber-me">Remamber Me</label><br>
                            </div>
                            <div class="user-login-flow-forget-password">
                                Forgot Password?
                            </div>
                        </div>
                        <div class="user-login-flow-form-field user-login-flow-login-button">
                            <input type="submit" id="user-login-submit" value="Log In">
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="user-login-flow-box-inner forget-password-box">
            <div class="user-login-flow-form">
                <div class="user-login-flow-logo">
                    <a href="#"><img src="<?php echo URIP_URL ?>assets/image/site-logo-wiki.png" alt="Site Logo"></a>
                </div>
                <div class="user-login-flow-form-heading">
                    <h2>Forgot Password</h2>
                </div>
                <div class="user-login-flow-form-inner">
                    <form action="#" id="cuim-user-forget-form" autocomplete="off" data-inited-validation="1">
                        <div class="user-login-flow-form-field user-login-flow-email">
                            <input type="email" class="cuim-user-login-flow-validation-254" name="user-login-flow-email" id="user-login-flow-email" placeholder="Please enter your email" required>
                        </div>
                        <div class="error-message"></div>
                        <div class="user-login-flow-form-field user-login-flow-login-button">
                            <input type="submit" id="user-login-submit" value="Log In">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    jQuery('.user-login-flow-forget-password').on('click', function(e) {
        e.preventDefault();

        // Hide login box with smooth transition
        jQuery('.login-box').fadeOut(300, function() {
            // After it's hidden, show forget password box
            jQuery('.forget-password-box').fadeIn(300);
        });
    });
</script>