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
            ORDER BY id DESC
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
                    <div class="filter-select">
                        <input type="hidden" name="filter-select-ip" id="filter-select-ip"
                            class="agqa-filter-select-hidden agqa-filter-select-ip">
                        <button class="filter-select-title select-ip">
                            <span class="filter-default-text">Select An Item</span>
                            <span class="filter-selected-text"></span>
                        </button>
                        <div class="filter-select-list">
                            <ul>
                                <li data-value="ipv4">IPv4</li>
                                <li data-value="ipv6">IPv6</li>
                            </ul>
                        </div>
                    </div>
                    <div class="filter-search-field">
                        <input type="search" class="cuim-manage-ipv4-search-validation-254" maxlength="254"
                            name="manage-ip-ipv4-search" id="manage-ip-ipv4-search"
                            placeholder="Enter IPv4 (E.G. 10.0.0.5)">
                    </div>
                    <!-- <div class="filter-search-field">
                        <input type="search" class="cuim-manage-ipv6-search-validation-254" maxlength="254"
                            name="manage-ip-ipv6-search" id="manage-ip-ipv6-search"
                            placeholder="Enter IPv6 (E.G. 10.0.0.5)">
                    </div> -->
                    <button type="submit" class="filter-select-button"
                        id="agqa-user-filters"><span>Search</span></button>
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
                        <?php foreach($get_ip_list as $ip_value) {?>
                       <div class="custom-table-row">
                        <div class="table-body-col">
                            <?php echo $ip_value->account?>
                        </div>
                        <div class="table-body-col">
                            <?php echo $ip_value->ipv4?>
                        </div>
                        <div class="table-body-col">
                            <?php echo $ip_value->ipv6?>
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
                                        <form action="#" id="edit-ip-from">
                                            <div class="form-field full-width required">
                                                <label for="account-name"><span>*</span> Account</label>
                                                <input type="text" name="account-name" id="manage-ip-account-field" placeholder="Description" required>
                                            </div>
                                            <div class="form-field full-width">
                                                <label for="manage-ip-ipv4-field">IPv4</label>
                                                <input type="text" name="manage-ip-ipv4-field" id="manage-ip-ipv4-field" placeholder="Description">
                                            </div>
                                            <div class="form-field full-width">
                                                <label for="manage-ip-ipv6-field">IPv6</label>
                                                <input type="text" name="manage-ip-ipv6-field" id="manage-ip-ipv6-field" placeholder="Description">
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
                                                            <button class="yes-cancel" type="button">Yes</button>
                                                        </div>
                                                    </div>
                                                </div>
                                                <button id="edit-ip-btn" type="submit" class="">Submit</button>
                                                <div class="confirm-submit-popup">
                                                    <div class="confirm-submit-popup-box">
                                                        <h2>Submit</h2>
                                                        <div class="popup-form-cross-icon submit-cross-icon"></div>
                                                        <div class="form-message">Are you sure you want to submit?</div>
                                                        <div class="form-buttons agqa-popup-form-buttons d-flex">
                                                            <button class="no-confirm-submit" type="button">No</button>
                                                            <button type="submit" value="Yes" class="confirm-submit">Yes</button>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="confirm-submit-popup">
                                                    <div class="confirm-submit-popup-box">
                                                        <h2>Submit</h2>
                                                        <div class="popup-form-cross-icon submit-cross-icon"></div>
                                                        <div class="form-message">You have set the same IP, Are you sure you want to submit?</div>
                                                        <div class="form-buttons submit-manage-ip agqa-popup-form-buttons d-flex">
                                                            <button class="no-confirm-submit" type="button">No</button>
                                                            <button type="submit" value="Yes" class="confirm-submit">Yes</button>
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
                                        <div class="agqa-popup-form-buttons delete-manage-ip d-flex">
                                            <button class="no-cancel" type="button">No</button>
                                            <button type="submit" value="yes-cancel" class="yes-cancel">Yes</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                       </div>
                       <?php }?>
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