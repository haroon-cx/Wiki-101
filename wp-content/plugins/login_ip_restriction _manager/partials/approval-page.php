<?php
$getUserRole = get_user_role_simple();
$get_user_id = get_current_user_id();
if ($getUserRole == 'viewer') {
    echo "Permission not allowed";
    return;
}
global $wpdb;
$table_approval_review = $wpdb->prefix . 'agqa_approval_review_page';
$table_agqa_manage_user = $wpdb->prefix . 'agqa_wiki_add_users';
$add_username = isset($_GET['username']) ? $_GET['username'] : '';
// echo $add_username;
// Custom state
$get_stauts_freez = $wpdb->get_var(
    $wpdb->prepare("SELECT state FROM {$table_agqa_manage_user} WHERE user_id = %d LIMIT 1", $get_user_id)
);
if (strtolower($get_stauts_freez) == 'freeze') {
    $disabledActionClass = 'table-body-disabled';
} else {
    $disabledActionClass = '';
}
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
                    <?php
                    // Get the current URL path
                    $path = $_SERVER['REQUEST_URI'];

                    // Detect if URL starts with /zh/
                    $isChinese = (strpos($path, '/zh/') === 0);
                    ?>

                    <span class="filter-default-text">
                        <?= $isChinese
                            ? '全部'   // Traditional Chinese
                            : 'All'; // English
                        ?>
                    </span>



                    <!-- <span class="filter-default-text">Select A Type</span> -->
                    <span class="filter-selected-text"></span>
                </button>
                <div class="filter-select-list">
                    <ul>
                        <?php
                        if (strpos($_SERVER['REQUEST_URI'], '/zh/') !== false) { ?>
                            <li data-value="all">全部</li>
                            <li data-value="API Add">API 新增</li>
                            <li data-value="API Edit">API 編輯</li>
                            <li data-value="FAQ Add">FAQ 新增</li>
                            <li data-value="FAQ Edit">FAQ 編輯</li>
                        <?php } else { ?>
                            <li data-value="all">All</li>
                            <li data-value="API Add">API Add</li>
                            <li data-value="API Edit">API Edit</li>
                            <li data-value="FAQ Add">FAQ Add</li>
                            <li data-value="FAQ Edit">FAQ Edit</li>
                        <?php } ?>
                    </ul>
                </div>
            </div>
            <div class="filter-select">
                <input type="hidden" name="filter-select-hidden" class="agqa-filter-select-hidden cuim-status"
                    value="pending">
                <button class="filter-select-title select-states">
                    <?php
                    // Get the current URL path
                    $path = $_SERVER['REQUEST_URI'];

                    // Detect if URL starts with /zh/
                    $isChinese = (strpos($path, '/zh/') === 0);
                    ?>

                    <span class="filter-default-text">
                        <?= $isChinese
                            ? '待審核'   // Traditional Chinese
                            : 'Pending'; // English
                        ?>
                    </span>
                    <!-- <span class="filter-default-text">Select A Status</span> -->
                    <span class="filter-selected-text"></span>
                </button>
                <div class="filter-select-list">
                    <ul>
                        <?php
                        if (strpos($_SERVER['REQUEST_URI'], '/zh/') !== false) { ?>
                            <li data-value="all">全部</li>
                            <li data-value="Pending">待審核</li>
                            <li data-value="Approved">已通過</li>
                            <li data-value="Rejected">已拒絕</li>
                        <?php } else { ?>
                            <li data-value="all">All</li>
                            <li data-value="Pending">Pending</li>
                            <li data-value="Approved">Approved</li>
                            <li data-value="Rejected">Rejected</li>
                        <?php } ?>
                    </ul>
                </div>
            </div>
            <button type="submit" class="filter-select-button"
                id="agqa-approval-page-filter"><span>Search</span></button>
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
                        <div class="table-body-col cuim-type-name-approval" data-approval-value="<?php echo $value_approval->type_name; ?>"><?php echo $value_approval->type_name; ?></div>
                        <div class="table-body-col"><?php echo $value_approval->question; ?></div>
                        <div class="table-body-col table-row-status <?php echo strtolower($value_approval->status); ?>">
                            <span
                                class="<?php echo $value_approval->status; ?>" data-approval-status-value="<?php echo $value_approval->status; ?>"><?php echo $value_approval->status; ?></span>
                        </div>
                        <div class="table-body-col">
                            <?php
                            // Get the current system time (local server time) as a string
                            $date = new DateTime($value_approval->created_at); // Convert the string into DateTime object

                            // Convert to the 'Asia/Kolkata' time zone (Indian Standard Time)
                            $date->setTimezone(new DateTimeZone($dataTimezone));
                            // $date->modify('-1 hour');
                            // Output the time in 'Y/m/d H:i' format
                            echo $date->format('Y/m/d H:i');
                            ?>
                        </div>
                        <div class="table-body-col table-body-col-buttons <?php echo $disabledActionClass; ?>">
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
        // var itemsPerPage = 15;
        // var totalItems = jQuery(".custom-table-ctn .custom-table-row").length;
        // var totalPages = Math.ceil(totalItems / itemsPerPage);
        //
        // // If no rows exist, disable pagination and return
        // if (totalItems === 0) {
        //     jQuery("#pagination-demo").hide(); // Hide pagination if no items
        //     return;
        // }
        //
        // jQuery("#pagination-demo").twbsPagination({
        //     totalPages: totalPages,
        //     visiblePages: totalPages,
        //     initiateStartPageClick: false,
        //     onPageClick: function(event, page) {
        //         // Hide all rows first
        //         jQuery(".custom-table-ctn .custom-table-row").hide();
        //
        //         // Show the rows for the current page
        //         jQuery('.custom-table-ctn .custom-table-row[data-page="' + page + '"]').show();
        //
        //         // Calculate the active items on the current page
        //         var totalActiveItems = jQuery(".custom-table-row.active").length;
        //         var totalActivePages = Math.ceil(totalActiveItems / itemsPerPage);
        //
        //         // Show/hide pagination links based on the active pages
        //         jQuery(".pagination-ctn ul li.page-item").nextAll().not(".next").show();
        //
        //         jQuery(".pagination-ctn ul li.page-item").not(".prev, .next").each(function() {
        //             var pageNumberss = parseInt(jQuery(this).text()); // Get the number of the page
        //
        //             if (pageNumberss === totalActivePages && totalActivePages !== 0) {
        //                 // Hide all <li> items that come after the last active page
        //                 jQuery(this).nextAll().not(".next").hide();
        //
        //                 var prevLi = jQuery(".pagination-ctn ul li.page-item.active").next();
        //                 var $nextBtn = jQuery(".pagination-ctn ul li.next");
        //
        //                 // Disable if: no next item, next is hidden, or next IS the .next button (last page)
        //                 if (!prevLi.length || prevLi.is(":hidden") || prevLi.hasClass("next")) {
        //                     $nextBtn.addClass("disabled");
        //                 } else {
        //                     $nextBtn.removeClass("disabled");
        //                 }
        //             }
        //         });
        //     },
        // });
        //
        // // Loop through each row and assign a page number based on its index
        // jQuery(".custom-table-ctn .custom-table-row").each(function(index) {
        //     var page = Math.floor(index / itemsPerPage) + 1;
        //     jQuery(this).attr("data-page", page); // Assign page data attribute
        //
        //     // Initially show or hide based on the page
        //     if (page === 1) {
        //         jQuery(this).show();
        //     } else {
        //         jQuery(this).hide();
        //     }
        // });

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
            visiblePages: totalPages, // ⚠️ same as you want
            initiateStartPageClick: false,
            onPageClick: function(event, page) {

                // ========= TUMHARA PURANA CODE JAISE KA TAISA =========
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
                    var pageNumberss = parseInt(jQuery(this)
                        .text()); // Get the number of the page

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
                // ========= YAHAN TAK PURANA BEHAVIOUR SAME =========


                // ========= NEW CODE: 1 hamesha show + center dots =========
                // Agar koi active page hi nahi to dots ka scene hi nahi
                if (!totalActivePages) {
                    return;
                }


                var $pager = jQuery(".pagination-ctn ul");

                // Pichle custom dots hata do (refresh ke liye)
                $pager.find("li.page-item.cust-ellipsis").remove();

                // Sirf number waale page items (prev/next/first/last ko hata ke)
                var $numItems = $pager.find("li.page-item").not(".prev, .next, .first, .last");

                // Pehle sab numeric pages ko hide kar dete hain
                $numItems.each(function() {
                    var n = parseInt(jQuery(this).text(), 10);
                    if (isNaN(n)) return;

                    // Sirf unhi numbers ke sath kaam jahan n <= totalActivePages
                    if (n > totalActivePages) {
                        jQuery(this).hide();
                    }
                });

                // Ab decide karte hain kaun se page dikhane hain
                var sideRange = 1; // current ke 1-1 neighbour

                $numItems.each(function() {
                    var n = parseInt(jQuery(this).text(), 10);
                    if (isNaN(n) || n > totalActivePages) return;

                    // hamesha show:
                    // 1, lastActivePage, current, current-1, current+1
                    if (
                        n === 1 ||
                        n === totalActivePages ||
                        n === page ||
                        n === page - sideRange ||
                        n === page + sideRange
                    ) {
                        jQuery(this).show();
                    } else {
                        jQuery(this).hide();
                    }
                });

                // 1st page <li> aur lastActivePage <li> pakdo
                var $page1 = $numItems.filter(function() {
                    return parseInt(jQuery(this).text(), 10) === 1;
                });
                var $lastPage = $numItems.filter(function() {
                    return parseInt(jQuery(this).text(), 10) === totalActivePages;
                });

                // Ensure page 1 visible
                if ($page1.length) {
                    $page1.show();
                }

                // Dots after 1 (agar 1 ke baad direct 2 na ho visible mein)
                if ($page1.length && $page1.is(":visible")) {
                    var $after1 = $page1.nextAll("li.page-item")
                        .not(".prev,.next,.first,.last")
                        .filter(":visible")
                        .first();

                    if ($after1.length) {
                        var nAfter = parseInt($after1.text(), 10);
                        if (!isNaN(nAfter) && nAfter > 2) {
                            jQuery(
                                    '<li class="page-item disabled cust-ellipsis"><span class="page-link">...</span></li>'
                                )
                                .insertAfter($page1);
                        }
                    }
                }

                // Ensure last active page visible
                if ($lastPage.length) {
                    $lastPage.show();
                }

                // Dots before lastActivePage (agar us se pehle vala visible number lastActivePage - 1 na ho)
                if ($lastPage.length && $lastPage.is(":visible")) {
                    var $beforeLast = $lastPage.prevAll("li.page-item")
                        .not(".prev,.next,.first,.last")
                        .filter(":visible")
                        .first();

                    if ($beforeLast.length) {
                        var nBefore = parseInt($beforeLast.text(), 10);
                        if (!isNaN(nBefore) && nBefore < (totalActivePages - 1)) {
                            jQuery(
                                    '<li class="page-item disabled cust-ellipsis"><span class="page-link">...</span></li>'
                                )
                                .insertBefore($lastPage);
                        }
                    }
                }
                // ========= NEW DOTS CODE END =========
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
            applyCustomDots(totalPages);
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

        function applyCustomDots(totalPages) {
            var $pager = jQuery(".pagination-ctn ul");

            // Agar 1 hi page hai to dots ka koi faida nahi
            if (!totalPages || totalPages <= 1) {
                $pager.find("li.page-item.cust-ellipsis").remove();
                return;
            }

            // Purane wale custom dots hata do
            $pager.find("li.page-item.cust-ellipsis").remove();

            // Sirf number wali li (prev / next ko hata ke)
            var $numItems = $pager.find("li.page-item").not(".prev, .next");

            // Current active page nikaalo (jo tum nth-child(3) se active kar rahe ho)
            var current = parseInt($pager.find("li.page-item.active").text(), 10);
            if (isNaN(current) || current < 1) current = 1;
            if (current > totalPages) current = totalPages;

            // Pehle sab numeric pages ko base state mein hide karo / > totalPages hide
            $numItems.each(function() {
                var n = parseInt(jQuery(this).text(), 10);
                if (isNaN(n)) return;

                if (n > totalPages) {
                    jQuery(this).hide();
                } else {
                    jQuery(this).hide(); // baad mein select karke show karenge
                }
            });

            var sideRange = 1; // current ke aas paas 1-1 page

            // 1, last, current, current-1, current+1 show karo
            $numItems.each(function() {
                var n = parseInt(jQuery(this).text(), 10);
                if (isNaN(n) || n > totalPages) return;

                if (
                    n === 1 ||
                    n === totalPages ||
                    n === current ||
                    n === current - sideRange ||
                    n === current + sideRange
                ) {
                    jQuery(this).show();
                }
            });

            // 1st page li aur last page li find karo
            var $page1 = $numItems.filter(function() {
                return parseInt(jQuery(this).text(), 10) === 1;
            });
            var $lastPage = $numItems.filter(function() {
                return parseInt(jQuery(this).text(), 10) === totalPages;
            });

            if ($page1.length) $page1.show();
            if ($lastPage.length) $lastPage.show();

            // 1 ke baad dots (agar gap ho)
            if ($page1.length && $page1.is(":visible")) {
                var $after1 = $page1.nextAll("li.page-item")
                    .not(".prev,.next")
                    .filter(":visible")
                    .first();

                if ($after1.length) {
                    var nAfter = parseInt($after1.text(), 10);
                    if (!isNaN(nAfter) && nAfter > 2) {
                        jQuery(
                            '<li class="page-item disabled cust-ellipsis"><span class="page-link">...</span></li>'
                        ).insertAfter($page1);
                    }
                }
            }

            // last se pehle dots (agar gap ho)
            if ($lastPage.length && $lastPage.is(":visible")) {
                var $beforeLast = $lastPage.prevAll("li.page-item")
                    .not(".prev,.next")
                    .filter(":visible")
                    .first();

                if ($beforeLast.length) {
                    var nBefore = parseInt($beforeLast.text(), 10);
                    if (!isNaN(nBefore) && nBefore < totalPages - 1) {
                        jQuery(
                            '<li class="page-item disabled cust-ellipsis"><span class="page-link">...</span></li>'
                        ).insertBefore($lastPage);
                    }
                }
            }
        }
    });
</script>