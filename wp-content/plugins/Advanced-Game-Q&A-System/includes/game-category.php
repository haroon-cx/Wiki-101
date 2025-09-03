<?php
// Game Categories shortcode
function game_categories_shortcode($atts)
{
    // Define default attributes
    $atts = shortcode_atts(
        array(
            'category' => 'all', // Default tab
        ),
        $atts,
        'game_categories'
    );

    // Base path for images (dynamically generated)
    $upload_dir = wp_upload_dir();
    $image_base_url = trailingslashit($upload_dir['baseurl']) . '2025/07/';
    $image_base_path = trailingslashit($upload_dir['basedir']) . '2025/07/'; // For file_exists()

    // Define tab icons
  $tab_icons = [
        'all' => '<img src="' . AGQA_URL . 'assets/images/7777.svg" alt="All Icon" class="tab-icon" />',
        'casino' => '<img src="' . AGQA_URL . 'assets/images/casino.svg" alt="Casino Icon" class="tab-icon" />',
        'sports' => '<img src="' . AGQA_URL . 'assets/images/sports-games.svg" alt="Sports Icon" class="tab-icon" />',
        'local'  => '<img src="' . AGQA_URL . 'assets/images/local-games.svg" alt="Local Games Icon" class="tab-icon" />',
        'p2p'    => '<img src="' . AGQA_URL . 'assets/images/p2p-games.svg" alt="P2P Icon" class="tab-icon" />',
        'casual' => '<img src="' . AGQA_URL . 'assets/images/casual-games.svg" alt="Casual Games Icon" class="tab-icon" />',
    ];

    // Fetch categories and types from the database
    global $wpdb;
    
    // Get all categories
    $categories = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}game_category");
    
    // Get all game types and link them to categories
    $types = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}game_type");
    
    // Fetch providers and link them to types
   // Fetching data from agqa_revenu
