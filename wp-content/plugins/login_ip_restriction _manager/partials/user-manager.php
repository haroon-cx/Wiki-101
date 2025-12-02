<?php
$getUserRole = get_user_role_simple();
$add_manage_id = isset($_GET['add']) ? intval($_GET['add']) : 0;

$edit_manage_id = isset($_GET['edit']) ? intval($_GET['edit']) : 0;

$state = isset($_GET['state']) ? sanitize_text_field($_GET['state']) : '';
global $wpdb;
$table_agqa_manage_user = $wpdb->prefix . 'agqa_wiki_add_users';

// if ($add_manage_id == 0 && $edit_manage_id == 0) {
//     $add_manage_users_data = $wpdb->get_results("
//             SELECT
//                 id,
//                user_id,
//                 account,
//                 new_password,
//                 confirm_password,
//                 state,
//                 user_role,
//                 company_name,
//                 email,
//                 delete_status,
//                 delete_user_name,
//                 custom_label_1,
//                 custom_label_2,
//                 custom_label_3,
//                 custom_label_4,
//                 custom_field_1,
//                 custom_field_2,
//                 custom_field_3,
//                 custom_field_4,
//                 created_at
//            FROM $table_agqa_manage_user
//         ORDER BY 
//             CASE 
//                 WHEN delete_status = 'table-body-disabled' THEN 1
//                 ELSE 0
//             END,
//             id DESC
//     ");
// }

if ($add_manage_id == 0 && $edit_manage_id == 0) {

    // Get current user's role
    $getUserRole = get_user_role_simple();

    // Select clause (same for both cases)
    $select_sql = "
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
            delete_status,
            delete_user_name,
            custom_label_1,
            custom_label_2,
            custom_label_3,
            custom_label_4,
            custom_field_1,
            custom_field_2,
            custom_field_3,
            custom_field_4,
            created_at
        FROM {$table_agqa_manage_user}
    ";

    // Only admins see deleted rows; others don't
    $is_admin = in_array(strtolower($getUserRole), ['admin', 'administrator'], true);

    if ($is_admin) {
        // Admin: show all rows, but push deleted ones to bottom
        $order_by = "
            ORDER BY 
                CASE 
                    WHEN delete_status = 'table-body-disabled' THEN 1
                    ELSE 0
                END,
                id DESC
        ";
        $where = ""; // no filtering
    } else {
        // Non-admin: hide deleted rows entirely
        $where = "WHERE (delete_status IS NULL OR delete_status <> 'table-body-disabled')";
        $order_by = "ORDER BY id DESC";
    }

    // Final query
    $sql = $select_sql . ' ' . $where . ' ' . $order_by;

    // Run
    $add_manage_users_data = $wpdb->get_results($sql);
}


if ($edit_manage_id !== 0) {
    $edit_user_data = $wpdb->get_results(
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
        WHERE id = %d
        ORDER BY id DESC",
            $edit_manage_id
        )
    );
}

if ($add_manage_id !== 0) {
    include 'add-manage-user-form.php';
}
// if ($add_manage_id !== 0) {
//     include 'edit-manage-user-form.php';
// }
if (strtolower($state) === 'pending') {
    include 'edit-manage-user-pending-form.php';
} elseif (in_array(strtolower($state), ['active', 'freeze', 'inactive'])) {
    // Include the Active form file if state is 'Active'
    include 'edit-manage-user-form.php';
}
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
    CURLOPT_URL => $url, // Use the dynamically generated URL
    CURLOPT_RETURNTRANSFER => true, // Return the response as a string
    CURLOPT_ENCODING => '', // Handle all encodings
    CURLOPT_MAXREDIRS => 10, // Maximum redirects
    CURLOPT_TIMEOUT => 30, // Timeout in seconds
    CURLOPT_FOLLOWLOCATION => true, // Follow redirects
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1, // HTTP version
    CURLOPT_CUSTOMREQUEST => 'GET', // Custom request method (GET)
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

