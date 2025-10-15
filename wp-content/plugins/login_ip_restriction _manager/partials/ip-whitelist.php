<?php

global $wpdb;
$table_agqa_ip_list = $wpdb->prefix . 'agqa_wiki_add_ip';

$get_ip_list = $wpdb->get_results("
            SELECT
                id,
                user_id,
                account,
                ipv4,
                ipv6,
                delete_status,
                delete_user_name,
                delete_user_id,
                created_at
            FROM $table_agqa_ip_list
             ORDER BY 
            CASE 
                WHEN delete_status = 'table-body-disabled' THEN 1
                ELSE 0
            END,
            id DESC
            ");

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
                        <?php foreach ($get_ip_list as $ip_value) { ?>
                            <div class="custom-table-row <?php echo $ip_value->delete_status; ?>">
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
                                                        <button class="cancel-button" type="button">Cancel</button>
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
            visiblePages: 3,
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
                        var prevLi = jQuery(".manage-ip-template .pagination-ctn ul li.page-item.active").next();

                        // Disable or enable the "Next" button based on the visibility of the next page
                        if (prevLi.is(":hidden")) {
                            jQuery(".manage-ip-template .pagination-ctn ul li.next").addClass("disabled"); // Disable Next button
                        } else {
                            jQuery(".manage-ip-template .pagination-ctn ul li.next").removeClass("disabled"); // Enable Next button
                        }
                    }
                });
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
    });
</script>