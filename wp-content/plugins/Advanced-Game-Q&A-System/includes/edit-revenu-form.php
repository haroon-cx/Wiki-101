<?php
    $edit_revenue_ids = isset($_GET['edit']) ? intval($_GET['edit']) : 0;
    $edit_revenue_back = isset($_GET['back']) ? intval($_GET['back']) : 0;
    $edit_back_button = '';
    if($edit_revenue_back){
        $edit_back_button = '?revenue=' . $edit_revenue_back;
    }
    
    // print_r($edit_revenue_ids);
    global $wpdb;
    $table_revenu_edit   = $wpdb->prefix . 'agqa_revenu';
    $table_category_edit = $wpdb->prefix . 'game_category';
    // print_r($table_category_edit);
    $table_type_edit = $wpdb->prefix . 'game_type';

    if ($edit_revenue_ids) {
        // Fetch the data for the specific revenue id
        $revenu_data_edit = $wpdb->get_row("
            SELECT
                r.*,
                gc.name AS game_category_name,
                gt.name AS game_type_name
            FROM $table_revenu_edit r
            LEFT JOIN $table_category_edit gc ON r.game_category_id = gc.id
            LEFT JOIN $table_type_edit gt ON r.game_type_id = gt.id
            WHERE r.id = $edit_revenue_ids
        ");
    }
    // Game Type Name
    $rows_type_names = $wpdb->get_results("
    SELECT DISTINCT id, game_category_id, name
    FROM {$table_type_edit}
    WHERE id IS NOT NULL
      AND id <> ''
    ORDER BY id ASC
", ARRAY_A);
    // Game Category Name & ID
    $rows_cat_names = $wpdb->get_results("
    SELECT DISTINCT id, name
    FROM {$table_category_edit}
    WHERE id IS NOT NULL
      AND id <> ''
    ORDER BY id ASC
", ARRAY_A);

?>
<div class="api-form-main">
    <div class="form-header-row">
        <a href="<?php echo esc_url(home_url('/api-revenue-share-lookup/revenue/' . $edit_back_button)) ?>" class="back-button"
            type="button">
            <img decoding="async" src="<?php echo AGQA_URL ?>assets/images/arrow-left.svg" alt="Arrow Left Icon">
            Back
        </a>
        <h2 class="form-heading">Edit Revenue</h2>
    </div>
    <div class="api-form-ctn">
        <div class="api-form-wrapper" id="UN">
            <form autocomplete="off" id="edit-revnue-form" class="custom-form" novalidate="novalidate"
                data-inited-validation="1">
                <div class="form-field required">
                    <input type="hidden" name="provider-id" value="<?php echo $edit_revenue_ids; ?>">
                    <input type="hidden" name="provider-game-name" value="<?php echo $revenu_data_edit->provider_name; ?>">
                    <label for="provider-name"><span>*</span> Provider Name</label>
                    <select name="provider-name" disabled id="provider-name">
                        <option value="<?php echo $revenu_data_edit->provider_name; ?>" selected>
                            <?php echo $revenu_data_edit->provider_name; ?></option>
                    </select>
                </div>
                <!-- New Input type -->
                <div class="form-field required">
                    <label for="select-game-type-id"><span>*</span>  State</label>
                    <div class="custom-select-dropdown">
                        <div class="custom-select-dropdown-title">
                            <span class="custom-dropdown-default-value"><?php echo empty($revenu_data_edit->state) ? 'Disabled' : 'Enabled'; ?></span>
                            <span class="custom-dropdown-selected-value"></span>
                                </div>
                        <div class="custom-select-dropdown-lists">
                            <ul>
                                <li data-value="1">Enabled</li>
                                <li data-value="0">Disabled</li>
                            </ul>
                        </div>
                        <input type="hidden" name="state" id="state" value="<?php echo empty($revenu_data_edit->state) ? '0' : '1'; ?>" required>
                    </div>
                </div>
                <!-- END -->
                <!-- New Game Cat Input -->
                    <div class="form-field required">
                        <label for="select-game-category-id"><span>*</span>  Game Categories</label>
                        <div class="custom-select-dropdown">
                            <div class="custom-select-dropdown-title">
                                <span class="custom-dropdown-default-value">
                                    <?php echo $revenu_data_edit->game_category_name; ?>
                                </span>
                                <span class="custom-dropdown-selected-value"></span>
                            </div>
                            <div class="custom-select-dropdown-lists" id="select-game-category-id">
                                <ul>
                                    <?php foreach ($rows_cat_names as $row) {?>
                                    <li  data-value="<?php echo $row['id']; ?>" class="<?php echo($revenu_data_edit->game_category_id == $row['id']) ? 'selected-dropdown-item' : ''; ?>"><?php echo $row['name']; ?></li>
                                    <?php } ?>
                                </ul>
                            </div>
                            <input type="hidden" name="select-game-category-id" id="select-game-category-id" value="<?php echo $revenu_data_edit->game_category_id;?>" required>
                        </div>
                    </div>
                <!-- END -->
                <!-- NEW Game Type Input -->
                <div class="form-field required">
                    <label for="select-game-type-id"><span>*</span> Game Type</label>
                    <div class="custom-select-dropdown agqa-main-game-type">
                        <div class="custom-select-dropdown-title">
                            <span class="custom-dropdown-default-value">
                           <?php echo $revenu_data_edit->game_type_name; ?>
                            </span>
                            <span class="custom-dropdown-selected-value"></span>
                        </div>
                        <div class="custom-select-dropdown-lists agqa-custom-type-list">
                            <ul>
                                <?php foreach ($rows_type_names as $row_type) {?>
                                    <li data-value="<?php echo $row_type['id']; ?>"
                                    <?php echo($revenu_data_edit->game_category_id == $row_type['game_category_id']) ? '' : 'style="display: none;"'; ?>
                                        data-id-cat="<?php echo $row_type['game_category_id']; ?>"
                                        ><?php echo $row_type['name']; ?>
                                    </li>
                                <?php } ?>
                            </ul>
                        </div>
                        <input type="hidden" name="select-game-type-id" id="select-game-type-id" value="<?php echo $revenu_data_edit->game_type_id; ?>" required>
                    </div>
                </div>
                <!-- END -->
                <div class="form-field required">
                    <label for="selling-price"><span>*</span> Selling Price (%)</label>
                    <input type="number" name="selling-price" min="0" max="100" step="1" inputmode="numeric" value="<?php echo $revenu_data_edit->selling_price; ?>"
                        id="selling-price" placeholder="Description" required>
                </div>
                <div class="form-field required">
                    <label for="api-cost"><span>*</span> API Cost (%)</label>
                    <input type="number" name="api-cost" min="0" max="100" step="1" inputmode="numeric" value="<?php echo $revenu_data_edit->api_cost; ?>"
                        id="api-cost" placeholder="Description" required>
                </div>
                <div class="form-field required">
                    <label for="game-info-website"><span>*</span> Game Info Website</label>
                    <input type="text" name="game-info-website"
                        value="<?php echo empty($revenu_data_edit->game_info_website) ? 'none' : $revenu_data_edit->game_info_website; ?>"
                        id="game-info-website" placeholder="Description" required>
                </div>
                <div class="form-field">
                    <label for="game-demo-website">Game Demo Website</label>
                    <input type="text" name="game-demo-website"
                        value="<?php echo empty($revenu_data_edit->game_demo_website) ? 'none' : $revenu_data_edit->game_demo_website; ?>"
                        id="game-demo-website" placeholder="Description">
                </div>
                <div class="form-field required">
                    <label for="api-type"><span>*</span> API Type</label>
                    <div class="form-multi-select agqa-popup-form-multi-select">
                        <button type="button" class="agqa-popup-form-button multi-select-open-button">
                            <span
                                class="default-text" <?php echo empty($revenu_data_edit->api_type) ? '' : 'style=display:none'; ?>>Select Role</span>
                            <span class="selected-dropdown-item">
                               <?php
                                    $api_types = $revenu_data_edit->api_type;

                                    // If it's a comma-separated string, split it into an array
                                    if (!is_array($api_types)) {
                                        $api_types = explode(',', $api_types);
                                    }
 									foreach ($api_types as $value_api) {
                                        $value_api = trim($value_api); // remove extra spaces
                                        if(!empty($value_api)) {
                                        ?>
                                        <span class="tag" data-value="<?php echo $value_api; ?>"><?php echo esc_html($value_api); ?>
                                            <span class="agqa-cross-icon"></span>
                                        </span>
                                        <?php }
                                    }
                                    ?>

                                </span>
                            </span>
                        </button>
                        <div class="agqa-popup-form-select">
                            <?php 
                                // DB value
                                $db_value = $revenu_data_edit->api_type; 
                                $selected_array = array_map('trim', explode(",", $db_value)); 
                                ?>
                            <ul>
                                <li data-value="Single Wallet" class="<?php echo in_array('Single Wallet', $selected_array) ? 'select-item' : ''; ?>">Single Wallet</li>
                                <li data-value="Transfer Wallet" class="<?php echo in_array('Transfer Wallet', $selected_array) ? 'select-item' : ''; ?>">Transfer Wallet</li>
                                <li data-value="Seamless Wallet" class="<?php echo in_array('Seamless Wallet', $selected_array) ? 'select-item' : ''; ?>">Seamless Wallet</li>
                                <li data-value="Credit Wallet" class="<?php echo in_array('Credit Wallet', $selected_array) ? 'select-item' : ''; ?>">Credit Wallet</li>
                                <li data-value="Crypto Wallet" class="<?php echo in_array('Crypto Wallet', $selected_array) ? 'select-item' : ''; ?>">Crypto Wallet</li>
                            </ul>
                        </div>
                        <div class="selected-tags"></div>
                        <!-- Hidden input to store selected values as CSV -->
                        <input type="hidden" name="api-type" required class="selected-values" value="<?php echo $revenu_data_edit->api_type ?>"/>
                    </div>
                </div>
                <div class="form-field required">
                    <label for="representative-name"><span>*</span> Representative’s Name</label>
                    <input type="text" name="representative-name"
                        value="<?php echo empty($revenu_data_edit->representative_contact_info) ? 'none' : $revenu_data_edit->representative_contact_info; ?>"
                        id="representative-name" placeholder="Description" required>
                </div>
                <div class="form-field">
                    <label for="representative-telegram">Representative’s Telegram</label>
                    <input type="text" name="representative-telegram"
                        value="<?php echo empty($revenu_data_edit->representative_telegram) ? 'none' : $revenu_data_edit->representative_telegram; ?>"
                        id="representative-telegram" placeholder="Description">
                </div>
                <!-- Custom fields -->
                <?php
                    // Initialize the fields array
                    $fields = [];

                    // Loop through the custom fields and labels
                    for ($i = 1; $i <= 4; $i++) {
                        // Get custom label and field dynamically
                        $custom_label = $revenu_data_edit->{'custom_label_' . $i};
                        $custom_field = $revenu_data_edit->{'custom_field_' . $i};

                        // Only add non-empty fields to the fields array
                        if (! empty($custom_label)) {
                            $fields[] = [
                                'label' => $custom_label,
                                'field' => $custom_field,
                                'index' => $i,
                            ];
                        }
                    }

                    // Use foreach to loop through valid fields and render them
                    foreach ($fields as $field) {
                        $i            = $field['index'];
                        $custom_label = $field['label'];
                        $custom_field = $field['field'];
                    ?>
                <div class="form-field custom-field-item">
                    <input type="hidden" name="custom-label-<?php echo $i; ?>"
                        value="<?php echo esc_attr($custom_label); ?>">
                    <label><?php echo esc_html($custom_label); ?></label>
                    <div class="custom-append-field">
                        <input type="text" name="custom-field-<?php echo $i; ?>"
                            value="<?php echo esc_attr($custom_field); ?>">
                        <button type="button" class="edit-field-btn"></button>
                        <button type="button" class="remove-field-btn"></button>
                    </div>
                </div>
                <?php
                    }
                ?>
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
                        placeholder="Please enter a note"><?php echo empty($revenu_data_edit->notes) ? 'none' : $revenu_data_edit->notes; ?></textarea>
                    <div class="char-counter">
                        <span class="current-count">0</span> / 1000
                    </div>
                    <div class="char-warning" style="display: none; color: #d9534f; font-size: 12px;">
                        Character limit reached!
                    </div>
                    <div class="form-response"></div>
                </div>
                <div class="form-field full-width">
                    <h2 class="contract-pdf-uploaded" <?php echo !empty($revenu_data_edit->contract_filename) ? '' : 'style="display:none;"'; ?>><?php echo esc_html(basename($revenu_data_edit->contract_filename)); ?> 
                    <span class="pdf-cross-icon"><img src="<?php echo AGQA_URL ?>assets/images/cross-white-icon.svg" alt="Plus Icon"></span>
                <input type="hidden" name="already-upload-contract" id="upload-contract" value="<?php echo $revenu_data_edit->contract_filename; ?>">
                </h2>

                    <div class="upload-contract" <?php echo !empty($revenu_data_edit->contract_filename) ? 'style="display:none;"' : ''; ?>>
                        <label for="pdf-upload-input">
                            Contract
                            <div class="tooltip-wrapper">
								<img src="<?php echo AGQA_URL ?>assets/images/contract-icon.svg"
                             	   alt="Contract Icon">
							</div>
                        </label>
                        <div class="custom-upload-area-pdf">
                            <div class="browse-link-pdf">Upload Contract</div>
                            <div class="error-message-pdf" style="display:none; color:red; font-size:14px;">
                            </div>
                            <h2 class="file-preview-pdf" style="display: none;"></h2>
                        </div>
                        <input type="file" name="upload-file-name" id="pdf-upload-input" accept="application/pdf"
                            style="display: none;">
                        <input type="hidden" name="upload-contract" class="upload-contract">
                    </div>
                </div>
                <div class="form-buttons agqa-popup-form-buttons d-flex full-width  agqa-add-default-btn">
                    <a href="<?php echo esc_url(home_url('/api-revenue-share-lookup/revenue/')) ?>" class="back-button">Cancel</a>
                    <input type="submit" value="Submit" class="agqa-edit-submit-btn">
                </div>

                <div class="form-buttons agqa-popup-form-buttons d-flex full-width agqa-add-update-btn" style="display:none;">
                    <div id="cancel-form-confirmation" class="cancel-form-confirmation" style="">
                         <div class="cancel-form-confirmation-box">
                            <h2>Cancel</h2>
                                <div class="popup-form-cross-icon"></div>
                                <div class="form-message">Are you sure you want to cancel?</div>
                                <div class="form-buttons agqa-popup-form-buttons d-flex">
                                    <button class="no-form-cancel" type="button">No</button>
                                    <a href="<?php echo esc_url(home_url('/api-revenue-share-lookup/revenue/')) ?>" class="back-button">Yes</a>
                                </div>
                         </div>       
                    </div>
                    <a href="<?php echo esc_url(home_url('/api-revenue-share-lookup/revenue/')) ?>" class="back-button" id="cancel-confirmation-button">Cancel</a>

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
        $('.agqa-custom-type-list li').show();
        if (selectedCatId) {
            $('.agqa-custom-type-list li').each(function() {
                var optionCatId = $(this).data('id-cat');
                if (optionCatId != selectedCatId) {
                    $(this).hide();
                }
            });
        }
        $('#select-game-type-id').val('');
        $('.agqa-main-game-type .custom-dropdown-default-value').text('Select Game Type');
    });
    // PDF Icon
    $('.pdf-cross-icon').click(function() {
     $('.contract-pdf-uploaded').hide();
    $('.upload-contract').show();
});
 $('input').keyup(function () {
    $('.agqa-add-default-btn').hide();
    $('.agqa-add-update-btn').show();
});
 $('textarea').keyup(function () {
    $('.agqa-add-default-btn').hide();
    $('.agqa-add-update-btn').show();
});
});
</script>