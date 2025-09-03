<?php
function hello_elementor_child_enqueue_styles() {
    // Load parent theme stylesheet
    wp_enqueue_style(
        'hello-elementor-parent-style',
        get_template_directory_uri() . '/style.css'
    );

    // Optionally enqueue additional parent theme CSS file, like theme.css
    wp_enqueue_style(
        'hello-theme-css',
        get_template_directory_uri() . '/assets/css/theme.css', // correct path adjust kar lein
        array('hello-elementor-parent-style'),
        '1.0.0'
    );

    // Load child theme custom stylesheet if you create one
    wp_enqueue_style(
        'hello-elementor-child-style',
        get_stylesheet_directory_uri() . '/style.css',
        array('hello-theme-css'),  // load after parent styles
        '1.0.0'
    );
}
add_action('wp_enqueue_scripts', 'hello_elementor_child_enqueue_styles');

function hide_reg_btn_for_logged_in_users() {
    if ( is_user_logged_in() ) {
        echo '<style>.reg-btn { display: none !important; }</style>';
    }
}
add_action('wp_head', 'hide_reg_btn_for_logged_in_users');

function current_year_shortcode() {
    return date('Y');
}
add_shortcode('current_year', 'current_year_shortcode');