<?php
?>
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
                        <input type="search" name="manage-user-search" id="manage-user-search" placeholder="please enter account name or email">
                        <div class="filter-select">
                            <input type="hidden" name="filter-select-states" class="agqa-filter-select-hidden agqa-filter-select-states">
                            <button class="filter-select-title select-states">
                                <span class="filter-default-text">Select States</span>
                                <span class="filter-selected-text"></span>
                            </button>
                            <div class="filter-select-list">
                                <ul>
                                    <li>Active</li>
                                    <li>Inactive</li>
                                    <li>Freeze</li>
                                    <li>Pending</li>
                                </ul>
                            </div>
                        </div>
                        <div class="filter-select">
                            <input type="hidden" name="filter-select-roles" class="agqa-filter-select-hidden agqa-filter-select-roles">
                            <button class="filter-select-title select-roles">
                                <span class="filter-default-text">Select Role</span>
                                <span class="filter-selected-text"></span>
                            </button>
                            <div class="filter-select-list">
                                <ul>
                                    <li>All</li>
                                    <li>Admin</li>
                                    <li>Manager</li>
                                    <li>Contributor</li>
                                    <li>Viewer</li>
                                </ul>
                            </div>
                        </div>
                        <div class="filter-select">
                            <input type="hidden" name="filter-select-companies" class="agqa-filter-select-hidden agqa-filter-select-companies">
                            <button class="filter-select-title select-companies">
                                <span class="filter-default-text">Select Company</span>
                                <span class="filter-selected-text"></span>
                            </button>
                            <div class="filter-select-list">
                                <ul>
                                    <li>Wagner Inc</li>
                                    <li>Wood and Sons</li>
                                    <li>Martinez, Nielsen and</li>
                                    <li>Underwood LLC</li>
                                    <li>Mack-Peterson</li>
                                    <li>Mack-Peterson</li>
                                    <li>Miller Group</li>
                                </ul>
                            </div>
                        </div>
                        <div class="date-field">
                            <input type="date" id="creation_time" name="creation_time" placeholder="YYYY/MM/DD - YYYY/MM/DD">
                            <span class="date-icon"></span>
                        </div>
                        <button type="submit" class="filter-select-button" id="agqa-game-filter"><span>Search</span></button>
                    </form>
                </div>
                <div class="filter-right-area">
                    <div class="add-button-ctn">
                        <a href="#" class="add-button">
                            <img src="<?php echo AGQA_URL ?>assets/images/plus-icon.svg" alt="Plus Icon">Add User
                        </a>
                    </div>
                </div>
        </div>
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
                <div class="custom-table-row">
                    <div class="table-body-col">johnsonjoshua</div>
                    <div class="table-body-col table-row-status active"><span>Active</span></div>
                    <div class="table-body-col">Viewer</div>
                    <div class="table-body-col">Wagner Inc</div>
                    <div class="table-body-col table-body-col-mail"><a href="mailto:jillrhodes@miller.com">jillrhodes@miller.com</a></div>
                    <div class="table-body-col table-body-col-userId"><a href="#">@johnsonjoshua</a></div>
                    <div class="table-body-col table-body-col-date">2025/11/12</div>
                    <div class="table-body-col table-body-col-buttons">
                         <div class="login-history-ctn">
                            <button class="login-history-icon"></button>
                         </div>
                    </div>
                </div>
                <div class="custom-table-row">
                    <div class="table-body-col">johnsonjoshua</div>
                    <div class="table-body-col table-row-status inactive"><span>Inactive</span></div>
                    <div class="table-body-col">Viewer</div>
                    <div class="table-body-col">Wagner Inc</div>
                    <div class="table-body-col table-body-col-mail"><a href="mailto:jillrhodes@miller.com">jillrhodes@miller.com</a></div>
                    <div class="table-body-col table-body-col-userId"><a href="#">@johnsonjoshua</a></div>
                    <div class="table-body-col table-body-col-date">2025/11/12</div>
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
                    </div>
                </div>
                <div class="custom-table-row">
                    <div class="table-body-col">johnsonjoshua</div>
                    <div class="table-body-col table-row-status freeze"><span>Freeze</span></div>
                    <div class="table-body-col">Viewer</div>
                    <div class="table-body-col">Wagner Inc</div>
                    <div class="table-body-col table-body-col-mail"><a href="mailto:jillrhodes@miller.com">jillrhodes@miller.com</a></div>
                    <div class="table-body-col table-body-col-userId"><a href="#">@johnsonjoshua</a></div>
                    <div class="table-body-col table-body-col-date">2025/11/12</div>
                    <div class="table-body-col table-body-col-buttons">
                         <div class="login-history-ctn">
                            <button class="login-history-icon"></button>
                         </div>
                    </div>
                </div>
                <div class="custom-table-row">
                    <div class="table-body-col">johnsonjoshua</div>
                    <div class="table-body-col table-row-status pending"><span>Pending</span></div>
                    <div class="table-body-col">Viewer</div>
                    <div class="table-body-col">Wagner Inc</div>
                    <div class="table-body-col table-body-col-mail"><a href="mailto:jillrhodes@miller.com">jillrhodes@miller.com</a></div>
                    <div class="table-body-col table-body-col-userId"><a href="#">@johnsonjoshua</a></div>
                    <div class="table-body-col table-body-col-date">2025/11/12</div>
                    <div class="table-body-col table-body-col-buttons">
                         <div class="login-history-ctn">
                            <button class="login-history-icon"></button>
                         </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>