<?php 

?>


<div class="add-manage-ip-form">
    <div class="add-manage-ip-form-inner">
        <div class="popup-form-cross-icon manage-ip-cross-icon"></div>
            <div class="popup-form-title">
                <h2>Add New IP</h2>
            </div>
            <form action="#" id="add-ip-from" autocomplete="off" data-inited-validation="1">
                <div class="form-field full-width">
                    <label for="manage-ip-account-field">Account</label>
                    <input type="text" name="account-name" id="manage-ip-account-field" placeholder="Description" required>
                </div>
                <div class="error-message account-error"></div>
                <div class="form-field full-width">
                    <label for="manage-ip-ipv4-field">IPv4</label>
                    <input type="text" name="ip-ipv4" class="manage-ip-ipv4-field" placeholder="Description">
                    <div class="error-message ip-error ipv4-error"></div>
                </div>
                <div class="form-field full-width">
                    <label for="manage-ip-ipv6-field">IPv6</label>
                    <input type="text" name="ip-ipv6" class="manage-ip-ipv6-field" placeholder="Description">
                    <div class="error-message ip-error ipv6-error"></div>
                </div>
                <div id="add-ip-form-buttons" class="form-buttons manage-ip-form-buttons d-flex">
                    <button class="cancel-button" type="button">Cancel</button>
                    <div class="cancel-form-confirmation">
                        <div class="cancel-form-confirmation-box">
                            <h2>Cancel</h2>
                            <div class="popup-form-cross-icon"></div>
                            <div class="form-message">Are you sure you want to cancel?</div>
                            <div class="form-buttons agqa-popup-form-buttons d-flex">
                                <button class="no-form-cancel" type="button">No</button>
                                <a href="http://wiki-101.local/manage-user" class="back-button">Yes</a>
                            </div>
                        </div>
                    </div>
                    <button id="add-ip-btn" type="button" class="">Submit</button>
                    <div class="confirm-submit-popup">
                        <div class="confirm-submit-popup-box">
                            <h2>Submit</h2>
                            <div class="popup-form-cross-icon submit-cross-icon"></div>
                            <div class="form-message">Are you sure you want to submit?</div>
                            <div class="form-buttons agqa-popup-form-buttons d-flex">
                                <button class="no-confirm-submit" type="button">No</button>
                                <input type="submit" value="Yes" class="confirm-submit add-ip-from-ajax">
                            </div>
                        </div>
                    </div>
                    <!-- <div class="confirm-submit-popup">
                        <div class="confirm-submit-popup-box">
                            <h2>Submit</h2>
                            <div class="popup-form-cross-icon submit-cross-icon"></div>
                            <div class="form-message">You   set the same IP,</br> Are you sure you want to submit?</div>
                            <div class="form-buttons agqa-popup-form-buttons d-flex">
                                <button class="no-confirm-submit" type="button">No</button>
                                <button type="submit" value="Yes" class="confirm-submit ">Yes</button>
                            </div>
                        </div>
                    </div> -->
                </div>
            </form>
    </div>
</div>