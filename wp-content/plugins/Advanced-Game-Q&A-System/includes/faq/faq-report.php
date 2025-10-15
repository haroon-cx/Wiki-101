<?php ?>
<div class="agqa-popup-form agqa-report-popup-form">
    <div class="agqa-popup-form-inner">
        <div class="popup-form-cross-icon"></div>
        <form id="faq_report_form" autocomplete="off" class="custom-form" data-inited-validation="1"
            novalidate="novalidate">
            <!-- Add Category Form Fields -->
            <div class="agqa-popup-form-title">
                <h2>Report</h2>
            </div>
            <div class="agqa-popup-form-field required">
                <label for="issue_type"><span>* </span>What problem did you encounter?</label>
                <div class="custom-select-dropdown">
                    <div class="custom-select-dropdown-title">
                        <span class="custom-dropdown-default-value">Select Role</span>
                        <span class="custom-dropdown-selected-value"></span>
                    </div>
                    <div class="custom-select-dropdown-lists">
                        <ul>
                            <li data-value="Functional issue / Operation not working as expected">Functional issue /
                                Operation not working as expected</li>
                            <li data-value="UI display issue">UI display issue</li>
                            <li data-value="Incorrect data display">Incorrect data display</li>
                            <li data-value="System error message">System error message</li>
                            <li data-value="Process interruption / Unable to complete operation">Process interruption /
                                Unable to complete operation
                            </li>
                            <li data-value="Performance issue / System lag">Performance issue / System lag</li>
                            <li data-value="Permission or account-related issue">Permission or account-related issue
                            </li>
                            <li data-value="Notification / Email / Task trigger issue">Notification / Email / Task
                                trigger issue</li>
                            <li data-value="Text / Language error">Text / Language error</li>
                            <li data-value="other">Other</li>
                        </ul>
                    </div>
                    <input type="hidden" name="faq-report-type" id="issue_type" required>
                </div>
            </div>
            <div class="agqa-popup-form-field required">
                <label for="detail-description"><span>* </span>Detailed Description</label>
                <textarea name="faq-report-answer" maxlength="1000" id="detail-description" placeholder="Typing...." required></textarea>
            </div>
            <div class="agqa-popup-form-field report-upload-field">
                <label for="report-upload-input">Upload Attachments</label>
                <div class="report-upload-area">
                    <div class="report-browse-link">
                        <img src="<?php echo AGQA_URL ?>assets/images/plus-gray-icon.svg" alt="Plus Icon">
                        <span>Upload</span>
                    </div>
                    <div class="report-file-preview" style="display: none;"></div>
                </div>
                <input type="file" id="report-upload-input" accept="image/jpeg" multiple style="display: none;">
                <input type="hidden" name="report-upload-files" class="report-upload-files">
            </div>
            <div class="agqa-popup-form-field agqa-popup-form-buttons d-flex">
                <input type="submit" value="Sent" id="faq-report-system">
            </div>
        </form>
    </div>
</div>