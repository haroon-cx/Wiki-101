
<div class="manage-user-form-ctn">
    <div class="template-title">
        <h1>Manage User</h1>
    </div>
    <div class="form-header-row">
        <a href="<?php echo esc_url(home_url('/manage-user')) ?>" class="back-button" type="button">
            <img decoding="async" src="<?php echo AGQA_URL ?>assets/images/arrow-left.svg" alt="Arrow Left Icon">
            Back
        </a>
        <h2 class="form-heading">Edit User</h2>
    </div>
    <div class="faq-add-form-ctn manage-user-form-ctn">
        <div id="manage-user-add-form">
            <form autocomplete="off" id="add-form-faq" class="custom-form" novalidate="novalidate"
                data-inited-validation="1">
                <div class="form-field required">
                    <label for="account-field"><span>* </span>Account</label>
                    <input type="text" name="faq-question" id="account-field" required placeholder="Description" disabled>
                    <div id="error-message"></div>
                </div>
                <div class="form-field required">
                    <label for="reset-password"><span>* </span>Reset Password</label>
                    <div class="generate-password-button">
                        Generate New Password
                    </div>
                    <div id="reset-password-confirmation" class="reset-password-confirmation" style="">
                        <div class="reset-password-confirmation-box">
                            <h2>Reset Password</h2>
                            <div class="popup-form-cross-icon"></div>
                            <div class="form-message">Are you sure you want to reset your password?</div>
                            <div class="form-buttons agqa-popup-form-buttons d-flex">
                                <button class="no-form-cancel" type="button">No</button>
                                <a href="<?php echo esc_url(home_url('/edit-manage-user-form')) ?>" class="back-button">Yes</a>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="reset-password" id="reset-password">
                </div>
                <div class="form-field required">
                    <label for="manage-user-state"><span>* </span>State</label>
                    <div class="custom-select-dropdown">
                        <div class="custom-select-dropdown-title">
                            <span class="custom-dropdown-default-value">Select Role</span>
                            <span class="custom-dropdown-selected-value"></span>
                        </div>
                        <div class="custom-select-dropdown-lists">
                            <ul>
                                <li data-value="Active">Active</li>
                                <li data-value="Inactive">Inactive</li>
                                <li data-value="Freeze">Freeze</li>
                            </ul>
                        </div>
                        <input type="hidden" name="manage-user-state" id="manage-user-state" required="">
                    </div>
                </div>
                <div class="form-field required">
                    <label for="question-type"><span>* </span>User Role </label>
                    <div class="custom-select-dropdown">
                        <div class="custom-select-dropdown-title">
                            <span class="custom-dropdown-default-value">Select Role</span>
                            <span class="custom-dropdown-selected-value"></span>
                        </div>
                        <div class="custom-select-dropdown-lists">
                            <ul>
                                <li>All</li>
                                <li data-value="Admin">Admin</li>
                                <li data-value="Manager">Manager</li>
                                <li data-value="Contributor">Contributor</li>
                                <li data-value="Viewer">Viewer</li>
                            </ul>
                        </div>
                        <input type="hidden" name="faq-category" id="issue_type" required="">
                    </div>
                </div>
                <div class="form-field required">
                    <label for="comapany-name-field"><span>* </span>Company Name</label>
                    <input type="text" name="faq-question" id="comapany-name-field" required placeholder="Description">
                </div>
                <div class="form-field required">
                    <label for="manage-user-email-field"><span>* </span>Email</label>
                    <input type="text" name="faq-question" id="manage-user-email-field" required placeholder="Description">
                    <!-- <div class="reset-link">
                        <a href="#">Reset Link</a>
                    </div> -->
                </div>
                <div class="form-field">
                    <div class="add-custom-field-ctn">
                        <div id="custom-field-popup" style="display:none;">

                            <!-- Add Field Popup -->
                            <div id="custom-field-popup-inner" class="popup-content add-field">
                                <h2>Add Custom Field</h2>
                                <div class="popup-form-cross-icon"></div>
                                <div class="form-field">
                                    <label for="field-name">Field Name</label>
                                    <input type="text" name="field-name" id="field-name" placeholder="Description">
                                </div>
                                <div class="form-buttons agqa-popup-form-buttons d-flex">
                                    <button class="cancel-button" type="button">Cancel</button>
                                    <input id="save-custom-field" type="submit" value="Save">
                                </div>
                            </div>

                            <!-- Submit Confirmation Popup -->
                            <div id="custom-field-popup-inner" class="popup-content submit-confirm"
                                style="display:none;">
                                <h2>Submit</h2>
                                <div class="popup-form-cross-icon"></div>
                                <div class="form-message">Are you sure you want to submit?</div>
                                <div class="form-buttons agqa-popup-form-buttons d-flex">
                                    <button class="no-submit" type="button">No</button>
                                    <input type="submit" value="Yes" class="yes-submit">
                                </div>
                            </div>

                            <!-- Cancel Confirmation Popup -->
                            <div id="custom-field-popup-inner" class="popup-content cancel-confirm"
                                style="display:none;">
                                <h2>Cancel</h2>
                                <div class="popup-form-cross-icon"></div>
                                <div class="form-message">Are you sure you want to cancel?</div>
                                <div class="form-buttons agqa-popup-form-buttons d-flex">
                                    <button class="no-cancel" type="button">No</button>
                                    <input id="yes-cancel" type="submit" value="Yes">
                                </div>
                            </div>
                        </div>
                        <button id="add-custom-field-btn" type="button">
                            <img src="<?php echo AGQA_URL ?>assets/images/plus-icon.svg" alt="Plus Icon"> Add
                            Custom Field
                        </button>
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
                                <a href="<?php echo esc_url(home_url('/manage-user')) ?>" class="back-button">Yes</a>
                            </div>
                        </div>
                    </div>
                    <a href="<?php echo esc_url(home_url('/manage-user')) ?>" class="back-button"
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
                    <input type="submit" value="Submit" class="manage-user-submit-btn" id="confirm-submit-popup-button">
                </div>
            </form>
        </div>
    </div>
</div>
