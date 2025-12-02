<?php
add_action('wp_body_open', 'cui_pm_add_logout_button_footer');
function cui_pm_add_logout_button_footer()
{
    if (is_user_logged_in()) {

?>
        <header class="header header_cuim_fixed">
            <div class="header-wrapper">
                <?php
                // Get saved viewer mode flag for current user
                $user_id = get_current_user_id();
                // Global WPDB object
                global $wpdb;
                $table_agqa_manage_user_get_name = $wpdb->prefix . 'agqa_wiki_add_users';
                // echo $add_username;
                // Custom state
                $get_stauts_get_freeze = $wpdb->get_var(
                    $wpdb->prepare("SELECT state FROM {$table_agqa_manage_user_get_name} WHERE user_id = %d LIMIT 1", $user_id)
                );
                if (strtolower($get_stauts_get_freeze) == 'freeze') {
                    $disabledActionClassFreeze = 'table-body-disabled-profile';
                } else {
                    $disabledActionClassFreeze = '';
                }
                $viewer_mode = get_user_meta($user_id, 'cuim_viewer_mode', true);
                $is_on = ($viewer_mode === '1'); // boolean

                if (function_exists('the_custom_logo') && has_custom_logo()) {
                    $custom_logo_id = get_theme_mod('custom_logo');
                    $logo = wp_get_attachment_image_src($custom_logo_id, 'full');

                    if ($logo) {
                        echo '<a href="' . esc_url(home_url('/')) . '" class="agqa-site-logo" rel="home">
                        <img src="' . URIP_URL . '/assets/image/site-logo.svg" alt="Site logo">
                    </a>';
                    }
                }
                $user = wp_get_current_user();
                $logout_url = wp_logout_url(home_url());
                $profile_image = get_user_meta($user_id, 'profile_image', true);
                if (empty($profile_image)) {
                    $profile_image = URIP_URL . '/assets/image/profile-user-image.jpg';
                }
                ?>
                <div class="header-right">

                    <!-- <div id="agqa-search-box">
            <input type="text" id="agqa-search-input" placeholder="search...">
            <div id="agqa-search-results"></div>
        </div> -->
                    <div class="language-switch-ctn">
                        <div class="language-button english-language" data-lang="" style="display: none;"></div>
                        <div class="language-button chinese-language" data-lang="zh"></div>
                    </div>
                    <?php
                    include URIP_PATH . 'partials/notification.php';
                    ?>

                    <div class="cuim-profile-box">
                        <img src="<?php echo $profile_image; ?>" alt="Avatar" />
                        <div class="cuim-profile-dropdown-ctn">
                            <div class="cuim-profile-dropdown">
                                <div class="cuim-profile-dropdown-head">
                                    <img src="<?php echo $profile_image; ?>" alt="Avatar" />
                                    <div>
                                        <h2 class="cuim-user-name"><?php echo $user->first_name; ?> </h2>
                                        <span class="cuim-profile-name"><?php echo $user->user_email; ?> </span>
                                    </div>
                                </div>
                                <div class="cuim-profile-button-box">
                                    <a href="#" class="cuim-edit-profile-button <?php echo $disabledActionClassFreeze; ?>">Edit Profile</a>
                                    <a href=" <?php echo $logout_url; ?>" class="cuim-logout-button">Log Out</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="hamburger-menu">
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>

                </div>
            </div>
        </header>
    <?php
    }
    ?>
    <?php

    // $user_id = get_current_user_id();
    $old_password = 'swxyz0123456789!@';  // Old password you want to match
    $hashed_old_password = wp_hash_password($old_password);  // Hash the old password for comparison

    // Get the current user’s data (including password)
    if (is_user_logged_in()) {
        $user = get_user_by('id', $user_id);
    } else {
        // Handle the case when the user is not logged in, if needed
        $user = null; // Or any other fallback logic
    }
    $user_active_class = '';
    $user_style_css = '';
    $user_exist = false;

    // Global WPDB object
    global $wpdb;

    // Table name with prefix
    $table_agqa_manage_user = $wpdb->prefix . 'agqa_wiki_add_users';
    if (is_user_logged_in()) {

        // Fetch user data from the custom table based on user_id
        $user_login_data = $wpdb->get_results(
            $wpdb->prepare(
                "
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
                custom_field_4
         FROM $table_agqa_manage_user
        WHERE user_id = %d
        ORDER BY user_id DESC
        ",
                $user_id // Make sure the user_id is passed as an integer
            )
        );
    }
    if ($user) {
        // Check if the user's current password matches the old password
        if (wp_check_password($old_password, $user->user_pass, $user_id)) {
            // If password matches, update the password
            $user_active_class = 'active';

            $user_style_css = 'style="display:none;"';
            $user_exist = true;
        }
        foreach ($user_login_data as $key => $get_user_pass) {
            // Check if the user's current password matches the old password
            if (wp_check_password($get_user_pass->new_password, $user->user_pass, $user_id)) {
                // If password matches, update the password
                $user_active_class = 'active';

                $user_style_css = 'style="display:none;"';
                $user_exist = true;
            }
        }
    }

    ?>

    <div class="cuim-profile-form-wrapper <?php echo $user_active_class; ?>">
        <div class="cuim-profile-form-inner" <?php echo $user_style_css; ?>>
            <form autocomplete="off" id="cuim-update-user-profile" class="custom-form" novalidate="novalidate"
                data-inited-validation="1">
                <?php
                foreach ($user_login_data as $key => $login_value) {
                ?>
                    <div style="text-align: center">
                        <div class="edit-profile-image-ctn">
                            <h2>Edit Profile</h2>
                            <label for="upload-file-button" class="cuim-file-upload-label" style="display: block;">
                                <div class="edit-profile-image">
                                    <img id="cuim-avatar-preview" src="<?php echo esc_url($profile_image); ?>" alt="image">
                                    <span class='camera-icon'></span>
                                </div>
                            </label>
                            <input type="file" name="cuim_avatar" accept="image/*" id="upload-file-button"
                                style="display: none;">
                            <div id="cropper-modal"
                                style="display:none;align-items:center;justify-content:center;position:fixed;inset:0;background:rgba(0,0,0,0.6);z-index:9999;">
                                <div style="background:#1D1C25; padding:24px;">
                                    <img id="cropper-image" src="" style="max-width:90vw;max-height:70vh;">
                                    <div class="cropper-buttons" style="margin-top:24px;text-align:center;">
                                        <button type="button" id="cancel-btn">Cancel</button>
                                        <button type="button" id="crop-btn">Save</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="cuim-edit-fields">
                        <div class="form-field required">
                            <label for="user-name"><span>* </span>User Name</label>
                            <input type="text" id="user-name" class="profile-username-validation-100" name="user-name"
                                value="<?php echo $user->first_name; ?>" placeholder="Please add User Name" required>

                        </div>
                        <div class="form-field required">
                            <label for="company-name"><span>* </span>Company Name</label>
                            <input type="text" id="company-name" placeholder="Description"
                                value="<?php echo ucwords($login_value->company_name); ?>" style="pointer-events: none;">
                        </div>
                        <div class=" form-field required">
                            <label for="question-type"><span>* </span>User Role</label>
                            <div class="custom-select-dropdown">
                                <div class="custom-select-dropdown-title" style="pointer-events: none;">
                                    <span
                                        class="custom-dropdown-default-value"><?php echo ucwords($login_value->user_role); ?></span>
                                    <san class="custom-dropdown-selected-value"></san>
                                </div>
                                <input type="hidden" name="user-role" id="issue_type" value="">
                            </div>
                        </div>
                        <div class="form-field">
                            <label for="rest-password">Reset Password</label>
                            <button class="reset-password-button" type="button">Reset password</button>
                        </div>

                        <div class="form-buttons edit-form-buttons d-flex">
                            <button class="cancel-button" type="button">Cancel</button>
                            <button id="save-update-user-profile" type="submit">Save</button>
                        </div>
                    </div>
                <?php } ?>
            </form>
            <div id="cuim-profile-update-message"></div>
        </div>
        <div class="reset-password-popup <?php echo $user_active_class; ?>">
            <div class="reset-password-popup-inner">
                <h2>Reset Password</h2>
                <?php if (!$user_exist) { ?>
                    <div class="popup-cross-icon"></div>
                <?php } ?>
                <div class="reset-password-form">
                    <form id="cuim-profile-reset-password" autocomplete="off" novalidate="novalidate"
                        data-inited-validation="1">
                        <div class="form-field required">
                            <label for="old-password"><span>*</span> Old Password</label>
                            <div class="toggle-password"></div>
                            <input type="password" class="cuim-manage-user-pwd-validation-20" name="old-password"
                                id="old-password" placeholder="Please Enter the Old Password" required>
                            <div id="error-message"></div>
                        </div>
                        <div class="form-field required">
                            <label for="new-password"><span>*</span> New Password</label>
                            <button class="toggle-password"></button>
                            <input type="password" class="cuim-manage-user-pwd-validation-20 cuim-profile-check-pwd"
                                name="new-password" id="new-password" placeholder="Please Enter The New Password" required>
                            <div id="error-message"></div>
                        </div>
                        <div class="form-field required">
                            <label for="confirm-password"><span>*</span> Confirm
                                Password</label>
                            <div class="toggle-password"></div>
                            <input type="password" class="cuim-manage-user-pwd-validation-20 cuim-profile-check-pwd"
                                name="confirm-password" id="confirm-password" placeholder="Confirm New Password" required>
                            <div id="error-message" class="cuim-confrim-pasword-error"></div>
                        </div>
                        <div id="reset-form-buttons" class="form-buttons reset-form-buttons d-flex">
                            <?php if (!$user_exist) { ?>
                                <button class="cancel-button" type="button">Cancel</button>
                            <?php } ?>
                            <button id="save-profile-btn" type="submit">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <style>
        .agqa-site-logo {
            display: inline-block;
            width: clamp(18.75rem, 12.5vw + 7.5rem, 22.5rem);
            height: clamp(3.438rem, 3.542vw + 0.25rem, 4.5rem);
        }

        .agqa-site-logo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        label.viewer-toggle-wrapper.checkbox_label,
        div#agqa-search-box {
            position: initial;
            border-radius: 16px;
            height: 60px;

        }

        html {
            background-image: linear-gradient(rgba(0, 0, 0, 0.67), rgba(0, 0, 0, 0.67)),
                url('<?php echo URIP_URL; ?>/assets/image/101-body-image.jpg');
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
            background-attachment: fixed;
        }

        body {
            background: transparent !important;
        }

        .top_panel {
            display: none;
        }

        .header {
            background-color: #1D1C25;
            position: relative;
        }

        .header-wrapper {
            max-width: 1860px;
            width: calc(100% - 60px);
            margin-inline: auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 19px 0;
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 16px 23px;
        }

        label.viewer-toggle-wrapper.checkbox_label {
            margin-bottom: 0;
        }
    </style>
    <!-- <script>
      jQuery(document).ready(function ($) {
        jQuery('.language-button').click(function(){
             var lang = jQuery(this).data('lang');
             jQuery(this).hide().siblings().show();
            // alert(lang);
        });
      });
    </script> -->
    <script>
        jQuery(document).ready(function($) {

            // 🔍 On page load — set which button to show
            var currentPath = window.location.pathname;
            if (currentPath.includes('/zh/')) {
                $('.chinese-language').hide();
                $('.english-language').show();
            } else {
                $('.english-language').hide();
                $('.chinese-language').show();
            }

            // 🌐 On language button click
            jQuery('.language-button').click(function() {
                var lang = jQuery(this).data('lang');
                jQuery(this).hide().siblings().show();

                // ✅ Get current path and clean it
                var pathParts = window.location.pathname.split('/').filter(Boolean);

                // 🔄 Remove existing language code if present
                if (pathParts[0] === 'zh') {
                    pathParts.shift(); // remove 'zh' from start
                }

                // 🧠 Build new path (insert lang only if it's not empty)
                var newPath = lang ? '/' + lang + '/' + pathParts.join('/') : '/' + pathParts.join('/');

                // ✅ Final URL (avoid double slashes)
                var newUrl = window.location.origin + newPath.replace(/\/+/g, '/') + '/';

                console.log("New URL:", newUrl);

                // 👉 Optional redirect after delay
                window.location.href = newUrl;
                setTimeout(function() {}, 1000);
            });
        });
    </script>


<?php
}
?>