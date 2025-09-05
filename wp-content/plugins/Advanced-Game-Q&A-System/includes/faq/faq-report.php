<div class="agqa-popup-form agqa-report-popup-form">
    <div class="agqa-popup-form-inner">
        <div class="popup-form-cross-icon"></div>
       <form id="report_form" autocomplete="off" class="custom-form" data-inited-validation="1" novalidate="novalidate">
            <!-- Add Category Form Fields -->
            <div class="agqa-popup-form-title">
                <h2>Report</h2>
            </div>
            <!-- <div class="agqa-popup-form-field">
                <label for="issue_type">What problem did you encounter?</label>
                <select name="issue_type" id="issue_type">
                    <option value="">Select Role</option>
                    <option value="functional">Functional issue / Operation not working as expected</option>
                    <option value="ui">UI display issue</option>
                    <option value="incorrect-data">Incorrect data display</option>
                    <option value="system-error">System error message</option>
                    <option value="process-interruption">Process interruption / Unable to complete operation</option>
                    <option value="performance">Performance issue / System lag</option>
                    <option value="permission">Permission or account-related issue</option>
                    <option value="notification">Notification / Email / Task trigger issue</option>
                    <option value="text-error">Text / Language error</option>
                    <option value="other">Other</option>
                </select>
            </div> -->
              <div class="agqa-popup-form-field required">
                 <label for="issue_type"><span>* </span>What problem did you encounter?</label>
                <div class="custom-select-dropdown">
                    <div class="custom-select-dropdown-title">
                        <span class="custom-dropdown-default-value">Select Role</span>
                        <span class="custom-dropdown-selected-value"></span>
                            </div>
                    <div class="custom-select-dropdown-lists">
                    <ul>
                        <li data-value="functional">Functional issue / Operation not working as expected</li>
                        <li data-value="ui">UI display issue</li>
                        <li data-value="incorrect-data">Incorrect data display</li>
                        <li data-value="system-error">System error message</li>
                        <li data-value="process-interruption">Process interruption / Unable to complete operation</li>
                        <li data-value="performance">Performance issue / System lag</li>
                        <li data-value="permission">Permission or account-related issue</li>
                        <li data-value="notification">Notification / Email / Task trigger issue</li>
                        <li data-value="text-error">Text / Language error</li>
                        <li data-value="other">Other</li>
                    </ul>
                </div>
                <input type="hidden" name="issue_type" id="issue_type" required>
                 </div>
            </div>
            <div class="agqa-popup-form-field required">
                <label for="detail-description"><span>* </span>Detailed Description</label>
                <textarea name="detail-description" id="detail-description" placeholder="Typing...." required></textarea>
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
                <input type="file"
                    id="report-upload-input"
                    accept="image/jpeg"
                    multiple
                    style="display: none;">
                <input type="hidden" name="report-upload-files" class="report-upload-files">
            </div>
            <div class="agqa-popup-form-field agqa-popup-form-buttons d-flex">
                <input type="submit" value="Sent">
            </div>
        </form>
    </div>
</div>