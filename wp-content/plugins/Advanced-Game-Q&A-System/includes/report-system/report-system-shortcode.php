<?php
// Function to generate the report system content
function report_system_shortcode() {
    
    
    $add_faq_report  = isset($_GET['add']) ? intval($_GET['add']) : 0;
    // echo $edit_faq_review;


    global $wpdb;
    $table_agqa_faq = $wpdb->prefix . 'agqa_faq';
    $table_agqa_report_system = $wpdb->prefix . 'faq_report_system';


    if ($add_faq_report == 0 ) {
        $report_system_data = $wpdb->get_results("
            SELECT
                id,
                user_id,
                report_type,
                status,
                issue_detail,
                issue_detail_reply,
                upload_attachments,
                answer,
                reporter,
                reply_time,
                create_time
            FROM $table_agqa_report_system
            ORDER BY id DESC
        ");

            $faq_data = $wpdb->get_results("
            SELECT
                id,
                question,
                answer,
                faq_category,
                verified_answer
            FROM $table_agqa_faq
            ORDER BY id DESC
        ");
    }

    ob_start(); // Start output buffering
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
                <input type="search" name="filter-search" id="report-filter-search" class="agqa-report-validation-100"
                    placeholder="Please Enter">
                <div class="filter-select">
                    <input type="hidden" name="filter-report-states" class="agqa-filter-select-hidden">
                    <button class="filter-select-title agqa-report-select-filter-button">
                        <span class="filter-default-text">Select States</span>
                        <span class="filter-selected-text report-filter-selected-text"></span>
                    </button>
                    <div class="filter-select-list agqa-report-cat-filter">
                        <ul>
                            <li>Pending Response</li>
                            <li>Responded</li>
                            <li>No response required</li>
                        </ul>
                    </div>
                </div>
                <button type="submit" class="filter-select-button"
                    id="agqa-report-system-filter"><span>Search</span></button>
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
                    <?php foreach ($report_system_data as $report_value) { ?>
                    <div class="custom-table-row">
                        <div class="table-body report-row">
                            <div class="report-row-head">
                                <div class="table-body-col">1</div>
                                <div class="table-body-col"><?php echo $report_value->report_type; ?></div>
                                <div class="table-body-col report-status-response">
                                    <span
                                        class="<?php echo str_replace(' ', '-', strtolower($report_value->status)); ?><?php if($report_value->status == 'Responded') echo '-status'; ?>">
                                        <?php echo $report_value->status; ?>
                                    </span>


                                </div>
                                <div class=" table-body-col"><?php echo $report_value->reporter ?>
                                </div>
                                <div class="table-body-col">
                                    <?php echo date('Y/m/d', strtotime($report_value->create_time)); ?>
                                </div>
                                <div class="table-body-col"><?php echo $report_value->reply_time ?></div>
                                <?php if($report_value->status === 'Pending Response'){ ?>
                                <div class="table-body-col report-action">
                                    <button class="respond-button pending-response-button"></button>
                                    <div class="respond-popup">
                                        <div class="respond-popup-inner">
                                            <div class="popup-form-cross-icon report-form-cancel-icon"></div>
                                            <form class="agqa-report-system-form" autocomplete="off"
                                                data-inited-validation="1" novalidate="novalidate">
                                                <div class="respond-form-title">
                                                    <h2>Respond</h2>
                                                </div>
                                                <div class="form-input-fields">
                                                    <div class="form-field required">
                                                        <label for="respond-report-type"><span>* </span>Report
                                                            Type</label>
                                                        <div class="custom-select-dropdown">
                                                            <div class="custom-select-dropdown-title"
                                                                style="pointer-events: none;">
                                                                <span
                                                                    class="custom-dropdown-default-value"><?php echo $report_value->report_type; ?></span>
                                                                <span class="custom-dropdown-selected-value"></span>
                                                            </div>
                                                            <input type="hidden" name="respond-report-type" required>
                                                        </div>
                                                    </div>
                                                    <div class="form-field required">
                                                        <label for="respond-status-type"><span>* </span>State</label>
                                                        <div class="custom-select-dropdown">
                                                            <div class="custom-select-dropdown-title">
                                                                <span class="custom-dropdown-default-value">Select
                                                                    States</span>
                                                                <span class="custom-dropdown-selected-value"></span>
                                                            </div>
                                                            <div class="custom-select-dropdown-lists">
                                                                <ul>
                                                                    <li data-value="Pending Response">Pending Response
                                                                    </li>
                                                                    <li data-value="No response needed">No response
                                                                        needed</li>
                                                                </ul>
                                                            </div>
                                                            <input type="hidden" name="respond-status-type" required>
                                                        </div>
                                                    </div>
                                                    <div class="form-field">
                                                        <label for="respond-disabled-textarea">Issue Detail</label>
                                                        <textarea name="respond-disabled-textarea"
                                                            class="respond-disabled-textarea"
                                                            disabled><?php echo $report_value->issue_detail; ?></textarea>
                                                    </div>
                                                    <div class="uploaded-images">
                                                        <span class="upload-image-label">Upload attachments</span>
                                                        <div class="uploaded-images-inner">
                                                            <?php
                                                                $reportUrl = $report_value->upload_attachments; 
                                                                        $reportUrl = explode(",", $reportUrl);
                                                                        foreach ($reportUrl as $url) {
                                                                        ?>

                                                            <div class="uploaded-image">
                                                                <img src="<?php echo  $url;?>" alt=" Report Image"
                                                                    class="stretchable">
                                                                <div class="stretch-image-icon"></div>
                                                            </div>
                                                            <?php } ?>
                                                        </div>
                                                    </div>
                                                    <!-- Hidden overlay for stretched image -->
                                                    <div id="stretch-overlay" class="stretch-overlay">
                                                        <div class="stretch-container">
                                                            <div class="zoom-close-icon"></div>
                                                            <img class="stretched-img" src="" alt="Stretched Image">
                                                        </div>
                                                    </div>
                                                    <div class="form-field">
                                                        <label for="respond-answer">Answer</label>
                                                        <div class="custom-select-dropdown">
                                                            <div class="custom-select-dropdown-title">
                                                                <span class="custom-dropdown-default-value">Import
                                                                    Answer From FAQ</span>
                                                                <span class="custom-dropdown-selected-value"></span>
                                                            </div>
                                                            <div class="custom-select-dropdown-lists">
                                                                <ul>
                                                                    <?php foreach ($faq_data as $faq_value) { ?>
                                                                    <li class="faq-item"
                                                                        data-faq-id="<?php echo esc_attr($faq_value->id); ?>">
                                                                        <?php echo esc_html($faq_value->question); ?>
                                                                    </li>
                                                                    <?php } ?>
                                                                </ul>
                                                            </div>
                                                            <input type="hidden" name="respond-answer"
                                                                class="respond-answer">
                                                        </div>
                                                    </div>
                                                    <div class="form-field">
                                                        <textarea name="respond-detail-textarea"
                                                            class="respond-detail-textarea"
                                                            placeholder="Typing...."></textarea>
                                                    </div>
                                                </div>
                                                <div class="form-buttons d-flex agqa-respond-buttons">
                                                    <div id="cancel-form-confirmation"
                                                        class="cancel-form-confirmation   report-cancel-popup-confirmation">
                                                        <div class="cancel-form-confirmation-box">
                                                            <h2>Cancel</h2>
                                                            <div class="popup-form-cross-icon"></div>
                                                            <div class="form-message">Are you sure you want to cancel?
                                                            </div>
                                                            <div class="form-buttons agqa-popup-form-buttons d-flex">
                                                                <button class="no-form-cancel" type="button">No</button>
                                                                <a href="#" class="back-button">Yes</a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div id="confirm-submit-popup"
                                                        class="confirm-submit-popup report-cancel-popup-confirmation">
                                                        <div class="confirm-submit-popup-box">
                                                            <h2>Submit</h2>
                                                            <div class="popup-form-cross-icon report-cancel-icon"></div>
                                                            <div class="form-message">Are you sure you want to submit?
                                                            </div>
                                                            <div class="form-buttons agqa-popup-form-buttons d-flex">
                                                                <button class="no-confirm-submit"
                                                                    type="button">No</button>
                                                                <input type="submit" value="Yes" id="confirm-submit">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <button class="back-button report-cancel-button"
                                                        id="cancel-confirmation-button">Cancel</button>
                                                    <button type="submit">Submit</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <?php } else { ?>
                                <!-- 2nd popup -->
                                <div class="table-body-col report-action">
                                    <button class="respond-button responded-button"></button>
                                    <div class="respond-popup">
                                        <div class="respond-popup-inner">
                                            <div class="popup-form-cross-icon report-form-cancel-icon"></div>
                                            <form class="respond-form-2" autocomplete="off" data-inited-validation="1"
                                                novalidate="novalidate">
                                                <div class="respond-form-title">
                                                    <h2>Respond</h2>
                                                </div>
                                                <div class="form-input-fields">
                                                    <div class="form-field disabled-field">
                                                        <label for="respond-report-type">Report Type</label>
                                                        <div class="custom-select-dropdown">
                                                            <div class="custom-select-dropdown-title">
                                                                <span
                                                                    class="custom-dropdown-default-value"><?php echo $report_value->report_type; ?></span>
                                                                <span class="custom-dropdown-selected-value"></span>
                                                            </div>
                                                            <div class="custom-select-dropdown-lists">
                                                                <ul>
                                                                    <li>All</li>
                                                                    <li>Functional issue / Operation not working as
                                                                        expected</li>
                                                                    <li>UI display issue</li>
                                                                    <li>Incorrect data display</li>
                                                                    <li>System error message</li>
                                                                    <li>Process interruption / Unable to complete
                                                                        operation</li>
                                                                    <li>Performance issue / System lag</li>
                                                                    <li>Permission or account-related issue</li>
                                                                    <li>Notification / Email / Task trigger issue</li>
                                                                    <li>Text / Language error</li>
                                                                    <li>Other (please specify)</li>
                                                                </ul>
                                                            </div>
                                                            <input type="hidden" name="respond-report-type">
                                                        </div>
                                                    </div>
                                                    <div class="form-field disabled-field">
                                                        <label for="respond-status-type">Status</label>
                                                        <div class="custom-select-dropdown">
                                                            <div class="custom-select-dropdown-title">
                                                                <span
                                                                    class="custom-dropdown-default-value"><?php echo $report_value->status; ?></span>
                                                                <span class="custom-dropdown-selected-value"></span>
                                                            </div>
                                                            <div class="custom-select-dropdown-lists">
                                                                <ul>
                                                                    <li>Pending Response</li>
                                                                    <li>No response needed</li>
                                                                </ul>
                                                            </div>
                                                            <input type="hidden" name="respond-status-type">
                                                        </div>
                                                    </div>
                                                    <div class="form-field">
                                                        <label for="respond-disabled-textarea">Issue Detail</label>
                                                        <textarea name="respond-disabled-textarea"
                                                            class="respond-disabled-textarea" placeholder="Typing...."
                                                            disabled><?php echo $report_value->issue_detail; ?></textarea>
                                                    </div>
                                                    <?php
                                                        $reportUrl = $report_value->upload_attachments; 
                                                                $reportUrl = explode(",", $reportUrl);
                                                                foreach ($reportUrl as $url) {
                                                                ?>
                                                    <div class="uploaded-images">
                                                        <span class="upload-image-label">Upload attachments</span>
                                                        <div class="uploaded-images-inner">
                                                            <div class="uploaded-image">
                                                                <img src="<?php echo  $url;?>" alt="Report Image"
                                                                    class="stretchable">
                                                                <div class="stretch-image-icon"></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <?php } ?>
                                                    <!-- Hidden overlay for stretched image -->
                                                    <div id="stretch-overlay" class="stretch-overlay">
                                                        <div class="stretch-container">
                                                            <div class="zoom-close-icon"></div>
                                                            <img class="stretched-img" src="" alt="Stretched Image">
                                                        </div>
                                                    </div>
                                                    <div class="form-field disabled-field">
                                                        <label for="respond-answer">Answer</label>
                                                        <div class="custom-select-dropdown">
                                                            <div class="custom-select-dropdown-title">
                                                                <span class="custom-dropdown-default-value">
                                                                    <?php echo esc_html($faq_value->question); ?></span>
                                                                <span class="custom-dropdown-selected-value"></span>
                                                            </div>
                                                            <div class="custom-select-dropdown-lists">
                                                                <ul>
                                                                    <?php foreach ($faq_data as $faq_value) { ?>
                                                                    <li class="faq-item"
                                                                        data-faq-id="<?php echo esc_attr($faq_value->id); ?>">
                                                                        <?php echo esc_html($faq_value->question); ?>
                                                                    </li>
                                                                    <?php } ?>
                                                                </ul>
                                                            </div>
                                                            <input type="hidden" name="respond-answer"
                                                                class="respond-answer">
                                                        </div>
                                                    </div>
                                                    <div class="form-field">
                                                        <textarea name="respond-detail-textarea"
                                                            class="respond-detail-textarea" placeholder="Typing...."
                                                            disabled><?php echo $report_value->issue_detail_reply ?></textarea>
                                                    </div>
                                                </div>
                                                <div class="form-buttons d-flex agqa-respond-buttons">
                                                    <div class="approval-info">
                                                        <span class="approval-time">2025/07/22 14:35</span>
                                                        <span class="approavl-account">heather01</span>
                                                    </div>
                                                    <button class="report-close-button">Close</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <?php } ?>
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
                                            <p><?php echo $report_value->issue_detail; ?>
                                            </p>
                                        </div>
                                        <div class="report-row-body-text">
                                            <p><?php echo $report_value->issue_detail_reply; ?></p>
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

<?php

    return ob_get_clean(); // Get the content generated by the buffer and return it
}

// Register the shortcode
add_shortcode('report_system', 'report_system_shortcode');
?>