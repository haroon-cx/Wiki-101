<?php
$add_manage_id = isset($_GET['add']) ? intval($_GET['add']) : 0;
$edit_manage_id = isset($_GET['edit']) ? intval($_GET['edit']) : 0;

$state = isset($_GET['state']) ? sanitize_text_field($_GET['state']) : '';
global $wpdb;
$table_agqa_manage_user = $wpdb->prefix . 'agqa_wiki_add_users';

if ($add_manage_id == 0 && $edit_manage_id == 0) {
    $add_manage_users_data = $wpdb->get_results("
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
                created_at
            FROM $table_agqa_manage_user
            ORDER BY id DESC
            ");
}

// $edit_user_data = null;

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
                        <input type="search" name="manage-user-search" id="manage-user-search"
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
                                <li data-value="">Select State</li>
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
                                <li data-value="">Select Role</li>
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
                                <li data-value="">Select Company</li>
                                <li data-value="Wagner Inc">Wagner Inc</li>
                                <li data-value="Wood and Sons">Wood and Sons</li>
                                <li data-value="Martinez, Nielsen and">Martinez, Nielsen</li>
                                <li data-value="Underwood LLC">Underwood LLC</li>
                            </ul>
                        </div>
                    </div>
                    <div class="filter-select date-field">
                        <input type="text" name="daterange" class="select-date-picker" value="" id="daterange"
                            placeholder="YYYY/MM/DD - YYYY/MM/DD">
                        <span class="calendar-icon"></span>
                    </div>
                    <button type="submit" class="filter-select-button"
                        id="agqa-game-filter"><span>Search</span></button>
                </form>
            </div>
            <div class="filter-right-area">
                <div class="add-button-ctn">
                    <a href="<?php echo esc_url(home_url('/manage-user/?add=1')) ?>" class="add-button">
                        <img src="<?php echo AGQA_URL ?>assets/images/plus-icon.svg" alt="Plus Icon">Add User
                    </a>
                </div>
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
                        <div class="table-head-col">Actions</div>
                    </div>
                    <div class="custom-table-body">
                        <?php
                            foreach ($add_manage_users_data as $key => $user_data) { ?>
                        <div class="custom-table-row">
                            <div class="table-body-col"><?php echo $user_data->account; ?></div>
                            <div class="table-body-col table-row-status <?php echo strtolower($user_data->state); ?>">
                                <span><?php echo $user_data->state; ?></span>
                            </div>
                            <div class="table-body-col"><?php echo $user_data->user_role; ?></div>
                            <div class="table-body-col"><?php echo $user_data->company_name; ?></div>
                            <div class="table-body-col table-body-col-mail"><a
                                    href="mailto:jillrhodes@miller.com"><?php echo $user_data->email; ?></a></div>
                            <div class="table-body-col table-body-col-userId"><a
                                    href="#">@<?php echo $user_data->account; ?></a></div>
                            <div class="table-body-col table-body-col-date">
                                <?php echo str_replace('-', '/', $user_data->created_at); ?>
                            </div>
                            <div class="table-body-col table-body-col-buttons">
                                <div class="login-history-ctn">
                                    <button class="login-history-icon"></button>
                                    <div class="login-history-popup">
                                        <div class="login-history-popup-inner">
                                            <div class="popup-form-cross-icon"></div>
                                            <div class="popup-head">
                                                <h2>Login History</h2>
                                                <span class="userName">johnsonjoshua</span>
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
                                                            <div class="user-history-record-list">
                                                                <span class="user-number">1</span>
                                                                <span class="user-login-time">2025/12/30 02:46</span>
                                                                <span class="user-ip">192.168.1.101</span>
                                                            </div>
                                                            <div class="user-history-record-list">
                                                                <span class="user-number">1</span>
                                                                <span class="user-login-time">2025/12/30 02:46</span>
                                                                <span class="user-ip">192.168.1.101</span>
                                                            </div>
                                                            <div class="user-history-record-list">
                                                                <span class="user-number">1</span>
                                                                <span class="user-login-time">2025/12/30 02:46</span>
                                                                <span class="user-ip">192.168.1.101</span>
                                                            </div>
                                                            <div class="user-history-record-list">
                                                                <span class="user-number">1</span>
                                                                <span class="user-login-time">2025/12/30 02:46</span>
                                                                <span class="user-ip">192.168.1.101</span>
                                                            </div>
                                                            <div class="user-history-record-list">
                                                                <span class="user-number">1</span>
                                                                <span class="user-login-time">2025/12/30 02:46</span>
                                                                <span class="user-ip">192.168.1.101</span>
                                                            </div>
                                                            <div class="user-history-record-list">
                                                                <span class="user-number">1</span>
                                                                <span class="user-login-time">2025/12/30 02:46</span>
                                                                <span class="user-ip">192.168.1.101</span>
                                                            </div>
                                                            <div class="user-history-record-list">
                                                                <span class="user-number">1</span>
                                                                <span class="user-login-time">2025/12/30 02:46</span>
                                                                <span class="user-ip">192.168.1.101</span>
                                                            </div>
                                                            <div class="user-history-record-list">
                                                                <span class="user-number">1</span>
                                                                <span class="user-login-time">2025/12/30 02:46</span>
                                                                <span class="user-ip">192.168.1.101</span>
                                                            </div>
                                                            <div class="user-history-record-list">
                                                                <span class="user-number">1</span>
                                                                <span class="user-login-time">2025/12/30 02:46</span>
                                                                <span class="user-ip">192.168.1.101</span>
                                                            </div>
                                                            <div class="user-history-record-list">
                                                                <span class="user-number">1</span>
                                                                <span class="user-login-time">2025/12/30 02:46</span>
                                                                <span class="user-ip">192.168.1.101</span>
                                                            </div>
                                                            <div class="user-history-record-list">
                                                                <span class="user-number">1</span>
                                                                <span class="user-login-time">2025/12/30 02:46</span>
                                                                <span class="user-ip">192.168.1.101</span>
                                                            </div>
                                                            <div class="user-history-record-list">
                                                                <span class="user-number">1</span>
                                                                <span class="user-login-time">2025/12/30 02:46</span>
                                                                <span class="user-ip">192.168.1.101</span>
                                                            </div>
                                                            <div class="user-history-record-list">
                                                                <span class="user-number">1</span>
                                                                <span class="user-login-time">2025/12/30 02:46</span>
                                                                <span class="user-ip">192.168.1.101</span>
                                                            </div>
                                                            <div class="user-history-record-list">
                                                                <span class="user-number">1</span>
                                                                <span class="user-login-time">2025/12/30 02:46</span>
                                                                <span class="user-ip">192.168.1.101</span>
                                                            </div>
                                                            <div class="user-history-record-list">
                                                                <span class="user-number">1</span>
                                                                <span class="user-login-time">2025/12/30 02:46</span>
                                                                <span class="user-ip">192.168.1.101</span>
                                                            </div>
                                                            <div class="user-history-record-list">
                                                                <span class="user-number">1</span>
                                                                <span class="user-login-time">2025/12/30 02:46</span>
                                                                <span class="user-ip">192.168.1.101</span>
                                                            </div>
                                                            <div class="user-history-record-list">
                                                                <span class="user-number">1</span>
                                                                <span class="user-login-time">2025/12/30 02:46</span>
                                                                <span class="user-ip">192.168.1.101</span>
                                                            </div>
                                                            <div class="user-history-record-list">
                                                                <span class="user-number">1</span>
                                                                <span class="user-login-time">2025/12/30 02:46</span>
                                                                <span class="user-ip">192.168.1.101</span>
                                                            </div>
                                                            <div class="user-history-record-list">
                                                                <span class="user-number">1</span>
                                                                <span class="user-login-time">2025/12/30 02:46</span>
                                                                <span class="user-ip">192.168.1.101</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="history-record-buttons d-flex">
                                                <button class="close-button">close</button>
                                                <button class="button">Go to Login History</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="manage-user-edit-ctn">
                                    <a href="<?php echo esc_url(home_url('/manage-user/?edit=' . $user_data->id . '&state=' . urlencode($user_data->state))); ?>"
                                        class="manage-user-edit-button"></a>
                                </div>

                                <div class="delete-user-ctn">
                                    <button class="delete-user-button"></button>
                                    <div id="custom-faq-field-popup">
                                        <div id="custom-faq-field-popup-inner">
                                            <h2>Delete</h2>
                                            <div class="popup-form-cross-icon"></div>
                                            <div class="form-message">Are you sure you want to Delete?</div>
                                            <div class="agqa-popup-form-buttons d-flex">
                                                <button class="no-cancel" type="button">No</button>
                                                <button id="yes-cancel" type="submit" value="">Yes</button>
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
        <div class="pagination-ctn">
            <div id="pagination-demo"></div>
        </div>
    </div>
</div>

<?php }