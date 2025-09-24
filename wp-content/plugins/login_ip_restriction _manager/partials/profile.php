<?php



// Profile completeness check function
//function cuim_is_profile_complete($user_id) {
//    $first = get_user_meta($user_id, 'first_name', true);
//    $last = get_user_meta($user_id, 'last_name', true);
//    $avatar = get_user_meta($user_id, 'cuim_profile_avatar', true);
//
//    return (!empty($first) && !empty($last) && !empty($avatar));
//}



add_action('wp_ajax_cuim_save_profile', 'cuim_save_profile');
function cuim_save_profile() {
    if (!is_user_logged_in()) wp_send_json_error("Not logged in.");

    $user_id = get_current_user_id();
    if (empty($_POST['cuim_first']) || empty($_POST['cuim_last'])) {
        wp_send_json_error("First and Last name are required.");
    }

    update_user_meta($user_id, 'first_name', sanitize_text_field($_POST['cuim_first']));
    update_user_meta($user_id, 'last_name', sanitize_text_field($_POST['cuim_last']));

    if (!empty($_FILES['cuim_avatar']['name'])) {
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/media.php');
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        $attachment_id = media_handle_upload('cuim_avatar', 0);
        if (is_wp_error($attachment_id)) {
            wp_send_json_error("Image upload failed: " . $attachment_id->get_error_message());
        }
        update_user_meta($user_id, 'cuim_profile_avatar', $attachment_id);
    }

    wp_send_json_success("Profile updated successfully.");
}


add_action('wp_body_open', 'cui_pm_add_logout_button_footer');
function cui_pm_add_logout_button_footer() {

    if (is_user_logged_in() && (
            current_user_can('administrator') ||
            current_user_can('editor') ||
            current_user_can('contributor')
        )) {
        echo '<header class="header">';
        echo '<div class="header-wrapper">';


        // Get saved viewer mode flag for current user
        $user_id = get_current_user_id();
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

        $user_id = get_current_user_id();
        $first = get_user_meta($user_id, 'first_name', true);
        $last = get_user_meta($user_id, 'last_name', true);
        $user = wp_get_current_user();
        $avatar_id = get_user_meta($user_id, 'cuim_profile_avatar', true);
        $avatar_url = $avatar_id ? wp_get_attachment_url($avatar_id) : get_avatar_url($user_id);
        $logout_url = wp_logout_url(home_url());

        echo '<div class="header-right">';



        echo '
        <div id="agqa-search-box">
            <input type="text" id="agqa-search-input" placeholder="search...">
            <div id="agqa-search-results"></div>
        </div>
        
         <div class="cuim-profile-box">
            <img src="' . esc_url($avatar_url) . '" alt="Avatar" />
            <div class="cuim-profile-dropdown-ctn">
            <div class="cuim-profile-dropdown">
                <div class="cuim-profile-dropdown-head">
                    <img src="' . esc_url($avatar_url) . '" alt="Avatar" />
                    <div>
                        <h2 class="cuim-user-name">' . esc_html($first . ' ' . $last) . '</h2>
                        <span class="cuim-profile-name">' . esc_html($user->user_email) . '</span>
                    </div>
                </div>
                <div class="cuim-profile-button-box">
                    <a href="#" class="cuim-edit-profile-button" >Edit Profile</a>
                    <a href="' . esc_url($logout_url) . '" class="cuim-logout-button">Log Out</a>
                </div>
            </div>
            </div>
        </div>
        <div class="hamburger-menu">
        <span></span>
        <span></span>
        <span></span>
        </div>
        ';



        echo '</div>';

        echo '</div>';
        echo '</header>';
    }
    ?>

<?php

    $user_id = get_current_user_id();
    $first = get_user_meta($user_id, 'first_name', true);
    $last = get_user_meta($user_id, 'last_name', true);
    $avatar_id = get_user_meta($user_id, 'cuim_profile_avatar', true);
    $avatar_url = $avatar_id ? wp_get_attachment_url($avatar_id) : get_avatar_url($user_id);
    ?>

<div class="cuim-profile-form-wrapper">
    <div class="cuim-profile-form-inner">
        <form autocomplete="off" id="cuim-profile-page-form" class="custom-form" novalidate="novalidate"
            data-inited-validation="1">
            <div style="text-align: center">
                <div class="edit-profile-image-ctn">
                    <h2>Edit Profile</h2>
                    <label for="upload-file-button" class="cuim-file-upload-label" style="display: block;">
                        <div class="edit-profile-image">
                            <img id="cuim-avatar-preview" src="<?php echo esc_url($avatar_url); ?>" alt="Avatar">
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
                    <input type="text" id="user-name" value="<?php echo $first . ' ' . $last; ?>"
                        placeholder="Please add User Name" required>

                </div>
                <div class="form-field required">
                    <label for="company-name"><span>* </span>Company Name</label>
                    <input type="text" id="company-name" placeholder="Description" required>
                </div>
                <div class="form-field required">
                    <label for="question-type"><span>* </span>User Role</label>
                    <div class="custom-select-dropdown">
                        <div class="custom-select-dropdown-title">
                            <span class="custom-dropdown-default-value">User Role</span>
                            <span class="custom-dropdown-selected-value"></span>
                        </div>
                        <div class="custom-select-dropdown-lists">
                            <ul>
                                <li data-value="Admin">Admin</li>
                                <li data-value="Manager">Manager</li>
                                <li data-value="Contributor">Contributor</li>
                                <li data-value="Viewer">Viewer</li>
                            </ul>
                        </div>
                        <input type="hidden" name="user-role" id="issue_type" required="" value="">
                    </div>
                </div>
                <div class="form-field">
                    <label for="rest-password">Reset Password</label>
                    <button class="reset-password-button">Reset password</button>
                </div>

                <div class="form-buttons edit-form-buttons d-flex">
                    <button class="cancel-button" type="button">Cancel</button>
                    <button id="save-custom-field-profile">Save</button>
                </div>
            </div>
        </form>
        <div id="cuim-profile-update-message"></div>
    </div>
    <div class="reset-password-popup">
        <div class="reset-password-popup-inner">
            <h2>Reset Password</h2>
            <div class="popup-cross-icon"></div>
            <div class="reset-password-form">
                <form action="#">
                    <div class="form-field required">
                        <label for="old-password"><span>*</span> Old Password</label>
                        <button class="toggle-password"></button>
                        <input type="password" name="old-password" id="old-password"
                            placeholder="Please Enter the Old Password">
                    </div>
                    <div class="form-field required">
                        <label for="new-password"><span>*</span> New Password</label>
                        <button class="toggle-password"></button>
                        <input type="password" name="new-password" id="new-password"
                            placeholder="Please Enter The New Password">
                    </div>
                    <div class="form-field required">
                        <label for="confirm-password"><span>*</span> Confirm Password</label>
                        <button class="toggle-password"></button>
                        <input type="password" name="confirm-password" id="confirm-password"
                            placeholder="Confirm New Password">
                    </div>
                    <div id="reset-form-buttons" class="form-buttons reset-form-buttons d-flex">
                        <button class="cancel-button" type="button">Cancel</button>
                        <button id="save-custom-field-profile" type="button">Save</button>
                        <>
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
<?php 

}
// ?>