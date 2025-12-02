<?php
// $getUserRole = get_user_role_simple();
// if ($getUserRole == 'viewer') {
//     echo "Permission not allowed";
//     return;
// }
// global $wpdb;
// $table_agqa_ip_list = $wpdb->prefix . 'agqa_wiki_add_ip';

// $get_ip_list = $wpdb->get_results("
//             SELECT
//                 id,
//                 user_id,
//                 account,
//                 ipv4,
//                 ipv6,
//                 delete_status,
//                 delete_user_name,
//                 delete_user_id,
//                 created_at
//             FROM $table_agqa_ip_list
//              ORDER BY 
//             CASE 
//                 WHEN delete_status = 'table-body-disabled' THEN 1
//                 ELSE 0
//             END,
//             id DESC
//             ");
// $getUserRole = get_user_role_simple();
// if ($getUserRole === 'viewer') {
//     echo "Permission not allowed";
//     return;
// }

// global $wpdb;

// // Tables
// $table_agqa_ip_list = $wpdb->prefix . 'agqa_wiki_add_ip';
// $table_agqa_users   = $wpdb->prefix . 'agqa_wiki_add_users';

// // Role hierarchy
// $role_level = [
//     'viewer'      => 1,
//     'contributor' => 2,
//     'manager'     => 3,
//     'admin'       => 4,
// ];

// // Helper to get roles up to ceiling
// $roles_up_to = function ($maxRole) use ($role_level) {
//     $maxRole = strtolower($maxRole);
//     if (!isset($role_level[$maxRole])) $maxRole = 'viewer';
//     $max = $role_level[$maxRole];
//     $out = [];
//     foreach ($role_level as $r => $lvl) {
//         if ($lvl <= $max) $out[] = $r;
//     }
//     return $out;
// };

// $is_admin = (strtolower($getUserRole) === 'admin');

// // Current user's allowed roles (admin gets all, others get up to their level)
// $allowed_roles = $roles_up_to($getUserRole);

// // Build placeholders for role IN (...)
// $role_placeholders = implode(',', array_fill(0, count($allowed_roles), '%s'));
// $params = array_map('strtolower', $allowed_roles);

// // Base SQL with JOIN to fetch account_role
// $sql = "
//     SELECT
//         i.id,
//         i.user_id,
//         i.account,
//         i.ipv4,
//         i.ipv6,
//         i.delete_status,
//         i.delete_user_name,
//         i.delete_user_id,
//         i.created_at,
//         u.user_role AS account_role
//     FROM {$table_agqa_ip_list} AS i
//     INNER JOIN {$table_agqa_users} AS u
//         ON u.account = i.account
//     WHERE LOWER(u.user_role) IN ($role_placeholders)
// ";

// // Non-admins should NOT see deleted rows
// if (!$is_admin) {
//     $sql .= " AND (i.delete_status IS NULL OR i.delete_status <> %s)";
//     $params[] = 'table-body-disabled';
// }

// $sql .= "
//     ORDER BY 
//         CASE WHEN i.delete_status = 'table-body-disabled' THEN 1 ELSE 0 END,
//         i.id DESC
// ";

// // Run
// // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
// $get_ip_list = $wpdb->get_results($wpdb->prepare($sql, $params));

$getUserRole = get_user_role_simple();
if ($getUserRole === 'viewer') {
    echo "Permission not allowed";
    return;
}

global $wpdb;

// Tables
$table_agqa_ip_list = $wpdb->prefix . 'agqa_wiki_add_ip';
$table_agqa_users   = $wpdb->prefix . 'agqa_wiki_add_users';

// Role hierarchy
$role_level = [
    'viewer'      => 1,
    'contributor' => 2,
    'manager'     => 3,
    'admin'       => 4,
];

// Helper to get roles up to ceiling
$roles_up_to = function ($maxRole) use ($role_level) {
    $maxRole = strtolower($maxRole);
    if (!isset($role_level[$maxRole])) $maxRole = 'viewer';
    $max = $role_level[$maxRole];
    $out = [];
    foreach ($role_level as $r => $lvl) {
        if ($lvl <= $max) $out[] = $r;
    }
    return $out;
};

