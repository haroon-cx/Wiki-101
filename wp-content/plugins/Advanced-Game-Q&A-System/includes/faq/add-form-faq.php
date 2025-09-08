<div class="faq-form-ctn">
    <div class="template-title">
        <h1>FAQ</h1>
    </div>
    <div class="form-header-row">
        <a href="<?php echo esc_url(home_url('/faq')) ?>" class="back-button" type="button">
            <img decoding="async" src="<?php echo AGQA_URL ?>assets/images/arrow-left.svg" alt="Arrow Left Icon">
            Back
        </a>
        <h2 class="form-heading">Add FAQ</h2>
    </div>
    <div class="faq-add-form-ctn">
        <div id="faq-add-form" class="faq-form">
            <form autocomplete="off" id="add-form-faq" class="custom-form" novalidate="novalidate"
                data-inited-validation="1">
                <div class="form-field required">
                    <label for="faq-title"><span>* </span>Title</label>
                    <input type="text" name="faq-question" id="faq-title" required placeholder="Description">
                </div>
                <div class="form-field required">
                    <label for="question-type"><span>* </span>Question Type</label>
                    <div class="custom-select-dropdown">
                        <div class="custom-select-dropdown-title">
                            <span class="custom-dropdown-default-value">Select Role</span>
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
                        <input type="hidden" name="faq-category" id="issue_type" required="">
                    </div>
                </div>
                <div class="form-field required">
                    <label for="editor-add-faq"><span>* </span>Details</label>
                    <div class="form-field-editor">
                        <textarea name="faq-answer" id="editor-add-faq" class="editor-faq" required
                            placeholder="Please enter a note"></textarea>
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
                            <h2>Submit</h2>
                            <div class="popup-form-cross-icon"></div>
                            <div class="form-message">Are you sure you want to submit?</div>
                            <div class="form-buttons agqa-popup-form-buttons d-flex">
                                <button class="no-confirm-submit" type="button">No</button>
                                <input type="submit" value="Yes" id="confirm-submit">
                            </div>
                        </div>
                    </div>

                    <input type="submit" value="Submit" class="agqa-edit-submit-btn" id="confirm-submit-popup-button">
                </div>
            </form>
        </div>
    </div>
</div>
<script>
document.querySelectorAll('.editor-faq').forEach(function(el) {
    // Initialize Froala Editor
    var editor = new FroalaEditor(el, {
        // Toolbar setup
        toolbarButtons: {
            moreText: {
                buttons: [
                    'bold', 'italic', 'underline', 'strikeThrough',
                    'fontFamily', 'fontSize', 'color', 'paragraphFormat', 'align',
                    'formatOL', 'formatUL', 'outdent', 'indent', 'clearFormatting'
                ]
            },
            moreRich: {
                buttons: [
                    'insertLink' // ✅ allow links only
                ]
            },
            moreMisc: {
                buttons: ['undo', 'redo', 'fullscreen', 'html']
            }
        },

        // Disable uploads
        imageUpload: false,
        videoUpload: false,
        fileUpload: false,

        // Ensure editor re-initializes after pasting
        events: {
            'paste.after': function() {
                // Reset toolbar or any custom actions to avoid the toolbars from hiding
                setTimeout(() => {
                    editor.refresh();
                }, 100);
            }
        }
    });

    // Manually refresh the editor after every paste event to prevent hiding
    el.addEventListener('paste', function() {
        setTimeout(function() {
            editor.refresh(); // Refresh the editor to show tools again
        }, 100); // Give a small delay to allow paste operation to complete
    });
});
</script>