ob_start(); // Start output buffering
?>
<?php if ($add_manage_id == 0 && $edit_manage_id == 0) { ?>
    <div class="manage-user-template">
        <div class="manage-user-container">
            <div id="page-content">
                <!-- Content will be dynamically updated based on pagination -->
            </div>
            <div class="template-title">
                <h1>Manage User</h1>
            </div>
            <div class="filter-container">
                <div class="filter-area">
                    <form action="#" autocomplete="off" data-inited-validation="1">
                        <div class="filter-search-field">
                            <input type="search" class="cuim-manage-user-search-validation-254" maxlength="254"
                                name="manage-user-search" id="manage-user-search"
                                placeholder="please enter account name or email">
                        </div>
                        <div class="filter-select">
                            <input type="hidden" name="filter-select-states" id="filter-select-states"
                                class="agqa-filter-select-hidden agqa-filter-select-states">
                            <button class="filter-select-title select-states">
                                <span class="filter-default-text">Select States</span>
                                <span class="filter-selected-text"></span>
                            </button>
                            <div class="filter-select-list">
                                <ul>
                                    <li data-value="Active">Active</li>
                                    <li data-value="Inactive">Inactive</li>
                                    <li data-value="Freeze">Freeze</li>
                                    <li data-value="Pending">Pending</li>
                                </ul>
                            </div>
                        </div>
                        <div class="filter-select">
                            <input type="hidden" name="filter-select-roles" id="filter-select-roles"
                                class="agqa-filter-select-hidden agqa-filter-select-roles">
                            <button class="filter-select-title select-roles">
                                <span class="filter-default-text">Select Role</span>
                                <span class="filter-selected-text"></span>
                            </button>
                            <div class="filter-select-list">
                                <ul>
                                    <li data-value="Admin">Admin</li>
                                    <li data-value="Manager">Manager</li>
                                    <li data-value="Contributor">Contributor</li>
                                    <li data-value="Viewer">Viewer</li>
                                </ul>
                            </div>
                        </div>
                        <div class="filter-select">
                            <input type="hidden" name="filter-select-companies" id="filter-select-companies"
                                class="agqa-filter-select-hidden agqa-filter-select-companies">
                            <button class="filter-select-title select-companies">
                                <span class="filter-default-text">Select Company</span>
                                <span class="filter-selected-text"></span>
                            </button>
                            <div class="filter-select-list">
                                <ul>
                                    <?php
                                    // Initialize an empty array to hold unique company names
                                    $unique_companies = [];

                                    // Loop through the user data to collect unique companies
                                    foreach ($add_manage_users_data as $key => $user_data) {
                                        // Check if the company is already in the unique list
                                        if (!in_array($user_data->company_name, $unique_companies)) {
                                            // Add company to the unique list if it's not already there
                                            $unique_companies[] = $user_data->company_name;
                                        }
                                    }

                                    // Now loop through the unique companies array to display them
                                    foreach ($unique_companies as $company) { ?>
                                        <li data-value="<?php echo $company; ?>"><?php echo $company; ?></li>
                                    <?php } ?>
                                </ul>
                            </div>
                        </div>
                        <div class="filter-select date-field">
                            <input type="text" name="daterange" class="select-date-picker" value="" id="daterange"
                                placeholder="YYYY/MM/DD - YYYY/MM/DD">
                            <span class="calendar-icon"></span>
                        </div>
                        <button type="submit" class="filter-select-button"
                            id="agqa-user-filters"><span>Search</span></button>
                    </form>
                </div>
                <div class="filter-right-area">
                    <?php if ($getUserRole !== 'viewer') { ?>
                        <div class="add-button-ctn">
                            <a href="<?php echo esc_url(home_url('/manage-user/?add=1')) ?>" class="add-button">
                                <img src="<?php echo AGQA_URL ?>assets/images/plus-icon.svg" alt="Plus Icon">Add User
                            </a>
                        </div>
                    <?php } ?>
                </div>
            </div>
            <div class="custom-table-ctn">
                <div class="custom-table-ctn-inner">
                    <div class="manage-user-table custom-table">
                        <div class="custom-table-head">
                            <div class="table-head-col">Account</div>
                            <div class="table-head-col">State</div>
                            <div class="table-head-col">Role</div>
                            <div class="table-head-col">Company Name</div>
                            <div class="table-head-col">Mail</div>
                            <div class="table-head-col">Contact Method</div>
                            <div class="table-head-col">Creation Time</div>
                            <?php if ($getUserRole !== 'viewer') { ?>
                                <div class="table-head-col">Actions</div><?php } ?>
                        </div>
                        <div class="custom-table-body">
                            <?php
                            foreach ($add_manage_users_data as $key => $user_data) {
                                $table_login_data = $wpdb->prefix . 'agqa_wiki_login_records';

                                // (Optional) extra safety: ensure table name looks sane
                                if (!preg_match('/^[A-Za-z0-9_]+$/', $table_login_data)) {
                                    wp_die('Invalid table name.');
                                }

                                $userr_ip_data = $wpdb->get_results(
                                    $wpdb->prepare(
                                        "
                                            SELECT
                                                id,
                                                user_id,
                                                account,
                                                login_ip,
                                                created_at
                                            FROM {$table_login_data}
                                            WHERE user_id = %d
                                            ORDER BY id DESC
                                            LIMIT 20
                                            ",
                                        $user_data->user_id
                                    )
                                );



                            ?>
                                <div class="custom-table-row active <?php echo $user_data->delete_status; ?>"
                                    username-data="<?php echo $user_data->account; ?>">
                                    <div class="table-body-col table-body-col-text">
                                        <?php echo $user_data->account; ?>
                                        <?php echo empty($user_data->delete_user_name) ? '' : '(deleter | ' . $user_data->delete_user_name . ')'; ?>
                                    </div>

                                    <div class="table-body-col table-row-status <?php echo strtolower($user_data->state); ?>">
                                        <span><?php echo $user_data->state; ?></span>
                                    </div>
                                    <div class="table-body-col table-row-user-role"><?php echo $user_data->user_role; ?></div>
                                    <div class="table-body-col table-row-company"><?php echo $user_data->company_name; ?></div>
                                    <div class="table-body-col table-body-col-mail table-body-col-text"><a
                                            href="mailto:<?php echo $user_data->email; ?>"><?php echo $user_data->email; ?></a>
                                    </div>
                                    <div class="table-body-col table-body-col-userId">
                                        <a href="https://t.me/<?php echo $user_data->custom_field_1; ?>"
                                            target="_blank"><?php echo $user_data->custom_field_1; ?></a>
                                    </div>
                                    <div class="table-body-col table-body-col-date">
                                        <?php echo str_replace('-', '/', $user_data->created_at); ?>
                                    </div>
                                    <?php if ($getUserRole !== 'viewer') { ?>
                                        <div class="table-body-col table-body-col-buttons">
                                            <div class="login-history-ctn">
                                                <button class="login-history-icon"></button>
                                                <div class="login-history-popup">
                                                    <div class="login-history-popup-inner">
                                                        <div class="popup-form-cross-icon"></div>
                                                        <div class="popup-head">
                                                            <h2>Login History</h2>
                                                            <span class="userName"><?php echo $user_data->account; ?></span>
                                                        </div>
                                                        <div class="user-history-records">
                                                            <div class="user-history-records-inner">
                                                                <div class="user-history-record-head">
                                                                    <span class="user-number-title">No.</span>
                                                                    <span class="user-login-time-title">Login Time</span>
                                                                    <span class="user-ip-title">Login IP Address</span>
                                                                </div>
                                                                <div class="user-history-record-lists">
                                                                    <div class="user-history-record-lists-inner">
                                                                        <?php
                                                                        $count = 0;
                                                                        if (!empty($userr_ip_data)) {
                                                                            foreach ($userr_ip_data as $ip_data) {
                                                                                $count++;

                                                                        ?>
                                                                                <div class="user-history-record-list">
                                                                                    <span class="user-number"><?php echo $count; ?></span>
                                                                                    <span class="user-login-time">
                                                                                        <?php

                                                                                        $date = new DateTime($ip_data->created_at); // Convert the string into DateTime object

                                                                                        // Convert to the 'Asia/Kolkata' time zone (Indian Standard Time)
                                                                                        $date->setTimezone(new DateTimeZone($dataTimezone));

                                                                                        // Output the time in 'Y/m/d H:i' format
                                                                                        echo $date->format('Y/m/d H:i');
                                                                                        ?>

                                                                                    </span>
                                                                                    <span class="user-ip"><?php echo $ip_data->login_ip; ?></span>
                                                                                </div>
                                                                        <?php
                                                                            }
                                                                        }
                                                                        ?>


                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="history-record-buttons d-flex">
                                                            <button class="close-button">close</button>
                                                            <a href="<?php echo home_url('/login-records/?username=' . $user_data->account);  ?>" class="button">Go to Login History</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="manage-user-edit-ctn">
                                                <?php
                                                $targetRole = $user_data->user_role;   // Jis user ko edit kar rahe hain, uska role

                                                // Default: button allowed
                                                $disable_button = false;

                                                // Contributor restrictions
                                                if ($getUserRole === 'contributor') {
                                                    if (in_array($targetRole, ['admin', 'manager', 'contributor'])) {
                                                        $disable_button = true;
                                                    }
                                                }

                                                // Manager restrictions
                                                elseif ($getUserRole === 'manager') {
                                                    if (in_array($targetRole, ['admin', 'manager'])) {
                                                        $disable_button = true;
                                                    }
                                                }

                                                // Admin can edit anyone, so no change needed

                                                // Button URL
                                                $edit_url = esc_url(
                                                    home_url('/manage-user/?edit=' . $user_data->id . '&state=' . urlencode($user_data->state))
                                                );
                                                ?>
                                                <a href="<?php echo $disable_button ? '#' : $edit_url; ?>"
                                                    class="manage-user-edit-button"
                                                    <?php echo $disable_button ? 'style="pointer-events:none;opacity:0.5;cursor:not-allowed;"' : ''; ?>>
                                                </a>
                                            </div>
                                            <?php if ($getUserRole !== 'contributor' && $getUserRole !== 'manager') { ?>
                                                <div class="delete-user-ctn">
                                                    <button class="delete-user-button"></button>
                                                    <div id="custom-faq-field-popup">
                                                        <div id="custom-faq-field-popup-inner">
                                                            <h2>Delete</h2>
                                                            <div class="popup-form-cross-icon"></div>
                                                            <div class="form-message">Are you sure you want to Delete?</div>
                                                            <div class="agqa-popup-form-buttons d-flex" id="delete-manage-users">
                                                                <button class="no-cancel" type="button">No</button>
                                                                <button id="yes-cancel" type="submit"
                                                                    value="<?php echo $user_data->account; ?>">Yes</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    <?php } ?>
                                </div>
                            <?php } ?>

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
        </div>
    </div>

    <script>
        jQuery(document).ready(function() {
            setTimeout(function() {
                // Clear the date range input field
                jQuery('input[name="daterange"]').val("");
            }, 2000); // 3000 milliseconds = 3 seconds

            // Initialize the date range picker with max 30 days selection
            jQuery('input[name="daterange"]').daterangepicker({
                opens: "right", // Position the calendar
                locale: {
                    format: "YYYY/MM/DD", // Specify the date format
                },
                maxSpan: {
                    days: 30, // Limit the date range selection to a maximum of 30 days
                },
            });

            // Handle the cancel or clear action
            jQuery('input[name="daterange"]').on(
                "cancel.daterangepicker",
                function(ev, picker) {
                    jQuery(this).val(""); // Reset the input field to empty when the user cancels or clears the date range
                }
            );
            // ==========================
            // 6. Pagination
            // ==========================

            var itemsPerPage = 15;
            var totalItems = jQuery(".manage-user-template .custom-table-row").length;
            var totalPages = Math.ceil(totalItems / itemsPerPage);

            // If no rows exist, disable pagination and return
            if (totalItems === 0) {
                jQuery(".manage-user-template #pagination-demo").hide(); // Hide pagination if no items
                return;
            }

            jQuery(".manage-user-template #pagination-demo").twbsPagination({
                totalPages: totalPages,
                visiblePages: totalPages,
                initiateStartPageClick: false,
                onPageClick: function(event, page) {
                    // Hide all rows first
                    jQuery(".manage-user-template .custom-table-row").hide();

                    // Show the rows for the current page
                    jQuery(
                        '.manage-user-template .custom-table-row[data-page="' + page + '"]'
                    ).show();

                    // Calculate the active items on the current page
                    var totalActiveItems = jQuery(".manage-user-template .custom-table-row.active").length;
                    var totalActivePages = Math.ceil(totalActiveItems / itemsPerPage);

                    // Show/hide pagination links based on the active pages
                    jQuery(".pagination-ctn ul li.page-item")
                        .nextAll()
                        .not(".next")
                        .show();

                    jQuery(".pagination-ctn ul li.page-item")
                        .not(".prev, .next")
                        .each(function() {
                            var pageNumberss = parseInt(jQuery(this).text()); // Get the number of the page

                            if (pageNumberss === totalActivePages && totalActivePages !== 0) {
                                // Hide all <li> items that come after the last active page
                                jQuery(this).nextAll().not(".next").hide();

                                // Check if the "Next" button should be disabled
                                // var prevLi = jQuery(
                                //   ".manage-user-template .pagination-ctn ul li.page-item.active"
                                // ).next();

                                // // Disable or enable the "Next" button based on the visibility of the next page
                                // if (prevLi.is(":hidden")) {
                                //   jQuery(
                                //     ".manage-user-template .pagination-ctn ul li.next"
                                //   ).addClass("disabled"); // Disable Next button
                                // } else {
                                //   jQuery(
                                //     ".manage-user-template .pagination-ctn ul li.next"
                                //   ).removeClass("disabled"); // Enable Next button
                                // }
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
                                jQuery('<li class="page-item disabled cust-ellipsis"><span class="page-link">...</span></li>')
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
                                jQuery('<li class="page-item disabled cust-ellipsis"><span class="page-link">...</span></li>')
                                    .insertBefore($lastPage);
                            }
                        }
                    }
                    // ========= NEW DOTS CODE END =========
                },
            });

            // Loop through each row and assign a page number based on its index
            jQuery(".manage-user-template .custom-table-row").each(function(index) {
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

<?php }
