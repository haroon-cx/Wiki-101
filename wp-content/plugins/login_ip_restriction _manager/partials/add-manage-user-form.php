<div class="manage-user-form-ctn">
    <div class="template-title">
        <h1>Manage User</h1>
    </div>
    <div class="form-header-row">
        <a href="<?php echo esc_url(home_url('/manage-user')) ?>" class="back-button" type="button">
            <img decoding="async" src="<?php echo AGQA_URL ?>assets/images/arrow-left.svg" alt="Arrow Left Icon">
            Back
        </a>
        <h2 class="form-heading">Add User</h2>
    </div>
    <div class="faq-add-form-ctn manage-user-form-ctn">
        <div id="manage-user-add-form">
            <form autocomplete="off" id="cuim-add-form-user-man" class="custom-form" novalidate="novalidate"
                data-inited-validation="1">
                <div class="form-field required">
                    <label for="account-field"><span>* </span>Account</label>
                    <input type="text" name="account" id="account-field" required placeholder="Description">
                    <div id="error-message"></div>
                </div>
                <div class="form-field required">
                    <label for="new-password-field"><span>* </span>New Password</label>
                    <button class="toggle-password"></button>
                    <input type="password" name="new-password" id="new-password-field" required
                        placeholder="Description">


                </div>
                <div class="form-field required">
                    <label for="confirm-password-field"><span>* </span>Confirm Password</label>
                    <button class="toggle-password"></button>
                    <input type="password" name="confirm-password" id="confirm-password-field" required
                        placeholder="Description">
                </div>
                <div class="form-field required">
                    <label for="issue_type"><span>* </span>State</label>
                    <input type="hidden" name="state" value="pending">
                    <select name="state" id="manage-user-state" required disabled>
                        <option value="Pending">Pending</option>
                        <option value="Active">Active</option>
                        <option value="Inactive">Inactive</option>
                        <option value="Freeze">Freeze</option>
                    </select>
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
                                <li data-value="Admin">Admin</li>
                                <li data-value="Manager">Manager</li>
                                <li data-value="Contributor">Contributor</li>
                                <li data-value="Viewer">Viewer</li>
                            </ul>
                        </div>
                        <input type="hidden" name="user-role" id="issue_type" required="">
                    </div>
                </div>
                <div class="form-field required">
                    <label for="comapany-name-field"><span>* </span>Company Name</label>
                    <input type="text" name="company-name" id="comapany-name-field" required placeholder="Description">
                </div>
                <div class="form-field required">
                    <label for="manage-user-email-field"><span>* </span>Email</label>
                    <input type="email" name="email" id="email-field" required placeholder="Description">
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
                                    <input type="text" name="first-name" id="first-name" placeholder="Description">
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
                                <button type="submit" value="Yes" id="confirm-submit">Yes</button>
                            </div>
                        </div>
                    </div>
                    <input type="submit" value="Submit" class="manage-user-submit-btn" id="confirm-submit-popup-button">
                </div>
            </form>
        </div>
    </div>
</div>