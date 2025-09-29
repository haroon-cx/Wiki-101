<?php
// Function to generate the report system content
function report_system_shortcode() {
    ob_start(); // Start output buffering

    // HTML structure for the report system, you can add your custom content here
    ?>
    <div class="report-system-template">
        <div id="page-content">
            <!-- Content will be dynamically updated based on pagination -->
        </div>
        <div class="template-title">
            <h1>Report System</h1>
        </div>    
    <!-- filter Start -->
        <div class="filter-container report-system-filter">
            <div class="filter-area">
                <form action="#" autocomplete="off">
                    <div class="filter-select">
                        <input type="hidden" name="filter-report-type" class="agqa-filter-select-hidden">
                        <button class="filter-select-title agqa-report-type-filter-button">
                            <span class="filter-default-text">Select Report Type</span>
                            <span class="filter-selected-text report-filter-selected-text"></span>
                        </button>
                        <div class="filter-select-list agqa-report-cat-filter">
                            <ul>
                                <li>All</li>
                                <li>Functional issue / Operation not working as expected</li>
                                <li>UI display issue</li>
                                <li>Incorrect data display</li>
                                <li>System error message</li>
                                <li>Process interruption / Unable to complete operation</li>
                                <li>Performance issue / System lag</li>
                                <li>Permission or account-related issue</li>
                                <li>Notification / Email / Task trigger issue</li>
                                <li>Text / Language error</li>
                                <li>Other (please specify)</li>
                            </ul>
                        </div>
                    </div>
                    <input type="search" name="filter-search" id="filter-search" class="agqa-report-validation-100"
                        placeholder="Please Enter">
                        <div class="filter-select">
                        <input type="hidden" name="filter-report-states" class="agqa-filter-select-hidden">
                        <button class="filter-select-title agqa-report-select-filter-button">
                            <span class="filter-default-text">Select States</span>
                            <span class="filter-selected-text"></span>
                        </button>
                        <div class="filter-select-list agqa-report-cat-filter">
                            <ul>
                                <li>Pending Response</li>
                                <li>Responded</li>
                                <li>No response required</li>
                            </ul>
                        </div>
                    </div>
                    <button type="submit" class="filter-select-button" id="agqa-faq-filter"><span>Search</span></button>
                </form>
            </div>
        </div>
        <div class="filter-pending-responses">
            <button>Pending Response <span class="pending-response-counting">3</span></button>
        </div>

        <div class="report-form-table-ctn custom-table-ctn">
            <div class="custom-table-ctn-inner">
                <div class="report-form-table custom-table">
                    <div class="custom-table-head">
                        <div class="table-head-col">No.</div>
                        <div class="table-head-col">Report Type</div>
                        <div class="table-head-col">Status</div>
                        <div class="table-head-col">Reporter</div>
                        <div class="table-head-col">Create Time</div>
                        <div class="table-head-col">Reply Time</div>
                        <div class="table-head-col">Actions</div>
                    </div>
                    <div class="custom-table-body">
                        <div class="custom-table-row">
                            <div class="table-body report-row">
                                <div class="report-row-head">
                                    <div class="table-body-col">1</div>
                                    <div class="table-body-col">Functional issue / Operation not 
                                        working as expected</div>
                                    <div class="table-body-col report-status-response">
                                        <span class="pending-response">Pending Response</span>
                                    </div>
                                    <div class="table-body-col">johnsonjoshua</div>
                                    <div class="table-body-col">2025/11/12</div>
                                    <div class="table-body-col">--</div>
                                    <div class="table-body-col report-action">
                                        <button class="respond-button"></button>
                                         <div class="respond-popup">
                                            <div class="respond-popup-inner">
                                                <div class="popup-form-cross-icon report-form-cancel-icon"></div>
                                                <form id="respond-form" autocomplete="off" data-inited-validation="1" novalidate="novalidate">
                                                    <div class="respond-form-title"><h2>Respond</h2></div>
                                                    <div class="form-field required">
                                                        <label for="respond-report-type"><span>* </span>Report Type</label>
                                                        <div class="custom-select-dropdown">
                                                            <div class="custom-select-dropdown-title">
                                                                <span class="custom-dropdown-default-value">Select Report Type</span>
                                                                <span class="custom-dropdown-selected-value"></span>
                                                                    </div>
                                                            <div class="custom-select-dropdown-lists">
                                                            <ul>
                                                                <li>All</li>
                                                                <li>Functional issue / Operation not working as expected</li>
                                                                <li>UI display issue</li>
                                                                <li>Incorrect data display</li>
                                                                <li>System error message</li>
                                                                <li>Process interruption / Unable to complete operation</li>
                                                                <li>Performance issue / System lag</li>
                                                                <li>Permission or account-related issue</li>
                                                                <li>Notification / Email / Task trigger issue</li>
                                                                <li>Text / Language error</li>
                                                                <li>Other (please specify)</li>
                                                            </ul>
                                                        </div>
                                                        <input type="hidden" name="respond-report-type" id="respond-report-type" required>
                                                        </div>
                                                    </div>
                                                    <div class="form-field required">
                                                        <label for="respond-report-type"><span>* </span>Report Type</label>
                                                        <div class="custom-select-dropdown">
                                                            <div class="custom-select-dropdown-title">
                                                                <span class="custom-dropdown-default-value">Select States</span>
                                                                <span class="custom-dropdown-selected-value"></span>
                                                                    </div>
                                                            <div class="custom-select-dropdown-lists">
                                                            <ul>
                                                                <li>Pending Response</li>
                                                                <li>No response needed</li>
                                                            </ul>
                                                        </div>
                                                        <input type="hidden" name="respond-report-type" id="respond-report-type" required>
                                                        </div>
                                                    </div>
                                                    <div class="form-field">
                                                        <label for="respond-disabled-textarea">Issue Detail</label>
                                                        <textarea name="respond-disabled-textarea" id="respond-disabled-textarea" placeholder="Typing...." disabled></textarea>
                                                    </div>
                                                    <div class="uploaded-images">
                                                        <div class="uploaded-image">
                                                            <img src="<?php echo AGQA_URL ?>assets/images/enlarger-image.png" alt="Report Image" class="stretchable">
                                                            <div class="stretch-image-icon"></div>
                                                        </div>
                                                        <div class="uploaded-image">
                                                            <img src="<?php echo AGQA_URL ?>assets/images/report-uploaded-image.png" alt="Report Image" class="stretchable">
                                                            <div class="stretch-image-icon"></div>
                                                        </div>
                                                        <div class="uploaded-image">
                                                            <img src="<?php echo AGQA_URL ?>assets/images/report-uploaded-image.png" alt="Report Image" class="stretchable">
                                                            <div class="stretch-image-icon"></div>
                                                        </div>
                                                        <div class="uploaded-image">
                                                            <img src="<?php echo AGQA_URL ?>assets/images/report-uploaded-image.png" alt="Report Image" class="stretchable">
                                                            <div class="stretch-image-icon"></div>
                                                        </div>
                                                        <div class="uploaded-image">
                                                            <img src="<?php echo AGQA_URL ?>assets/images/report-uploaded-image.png" alt="Report Image" class="stretchable">
                                                            <div class="stretch-image-icon"></div>
                                                        </div>
                                                    </div>
                                                    <!-- Hidden overlay for stretched image -->
                                                    <div id="stretch-overlay" class="stretch-overlay">
                                                        <div class="stretch-container">
                                                            <div class="zoom-close-icon"></div>
                                                            <img id="stretched-img" src="" alt="Stretched Image">
                                                        </div>
                                                    </div>
                                                    <div class="form-field required">
                                                        <label for="respond-report-type"><span>* </span>Report Type</label>
                                                        <div class="custom-select-dropdown">
                                                            <div class="custom-select-dropdown-title">
                                                                <span class="custom-dropdown-default-value">Import Answer From FAQ</span>
                                                                <span class="custom-dropdown-selected-value"></span>
                                                                    </div>
                                                            <div class="custom-select-dropdown-lists">
                                                            <ul>
                                                                <li>Approved FAQ titles</li>
                                                                <li>Approved FAQ titles</li>
                                                                <li>Approved FAQ titles</li>
                                                                <li>Approved FAQ titles</li>
                                                                <li>Approved FAQ titles</li>
                                                            </ul>
                                                        </div>
                                                        <input type="hidden" name="respond-report-type" id="respond-report-type" required>
                                                        </div>
                                                    </div>
                                                    <div class="form-field">
                                                        <textarea name="respond-detail-textarea" id="respond-detail-textarea" placeholder="Typing...."></textarea>
                                                    </div>
                                                    <div class="form-buttons d-flex agqa-respond-buttons">
                                                        <div id="cancel-form-confirmation" class="cancel-form-confirmation   report-cancel-popup-confirmation">
                                                            <div class="cancel-form-confirmation-box">
                                                                <h2>Cancel</h2>
                                                                    <div class="popup-form-cross-icon"></div>
                                                                    <div class="form-message">Are you sure you want to cancel?</div>
                                                                    <div class="form-buttons agqa-popup-form-buttons d-flex">
                                                                        <button class="no-form-cancel" type="button">No</button>
                                                                        <a href="#" class="back-button">Yes</a>
                                                                    </div>
                                                            </div>       
                                                        </div>
                                                        <div id="confirm-submit-popup" class="confirm-submit-popup report-cancel-popup-confirmation">
                                                            <div class="confirm-submit-popup-box">
                                                                <h2>Submit</h2>
                                                                    <div class="popup-form-cross-icon report-cancel-icon"></div>
                                                                    <div class="form-message">Are you sure you want to submit?</div>
                                                                    <div class="form-buttons agqa-popup-form-buttons d-flex">
                                                                        <button class="no-confirm-submit" type="button">No</button>
                                                                        <input type="submit" value="Yes" id="confirm-submit">
                                                                    </div>
                                                            </div>       
                                                        </div>
                                                        <button class="back-button report-cancel-button" id="cancel-confirmation-button">Cancel</button>
                                                        <button type="button" id="respond-popup-button">Submit</button>
                                                    </div>
                                                </form>
                                            </div>
                                         </div>
                                    </div>
                                </div>
                                <div class="report-row-body">
                                    <div class="report-row-body-head">
                                        <div class="report-row-body-heading">
                                            <h2>Issue Detail</h2>
                                        </div>
                                        <div class="report-row-body-heading">
                                            <h2>Answer</h2>
                                        </div>
                                    </div>
                                    <div class="report-row-body-bottom">
                                        <div class="report-row-body-detail">
                                            <div class="report-row-body-text">
                                                <p>When I click on the game link,the page keepsloading and never displays 
                                                the game. I have tried refreshing and using another browser, 
                                                but the issue remains. Please help.</p>
                                            </div>
                                            <div class="report-row-body-text">
                                                <p>--</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="custom-table-row">
                            <div class="table-body report-row">
                                <div class="report-row-head">
                                    <div class="table-body-col">1</div>
                                    <div class="table-body-col">Functional issue / Operation not 
                                        working as expected</div>
                                    <div class="table-body-col report-status-response">
                                        <span class="responded-status">Responded</span>
                                    </div>
                                    <div class="table-body-col">johnsonjoshua</div>
                                    <div class="table-body-col">2025/11/12</div>
                                    <div class="table-body-col">--</div>
                                    <div class="table-body-col report-action">
                                        <button class="respond-button"></button>
                                         <div class="respond-popup">
                                            <div class="respond-popup-inner">
                                                <div class="popup-form-cross-icon report-form-cancel-icon"></div>
                                                <form id="respond-form" autocomplete="off" data-inited-validation="1" novalidate="novalidate">
                                                    <div class="respond-form-title"><h2>Respond</h2></div>
                                                    <div class="form-field required">
                                                        <label for="respond-report-type"><span>* </span>Report Type</label>
                                                        <div class="custom-select-dropdown">
                                                            <div class="custom-select-dropdown-title">
                                                                <span class="custom-dropdown-default-value">Select Report Type</span>
                                                                <span class="custom-dropdown-selected-value"></span>
                                                                    </div>
                                                            <div class="custom-select-dropdown-lists">
                                                            <ul>
                                                                <li>All</li>
                                                                <li>Functional issue / Operation not working as expected</li>
                                                                <li>UI display issue</li>
                                                                <li>Incorrect data display</li>
                                                                <li>System error message</li>
                                                                <li>Process interruption / Unable to complete operation</li>
                                                                <li>Performance issue / System lag</li>
                                                                <li>Permission or account-related issue</li>
                                                                <li>Notification / Email / Task trigger issue</li>
                                                                <li>Text / Language error</li>
                                                                <li>Other (please specify)</li>
                                                            </ul>
                                                        </div>
                                                        <input type="hidden" name="respond-report-type" id="respond-report-type" required>
                                                        </div>
                                                    </div>
                                                    <div class="form-field required">
                                                        <label for="respond-report-type"><span>* </span>Report Type</label>
                                                        <div class="custom-select-dropdown">
                                                            <div class="custom-select-dropdown-title">
                                                                <span class="custom-dropdown-default-value">Select States</span>
                                                                <span class="custom-dropdown-selected-value"></span>
                                                                    </div>
                                                            <div class="custom-select-dropdown-lists">
                                                            <ul>
                                                                <li>Pending Response</li>
                                                                <li>No response needed</li>
                                                            </ul>
                                                        </div>
                                                        <input type="hidden" name="respond-report-type" id="respond-report-type" required>
                                                        </div>
                                                    </div>
                                                    <div class="form-field">
                                                        <label for="respond-disabled-textarea">Issue Detail</label>
                                                        <textarea name="respond-disabled-textarea" id="respond-disabled-textarea" placeholder="Typing...." disabled></textarea>
                                                    </div>
                                                    <div class="uploaded-images">
                                                        <div class="uploaded-image">
                                                            <img src="<?php echo AGQA_URL ?>assets/images/enlarger-image.png" alt="Report Image" class="stretchable">
                                                            <div class="stretch-image-icon"></div>
                                                        </div>
                                                        <div class="uploaded-image">
                                                            <img src="<?php echo AGQA_URL ?>assets/images/report-uploaded-image.png" alt="Report Image" class="stretchable">
                                                            <div class="stretch-image-icon"></div>
                                                        </div>
                                                        <div class="uploaded-image">
                                                            <img src="<?php echo AGQA_URL ?>assets/images/report-uploaded-image.png" alt="Report Image" class="stretchable">
                                                            <div class="stretch-image-icon"></div>
                                                        </div>
                                                        <div class="uploaded-image">
                                                            <img src="<?php echo AGQA_URL ?>assets/images/report-uploaded-image.png" alt="Report Image" class="stretchable">
                                                            <div class="stretch-image-icon"></div>
                                                        </div>
                                                        <div class="uploaded-image">
                                                            <img src="<?php echo AGQA_URL ?>assets/images/report-uploaded-image.png" alt="Report Image" class="stretchable">
                                                            <div class="stretch-image-icon"></div>
                                                        </div>
                                                    </div>
                                                    <!-- Hidden overlay for stretched image -->
                                                    <div id="stretch-overlay" class="stretch-overlay">
                                                        <div class="stretch-container">
                                                            <div class="zoom-close-icon"></div>
                                                            <img id="stretched-img" src="" alt="Stretched Image">
                                                        </div>
                                                    </div>
                                                    <div class="form-field required">
                                                        <label for="respond-report-type"><span>* </span>Report Type</label>
                                                        <div class="custom-select-dropdown">
                                                            <div class="custom-select-dropdown-title">
                                                                <span class="custom-dropdown-default-value">Import Answer From FAQ</span>
                                                                <span class="custom-dropdown-selected-value"></span>
                                                                    </div>
                                                            <div class="custom-select-dropdown-lists">
                                                            <ul>
                                                                <li>Approved FAQ titles</li>
                                                                <li>Approved FAQ titles</li>
                                                                <li>Approved FAQ titles</li>
                                                                <li>Approved FAQ titles</li>
                                                                <li>Approved FAQ titles</li>
                                                            </ul>
                                                        </div>
                                                        <input type="hidden" name="respond-report-type" id="respond-report-type" required>
                                                        </div>
                                                    </div>
                                                    <div class="form-field">
                                                        <textarea name="respond-detail-textarea" id="respond-detail-textarea" placeholder="Typing...."></textarea>
                                                    </div>
                                                    <div class="form-buttons d-flex agqa-respond-buttons">
                                                        <div id="cancel-form-confirmation" class="cancel-form-confirmation   report-cancel-popup-confirmation">
                                                            <div class="cancel-form-confirmation-box">
                                                                <h2>Cancel</h2>
                                                                    <div class="popup-form-cross-icon"></div>
                                                                    <div class="form-message">Are you sure you want to cancel?</div>
                                                                    <div class="form-buttons agqa-popup-form-buttons d-flex">
                                                                        <button class="no-form-cancel" type="button">No</button>
                                                                        <a href="#" class="back-button">Yes</a>
                                                                    </div>
                                                            </div>       
                                                        </div>
                                                        <div id="confirm-submit-popup" class="confirm-submit-popup report-cancel-popup-confirmation">
                                                            <div class="confirm-submit-popup-box">
                                                                <h2>Submit</h2>
                                                                    <div class="popup-form-cross-icon report-cancel-icon"></div>
                                                                    <div class="form-message">Are you sure you want to submit?</div>
                                                                    <div class="form-buttons agqa-popup-form-buttons d-flex">
                                                                        <button class="no-confirm-submit" type="button">No</button>
                                                                        <input type="submit" value="Yes" id="confirm-submit">
                                                                    </div>
                                                            </div>       
                                                        </div>
                                                        <button class="back-button report-cancel-button" id="cancel-confirmation-button">Cancel</button>
                                                        <button type="button" id="respond-popup-button">Submit</button>
                                                    </div>
                                                </form>
                                            </div>
                                         </div>
                                    </div>
                                </div>
                                <div class="report-row-body">
                                    <div class="report-row-body-head">
                                        <div class="report-row-body-heading">
                                            <h2>Issue Detail</h2>
                                        </div>
                                        <div class="report-row-body-heading">
                                            <h2>Answer</h2>
                                        </div>
                                    </div>
                                    <div class="report-row-body-bottom">
                                        <div class="report-row-body-detail">
                                            <div class="report-row-body-text">
                                                <p>When I click on the game link,the page keepsloading and never displays 
                                                    the game. I have tried refreshing and using another browser, 
                                                    but the issue remains. Please help.</p>
                                            </div>
                                            <div class="report-row-body-text">
                                                <p>Thank you for your report. We have identified that the issue was caused 
                                                    by a temporary server outage. The problem has been fixed, and the game page 
                                                    should now load normally. Please clear your browser cache and try again.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="custom-table-row">
                            <div class="table-body report-row">
                                <div class="report-row-head">
                                    <div class="table-body-col">1</div>
                                    <div class="table-body-col">Functional issue / Operation not 
                                        working as expected</div>
                                    <div class="table-body-col report-status-response">
                                        <span class="no-response-needed">No Response Needed</span>
                                    </div>
                                    <div class="table-body-col">johnsonjoshua</div>
                                    <div class="table-body-col">2025/11/12</div>
                                    <div class="table-body-col">--</div>
                                    <div class="table-body-col report-action">
                                        <button class="respond-button"></button>
                                         <div class="respond-popup">
                                            <div class="respond-popup-inner">
                                                <div class="popup-form-cross-icon report-form-cancel-icon"></div>
                                                <form id="respond-form" autocomplete="off" data-inited-validation="1" novalidate="novalidate">
                                                    <div class="respond-form-title"><h2>Respond</h2></div>
                                                    <div class="form-field required">
                                                        <label for="respond-report-type"><span>* </span>Report Type</label>
                                                        <div class="custom-select-dropdown">
                                                            <div class="custom-select-dropdown-title">
                                                                <span class="custom-dropdown-default-value">Select Report Type</span>
                                                                <span class="custom-dropdown-selected-value"></span>
                                                                    </div>
                                                            <div class="custom-select-dropdown-lists">
                                                            <ul>
                                                                <li>All</li>
                                                                <li>Functional issue / Operation not working as expected</li>
                                                                <li>UI display issue</li>
                                                                <li>Incorrect data display</li>
                                                                <li>System error message</li>
                                                                <li>Process interruption / Unable to complete operation</li>
                                                                <li>Performance issue / System lag</li>
                                                                <li>Permission or account-related issue</li>
                                                                <li>Notification / Email / Task trigger issue</li>
                                                                <li>Text / Language error</li>
                                                                <li>Other (please specify)</li>
                                                            </ul>
                                                        </div>
                                                        <input type="hidden" name="respond-report-type" id="respond-report-type" required>
                                                        </div>
                                                    </div>
                                                    <div class="form-field required">
                                                        <label for="respond-report-type"><span>* </span>Report Type</label>
                                                        <div class="custom-select-dropdown">
                                                            <div class="custom-select-dropdown-title">
                                                                <span class="custom-dropdown-default-value">Select States</span>
                                                                <span class="custom-dropdown-selected-value"></span>
                                                                    </div>
                                                            <div class="custom-select-dropdown-lists">
                                                            <ul>
                                                                <li>Pending Response</li>
                                                                <li>No response needed</li>
                                                            </ul>
                                                        </div>
                                                        <input type="hidden" name="respond-report-type" id="respond-report-type" required>
                                                        </div>
                                                    </div>
                                                    <div class="form-field">
                                                        <label for="respond-disabled-textarea">Issue Detail</label>
                                                        <textarea name="respond-disabled-textarea" id="respond-disabled-textarea" placeholder="Typing...." disabled></textarea>
                                                    </div>
                                                    <div class="uploaded-images">
                                                        <div class="uploaded-image">
                                                            <img src="<?php echo AGQA_URL ?>assets/images/enlarger-image.png" alt="Report Image" class="stretchable">
                                                            <div class="stretch-image-icon"></div>
                                                        </div>
                                                        <div class="uploaded-image">
                                                            <img src="<?php echo AGQA_URL ?>assets/images/report-uploaded-image.png" alt="Report Image" class="stretchable">
                                                            <div class="stretch-image-icon"></div>
                                                        </div>
                                                        <div class="uploaded-image">
                                                            <img src="<?php echo AGQA_URL ?>assets/images/report-uploaded-image.png" alt="Report Image" class="stretchable">
                                                            <div class="stretch-image-icon"></div>
                                                        </div>
                                                        <div class="uploaded-image">
                                                            <img src="<?php echo AGQA_URL ?>assets/images/report-uploaded-image.png" alt="Report Image" class="stretchable">
                                                            <div class="stretch-image-icon"></div>
                                                        </div>
                                                        <div class="uploaded-image">
                                                            <img src="<?php echo AGQA_URL ?>assets/images/report-uploaded-image.png" alt="Report Image" class="stretchable">
                                                            <div class="stretch-image-icon"></div>
                                                        </div>
                                                    </div>
                                                    <!-- Hidden overlay for stretched image -->
                                                    <div id="stretch-overlay" class="stretch-overlay">
                                                        <div class="stretch-container">
                                                            <div class="zoom-close-icon"></div>
                                                            <img id="stretched-img" src="" alt="Stretched Image">
                                                        </div>
                                                    </div>
                                                    <div class="form-field required">
                                                        <label for="respond-report-type"><span>* </span>Report Type</label>
                                                        <div class="custom-select-dropdown">
                                                            <div class="custom-select-dropdown-title">
                                                                <span class="custom-dropdown-default-value">Import Answer From FAQ</span>
                                                                <span class="custom-dropdown-selected-value"></span>
                                                                    </div>
                                                            <div class="custom-select-dropdown-lists">
                                                            <ul>
                                                                <li>Approved FAQ titles</li>
                                                                <li>Approved FAQ titles</li>
                                                                <li>Approved FAQ titles</li>
                                                                <li>Approved FAQ titles</li>
                                                                <li>Approved FAQ titles</li>
                                                            </ul>
                                                        </div>
                                                        <input type="hidden" name="respond-report-type" id="respond-report-type" required>
                                                        </div>
                                                    </div>
                                                    <div class="form-field">
                                                        <textarea name="respond-detail-textarea" id="respond-detail-textarea" placeholder="Typing...."></textarea>
                                                    </div>
                                                    <div class="form-buttons d-flex agqa-respond-buttons">
                                                        <div id="cancel-form-confirmation" class="cancel-form-confirmation   report-cancel-popup-confirmation">
                                                            <div class="cancel-form-confirmation-box">
                                                                <h2>Cancel</h2>
                                                                    <div class="popup-form-cross-icon"></div>
                                                                    <div class="form-message">Are you sure you want to cancel?</div>
                                                                    <div class="form-buttons agqa-popup-form-buttons d-flex">
                                                                        <button class="no-form-cancel" type="button">No</button>
                                                                        <a href="#" class="back-button">Yes</a>
                                                                    </div>
                                                            </div>       
                                                        </div>
                                                        <div id="confirm-submit-popup" class="confirm-submit-popup report-cancel-popup-confirmation">
                                                            <div class="confirm-submit-popup-box">
                                                                <h2>Submit</h2>
                                                                    <div class="popup-form-cross-icon report-cancel-icon"></div>
                                                                    <div class="form-message">Are you sure you want to submit?</div>
                                                                    <div class="form-buttons agqa-popup-form-buttons d-flex">
                                                                        <button class="no-confirm-submit" type="button">No</button>
                                                                        <input type="submit" value="Yes" id="confirm-submit">
                                                                    </div>
                                                            </div>       
                                                        </div>
                                                        <button class="back-button report-cancel-button" id="cancel-confirmation-button">Cancel</button>
                                                        <button type="button" id="respond-popup-button">Submit</button>
                                                    </div>
                                                </form>
                                            </div>
                                         </div>
                                    </div>
                                </div>
                                <div class="report-row-body">
                                    <div class="report-row-body-head">
                                        <div class="report-row-body-heading">
                                            <h2>Issue Detail</h2>
                                        </div>
                                        <div class="report-row-body-heading">
                                            <h2>Answer</h2>
                                        </div>
                                    </div>
                                    <div class="report-row-body-bottom">
                                        <div class="report-row-body-detail">
                                            <div class="report-row-body-text">
                                                <p>When I click on the game link,the page keepsloading and never displays 
                                                the game. I have tried refreshing and using another browser, 
                                                but the issue remains. Please help.</p>
                                            </div>
                                            <div class="report-row-body-text">
                                                <p>--</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php

    return ob_get_clean(); // Get the content generated by the buffer and return it
}

// Register the shortcode
add_shortcode('report_system', 'report_system_shortcode');
?>
