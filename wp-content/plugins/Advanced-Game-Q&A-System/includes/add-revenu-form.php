<?php
$edit_revenue_ids = isset($_GET['edit']) ? intval($_GET['edit']) : 0;
// print_r($edit_revenue_ids);
global $wpdb;
$table_add_revenu = $wpdb->prefix . 'agqa_revenu';
$table_add_category = $wpdb->prefix . 'game_category';
// print_r($table_category_edit);

$table_add_game_type = $wpdb->prefix . 'game_type';
$rows_type_game = $wpdb->get_results("
    SELECT DISTINCT id, provider_name
    FROM {$table_add_revenu}
    WHERE provider_name IS NOT NULL
      AND provider_name <> ''
    ORDER BY provider_name ASC
", ARRAY_A);
// print_r($rows_type_game);

?>
<div class="api-form-main">
    <div class="form-header-row">
        <a href="<?php echo esc_url(home_url('/api-revenue-share-lookup/revenue/')) ?>" class="back-button"
            type="button">
            <img decoding="async" src="<?php echo AGQA_URL ?>assets/images/arrow-left.svg" alt="Arrow Left Icon">
            Back
        </a>
        <h2 class="form-heading">Add Revenue</h2>
    </div>
    <div class="api-form-ctn">
        <div class="api-form-wrapper" id="UN">
            <form autocomplete="off" id="add-revnue-form" class="custom-form" novalidate="novalidate"
                data-inited-validation="1">

                <!-- NEW Game Type Input -->
                <div class="form-field required">
                    <label for="provider-name"><span>*</span> Provider Name</label>
                    <div class="custom-select-dropdown agqa-main-game-type">
                        <div class="custom-select-dropdown-title">
                            <span class="custom-dropdown-default-value">
                                Select Provider Name
                            </span>
                            <span class="custom-dropdown-selected-value"></span>
                        </div>
                        <div class="custom-select-dropdown-lists">
                            <ul>
                                <?php foreach ($rows_type_game as $value) { ?>
                                <li data-value="<?php echo $value['id']; ?>"><?php echo $value['provider_name']; ?></li>
                                <?php } ?>
                            </ul>
                        </div>
                        <input type="hidden" name="provider-id" id="provider-id" required>
                    </div>
                </div>
                <!-- END -->
                <!-- New Input type -->
                <div class="form-field required">
                    <label for="select-game-type-id"><span>*</span> State</label>
                    <div class="custom-select-dropdown">
                        <div class="custom-select-dropdown-title">
                            <span
                                class="custom-dropdown-default-value"><?php echo empty($revenu_data_edit->state) ? 'Disabled' : 'Enabled'; ?></span>
                            <span class="custom-dropdown-selected-value"></span>
                        </div>
                        <div class="custom-select-dropdown-lists">
                            <ul>
                                <li data-value="1">Enabled</li>
                                <li data-value="0">Disabled</li>
                            </ul>
                        </div>
                        <input type="hidden" name="state" id="state"
                            value="<?php echo empty($revenu_data_edit->state) ? '0' : '1'; ?>" required>
                    </div>
                </div>
                <!-- END -->
                <div class="form-field required">
                    <label for="select-game-category"><span>*</span> Game Categories</label>
                    <div class="form-multi-select agqa-popup-form-multi-select">
                        <button type="button" class="agqa-popup-form-button multi-select-open-button">
                            <span class="default-text">Select Role</span>
                            <span class="selected-dropdown-item agqa-cat-id-list"></span>
                        </button>
                        <div class="agqa-popup-form-select" id="select-game-category-id">
                            <ul>
                                <li data-value="1">Casino Games</li>
                                <li data-value="2">Sports</li>
                                <li data-value="3">Traditional / Local Games</li>
                                <li data-value="4">P2P Games</li>
                                <li data-value="5">Fast / Casual Games</li>
                            </ul>
                        </div>
                        <div class="selected-tags"></div>
                        <!-- Hidden input to store selected values as CSV -->
                        <input type="hidden" name="select-game-category" class="selected-values" required />
                    </div>
                </div>
                <div class="form-field required">
                    <label for="select-game-type"><span>*</span> Game Type</label>
                    <div class="form-multi-select agqa-popup-form-multi-select">
                        <button type="button" class="agqa-popup-form-button multi-select-open-button">
                            <span class="default-text">Select Role</span>
                            <span class="selected-dropdown-item"></span>
                        </button>
                        <div class="agqa-popup-form-select agqa-custom-type-list">
                            <ul>
                                <li data-value="1" data-id-cat="1">Slot</li>
                                <li data-value="2" data-id-cat="1">Table Games</li>
                                <li data-value="3" data-id-cat="1">Live Casino</li>
                                <li data-value="4" data-id-cat="2">Sportsbook</li>
                                <li data-value="5" data-id-cat="2">eSports</li>
                                <li data-value="6" data-id-cat="2">Virtual Sports</li>
                                <li data-value="7" data-id-cat="3">Cockfight</li>
                                <li data-value="8" data-id-cat="3">Fishing</li>
                                <li data-value="9" data-id-cat="3">Lottery</li>
                                <li data-value="10" data-id-cat="3">Number Games</li>
                                <li data-value="11" data-id-cat="4">Poker</li>
                                <li data-value="12" data-id-cat="4">P2P</li>
                                <li data-value="13" data-id-cat="5">Crash Games</li>
                                <li data-value="14" data-id-cat="5">Arcade / Mini Games</li>
                                <li data-value="15" data-id-cat="5">Keno / Bingo</li>
                            </ul>
                        </div>
                        <div class="selected-tags"></div>
                        <!-- Hidden input to store selected values as CSV -->
                        <input type="hidden" name="select-game-type-id" class="selected-values" required />
                    </div>
                </div>
                <div class="form-field required">
                    <label for="selling-price"><span>*</span> Selling Price (%)</label>
                    <!-- <input type="text" name="selling-price" id="selling-price" placeholder="Description"
                        required> -->
                    <input type="number" name="selling-price" min="0" max="100" step="1" inputmode="numeric" value=""
                        id="selling-price" placeholder="Description" required>
                </div>
                <div class="form-field required">
                    <label for="api-cost"><span>*</span> API Cost (%)</label>
                    <!-- <input type="text" name="api-cost" id="api-cost" placeholder="Description" required> -->
                    <input type="number" name="api-cost" min="0" max="100" step="1" inputmode="numeric" value=""
                        id="api-cost" placeholder="Description" required>
                </div>
                <div class="form-field required">
                    <label for="game-info-website"><span>*</span> Game Info Website</label>
                    <input type="text" name="game-info-website" id="game-info-website" placeholder="Description"
                        required>
                </div>
                <div class="form-field">
                    <label for="game-demo-website">Game Demo Website</label>
                    <input type="text" name="game-demo-website" id="game-demo-website" placeholder="Description">
                </div>
                <div class="form-field required">
                    <label for="select-game-category"><span>*</span> API Type</label>
                    <div class="form-multi-select agqa-popup-form-multi-select">
                        <button type="button" class="agqa-popup-form-button multi-select-open-button">
                            <span class="default-text">Please Select</span>
                            <span class="selected-dropdown-item"></span>
                        </button>
                        <div class="agqa-popup-form-select">
                            <ul>
                                <li data-value="single-wallet">Single Wallet</li>
                                <li data-value="transfer-wallet">Transfer Wallet</li>
                                <li data-value="seamless-wallet">Seamless Wallet</li>
                                <li data-value="credit-wallet">Credit Wallet</li>
                                <li data-value="crypto-wallet">Crypto Wallet</li>
                            </ul>
                        </div>
                        <div class="selected-tags"></div>
                        <!-- Hidden input to store selected values as CSV -->
                        <input type="hidden" name="api-type" required class="selected-values" />
                    </div>
                </div>
                <div class="form-field required">
                    <label for="representative-name"><span>*</span> Representative’s Name</label>
                    <input type="text" name="representative-name" id="representative-name" placeholder="Description"
                        required>
                </div>
                <div class="form-field">
                    <label for="representative-telegram">Representative’s Telegram</label>
                    <input type="text" name="representative-telegram" id="representative-telegram"
                        placeholder="Description">
                </div>
                <div class="form-field">
                    <div class="add-custom-field-ctn">
                        <div id="custom-field-popup" style="display:none;">

                            <!-- Add Field Popup -->
                            <div id="custom-field-popup-inner" class="popup-content add-field">
                                <h2>Add Custom Field</h2>
                                <div class="popup-form-cross-icon"></div>
                                <div class="form-field">
                                    <label for="first-name">First Name</label>
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
                <div class="form-field full-width">
                    <label for="notes-detail">Notes</label>
                    <textarea name="notes-detail" maxlength="1000" rows="5" id="notes-detail"
                        placeholder="Please enter a note"></textarea>
                    <div class="char-counter">
                        <span class="current-count">0</span> / 1000
                    </div>
                    <div class="char-warning" style="display: none; color: #d9534f; font-size: 12px;">
                        Character limit reached!
                    </div>
                    <div class="form-response"></div>
                </div>
                <div class="form-field full-width">
                    <div class="upload-contract">
                        <label for="pdf-upload-input">
                            Contract
                            <div class="tooltip-wrapper">
                                <img src="<?php echo AGQA_URL ?>assets/images/contract-icon.svg" alt="Contract Icon">
                            </div>
                        </label>
                        <div class="custom-upload-area-pdf">
                            <div class="browse-link-pdf">Upload Contract</div>
                            <div class="error-message-pdf" style="display:none; color:red; font-size:14px;">
                            </div>
                            <h2 class="file-preview-pdf" style="display: none;"></h2>
                        </div>
                        <input type="file" id="pdf-upload-input" accept="application/pdf" style="display: none;">
                        <input type="hidden" name="upload-contract" class="upload-contract">
                    </div>
                </div>
                <div class="form-buttons agqa-popup-form-buttons d-flex full-width agqa-add-default-btn">
                    <a href="<?php echo esc_url(home_url('/api-revenue-share-lookup/revenue/')) ?>"
                        class="back-button">Cancel</a>
                    <input type="submit" value="Submit" class="">
                </div>
                <div class="form-buttons agqa-popup-form-buttons d-flex full-width agqa-add-update-btn"
                    style="display:none;">
                    <div id="cancel-form-confirmation" class="cancel-form-confirmation" style="">
                        <div class="cancel-form-confirmation-box">
                            <h2>Cancel</h2>
                            <div class="popup-form-cross-icon"></div>
                            <div class="form-message">Are you sure you want to cancel?</div>
                            <div class="form-buttons agqa-popup-form-buttons d-flex">
                                <button class="no-form-cancel" type="button">No</button>
                                <a href="<?php echo esc_url(home_url('/api-revenue-share-lookup/revenue/')) ?>"
                                    class="back-button">Yes</a>
                            </div>
                        </div>
                    </div>
                    <a href="<?php echo esc_url(home_url('/api-revenue-share-lookup/revenue/')) ?>" class="back-button"
                        id="cancel-confirmation-button">Cancel</a>

                    <div id="confirm-submit-popup" class="confirm-submit-popup">
                        <div class="confirm-submit-popup-box">
                            <h2>Submit</h2>
                            <div class="popup-form-cross-icon"></div>
                            <div class="form-message">Are you sure you want to submit?</div>
                            <div class="form-buttons agqa-popup-form-buttons d-flex">
                                <button class="no-confirm-submit" type="button">No</button>
                                <input type="submit" value="Submit" id="confirm-submit">
                            </div>
                        </div>
                    </div>
                    <input type="submit" value="Submit" class="agqa-edit-submit-btn" id="confirm-submit-popup-button">
                    <!-- <input type="submit" value="Submit" class="agqa-edit-submit-btn"> -->
                </div>
            </form>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    $('#select-game-category-id li').on('click', function() {
        var selectedCatId = $(this).data('value');

        // Set a timeout to allow the hidden input to update and then process the data
        setTimeout(function() {
            // Get the value from the hidden input (CSV of category IDs)
            var hiddenInputValue = $("input[name='select-game-category']").val();

            // Split the CSV string into an array of category IDs
            var categoryIds = hiddenInputValue.split(',');

            // Iterate over each custom type list item
            $('.agqa-custom-type-list li').each(function() {
                var optionCatId = $(this).data(
                    'id-cat'); // Get the data-id-cat of the li

                // Ensure that optionCatId is defined before using .toString()
                if (optionCatId && categoryIds.includes(optionCatId.toString())) {
                    $(this).show(); // Show the li if it matches any category ID
                } else {
                    $(this).hide(); // Hide the li if it doesn't match any category ID
                }
            });

        }, 500); // Delay of 500ms

        // Reset the game type input and dropdown text
        $('#select-game-type-id').val('');
    });
    $('input').keyup(function() {
        $('.agqa-add-default-btn').hide();
        $('.agqa-add-update-btn').show();
    });
    $('textarea').keyup(function() {
        $('.agqa-add-default-btn').hide();
        $('.agqa-add-update-btn').show();
    });
});
</script>