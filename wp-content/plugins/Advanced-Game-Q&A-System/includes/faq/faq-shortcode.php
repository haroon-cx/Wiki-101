<?php
function custom_faq_shortcode()
{
    $getUserRole = get_user_role_simple();
    $get_user_id = get_current_user_id();
    $add_faq_new  = isset($_GET['add']) ? intval($_GET['add']) : 0;
    $add_faq_edit  = isset($_GET['edit']) ? intval($_GET['edit']) : 0;
    $add_faq_history = isset($_GET['history']) ? intval($_GET['history']) : 0;
    $add_faq_review = isset($_GET['review']) ? intval($_GET['review']) : 0;
    $edit_faq_review = isset($_GET['edit-review']) ? intval($_GET['edit-review']) : 0;
    // echo $edit_faq_review;


    global $wpdb;
    $table_agqa_faq = $wpdb->prefix . 'agqa_faq';
    $table_agqa_faq_like = $wpdb->prefix . 'agqa_faq_likes_dislikes';
    $table_agqa_manage_user = $wpdb->prefix . 'agqa_wiki_add_users';
    // echo $add_username;
    // Custom state
    $get_stauts_freez = $wpdb->get_var(
        $wpdb->prepare("SELECT state FROM {$table_agqa_manage_user} WHERE user_id = %d LIMIT 1", $get_user_id)
    );
    if (strtolower($get_stauts_freez) == 'freeze') {
        $disabledActionClass = 'table-body-disabled-faq';
    } else {
        $disabledActionClass = '';
    }


    if ($add_faq_edit == 0 && $add_faq_new == 0) {
        // $faq_data = $wpdb->get_results("
        //     SELECT
        //         id,
        //         question,
        //         answer,
        //         faq_category,
        //         verified_answer,
        //         delete_status,
        //         delete_user_name,
        //         delete_user_date
        //     FROM $table_agqa_faq
        //     ORDER BY 
        //     CASE 
        //         WHEN delete_status = 'table-body-disabled' THEN 1
        //         ELSE 0
        //     END,
        //     id DESC
        // ");
        // Base SELECT
        $select_sql = "
    SELECT
        id,
        question,
        answer,
        faq_category,
        verified_answer,
        delete_status,
        delete_user_name,
        delete_user_date
    FROM {$table_agqa_faq}
";

        // Role-wise WHERE + ORDER
        if ($getUserRole == 'admin') {
            // Admin: show all; deleted at bottom
            $where    = "";
            $order_by = "
        ORDER BY 
            CASE 
                WHEN delete_status = 'table-body-disabled' THEN 1
                ELSE 0
            END,
            id DESC
    ";
        } else {
            // Non-admin: hide deleted entirely
            $where    = "WHERE (delete_status IS NULL OR delete_status <> 'table-body-disabled')";
            $order_by = "ORDER BY id DESC";
        }

        // Final query
        $sql = $select_sql . ' ' . $where . ' ' . $order_by;

        $faq_data = $wpdb->get_results($sql);
        $table_data_like = $wpdb->get_results("
            SELECT
                id,
                faq_id,
                user_id,
                action_type
            FROM $table_agqa_faq_like
            ORDER BY id DESC
        ");
    }
    if ($add_faq_edit !== 0) {
        $faq_data = $wpdb->get_results($wpdb->prepare("
        SELECT
            id,
            question,
            answer,
            faq_category,
            verified_answer
        FROM $table_agqa_faq
        WHERE id = %d
        ORDER BY id DESC
    ", $add_faq_edit));
    }


    ob_start();
    include AGQA_PATH . 'includes/faq/faq-report.php';

    if ($add_faq_new !== 0) {
        include AGQA_PATH . 'includes/faq/add-form-faq.php';
    }

    if ($add_faq_edit !== 0) {
        include AGQA_PATH . 'includes/faq/edit-form-faq.php';
    }
    if ($add_faq_history !== 0) {
        include AGQA_PATH . 'includes/faq/faq-history.php';
    }
    if ($add_faq_review !== 0) {
        include AGQA_PATH . 'includes/faq/faq-review.php';
    }
    if ($edit_faq_review !== 0) {
        include AGQA_PATH . 'includes/faq/faq-edit-review.php';
    }
?>
    <?php if ($add_faq_new == 0 && $add_faq_edit == 0 && $add_faq_review == 0 && $add_faq_history == 0 && $edit_faq_review == 0) { ?>
        <div class="faq-template">
            <div id="page-content">
                <!-- Content will be dynamically updated based on pagination -->
            </div>
            <div class="template-title">
                <h1>FAQs</h1>
            </div>
            <!-- filter Start -->
            <div class="filter-container">
                <div class="filter-area">
                    <form action="#" autocomplete="off">
                        <input type="search" name="filter-search" id="filter-search" class="agqa-faq-validation-100"
                            placeholder="Please enter ...">
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
                                <!-- <span class="filter-default-text">All</span> -->
                                <span class="filter-selected-text"></span>
                            </button>
                            <div class="filter-select-list agqa-faq-cat-filter">
                                <ul>
                                    <?php
                                    if (strpos($_SERVER['REQUEST_URI'], '/zh/') !== false) { ?>
                                        <li data-value="all">全部</li>
                                        <li data-value="Account & Access Management">帳號與存取管理</li>
                                        <li data-value="System Features & Workflow">系統功能與操作流程</li>
                                        <li data-value="Reports & Data Queries">報表與資料查詢</li>
                                        <li data-value="Errors & Troubleshooting">錯誤與問題排查</li>
                                        <li data-value="Notifications & Alerts">通知與警示</li>
                                        <li data-value="Performance & Compatibility">效能與相容性</li>
                                        <li data-value="System Settings & Customization">系統設定與自訂化</li>
                                        <li data-value="Customer Support & Contact">客服支援與聯絡</li>
                                        <li data-value="Other">其他</li>
                                    <?php } else { ?>
                                        <li data-value="all">All</li>
                                        <li data-value="Account & Access Management">Account & Access Management</li>
                                        <li data-value="System Features & Workflow">System Features & Workflow</li>
                                        <li data-value="Reports & Data Queries">Reports & Data Queries</li>
                                        <li data-value="Errors & Troubleshooting">Errors & Troubleshooting</li>
                                        <li data-value="Notifications & Alerts">Notifications & Alerts</li>
                                        <li data-value="Performance & Compatibility">Performance & Compatibility</li>
                                        <li data-value="System Settings & Customization">System Settings & Customization</li>
                                        <li data-value="Customer Support & Contact">Customer Support & Contact</li>
                                        <li data-value="Other">Other</li>
                                    <?php } ?>
                                </ul>
                            </div>
                        </div>
                        <button type="submit" class="filter-select-button" id="agqa-faq-filter"><span>Search</span></button>
                    </form>
                </div>
                <?php if ($getUserRole !== 'viewer') { ?>
                    <div class="filter-right-area">
                        <div class="add-button-ctn <?php echo $disabledActionClass; ?>">
                            <a href="<?php echo esc_url(home_url('faq/') . '?add=1'); ?>" class="add-button">
                                <img src="<?php echo AGQA_URL ?>assets/images/plus-icon.svg" alt="Plus Icon">Add FAQ
                            </a>
                        </div>
                    </div>
                <?php } ?>
            </div>
            <!-- filter End -->

            <!-- Main Content Start -->
            <div class="faq-main-content">

                <div class="faq-accordions">
                    <?php foreach ($faq_data as $faq_value) {
                        // Initialize like and dislike counts for this FAQ
                        $likes_data = 0;
                        $unlikes_data = 0;

                        // Track the user's action (like or dislike) for this FAQ
                        $user_action = ''; // empty, 'liked', or 'disliked'

                        foreach ($table_data_like as $aga_like) {
                            // Fetch like count for the specific faq_id and user_id
                            if ($aga_like->faq_id == $faq_value->id) {
                                if ($aga_like->action_type == '1') {
                                    $likes_data++; // Increment like count for this faq_id
                                } elseif ($aga_like->action_type == '0') {
                                    $unlikes_data++; // Increment dislike count for this faq_id
                                }

                                // Check if the current user has liked or disliked this FAQ
                                if ($aga_like->user_id == get_current_user_id()) {
                                    if ($aga_like->action_type == '1') {
                                        $user_action = 'liked'; // User has liked this FAQ
                                    } elseif ($aga_like->action_type == '0') {
                                        $user_action = 'disliked'; // User has disliked this FAQ
                                    }
                                }
                            }
                        }

                        // Default like and dislike counts
                        $like_count = $likes_data;
                        $unlike_count = $unlikes_data;
                    ?>

                        <div class="faq-accordion active" data-id="<?php echo $faq_value->id ?>">
                            <div class="agqa-deleter-status">
                                <?php echo empty($faq_value->delete_user_name) ? '' : 'Deleter | ' . $faq_value->delete_user_name . " " .
                                    date('Y/m/d', strtotime($faq_value->delete_user_date)); ?>
                            </div>
                            <div class="<?php echo $faq_value->delete_status; ?>">

                                <div class="faq-accodion-status" data-faq-value="<?php echo $faq_value->faq_category; ?>"><?php echo $faq_value->faq_category; ?></div>
                                <div class="faq-accordion-head">
                                    <h2><?php echo esc_html($faq_value->question); ?></h2>
                                    <button class="button agqa-status-toggle"
                                        style="background: transparent !important; padding: 0; padding-left: 10px;">
                                        <img src="<?php echo esc_url(AGQA_URL . 'assets/images/accordian-arrow.svg'); ?>" alt="Arrow">
                                    </button>
                                </div>

                                <div class="faq-accordion-body">
                                    <?php if ($faq_value->answer) { ?>
                                        <?php echo ($faq_value->answer); ?>
                                    <?php } ?>
                                </div>

                                <div class="faq-accordion-bottom <?php echo $disabledActionClass; ?>">
                                    <button
                                        class="faq-accordion-button like-button <?php echo ($user_action == 'liked') ? 'active' : ''; ?>"
                                        name="action-type">
                                        <div class="faq-accordion-icon">
                                            <input type="hidden" class="agqa-like" name="faq-id"
                                                value="<?php echo esc_attr($faq_value->id); ?>">
                                            <img src="<?php echo esc_url(AGQA_URL . 'assets/images/like-icon.svg'); ?>" alt="Like Icon">
                                        </div>
                                        <span class="like-coounting"><?php echo esc_html($like_count); ?></span>
                                    </button>

                                    <!-- Unlike Button -->
                                    <button
                                        class="faq-accordion-button unlike-button <?php echo ($user_action == 'disliked') ? 'active' : ''; ?>"
                                        name="action-type">
                                        <div class="faq-accordion-icon">
                                            <input type="hidden" class="agqa-dislike" name="faq-id"
                                                value="<?php echo esc_attr($faq_value->id); ?>">
                                            <img src="<?php echo esc_url(AGQA_URL . 'assets/images/unlike-icon.svg'); ?>"
                                                alt="Un Like Icon">
                                        </div>
                                        <span class="unlike-coounting"><?php echo esc_html($unlike_count); ?></span>
                                    </button>

                                    <!-- Copy, Edit, Delete and Report buttons (not modified) -->
                                    <button class="faq-accordion-button copy-button">
                                        <div class="faq-accordion-icon">
                                            <img src="<?php echo esc_url(AGQA_URL . 'assets/images/copy-text-icon.svg'); ?>"
                                                alt="Copy Icon">
                                        </div>
                                        <span>Copy</span>
                                    </button>
                                    <?php if ($getUserRole !== 'viewer') { ?>
                                        <a href="<?php echo esc_url(home_url('faq/') . '?edit=' . $faq_value->id); ?>"
                                            class="faq-accordion-button edit-button">
                                            <div class="faq-accordion-icon">
                                                <img src="<?php echo esc_url(AGQA_URL . 'assets/images/edit-icon.svg'); ?>" alt="Edit Icon">
                                            </div>
                                            Edit
                                        </a>
                                    <?php } ?>
                                    <?php if ($getUserRole !== 'viewer' && $getUserRole !== 'contributor') { ?>
                                        <div id="custom-faq-field-popup" class="agqa-delete-popup-faq">
                                            <div id="custom-faq-field-popup-inner">
                                                <h2>Delete</h2>
                                                <div class="popup-form-cross-icon"></div>
                                                <div class="form-message">Are you sure you want to Delete?</div>
                                                <div class="agqa-popup-form-buttons d-flex" id="delete-faq-div">
                                                    <button class="no-cancel" type="button">No</button>
                                                    <button id="yes-cancel" type="submit" value="<?php echo $faq_value->id; ?>">Yes</button>
                                                </div>
                                            </div>
                                        </div>
                                        <button class="faq-accordion-button delete-button">
                                            <div class="faq-accordion-icon">
                                                <input type="hidden" class="agqa-dell" name="faq-id"
                                                    value="<?php echo esc_attr($faq_value->id); ?>">
                                                <img src="<?php echo esc_url(AGQA_URL . 'assets/images/delete-icon.svg'); ?>"
                                                    alt="Delete Icon">
                                            </div>
                                            <span>Delete</span>
                                        </button>
                                    <?php } ?>

                                    <button class="faq-accordion-button report-button">
                                        <div class="faq-accordion-icon">
                                            <img src="<?php echo esc_url(AGQA_URL . 'assets/images/alert-icon.svg'); ?>"
                                                alt="Report Icon">
                                        </div>
                                        <span>Report</span>
                                    </button>

                                    <button class="faq-accordion-button verified-button">
                                        <div class="faq-accordion-icon">
                                            <img src="<?php echo esc_url(AGQA_URL . 'assets/images/verified-icon.svg'); ?>"
                                                alt="Verified Answer Icon">
                                        </div>
                                        <span>Verified answer</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
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
            </div>
            <div class="pagination-ctn">
                <div id="pagination-demo"></div>
            </div>
            <!-- Main Content End -->
        </div>
        <script>
            jQuery(document).ready(function() {

                // ==========================
                // 6. Pagination
                // ==========================
                var itemsPerPage = 15;
                var totalItems = jQuery(".faq-accordion").length;
                var totalPages = Math.ceil(totalItems / itemsPerPage);


                jQuery("#pagination-demo").twbsPagination({
                    totalPages: totalPages,
                    visiblePages: totalPages,
                    initiateStartPageClick: false,
                    onPageClick: function(event, page) {
                        jQuery(".faq-accordion").hide();
                        jQuery('.faq-accordion[data-page="' + page + '"]').show();
                        var totalActiveItems = jQuery(".faq-accordion.active").length;
                        var totalActivePages = Math.ceil(totalActiveItems / itemsPerPage);

                        // Loop through each page <li> (exclude Prev/Next)
                        // Loop through each page <li> (exclude Prev/Next)
                        jQuery('.pagination-ctn ul li.page-item').nextAll().not('.next').show();
                        jQuery(".pagination-ctn ul li.page-item").not(".prev, .next").each(function() {
                            var pageNumberss = parseInt(jQuery(this).text()); // Get the number of the page

                            if (pageNumberss === totalActivePages && totalActivePages !== 0) {

                                // Remove all <li> items that come after this one
                                jQuery(this).nextAll().not('.next').hide();

                                // Check the <li> just before the Next button
                                var prevLi = jQuery(".pagination-ctn ul li.page-item.active").next();
                                var $nextBtn = jQuery(".pagination-ctn ul li.next");

                                // Disable if: no next item, next is hidden, or next IS the .next button (last page)
                                if (!prevLi.length || prevLi.is(":hidden") || prevLi.hasClass("next")) {
                                    $nextBtn.addClass("disabled");
                                } else {
                                    $nextBtn.removeClass("disabled");
                                }
                                // Break the loop since we found the match
                                // return false;
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

                jQuery(".faq-accordion").each(function(index) {
                    var page = Math.floor(index / itemsPerPage) + 1;
                    jQuery(this).attr("data-page", page);
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
    <?php } ?>
<?php
    return ob_get_clean();
} ?>
<?php add_shortcode('custom_faq', 'custom_faq_shortcode'); ?>