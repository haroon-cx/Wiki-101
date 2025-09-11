<?php

/**
 * Plugin Name: Advanced Game Q&A System
 * Description: Custom front-end Q&A system for users and admins with AJAX, search, and moderation.
 * Version: 1.0
 */
if (! defined('ABSPATH')) {
    exit;
}

// Plugin Setup
define('AGQA_PATH', plugin_dir_path(__FILE__));
define('AGQA_URL', plugin_dir_url(__FILE__));

// Includes
include_once AGQA_PATH . 'includes/install.php';
include_once AGQA_PATH . 'includes/shortcodes.php';
include_once AGQA_PATH . 'includes/ajax-handlers.php';
include_once AGQA_PATH . 'includes/faq/faq-ajax-handler.php';
include_once AGQA_PATH . 'includes/game-category.php';
include_once AGQA_PATH . 'includes/api.php';
include_once AGQA_PATH . 'includes/sale-api.php';
include_once AGQA_PATH . 'includes/faq/faq-shortcode.php';
include_once AGQA_PATH . 'includes/report-system/report-system-shortcode.php';

register_activation_hook(__FILE__, 'agqa_create_tables');

// Enqueue Scripts
add_action('wp_enqueue_scripts', function () {
    // Load Poppins Font from Google Fonts
    wp_enqueue_style('agqa-google-fonts', 'https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap', false);
    wp_enqueue_style('agqa-style-font-icon', '//cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css');

    // wp_enqueue_style('editor-style-css', 'https://cdn.jsdelivr.net/npm/froala-editor@latest/css/froala_editor.pkgd.min.css');

    wp_enqueue_style('agqa-style', AGQA_URL . 'assets/style.css');
    // FAQ style.css
    wp_enqueue_style('agqa-faq-style', AGQA_URL . 'assets/faq/faq-style.css');
    // FAQ faq-responsive.css
    wp_enqueue_style('agqa-faq-responsive', AGQA_URL . 'assets/faq/faq-responsive.css');
    wp_enqueue_style('agqa-responsive', AGQA_URL . 'assets/responsive.css');

    // Report System CSS file
    wp_enqueue_style('agqa-report-system-css', AGQA_URL . 'assets/report-system/report-style.css');

    

    // wp_enqueue_script('editor-faq-js', 'https://cdn.jsdelivr.net/npm/froala-editor@latest/js/froala_editor.pkgd.min.js', [], null, true);

    // Correctly load PDF.js as a script (not as a style)
    wp_enqueue_script('pdf-new', 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.min.js', [], null, true);
    wp_enqueue_script('pagination-js', 'https://cdnjs.cloudflare.com/ajax/libs/twbs-pagination/1.4.2/jquery.twbsPagination.min.js', array('jquery'), null, true);

    // F
    // Pdf Library (if you also want to include the pdf-lib library)
    wp_enqueue_script('agqa-pdf-lib', 'https://unpkg.com/pdf-lib/dist/pdf-lib.min.js', [], null, true);

    wp_enqueue_script('agqa-frontend-js', AGQA_URL . 'assets/frontend.js', ['jquery'], null, true);
    // FAQ JS file
    wp_enqueue_script('agqa-faq-js', AGQA_URL . 'assets/faq/faq.js', ['jquery'], null, true);
    wp_enqueue_script('agqa-faq-main-js', AGQA_URL . 'includes/faq/faq-main.js', ['jquery'], null, true);
    wp_enqueue_script('agqa-script', AGQA_URL . 'assets/main.js', ['jquery'], null, true);

    // Report System JS File
    wp_enqueue_script('agqa-report-system-frontend', AGQA_URL . 'assets/report-system/report-system-frontend.js', ['jquery'], null, true);
    wp_enqueue_script('agqa-report-system-js', AGQA_URL . 'includes/report-system/report-system.js', ['jquery'], null, true);

    wp_localize_script('agqa-script', 'agqa_ajax', [
        'ajax_url'        => admin_url('admin-ajax.php'),
        'nonce'           => wp_create_nonce('agqa_nonce'),
        'is_admin'        => current_user_can('administrator'),
        'user_logged_in'  => is_user_logged_in(),
        'current_user_id' => get_current_user_id(),
    ]);

    if (! is_admin()) {
        wp_enqueue_media();
    }
});


add_action('wp_head', 'hide_mobile_menu_for_non_admin');
function hide_mobile_menu_for_non_admin()
{
    if (is_user_logged_in() && (
        ! current_user_can('administrator') &&
        ! current_user_can('editor') &&
        ! current_user_can('contributor')
    )) {
?>
        <style>
            ul#menu-main-menu .menu-item {
                display: none;
            }

            ul#menu-main-menu .menu-item:nth-child(2) {
                display: block;
                font-size: 0;
            }

            ul#menu-main-menu .menu-item:nth-child(2) a {
                font-size: 0 !important;
            }

            ul#menu-main-menu .menu-item:nth-child(2) a:before {
                content: "Games";
                font-size: 20px !important;
            }

            .sidebar>.sidebar_inner>.widget ul#menu-main-menu li {
                background: var(--cuim-color-accent);
                border-radius: 8px;
            }
        </style>
    <?php
    }
    $user_id = get_current_user_id();
    if (get_user_meta($user_id, 'cuim_viewer_mode', true)) { ?>

        <style>
            ul#menu-main-menu .menu-item {
                display: none;
            }

            ul#menu-main-menu .menu-item:nth-child(2) {
                display: block;
                font-size: 0;
            }

            ul#menu-main-menu .menu-item:nth-child(2) a {
                font-size: 0 !important;
            }

            ul#menu-main-menu .menu-item:nth-child(2) a:before {
                content: "Games";
                font-size: 20px !important;
                color: #fff;
            }

            .sidebar>.sidebar_inner>.widget ul#menu-main-menu li {
                background: var(--cuim-color-accent);
                border-radius: 8px;
            }
        </style>
<?php
    }
}
function cuim_allow_contributor_uploads()
{
    $role = get_role('contributor');
    if ($role && ! $role->has_cap('upload_files')) {
        $role->add_cap('upload_files');
    }
}
add_action('admin_init', 'cuim_allow_contributor_uploads');