$is_admin = (strtolower($getUserRole) === 'admin');

// Current user's allowed roles
$allowed_roles = $roles_up_to($getUserRole);
$role_placeholders = implode(',', array_fill(0, count($allowed_roles), '%s'));
$params = array_map('strtolower', $allowed_roles);

// ✅ MAIN SQL – only latest record per *unique (lowercase, trimmed)* account
$sql = "
    SELECT
        i.id,
        i.user_id,
        i.account,
        i.ipv4,
        i.ipv6,
        i.delete_status,
        i.delete_user_name,
        i.delete_user_id,
        i.created_at,
        u.user_role AS account_role
    FROM {$table_agqa_ip_list} AS i
    INNER JOIN {$table_agqa_users} AS u
        ON TRIM(LOWER(u.account)) = TRIM(LOWER(i.account))
    INNER JOIN (
        SELECT 
            TRIM(LOWER(account)) AS account_key,
            MAX(id) AS max_id
        FROM {$table_agqa_ip_list}
        GROUP BY TRIM(LOWER(account))
    ) AS latest
        ON TRIM(LOWER(i.account)) = latest.account_key
        AND i.id = latest.max_id
    WHERE LOWER(u.user_role) IN ($role_placeholders)
";

// Non-admins don’t see deleted rows
if (!$is_admin) {
    $sql .= " AND (i.delete_status IS NULL OR i.delete_status <> %s)";
    $params[] = 'table-body-disabled';
}

$sql .= "
    ORDER BY 
        CASE WHEN i.delete_status = 'table-body-disabled' THEN 1 ELSE 0 END,
        i.id DESC
";

// Run query
// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
$get_ip_list = $wpdb->get_results($wpdb->prepare($sql, $params));

// ✅ Display
$displayed_accounts = [];
// if (!empty($get_ip_list)) {
//     foreach ($get_ip_list as $row) {
//         // Check if account already displayed (avoid duplicates)
//         if (!in_array($row->account, $displayed_accounts)) {
//             $displayed_accounts[] = $row->account;
//             echo "<p>{$row->account} - {$row->ipv4} ({$row->account_role})</p>";
//         }
//     }
// } else {
//     echo "<p>No records found.</p>";
// }

include 'add-ip-form.php';

