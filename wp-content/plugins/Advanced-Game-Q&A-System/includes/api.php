<?php

function merged_api_ui_shortcode() {
    // Datebase
    $revenue_id      = isset( $_GET[ 'revenue' ] ) ? intval( $_GET[ 'revenue' ] ) : 0;
    $edit_revenue_id = isset( $_GET[ 'edit' ] ) ? intval( $_GET[ 'edit' ] ) : 0;
    $add_revenue_id  = isset( $_GET[ 'add' ] ) ? intval( $_GET[ 'add' ] ) : 0;
    $revenu_back_button = '';
    global $wpdb;
    $table_revenu   = $wpdb->prefix . 'agqa_revenu';
    $table_category = $wpdb->prefix . 'game_category';
    $table_type     = $wpdb->prefix . 'game_type';
    $table_reorder  = $wpdb->prefix . 'reorder_revenue';

    // Get current user ID
    $user_id = get_current_user_id();

    // Fetch the sort_order for the current user from the reorder_revenue table
    $sort_order = $wpdb->get_var( $wpdb->prepare(
        "SELECT sort_order FROM $table_reorder WHERE user_id = %d LIMIT 1",
        $user_id
    ) );
        // Ensure proper sorting logic (case-insensitive)
        $order_by = "CAST(r.selling_price AS DECIMAL(10,2)) " . strtoupper($sort_order) . ", r.id DESC";

        // Check if the revenue_id is 0 or a specific value
        if ($revenue_id == 0 && !empty($sort_order)) {
            // Query when no specific revenue ID is provided
            $revenu_data = $wpdb->get_results("
                SELECT
                    r.*,
                    gc.name AS game_category_name,
                    gt.name AS game_type_name
                FROM $table_revenu r
                LEFT JOIN $table_category gc ON r.game_category_id = gc.id
                LEFT JOIN $table_type gt ON r.game_type_id = gt.id
                ORDER BY $order_by
            ");
        } 

        // Check if the revenue_id is 0 or a specific value
        if ($revenue_id == 0 && empty($sort_order)) {
            // Query when no specific revenue ID is provided
            $revenu_data = $wpdb->get_results("
        SELECT
            r.*,
            gc.name AS game_category_name,
            gt.name AS game_type_name
        FROM $table_revenu r
        LEFT JOIN $table_category gc ON r.game_category_id = gc.id
        LEFT JOIN $table_type gt ON r.game_type_id = gt.id
        ORDER BY r.id DESC
    ");
        }
        if ($revenue_id != 0) {
            // Query when a specific revenue ID is provided
            $revenu_data = $wpdb->get_results("
        SELECT
            r.*,
            gc.name AS game_category_name,
            gt.name AS game_type_name
        FROM $table_revenu r
        LEFT JOIN $table_category gc ON r.game_category_id = gc.id
        LEFT JOIN $table_type gt ON r.game_type_id = gt.id
        WHERE r.id = $revenue_id
        ORDER BY r.id DESC
    ");
        $revenu_back_button = '&back=' . $revenue_id;
        }

        ob_start();
        if ($edit_revenue_id) {
            include 'edit-revenu-form.php';
        }
        if ($add_revenue_id) {
            include 'add-revenu-form.php';
        }
		if ($edit_revenue_id == 0 && $add_revenue_id == 0) {
        include 'revenu-reorder.php';
        include 'report-form.php';
		}
    ?>
        <!-- HEADING AREA - PRESERVED -->
        <?php if ($edit_revenue_id == 0 && $add_revenue_id == 0) {?>
        <div class="merged-ui-wrapper">
            <!--  Heading at Top -->
            <div class="api-heading-wrapper">
                <h1 class="text-2xl font-semibold mb-2 text-purple-400 select-none fz">Revenue</h1>
                <hr class="heading-divider" />
                <div class="filter-container api-filter">
                    <div class="filter-area">
                        <?php if ( isset($_GET['revenue'])) { ?>
                            <a href="/revenue/" class="back-button">View All</a>
                        <?php } ?>
                        <?php if (empty($_GET['revenue'])) { ?>
                            <form id="filter-form">
                                <div class="filter-select">
                                    <input type="hidden" name="filter-select-hidden" class="agqa-filter-select-hidden">
                                    <button class="filter-select-title">
                                        <span class="filter-default-text">select Role</span>
                                        <span class="filter-selected-text"></span>
                                    </button>
                                    <div class="filter-select-list">
                                        <ul>
                                            <li>Disabled</li>
                                            <li>Enabled</li>
                                        </ul>
                                    </div>
                                </div>
                                <button type="submit" class="filter-select-button"><span>Search</span></button>
                            </form>
                        <?php } ?>
                    </div>
                    <div class="api-filter-buttons">
                           <?php if (empty($_GET['revenue'])) { ?>
                            <button class="reorder-button"><img src="<?php echo AGQA_URL ?>assets/images/reorder-icon.svg"
                                alt="Reorder Icon">
                            Reorder</button>
                            <?php } ?>
                             <a href="<?php echo esc_url(home_url('/api-revenue-share-lookup/revenue/') . '?add=1'); ?>"
                            class="add-category-button"><img src="<?php echo AGQA_URL ?>assets/images/plus-icon.svg"
                                alt="Plus Icon">
                            Add Api</a>
                    </div>
                </div>
            </div>
            <!--  CARDS WRAPPER -->
            <div class="api-cards-wrapper" id="UN">
                <?php foreach ($revenu_data as $item) {
                    ?>
                <?php if ($item->id == $revenue_id) {
                    $class_none   = "";
                    $class_block  = "style='display: block'";
                    $active_class = "active";
                } else {
                    $class_none   = "style='display: none'";
                    $class_block  = "";
                    $active_class = "";
                }

                    if ($revenue_id == 0) {
                        $class_none   = "";
                        $active_class = "";
                    }
                ?>
                <div class="api-card-container-box <?php echo $active_class ?> <?php echo empty($item->state) ? 'Disabled' : 'Enabled'; ?>"
                    data-revenue-id="<?php echo $item->id; ?>" <?php echo $class_none; ?>>
                    <div class="api-card-container">
                        <div class="api-card-header api-toggle-header">
                            <div class="api-price-api-cost">
                                <div class="api-price-section">
                                    <div class="label-with-icon">
                                        <span class="label">Selling Price (%)</span>
                                        <button class="copy-detail"><img src="<?php echo AGQA_URL ?>assets/images/copy-icon.svg"
                                                alt="Copy Icon"></button>
                                    </div>
                                    <h2 class="large-text"><?php echo esc_html(number_format($item->selling_price, 0)); ?>%</h2>

                                </div>
                                <div class="api-price-section">
                                    <span class="label">API Cost (%)</span>
                                    <h2 class="large-text"><?php echo esc_html(number_format($item->api_cost, 0)); ?>%</h2>
                                </div>
                            </div>
                            <div class="api-provider-section">
                                <div class="api-provider-section-inner">
                                    <span class="api-info-label">Provider Name</span>
                                    <div class="api-info-img">
                                        <img src="<?php echo esc_url($item->image_url); ?>" alt="">
                                    </div>
                                    <span class="provider-name-text"><?php echo esc_html($item->provider_name); ?></span>
                                </div>
                            </div>
                            <div class="api-revenue-states">
                                <div class="api-revenue-states-label">
                                    <span>State</span>
                                </div>
                                <div class="api-revenue-states-buttons">
                                    <span class="<?php echo empty($item->state) ? 'disabled' : 'enabled'; ?>">
                                        <?php echo empty($item->state) ? 'Disabled' : 'Enabled'; ?>
                                    </span>
                                </div>

                            </div>
                        </div>
                        <div class="api-details" <?php echo $class_block; ?>>
                            <div class="api-info-grid">
                                <div class="api-info-grid-item">
                                    <span class="api-info-label">Game Category</span>
                                    <h2 class="api-info-value"><?php echo esc_html($item->game_category_name); ?></h2>
                                </div>
                                <div class="api-info-grid-item">
                                    <span class="api-info-label">Game Type</span>
                                    <h2 class="api-info-value"><?php echo esc_html($item->game_type_name); ?></h2>
                                </div>
                                <div class="api-info-grid-item">
                                    <span class="api-info-label">Game Info Website</span>
                                    <h2 class="api-info-value">
                                        <a href="<?php echo esc_url($item->game_info_website); ?>"
                                            target="_blank"><?php echo esc_html($item->game_info_website); ?></a>
                                    </h2>
                                </div>
                                <div class="api-info-grid-item">
                                    <span class="api-info-label">Game Demo Website</span>
                                    <h2 class="api-info-value">
                                        <a href="<?php echo esc_url($item->game_demo_website); ?>"
                                            target="_blank"><?php echo esc_html($item->game_demo_website); ?></a>
                                    </h2>
                                </div>
                                <div class="api-info-grid-item">
                                    <span class="api-info-label">API Type</span>
                                    <h2 class="api-info-value"><?php echo esc_html($item->api_type); ?></h2>
                                </div>
                                <div class="api-info-grid-item">
                                    <span class="api-info-label">Representative’s Name</span>
                                    <h2 class="api-info-value"><?php echo $item->representative_contact_info; ?>
                                    </h2>
                                </div>
                                <div class="api-info-grid-item">
                                    <span class="api-info-label">Telegram</span>
                                    <h2 class="api-info-value">
                                        <a href="https://t.me/<?php echo ltrim(esc_html($item->representative_telegram), '@'); ?>"
                                            target="_blank">
                                            <?php echo esc_html($item->representative_telegram); ?>
                                        </a>
                                    </h2>
                                </div>
                                <div class="api-info-grid-item">
                                    <span class="api-info-label">Contract</span>
                                    <h2 class="api-info-value">
                                        <a href="<?php echo esc_url($item->contract_filename); ?>" class="pdf-link"
                                            data-pdf="<?php echo esc_attr($item->contract_filename); ?>">
                                            <?php echo esc_html(basename($item->contract_filename)); ?>
                                        </a>
                                    </h2>

                                </div>
                                <!-- <?php
                                    for ($i = 1; $i <= 4; $i++) {
                                        $customLabel = isset($item->{'custom_label_' . $i}) ? $item->{'custom_label_' . $i} : '';
                                        $customField = isset($item->{'custom_field_' . $i}) ? $item->{'custom_field_' . $i} : '';

                                        if (! empty($customLabel)) { // Render only if custom label is not empty
                                    ?>
                                    <div class="api-info-grid-item">
                                        <span class="api-info-label"><?php echo esc_html($customLabel); ?></span>
                                        <h2 class="api-info-value">
                                            <a href="<?php echo esc_url($item->contract_filename); ?>"
                                                data-pdf="<?php echo esc_attr($item->contract_filename); ?>">
                                                <?php echo esc_html($customField); ?>
                                            </a>
                                        </h2>
                                    </div>
                                <?php }  }?> -->
                                <?php
                                    for ($i = 1; $i <= 4; $i++) {
                                        $customLabel = isset($item->{'custom_label_' . $i}) ? $item->{'custom_label_' . $i} : '';
                                        $customField = isset($item->{'custom_field_' . $i}) ? $item->{'custom_field_' . $i} : '';

                                        if (! empty($customLabel)) { // Render only if custom label is not empty
                                ?>
                                            <div class="api-info-grid-item">
                                                <span class="api-info-label"><?php echo esc_html($customLabel); ?></span>
                                                <h2 class="api-info-value">
                                                    <a href="<?php echo esc_url($customField); ?>" target="_blank">
                                                        <?php echo esc_html($customField); ?>
                                                    </a>
                                                </h2>
                                            </div>
                                <?php }  
                                    }
                                ?>

                            </div>
                            <!-- Modal Popup -->
                            <!--                     <div class="pdf-modal-overlay" style="display: none;">
                                <div class="pdf-modal">
                                    <span class="pdf-close"></span>
                                    <iframe src="<?php echo esc_url($item->contract_filename); ?>" frameborder="0" class="pdf-frame" ></iframe>
                                </div>
                            </div> -->
                            <div class="pdf-modal-overlay">
                                <div class="pdf-modal">

                                    <div class="popup-model-head">
                                        <h2><?php echo esc_html($item->game_type_name); ?></h2>
                                        <span class="pdf-close"></span>
                                    </div>
                                    <div id="pdfWrapper">
                                        <canvas id="pdfCanvas"></canvas>
                                    </div>
                                    <div class="popup-model-bottom">
                                        <button class="popup-model-button">Close</button>
                                    </div>
                                </div>
                            </div>
                            <div class="api-info-detail">
                                <span class="api-info-label">Notes</span>
                                <div class="api-info-text">
                                    <p><strong><?php echo esc_html($item->notes); ?></strong></p>
                                </div>
                            </div>
                        </div>
                        <!-- <div class="api-card-bottom-buttons">
                            <a href="<?php echo esc_url(home_url('/api-revenue-share-lookup/revenue/') . '?edit = ' . $item->id); ?>"
                                class="api-report-button api-card-button">
                                <img src="<?php echo AGQA_URL ?>assets/images/edit-icon.svg" alt="Edit Icon"> Edit
                            </a>
                            <button class="api-edit-button api-card-button">
                                <img src="<?php echo AGQA_URL ?>assets/images/alert-icon.svg" alt="Alert Icon"> Report
                            </button>
                        </div> -->
                        <div class="api-card-bottom-buttons">
                            <a href="<?php echo esc_url(home_url('/api-revenue-share-lookup/revenue/') . '?edit=' . $item->id) . $revenu_back_button; ?>"
                                class="api-edit-button api-card-button">
                                <img src="<?php echo AGQA_URL ?>assets/images/edit-icon.svg" alt="Edit Icon"> Edit
                            </a>
                            <button class="api-report-button api-card-button">
                                <img src="<?php echo AGQA_URL ?>assets/images/alert-icon.svg" alt="Alert Icon"> Report
                            </button>
                            <div class="api-card-approval-history">
                                <div class="approval-history-head">
                                    <span class="approval-duration">2025/07/22 14:35</span>
                                    <span class="approval-account">heather01 </span>
                                </div>
                                <div class="dropdown-lists">
                                    <div class="dropdown-list-head">
                                        <div class="dropdown-list-head-item">
                                            Change Time
                                        </div>
                                        <div class="dropdown-list-head-item">
                                            Account
                                        </div>
                                        <div class="dropdown-list-head-item">
                                            Approval Time
                                        </div>
                                    </div>
                                    <div class="dropdown-list-body">
                                        <div class="dropdown-list-row">
                                            <div class="dropdown-list-head-item">
                                                2025/07/22
                                            </div>
                                            <div class="dropdown-list-head-item">
                                                Heather01
                                            </div>
                                            <div class="dropdown-list-head-item">
                                                2025/07/25
                                            </div>
                                        </div>
                                        <div class="dropdown-list-row">
                                            <div class="dropdown-list-head-item">
                                                2025/07/22
                                            </div>
                                            <div class="dropdown-list-head-item">
                                                Heather01
                                            </div>
                                            <div class="dropdown-list-head-item">
                                                2025/07/25
                                            </div>
                                        </div>
                                        <div class="dropdown-list-row">
                                            <div class="dropdown-list-head-item">
                                                2025/07/22
                                            </div>
                                            <div class="dropdown-list-head-item">
                                                Heather01
                                            </div>
                                            <div class="dropdown-list-head-item">
                                                2025/07/25
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <?php }?>
                <div class="section-found">
                    <div class="no-found-ctn">
                        <div class="search-no-found">
                            <div class="search-no-found-icon">
                                <img src="<?php echo AGQA_URL ?>assets/images/search-forund-icon.svg" alt="Search Icon">
                            </div>
                            <div class="search-no-found-text">
                                <h2>Nothing matched your search</h2>
                            </div>
                        </div>
                    </div>
        </div>
        <?php
            return ob_get_clean();}
        }
 add_shortcode('merged_api_ui', 'merged_api_ui_shortcode' );