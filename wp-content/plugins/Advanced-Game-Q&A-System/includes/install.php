<?php
// includes/install.php

// 🔧 Register main activation hook
register_activation_hook(__FILE__, 'agqa_install_all_tables');

function agqa_install_all_tables()
{
    require_once ABSPATH . 'wp-admin/includes/upgrade.php';

    agqa_create_tables();            // main post/questions/answers
    agqa_create_feedback_table();    // feedback system with file uploads
    create_agqa_api_entries_table(); // API revenue share entries
}

function agqa_create_tables()
{
    global $wpdb;
    $charset = $wpdb->get_charset_collate();

    // Categories Table
    $wpdb->query("CREATE TABLE IF NOT EXISTS {$wpdb->prefix}agqa_categories (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    ) $charset;");

    // Posts Table
    $wpdb->query("CREATE TABLE IF NOT EXISTS {$wpdb->prefix}agqa_posts (
        id INT AUTO_INCREMENT PRIMARY KEY,
        category_id INT NOT NULL,
        title VARCHAR(255),
        content TEXT,
        image_url TEXT,
        visible TINYINT(1) DEFAULT 1,
        status VARCHAR(20) DEFAULT 'pending',
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    ) $charset;");

    // Questions Table
    $wpdb->query("CREATE TABLE IF NOT EXISTS {$wpdb->prefix}agqa_questions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        post_id INT NOT NULL,
        question TEXT NOT NULL,
        visible TINYINT(1) DEFAULT 1,
        status VARCHAR(20) DEFAULT 'pending',
        created_by INT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    ) $charset;");

    // Answers Table
    $wpdb->query("CREATE TABLE IF NOT EXISTS {$wpdb->prefix}agqa_answers (
        id INT AUTO_INCREMENT PRIMARY KEY,
        question_id INT NOT NULL,
        user_id INT,
        content TEXT NOT NULL,
        display_name VARCHAR(255),
        is_featured TINYINT(1) DEFAULT 0,
        visible TINYINT(1) DEFAULT 1,
        status VARCHAR(20) DEFAULT 'pending',
        like_count INT DEFAULT 0,
        dislike_count INT DEFAULT 0,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    ) $charset;");

    // Answer Complaints Table
    $wpdb->query("CREATE TABLE IF NOT EXISTS {$wpdb->prefix}agqa_complaints (
        id INT AUTO_INCREMENT PRIMARY KEY,
        answer_id INT NOT NULL,
        user_id INT,
        reason TEXT,
        note TEXT,
        status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
        admin_note TEXT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT NULL
    ) $charset;");

    // Question Complaints Table
    $wpdb->query("CREATE TABLE IF NOT EXISTS {$wpdb->prefix}agqa_complaints_questions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        question_id INT NOT NULL,
        user_id INT,
        reason TEXT,
        note TEXT,
        status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
        admin_note TEXT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT NULL
    ) $charset;");

    // Answer Likes/Dislikes Table
    $wpdb->query("CREATE TABLE IF NOT EXISTS {$wpdb->prefix}agqa_faq_likes_dislikes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        faq_id INT NOT NULL,
        user_id INT NOT NULL,
        action_type ENUM('like', 'dislike') NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        UNIQUE(user_id, answer_id, action_type)
    ) $charset;");


    // Feedback Table
    $wpdb->query("CREATE TABLE IF NOT EXISTS {$wpdb->prefix}agqa_feedback (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT,
        answer_id INT NOT NULL,
        type ENUM('report','dislike') NOT NULL,
        reason TEXT,
        details TEXT,
        attachment_url TEXT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    ) $charset;");
    // Game Categories Table
    $wpdb->query("CREATE TABLE IF NOT EXISTS {$wpdb->prefix}agqa_game_categories (
        id INT AUTO_INCREMENT PRIMARY KEY,
        category_key VARCHAR(50) UNIQUE NOT NULL,
        category_label VARCHAR(100) NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    ) $charset;");

    // Game Sections Table
    $wpdb->query("CREATE TABLE IF NOT EXISTS {$wpdb->prefix}agqa_game_sections (
        id INT AUTO_INCREMENT PRIMARY KEY,
        category_key VARCHAR(50) NOT NULL,
        section_key VARCHAR(50) NOT NULL,
        section_label VARCHAR(100) NOT NULL,
        FOREIGN KEY (category_key) REFERENCES {$wpdb->prefix}agqa_game_categories(category_key) ON DELETE CASCADE
    ) $charset;");

    // Revenu Table
    $wpdb->query("CREATE TABLE IF NOT EXISTS {$wpdb->prefix}agqa_revenu (
            id INT AUTO_INCREMENT PRIMARY KEY,
            provider_name VARCHAR(255) NOT NULL,
            state VARCHAR(255) NOT NULL,
            game_category_id BIGINT NOT NULL,
            game_type_id BIGINT NOT NULL,
            selling_price DECIMAL(5,2) NOT NULL,
            api_cost DECIMAL(5,2) NOT NULL,
            api_type VARCHAR(255) NOT NULL,
            game_info_website VARCHAR(255) NOT NULL,
            game_demo_website VARCHAR(255) NOT NULL,
            representative_contact_info VARCHAR(255) NOT NULL,
            representative_telegram VARCHAR(255) NOT NULL,
            custom_label_1 VARCHAR(255),
            custom_label_2 VARCHAR(255),
            custom_label_3 VARCHAR(255),
            custom_label_4 VARCHAR(255),
            custom_field_1 VARCHAR(255),
            custom_field_2 VARCHAR(255),
            custom_field_3 VARCHAR(255),
            custom_field_4 VARCHAR(255),
            notes TEXT,
            image_url VARCHAR(255),
            contract_filename VARCHAR(255) NOT NULL,
            contract_upload_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            url_update_date DATE NOT NULL
        ) $charset;");

    // Sales Table
    $wpdb->query("CREATE TABLE IF NOT EXISTS {$wpdb->prefix}agqa_sales (
            id INT AUTO_INCREMENT PRIMARY KEY,
            provider_name VARCHAR(255) NOT NULL,
            state VARCHAR(255) NOT NULL,
            game_type_id VARCHAR(255) NOT NULL,
            game_category_id VARCHAR(255) NOT NULL,
            min_revenue_share DECIMAL(5, 2) NOT NULL,
            max_resale_share DECIMAL(5, 2) NOT NULL,
            game_info_website VARCHAR(255) NOT NULL,
            game_demo_website VARCHAR(255) NOT NULL,
            representative_name VARCHAR(255) NOT NULL,
            representative_telegram VARCHAR(255) NOT NULL,
            max_resale_percentage DECIMAL(5, 2) NOT NULL,
            api_type VARCHAR(255) NOT NULL,
            custom_label_1 VARCHAR(255),  -- Additional Custom label
            custom_label_2 VARCHAR(255),  -- Additional Custom label
            custom_label_3 VARCHAR(255),  -- Additional Custom label
            custom_label_4 VARCHAR(255),  -- Additional Custom label
            custom_field_1 VARCHAR(255),  -- Additional Custom Field
            custom_field_2 VARCHAR(255),  -- Additional Custom Field
            custom_field_3 VARCHAR(255),  -- Additional Custom Field
            custom_field_4 VARCHAR(255),  -- Additional Custom Field
            notes TEXT,
            image_url VARCHAR(255),
            contract_filename VARCHAR(255) NOT NULL,
            contract_upload_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            url_update_date DATE NOT NULL
        ) $charset;");
    // $charset = $wpdb->get_charset_collate();

    // Create wp_game_category
    $wpdb->query("CREATE TABLE IF NOT EXISTS {$wpdb->prefix}game_category (
        id BIGINT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL UNIQUE,
        slug VARCHAR(255) NOT NULL UNIQUE,
        description TEXT
    ) $charset;");

    // Create wp_game_type
    $wpdb->query("CREATE TABLE IF NOT EXISTS {$wpdb->prefix}game_type (
        id BIGINT AUTO_INCREMENT PRIMARY KEY,
        game_category_id BIGINT NOT NULL,
        name VARCHAR(255) NOT NULL,
        slug VARCHAR(255) NOT NULL,
        description TEXT,
        UNIQUE (slug),
        FOREIGN KEY (game_category_id) REFERENCES {$wpdb->prefix}game_category(id) ON DELETE CASCADE
    ) $charset;");

    // Revenue Reorder Table

    $wpdb->query("CREATE TABLE IF NOT EXISTS {$wpdb->prefix}reorder_revenue (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT NOT NULL,
    sort_order VARCHAR(10) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY user_sort_unique (user_id)
    ) $charset;");

     // FAQ Table
    $wpdb->query("CREATE TABLE IF NOT EXISTS {$wpdb->prefix}agqa_faq (
        id INT AUTO_INCREMENT PRIMARY KEY,
        question TEXT NOT NULL,
        answer TEXT NOT NULL,
        verified_answer TEXT,
        faq_category VARCHAR(255) NOT NULL,
        user_id BIGINT(20) NOT NULL,
        time TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) $charset;");

    // FAQ Table HIstory

    $wpdb->query("CREATE TABLE IF NOT EXISTS {$wpdb->prefix}agqa_faq_history (
        id INT AUTO_INCREMENT PRIMARY KEY,
        faq_id INT NOT NULL,
        question TEXT NOT NULL,
        answer TEXT NOT NULL,
        verified_answer TEXT,
        faq_category VARCHAR(255) NOT NULL,
        user_id BIGINT(20) NOT NULL,
        time TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) $charset;");


    // FAQ Table Review

    $wpdb->query("CREATE TABLE IF NOT EXISTS {$wpdb->prefix}agqa_faq_review (
        id INT AUTO_INCREMENT PRIMARY KEY,
        faq_id INT NOT NULL,
        question TEXT NOT NULL,
        answer TEXT NOT NULL,
        verified_answer TEXT,
        faq_category VARCHAR(255) NOT NULL,
        user_id BIGINT(20) NOT NULL,
        status VARCHAR(20) DEFAULT 'pending',
        time TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) $charset;");


     // Sales Reorder Table
    $wpdb->query("CREATE TABLE IF NOT EXISTS {$wpdb->prefix}reorder_sales (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id BIGINT NOT NULL,
        sort_order VARCHAR(10) DEFAULT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        UNIQUE KEY user_sort_unique (user_id)
        ) $charset;");

    // Define categories and sub-categories
    $categories = [
        'casino' => ['name' => 'Casino Games', 'sub' => ['Slot', 'Table Games', 'Live Casino']],
        'sports' => ['name' => 'Sports Games', 'sub' => ['Sportsbook', 'eSports', 'Virtual Sports']],
        'local'  => ['name' => 'Local Games', 'sub' => ['Cockfight', 'Fishing', 'Lottery', 'Number Games']],
        'p2p'    => ['name' => 'P2P Games', 'sub' => ['Poker', 'P2P']],
        'casual' => ['name' => 'Casual Games', 'sub' => ['Crash Games', 'Arcade / Mini Games', 'Keno / Bingo']],
    ];

    foreach ($categories as $slug => $data) {
        // Insert main category
        $wpdb->insert("{$wpdb->prefix}game_category", [
            'name'        => $data['name'],
            'slug'        => sanitize_title($slug),
            'description' => '',
        ]);

        $category_id = $wpdb->insert_id;

        // Insert sub-categories
        foreach ($data['sub'] as $sub_type) {
            $wpdb->insert("{$wpdb->prefix}game_type", [
                'game_category_id' => $category_id,
                'name'             => $sub_type,
                'slug'             => sanitize_title($sub_type),
                'description'      => '',
            ]);
        }
    }

}