?>
<div class="manage-ip-template">
    <div class="manage-ip-container">
        <div id="page-content">
            <!-- Content will be dynamically updated based on pagination -->
        </div>
        <div class="template-title">
            <h1>Manage IP’s Whitelist</h1>
        </div>
        <div class="filter-container">
            <div class="filter-area">
                <form action="#" autocomplete="off" data-inited-validation="1">
                    <div class="filter-search-field">
                        <input type="search" class="cuim-manage-account-search-validation-254" maxlength="254"
                            name="manage-ip-account-search" id="manage-ip-account-search"
                            placeholder="Please Enter Account">
                    </div>
                    <div class="filter-select cuim-filter-select-list-li">
                        <input type="hidden" name="filter-select-ip" id="filter-select-ip"
                            class="agqa-filter-select-hidden agqa-filter-select-ip">
                        <button class="filter-select-title select-ip">
                            <span class="filter-default-text">Select An Item</span>
                            <span class="filter-selected-text"></span>
                        </button>
                        <div class="filter-select-list">
                            <ul>
                                <li data-value="cuim-ipv4-selected-li">IPv4</li>
                                <li data-value="cuim-ipv6-selected-li">IPv6</li>
                            </ul>
                        </div>
                    </div>
                    <div class="filter-search-field cuim-ipv4-selected-li cuim-ipv-selected" style="display:none;">
                        <input type="search" class="cuim-manage-ipv4-search-validation-254" maxlength="254"
                            name="manage-ip-ipv4-search" id="manage-ip-ipv4-search"
                            placeholder="Enter IPv4 (E.G. 10.0.0.5)">
                    </div>
                    <div class="filter-search-field cuim-ipv6-selected-li cuim-ipv-selected" style="display: none">
                        <input type="search" class="cuim-manage-ipv6-search-validation-254" maxlength="254"
                            name="manage-ip-ipv6-search" id="manage-ip-ipv6-search"
                            placeholder="Enter IPv6 (E.G. 2400:3200::1)">
                    </div>
                    <button type="submit" class="filter-select-button"
                        id="cuim-ip-serch-filters"><span>Search</span></button>
                </form>
            </div>
            <div class="filter-right-area">
                <div class="add-button-ctn">
                    <a href="#" class="add-button">
                        <img src="<?php echo AGQA_URL ?>assets/images/plus-icon.svg" alt="Plus Icon">Add New IP
                    </a>
                </div>
            </div>
        </div>
        <div class="custom-table-ctn">
            <div class="custom-table-ctn-inner">
                <div class="manage-ip-table custom-table">
                    <div class="custom-table-head">
                        <div class="table-head-col">Account</div>
                        <div class="table-head-col">IPv4</div>
                        <div class="table-head-col">IPv6</div>
                        <div class="table-head-col">Actions</div>
                    </div>
                    <div class="custom-table-body">
                        <?php foreach ($get_ip_list as $ip_value) {

                            if (!in_array($ip_value->account, $displayed_accounts)) {

                                $displayed_accounts[] = $ip_value->account;
                        ?>
                                <div class="custom-table-row active <?php echo $ip_value->delete_status; ?>" id="ip-row-<?php echo $ip_value->id; ?>">
                                    <div class="table-body-col cuim-ip-user-account">
                                        <?php echo $ip_value->account; ?>
                                        <?php echo empty($ip_value->delete_user_name) ? '' : '(deleter | ' . $ip_value->delete_user_name . ')'; ?>
                                    </div>
                                    <div class="table-body-col cuim-ip-user-ipv4">
                                        <?php echo $ip_value->ipv4 ?>
                                    </div>
                                    <div class="table-body-col cuim-ip-user-ipv6">
                                        <?php echo $ip_value->ipv6 ?>
                                    </div>
                                    <div class="table-body-col manage-ip-actions">
                                        <div class="edit-ip-ctn">
                                            <button class="manage-ip-edit-button"></button>
                                            <div class="edit-manage-ip-form">
                                                <div class="edit-manage-ip-form-inner">
                                                    <div class="popup-form-cross-icon manage-ip-cross-icon"></div>
                                                    <div class="popup-form-title">
                                                        <h2>Edit IP</h2>
                                                    </div>
                                                    <form action="#" class="edit-ip-from-list">
                                                        <div class="form-field full-width required">
                                                            <label for="account-name"><span>*</span> Account</label>
                                                            <input type="hidden" name="ip-edit-id" value="<?php echo $ip_value->id; ?>">
                                                            <input type="hidden" class="ip-edit-ip4-check" value="<?php echo $ip_value->ipv4; ?>">
                                                            <input type="hidden" class="ip-edit-ip6-check" value="<?php echo $ip_value->ipv6; ?>">
                                                            <input type="text" name="account-name" placeholder="Description" value="<?php echo $ip_value->account ?>" required style="pointer-events: none;1">
                                                        </div>
                                                        <div class="form-field full-width">
                                                            <label>IPv4</label>
                                                            <input type="text" name="ip-ipv4" class="manage-ip-ipv4-field" placeholder="Enter IPv4 (e.g. 10.0.0.5)" value="<?php echo $ip_value->ipv4 ?>">
                                                            <div class="error-message ip-error ipv4-error"></div>
                                                        </div>
                                                        <div class="form-field full-width">
                                                            <label>IPv6</label>
                                                            <input type="text" name="ip-ipv6" class="manage-ip-ipv6-field" placeholder="Enter IPv6 (E.G. 2400:3200::1)" value="<?php echo $ip_value->ipv6 ?>">
                                                            <div class="error-message ip-error ipv6-error"></div>
                                                        </div>
                                                        <div class="form-buttons manage-ip-form-buttons d-flex">
                                                            <button class="cuim-ip-cancel-btn" type="button">Cancel</button>
                                                            <div class="cancel-form-confirmation" style="">
                                                                <div class="cancel-form-confirmation-box">
                                                                    <h2>Cancel</h2>
                                                                    <div class="popup-form-cross-icon"></div>
                                                                    <div class="form-message">Are you sure you want to cancel?</div>
                                                                    <div class="form-buttons agqa-popup-form-buttons d-flex">
                                                                        <button class="no-form-cancel" type="button">No</button>
                                                                        <button class="yes-cancel cuim-cancel-button-ip" type="button">Yes</button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <button class="edit-ip-btn cuim-edit-button-ip" type="submit">Submit</button>
                                                            <div class="confirm-submit-popup cuim-edit-submit-popup">
                                                                <div class="confirm-submit-popup-box">
                                                                    <h2>Submit</h2>
                                                                    <div class="popup-form-cross-icon submit-cross-icon"></div>
                                                                    <div class="form-message">Are you sure you want to submit?</div>
                                                                    <div class="form-buttons agqa-popup-form-buttons d-flex">
                                                                        <button class="no-confirm-submit" type="button">No</button>
                                                                        <button type="submit" value="Yes" class="confirm-submit cuim-confirm-submit-ip">Yes</button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="confirm-submit-popup cuim-edit-submit-popup-again">
                                                                <div class="confirm-submit-popup-box">
                                                                    <h2>Submit</h2>
                                                                    <div class="popup-form-cross-icon submit-cross-icon"></div>
                                                                    <div class="form-message">You have set the same IP, Are you sure you want to submit?</div>
                                                                    <div class="form-buttons submit-manage-ip agqa-popup-form-buttons d-flex">
                                                                        <button class="no-confirm-submit" type="button">No</button>
                                                                        <button type="button" value="Yes" class="confirm-submit cuim-submit-again-btn">Yes</button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="delete-ip-ctn">
                                            <button class="delete-user-button"></button>
                                            <div class="delete-popup">
                                                <div class="delete-popup-inner">
                                                    <h2>Delete</h2>
                                                    <div class="popup-form-cross-icon"></div>
                                                    <div class="form-message">Are you sure you want to Delete?</div>
                                                    <div class="agqa-popup-form-buttons delete-manage-ip d-flex" id="delete-ip-users">
                                                        <button class="no-cancel" type="button">No</button>
                                                        <button type="submit" value="<?php echo $ip_value->account; ?>" class="yes-cancel">Yes</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                        <?php }
                        } ?>
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
        var itemsPerPage = 15;
        var totalItems = jQuery(".manage-ip-template .custom-table-row").length;
        var totalPages = Math.ceil(totalItems / itemsPerPage);

        // If no rows exist, disable pagination and return
        if (totalItems === 0) {
            jQuery(".manage-ip-template #pagination-demo").hide(); // Hide pagination if no items
            return;
        }

        jQuery(".manage-ip-template #pagination-demo").twbsPagination({
            totalPages: totalPages,
            visiblePages: totalPages,
            initiateStartPageClick: false,
            onPageClick: function(event, page) {
                // Hide all rows first
                jQuery(".manage-ip-template .custom-table-row").hide();

                // Show the rows for the current page
                jQuery('.manage-ip-template .custom-table-row[data-page="' + page + '"]').show();

                // Calculate the active items on the current page
                var totalActiveItems = jQuery(".custom-table-row.active").length;
                var totalActivePages = Math.ceil(totalActiveItems / itemsPerPage);

                // Show/hide pagination links based on the active pages
                jQuery(".manage-ip-template .pagination-ctn ul li.page-item").nextAll().not(".next").show();

                jQuery(".manage-ip-template .pagination-ctn ul li.page-item").not(".prev, .next").each(function() {
                    var pageNumberss = parseInt(jQuery(this).text()); // Get the number of the page

                    if (pageNumberss === totalActivePages && totalActivePages !== 0) {
                        // Hide all <li> items that come after the last active page
                        jQuery(this).nextAll().not(".next").hide();

                        // Check if the "Next" button should be disabled
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
        jQuery(".manage-ip-template .custom-table-row").each(function(index) {
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