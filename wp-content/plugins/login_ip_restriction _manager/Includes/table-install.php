<?php
// Main plugin file: register activation hook

// Function to create tables
function agqa_create_tables_ip_users()
{
    global $wpdb;
    $charsets = $wpdb->get_charset_collate();

    // Create the agqa_wiki_add_users table

    $wpdb->query("CREATE TABLE IF NOT EXISTS {$wpdb->prefix}agqa_wiki_add_users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            account VARCHAR(255) NOT NULL,
            new_password VARCHAR(255) NOT NULL,
            confirm_password VARCHAR(255) NOT NULL,
            state VARCHAR(255) NOT NULL,
            user_role VARCHAR(255) NOT NULL,
            company_name VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL,
            custom_label_1 VARCHAR(255),
            custom_label_2 VARCHAR(255),
            custom_label_3 VARCHAR(255),
            custom_label_4 VARCHAR(255),
            custom_field_1 VARCHAR(255),
            custom_field_2 VARCHAR(255),
            custom_field_3 VARCHAR(255),
            custom_field_4 VARCHAR(255),
            created_at DATE NOT NULL DEFAULT (CURRENT_DATE)
        ) $charsets;");
}
