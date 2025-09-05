<?php
$table_agqa_faq_review = $wpdb->prefix . 'agqa_faq_review';

if ($edit_faq_review !== 0) {
    $faq_data_review = $wpdb->get_results($wpdb->prepare("
        SELECT
            id,
            faq_id,
            question,
            answer,
            faq_category,
            verified_answer
        FROM $table_agqa_faq_review
        WHERE id = %d
        ORDER BY id DESC
    ", $edit_faq_review));
}

?>

<div class="faq-form-ctn">
    <div class="template-title">
        <h1>FAQ</h1>
    </div>
    <div class="form-header-row">
        <a href="<?php echo esc_url(home_url('/faq/?review=1')) ?>" class="back-button" type="button">
            <img decoding="async" src="<?php echo AGQA_URL ?>assets/images/arrow-left.svg" alt="Arrow Left Icon">
            Back
        </a>
        <h2 class="form-heading">Edit FAQ</h2>
    </div>
    <div class="faq-add-form-ctn">
        <div id="faq-add-form" class="faq-form">
            <?php if (!empty($faq_data_review)) {
                foreach ($faq_data_review as $faq_value) { ?>
            <form autocomplete="off" id="edit-form-faq-review" class="custom-form" novalidate="novalidate"
                data-inited-validation="1">
                <div class="form-field required">
                    <input type="hidden" name="faq-id" value="<?php echo $faq_value->faq_id; ?>">
                    <input type="hidden" name="status" value="approve">
                    <input type="hidden" name="review-id" value="<?php echo $edit_faq_review ?>">
                    <label for="faq-title"><span>* </span>Title</label>
                    <input type="text" name="faq-question" id="faq-title" required placeholder="Description"
                        value="<?php echo $faq_value->question; ?>">
                </div>
                <div class="form-field required">
                    <label for="question-type"><span>* </span>Question Type</label>
                    <div class="custom-select-dropdown">
                        <div class="custom-select-dropdown-title">
                            <span class="custom-dropdown-default-value">
                                <?php if (!empty($faq_value->faq_category)) { ?>
                                <?php echo $faq_value->faq_category; ?>
                                <?php } else { ?>
                                Select Role
                                <?php } ?>
                            </span>
                            <span class="custom-dropdown-selected-value"></span>
                        </div>
                        <div class="custom-select-dropdown-lists">
                            <ul>
                                <li>All</li>
                                <li data-value="Account & Access Management">Account & Access Management</li>
                                <li data-value="System Features & Workflow">System Features & Workflow</li>
                                <li data-value="Reports & Data Queries">Reports & Data Queries</li>
                                <li data-value="Errors & Troubleshooting">Errors & Troubleshooting</li>
                                <li data-value="Notifications & Alerts">Notifications & Alerts</li>
                                <li data-value="Performance & Compatibility">Performance & Compatibility</li>
                                <li data-value="System Settings & Customization">System Settings & Customization</li>
                                <li data-value="Customer Support & Contact">Customer Support & Contact</li>
                                <li data-value="Other">Other</li>
                            </ul>
                        </div>
                        <input type="hidden" name="faq-category" id="issue_type" required=""
                            value="<?php echo $faq_value->faq_category; ?>">
                    </div>
                </div>
                <div class="form-field required">
                    <label for="editor-add-faq"><span>* </span>Details</label>
                    <div class="form-field-editor">
                        <textarea name="faq-answer" id="editor-add-faq" class="editor-faq" required
                            placeholder="Please enter a note"><?php echo $faq_value->answer; ?></textarea>
                        <div class="char-counter">
                            <span class="current-count">0</span> / 3000
                        </div>
                    </div>
                </div>
                <div class="form-buttons agqa-popup-form-buttons d-flex full-width agqa-add-update-btn">
                    <div id="cancel-form-confirmation" class="cancel-form-confirmation" style="">
                        <div class="cancel-form-confirmation-box">
                            <h2>Cancel</h2>
                            <div class="popup-form-cross-icon"></div>
                            <div class="form-message">Are you sure you want to cancel?</div>
                            <div class="form-buttons agqa-popup-form-buttons d-flex">
                                <button class="no-form-cancel" type="button">No</button>
                                <a href="<?php echo esc_url(home_url('/faq')) ?>" class="back-button">Yes</a>
                            </div>
                        </div>
                    </div>
                    <a href="<?php echo esc_url(home_url('/faq')) ?>" class="back-button"
                        id="cancel-confirmation-button">Cancel</a>

                    <div id="confirm-submit-popup" class="confirm-submit-popup">
                        <div class="confirm-submit-popup-box">
                            <h2>Approve</h2>
                            <div class="popup-form-cross-icon"></div>
                            <div class="form-message">Are you sure you want to submit?</div>
                            <div class="form-buttons agqa-popup-form-buttons d-flex">
                                <button class="no-confirm-submit" type="button">No</button>
                                <input type="submit" value="Submit" id="confirm-submit">
                            </div>
                        </div>
                    </div>
                    <button class="reject-button">Reject</button>
                    <input type="submit" value="Approve" class="agqa-edit-submit-btn" id="confirm-submit-popup-button">
                </div>
            </form>
            <?php }
            } ?>
        </div>
    </div>
</div>