$providers_revenue = $wpdb->get_results("
    SELECT r.*, c.name AS category_name, c.slug AS category_slug, t.name AS type_name, t.slug AS type_slug
    FROM {$wpdb->prefix}agqa_revenu r
    INNER JOIN {$wpdb->prefix}game_category c ON r.game_category_id = c.id
    INNER JOIN {$wpdb->prefix}game_type t ON r.game_type_id = t.id
");

// Fetching data from agqa_sales
$providers_sales = $wpdb->get_results("
    SELECT r.*, c.name AS category_name, c.slug AS category_slug, t.name AS type_name, t.slug AS type_slug
    FROM {$wpdb->prefix}agqa_sales r
    INNER JOIN {$wpdb->prefix}game_category c ON r.game_category_id = c.id
    INNER JOIN {$wpdb->prefix}game_type t ON r.game_type_id = t.id
");

// Merging the data based on the common fields (e.g., game_category_id, game_type_id)
$merged_providers = [];
foreach ($providers_revenue as $revenue) {
    $merged_provider = new stdClass();
    $merged_provider->id = $revenue->id;
    $merged_provider->provider_name = $revenue->provider_name;
    $merged_provider->state = $revenue->state;
    $merged_provider->game_type_id = $revenue->game_type_id;
    $merged_provider->game_category_id = $revenue->game_category_id;
    $merged_provider->category_name = $revenue->category_name;
    $merged_provider->category_slug = $revenue->category_slug;
    $merged_provider->type_name = $revenue->type_name;
    $merged_provider->type_slug = $revenue->type_slug;
    $merged_provider->type_contract_upload_date = $revenue->contract_upload_date;
    $merged_provider->type_selling_price = $revenue->selling_price;
    $merged_provider->type_api_cost = $revenue->api_cost;
    $merged_provider->type_image_url = $revenue->image_url;
    $merged_provider->type_home_url =  home_url( '/api-revenue-share-lookup/revenue/' ) . '?revenue=' . $revenue->id;
    $merged_provider->data_source = 'revenue'; // Indicating this is from agqa_revenu
    $merged_providers[] = $merged_provider;
}

// Merge the sales data
foreach ($providers_sales as $sale) {
    $merged_provider = new stdClass();
    $merged_provider->id = $sale->id;
    $merged_provider->provider_name = $sale->provider_name;
    $merged_provider->state = $sale->state;
    $merged_provider->game_type_id = $sale->game_type_id;
    $merged_provider->game_category_id = $sale->game_category_id;
    $merged_provider->category_name = $sale->category_name;
    $merged_provider->category_slug = $sale->category_slug;
    $merged_provider->type_name = $sale->type_name;
    $merged_provider->type_slug = $sale->type_slug;
    $merged_provider->type_contract_upload_date = $sale->contract_upload_date;
     $merged_provider->type_selling_price = $sale->min_revenue_share;
    $merged_provider->type_api_cost = $sale->max_resale_share;
    $merged_provider->type_image_url = $sale->image_url;
    $merged_provider->type_home_url =  home_url( '/api-revenue-share-lookup/sale/' ) . '?sale=' . $sale->id;
    $merged_provider->data_source = 'sale'; // Indicating this is from agqa_sales
    $merged_providers[] = $merged_provider;
}
    // Group providers by category and type
    $category_data = [];
    foreach ($merged_providers as $provider) {
        $category_slug = $provider->category_slug;
        $type_name = $provider->type_name;
        
        if (!isset($category_data[$category_slug])) {
            $category_data[$category_slug] = [];
        }
        
        if (!isset($category_data[$category_slug][$type_name])) {
            $category_data[$category_slug][$type_name] = [];
        }
        
        $category_data[$category_slug][$type_name][] = $provider;
    }
    ob_start();
    // Only render tabs & sections if user is NOT admin or contributor
    if (current_user_can('administrator') || current_user_can('contributor')) {
?>

<div class="p-6 max-w-7xl mx-auto">
    <h1 class="text-2xl font-semibold mb-2 text-purple-400 select-none fzz">Game Categories</h1>
    <hr class="heading-divider" />

    <!-- Filter Section and Add New Category -->
    <div class="filter-container">
        <div class="filter-area">
            <form action="#" autocomplete="off">
                <input type="search" name="filter-search" id="filter-search" placeholder="Search Providers...">
                <div class="filter-select">
                    <input type="hidden" name="filter-select-hidden" class="agqa-filter-select-hidden">
                    <button class="filter-select-title">
                        <span class="filter-default-text">select Role</span>
                        <span class="filter-selected-text"></span>
                    </button>
                    <div class="filter-select-list">
                        <ul>
                            <li>New Game Categories</li>
                            <li>API Not Filled in</li>
                            <li>All</li>
                        </ul>
                    </div>
                </div>
                <button type="submit" class="filter-select-button" id="agqa-game-filter"><span>Search</span></button>
            </form>
        </div>
        <div class="agqa-popup-form-ctn">
            <div class="button-bar1">
                <button class="add-category-button"><img src="<?php echo AGQA_URL ?>assets/images/plus-icon.svg" alt="">
                    Add New Categories</button>
                <div class="agqa-popup-form">
                    <div class="agqa-popup-form-inner">
                        <div class="popup-form-cross-icon"></div>
                        <form id="insert_provider_form" autocomplete="off" data-inited-validation="1">
                            <!-- Add Category Form Fields -->
                            <div class="agqa-popup-form-title">
                                <h2>Add new Categories</h2>
                            </div>
                            <div class="agqa-popup-form-field required">
                                <label for="provider-name"><span>*</span> Provider Name</label>
                                <input type="text" name="provider-name" id="provider-name" placeholder="Description"
                                    required>
                            </div>
                            <div class="agqa-popup-form-field required">
                                <label><span>*</span> Game Type</label>
                                <div class="agqa-popup-form-multi-select" id="select-role">
                                    <button type="button" class="agqa-popup-form-button">
                                        <span class="default-text">Select Category</span>
                                        <span class="selected-dropdown-item"></span>
                                    </button>
                                    <div class="agqa-popup-form-select">
                                        <ul>
                                            <li data-value="slot">Slot</li>
                                            <li data-value="table-games">Table Games</li>
                                            <li data-value="live-casino">Live Casino</li>
                                            <li data-value="sportsbook">Sportsbook</li>
                                            <li data-value="esports">eSports</li>
                                            <li data-value="virtual-sports">Virtual Sports</li>
                                            <li data-value="cockfight">Cockfight</li>
                                            <li data-value="fishing">Fishing</li>
                                            <li data-value="lottery">Lottery</li>
                                            <li data-value="number-games">Number Games</li>
                                            <li data-value="poker">Poker</li>
                                            <li data-value="p2p">P2P</li>
                                            <li data-value="crash-games">Crash Games</li>
                                            <li data-value="arcade-mini-games">Arcade / Mini Games</li>
                                            <li data-value="keno-bingo">Keno / Bingo</li>
                                        </ul>
                                    </div>
                                    <div class="selected-tags"></div>
                                    <input type="hidden" name="select-role" class="selected-values" required />
                                </div>
                            </div>
                            <div class="agqa-popup-form-field required">
                                <!-- <label for="business-model"><span>*</span> Business Model</label> -->
                                <!-- <select name="business-model" id="business-model" required>
                                    <option value="" selected>Select Business Model</option>
                                    <option value="Sale">Sale</option>
                                    <option value="Revenue">Revenue</option>
                                </select> -->
                                 <!-- New Input type -->
                                        <label for="business-model"><span>*</span> Business Model</label>
                                        <div class="custom-select-dropdown">
                                            <div class="custom-select-dropdown-title">
                                                <span class="custom-dropdown-default-value">Select Business Model</span>
                                                <span class="custom-dropdown-selected-value"></span>
                                                    </div>
                                            <div class="custom-select-dropdown-lists">
                                                <ul>
                                                    <li data-value="Sale">Sale</li>
                                                    <li data-value="Revenue">Revenue</li>
                                                </ul>
                                            </div>
                                            <input type="hidden" name="business-model" id="business-model" required>
                                        </div>
                                <!-- END -->
                            </div>
                            <div class="agqa-popup-form-field required">
                                <label for="upload-file"><span>*</span> Logo</label>
                                <div class="custom-upload-area">
                                    <div class="browse-link">Upload Logo</div>
                                    <div class="file-preview" style="display: none;"></div>
                                </div>
                                <input type="file" id="upload-logo-drag" accept="image/png" style="display: none;" />
                                <input type="hidden" name="upload-file" class="upload-file" required>
                            </div>
                            <div class="agqa-popup-form-field agqa-popup-form-buttons d-flex">
                                <button class="cancel-button">Cancel</button>
                                <input type="submit" value="Submit">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="tabs" role="tablist" aria-label="Game categories tabs">
        <!-- Tab for All Categories -->
        <button class="tab active " role="tab" id="tab-all" aria-controls="panel-all"
            aria-selected="<?php echo ($atts['category'] === 'all') ? 'true' : 'false'; ?>"
            tabindex="<?php echo ($atts['category'] === 'all') ? '0' : '-1'; ?>">
            <?php echo $tab_icons['all']; ?> All
        </button>


        <!-- Loop through other categories -->
        <?php
        foreach ($categories as $cat) {
            $active = ($atts['category'] === $cat->slug) ? 'active' : '';
            $selected = ($atts['category'] === $cat->slug) ? 'true' : 'false';
            $tabindex = ($atts['category'] === $cat->slug) ? '0' : '-1';
            echo "<button class='tab $active' role='tab' id='tab-{$cat->slug}' aria-controls='panel-{$cat->slug}' aria-selected='$selected' tabindex='$tabindex'>";
            echo $tab_icons[$cat->slug] . esc_html($cat->name);
            echo "</button>";
        }
        ?>
    </div>
    <?php 
    // Display the "All" tab content
    if ($atts['category'] === 'all') :
        // Loop through all categories and display them
        foreach ($categories as $cat) {
            // Check if there are providers for this category
            if (isset($category_data[$cat->slug]) && !empty($category_data[$cat->slug])) : ?>
            <section id="panel-<?php echo $cat->slug; ?>" class="agqa-main-section-card" role="tabpanel" tabindex="0"
                aria-labelledby="tab-<?php echo $cat->slug; ?>" <?php echo ($atts['category'] !== $cat->slug) ? '' : ''; ?>>
                <h2 class="category-heading"><?php echo ucfirst($cat->name); ?></h2>
                <?php foreach ($category_data[$cat->slug] as $type_name => $providers) { ?>
                <div class="section open" id="sec-<?php echo $type_name; ?>">
                    <div class="section-header" role="button" tabindex="0" aria-expanded="true">
                        <?php echo ucfirst($type_name); ?>
                        <button class="button agqa-status-toggle"
                            style="background: transparent !important; padding: 0; padding-left: 10px;">
                            <img src="<?php echo AGQA_URL ?>assets/images/accordian-arrow.svg" alt="Arrow">
                        </button>
                    </div>

                    <div class="section-body">
                        <h2>Common Providers</h2>
                        <div class="section-content">
                            <div class="section-provider-inner">
                            <?php foreach ($providers as $provider) { ?>
                            <?php
                                    $date_new = $provider->type_contract_upload_date;
                                    $formatted_date = date('Y-m-d', strtotime($date_new)); 

                                    // Get today's date
                                    $today_date = date('Y-m-d');

                                    // Calculate the difference in days between today's date and the provider's contract upload date
                                    $diff = strtotime($today_date) - strtotime($formatted_date);
                                    $days_diff = round($diff / (60 * 60 * 24)); 

                                    // Check if the contract upload date is older than 6 days
                                    if ($days_diff < 6) {
                                        $label_class = 'agqa-new-label'; 
                                    } else {
                                        $label_class = ''; 
                                    } 

                                        $selling_price = $provider->type_selling_price ;
                                        $api_cost = $provider->type_api_cost;


                                        // Check if either selling_price or api_cost is equal to 1
                                        if ($selling_price == 0 && $api_cost == 0) {
                                            $api_class = 'agqa-api-not-filled'; // Add class if either value is 1
                                        } else {
                                            $api_class = ''; // No class if neither value is 1
                                        }
                                        ?>
                                    <div class="provider-card" title="<?php echo esc_attr($provider->provider_name); ?> Provider">
                                        <a href="<?php echo $provider->type_home_url; ?>"
                                            class="<?php echo $label_class;?> <?php echo $api_class; ?>">
                                            <div class="provide-card-img">
                                                <img src="<?php echo $provider->type_image_url ?>"
                                                    alt="<?php echo esc_attr($provider->provider_name); ?> Provider"
                                                    onerror="this.style.filter='grayscale(1)'; this.style.opacity='0.6';" />
                                            </div>
                                        </a>
                                    </div>
                            <?php } ?>
                        </div>
                    </div>
                    </div>
                </div>
                <?php } ?>
            </section>
            <?php else : ?>
            <p>No data available for <?php echo esc_html($cat->name); ?> category.</p>
            <?php endif; 
            }
            endif;
    ?>
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
</div>
<script>
jQuery(document).ready(function() {
    // When a tab is clicked
    jQuery('.tab').on('click', function() {
        var $this = jQuery(this); // The clicked tab
        var panelId = $this.attr('aria-controls'); // Get the id of the panel to show

        // Remove active class from all tabs and hide all panels
        jQuery('.tab').removeClass('active').attr('aria-selected', 'false').attr('tabindex', '-1');
        jQuery('[role="tabpanel"]').hide().attr('hidden', true);

        // Add active class to the clicked tab and show the corresponding panel
        $this.addClass('active').attr('aria-selected', 'true').attr('tabindex', '0');
        jQuery('#' + panelId).show().removeAttr('hidden');

        // If the "All Categories" tab is clicked, show all panels
        if (panelId === 'panel-all') {
            jQuery('[role="tabpanel"]').show().removeAttr('hidden'); // Show all panels
        }
        
        // Check if the h2 element is hidden
        if (jQuery('#' + panelId + ' h2.category-heading').css('display') === 'none') {

            jQuery('.section-found').show();  // Show the no-results message
        } else {
            // alert('ss');
            jQuery('.section-found').hide();  // Hide the no-results message if h2 is visible
        }
            });

            // Allow for left/right keyboard navigation between tabs
            jQuery('.tab').on('keydown', function(e) {
                var $tabs = jQuery('.tab');
                var index = $tabs.index(this); // Get the index of the currently focused tab

                if (e.key === "ArrowRight") {
                    e.preventDefault();
                    var nextTab = $tabs.eq((index + 1) % $tabs.length);
                    nextTab.focus();
                } else if (e.key === "ArrowLeft") {
                    e.preventDefault();
                    var prevTab = $tabs.eq((index - 1 + $tabs.length) % $tabs.length);
                    prevTab.focus();
                }
            });
        });
    </script>

<?php
    } else {
        // Placeholder for non-admin/contributor users
        echo '<p class="text-center text-gray-400 py-6 select-none">Restricted access for non-admin users.</p>';
    }

    return ob_get_clean();
}

// Register the shortcode
add_shortcode('game_categories', 'game_categories_shortcode');
?>