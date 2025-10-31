<?php
$getUserRole = get_user_role_simple();
if ($getUserRole == 'viewer') {
    echo "Permission not allowed";
    return;
}
global $wpdb;
$table_approval_review = $wpdb->prefix . 'agqa_approval_review_page';
$add_username = isset($_GET['username']) ? $_GET['username'] : '';
// echo $add_username;
$get_data_approval = $wpdb->get_results("
            SELECT
                id,
                question,
                type_name,
                status,
                provider_name,
                api_status,
                created_at
            FROM $table_approval_review
             ORDER BY
            id DESC
            ");

$dataTimezone = "Asia/Karachi";
$curl = curl_init();
$ip = ipum_get_client_ip();  // The IP address you want to use
if ($ip == '::1') {
    $ip = '39.61.50.216';
}
// Create the API request URL (dynamically pass the IP)
$url = "https://get.geojs.io/v1/ip/geo/{$ip}.json";

// Set cURL options
curl_setopt_array($curl, array(
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'GET',
));

// Execute cURL request and get the response
$response = curl_exec($curl);

if (curl_errno($curl)) {
    echo 'cURL Error: ' . curl_error($curl); // Handle any errors
} else {
    // Decode the JSON response
    $data = json_decode($response, true);
    // print_r($data);

    // Check if the response contains the needed data and output it
    if (isset($data['timezone'])) {
        $dataTimezone = $data['timezone'];
    } else {
        echo "Error: Required data not found in the response.";
    }
}
// Close cURL session
curl_close($curl);
?>

<style>

</style>

<div class="approval-template">
    <div class="template-title">
        <h1>Approval Page</h1>
    </div>
</div>
<div class="filter-container">
    <div class="approval-filter filter-area">
        <form action="#" autocomplete="off">
            <div class="filter-select">
                <input type="hidden" name="filter-select-hidden" class="agqa-filter-select-hidden" value="all">
                <button class="filter-select-title">
                    <span class="filter-default-text">All</span>
                    <!-- <span class="filter-default-text">Select A Type</span> -->
                    <span class="filter-selected-text"></span>
                </button>
                <div class="filter-select-list">
                    <ul>
                        <li>All</li>
                        <li data-value="API Add">API Add</li>
                        <li data-value="API Edit">API Edit</li>
                        <li data-value="FAQ Add">FAQ Add</li>
                        <li data-value="FAQ Edit">FAQ Edit</li>
                    </ul>
                </div>
            </div>
            <div class="filter-select">
                <input type="hidden" name="filter-select-hidden" class="agqa-filter-select-hidden cuim-status" value="pending">
                <button class="filter-select-title select-states">
                    <span class="filter-default-text">Pending</span>
                    <!-- <span class="filter-default-text">Select A Status</span> -->
                    <span class="filter-selected-text"></span>
                </button>
                <div class="filter-select-list">
                    <ul>
                        <li>All</li>
                        <li data-value="Pending">Pending</li>
                        <li data-value="Approved">Approved</li>
                        <li data-value="Rejected">Rejected</li>
                    </ul>
                </div>
            </div>
            <button type="submit" class="filter-select-button" id="agqa-approval-page-filter"><span>Search</span></button>
        </form>
    </div>
</div>
<div class="custom-table-ctn">
    <div class="custom-table-ctn-inner">
        <div class="approval-table custom-table">
            <div class="custom-table-head">
                <div class="table-head-col">Type</div>
                <div class="table-head-col">Summary</div>
                <div class="table-head-col">Status</div>
                <div class="table-head-col">Submission Time</div>
                <div class="table-head-col">Action</div>
            </div>
            <div class="custom-table-body">
                <?php foreach ($get_data_approval as $value_approval) {
                    // var_dump($value_approval);
                ?>
                    <div class="custom-table-row">
                        <div class="table-body-col cuim-type-name-approval"><?php echo $value_approval->type_name; ?></div>
                        <div class="table-body-col"><?php echo $value_approval->question; ?></div>
                        <div class="table-body-col table-row-status <?php echo strtolower($value_approval->status); ?>"><span><?php echo $value_approval->status; ?></span></div>
                        <div class="table-body-col">
                            <?php
                            // Get the current system time (local server time) as a string
                            $date = new DateTime($value_approval->created_at); // Convert the string into DateTime object

                            // Convert to the 'Asia/Kolkata' time zone (Indian Standard Time)
                            $date->setTimezone(new DateTimeZone($dataTimezone));

                            // Output the time in 'Y/m/d H:i' format
                            echo $date->format('Y/m/d H:i');
                            ?>
                        </div>
                        <div class="table-body-col table-body-col-buttons">
                            <?php
                            if ($value_approval->type_name == 'FAQ Edit' || $value_approval->type_name == 'FAQ Add') {
                                $cuim_page_value = 'faq/?view=page&edit-review=' . $value_approval->id;
                            } elseif ($value_approval->api_status == 'revnue') {
                                $cuim_page_value = '/api-revenue-share-lookup/revenue/?view=page&review_revnue_api=' . $value_approval->id;
                            } elseif ($value_approval->api_status == 'sale') {
                                $cuim_page_value = '/api-revenue-share-lookup/sale/?view=page&review_sale_api=' . $value_approval->id;
                            } elseif ($value_approval->api_status == 'revnueadd') {
                                $cuim_page_value = '/api-revenue-share-lookup/revenue/?view=page&review_add_revnue_api=' . $value_approval->id;
                            } elseif ($value_approval->api_status == 'saleadd') {
                                $cuim_page_value = '/api-revenue-share-lookup/sale/?view=page&review_add_sale_api=' . $value_approval->id;
                            } else {
                                $cuim_page_value = '';
                            }

                            ?>

                            <a href="<?php echo home_url('/' . $cuim_page_value); ?>" class="approval-view-button"></a>
                            <?php if (strtolower($value_approval->status) == 'pending' && $getUserRole !== 'contributor') { ?>
                                <?php

                                if ($value_approval->type_name == 'FAQ Edit' || $value_approval->type_name == 'FAQ Add') {
                                    $cuim_page_value = 'faq/?edit-review=' . $value_approval->id;
                                } elseif ($value_approval->api_status == 'revnue') {
                                    $cuim_page_value = '/api-revenue-share-lookup/revenue/?review_revnue_api=' . $value_approval->id;
                                } elseif ($value_approval->api_status == 'sale') {
                                    $cuim_page_value = '/api-revenue-share-lookup/sale/?review_sale_api=' . $value_approval->id;
                                } elseif ($value_approval->api_status == 'revnueadd') {
                                    $cuim_page_value = '/api-revenue-share-lookup/revenue/?review_add_revnue_api=' . $value_approval->id;
                                } elseif ($value_approval->api_status == 'saleadd') {
                                    $cuim_page_value = '/api-revenue-share-lookup/sale/?review_add_sale_api=' . $value_approval->id;
                                } else {
                                    $cuim_page_value = '';
                                }

                                ?>

                                <a href="<?php echo home_url('/' . $cuim_page_value); ?>" class="approval-edit-button"></a>
                            <?php } ?>
                        </div>
                    </div>
                <?php  } ?>

            </div>
        </div>
    </div>
</div>
<div class="section-found">
    <div class="no-found-ctn">
        <div class="search-no-found">
            <div class="search-no-found-icon">
                <img src="<?php echo URIP_URL ?>assets/image/search-forund-icon.svg" alt="Search Icon">
            </div>
            <div class="search-no-found-text">
                <h2>Nothing matched your search</h2>
            </div>
        </div>
    </div>
</div>
<div class="pagination-ctn">
    <div id="pagination-demo"></div>
</div>
<script>
    jQuery(document).ready(function() {
        var itemsPerPage = 15;
        var totalItems = jQuery(".custom-table-ctn .custom-table-row").length;
        var totalPages = Math.ceil(totalItems / itemsPerPage);

        // If no rows exist, disable pagination and return
        if (totalItems === 0) {
            jQuery("#pagination-demo").hide(); // Hide pagination if no items
            return;
        }

        jQuery("#pagination-demo").twbsPagination({
            totalPages: totalPages,
            visiblePages: totalPages,
            initiateStartPageClick: false,
            onPageClick: function(event, page) {
                // Hide all rows first
                jQuery(".custom-table-ctn .custom-table-row").hide();

                // Show the rows for the current page
                jQuery('.custom-table-ctn .custom-table-row[data-page="' + page + '"]').show();

                // Calculate the active items on the current page
                var totalActiveItems = jQuery(".custom-table-row.active").length;
                var totalActivePages = Math.ceil(totalActiveItems / itemsPerPage);

                // Show/hide pagination links based on the active pages
                jQuery(".pagination-ctn ul li.page-item").nextAll().not(".next").show();

                jQuery(".pagination-ctn ul li.page-item").not(".prev, .next").each(function() {
                    var pageNumberss = parseInt(jQuery(this).text()); // Get the number of the page

                    if (pageNumberss === totalActivePages && totalActivePages !== 0) {
                        // Hide all <li> items that come after the last active page
                        jQuery(this).nextAll().not(".next").hide();

                        var prevLi = jQuery(".pagination-ctn ul li.page-item.active").next();
var $nextBtn = jQuery(".pagination-ctn ul li.next");

// Disable if: no next item, next is hidden, or next IS the .next button (last page)
if (!prevLi.length || prevLi.is(":hidden") || prevLi.hasClass("next")) {
  $nextBtn.addClass("disabled");
} else {
  $nextBtn.removeClass("disabled");
}
                    }
                });
            },
        });

        // Loop through each row and assign a page number based on its index
        jQuery(".custom-table-ctn .custom-table-row").each(function(index) {
            var page = Math.floor(index / itemsPerPage) + 1;
            jQuery(this).attr("data-page", page); // Assign page data attribute

            // Initially show or hide based on the page
            if (page === 1) {
                jQuery(this).show();
            } else {
                jQuery(this).hide();
            }
        });


        setTimeout(function() {
            var $btn = jQuery('#agqa-approval-page-filter');
            const btn = $btn.get(0);
            // Create and dispatch mouse events
            ['mousedown', 'mouseup', 'click'].forEach(eventType => {
                btn.dispatchEvent(
                    new MouseEvent(eventType, {
                        view: window,
                        bubbles: true,
                        cancelable: true
                    })
                );
            });

        }, 500);
    });
</script>