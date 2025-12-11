<?php
// Function to generate the report system content
function report_system_shortcode()
{

    $getUserRole = get_user_role_simple();
    $get_user_id = get_current_user_id();
    $add_faq_report = isset($_GET['add']) ? intval($_GET['add']) : 0;
    $add_report_status = isset($_GET['status']) ? $_GET['status'] : '';
    // echo $add_report_status;
    $user_id = get_current_user_id();


    global $wpdb;
    $table_agqa_faq = $wpdb->prefix . 'agqa_faq';
    $table_agqa_report_system = $wpdb->prefix . 'faq_report_system';
    $table_agqa_user = $wpdb->prefix . 'agqa_wiki_add_users';
    $table_agqa_manage_user = $wpdb->prefix . 'agqa_wiki_add_users';
    // echo $add_username;
    // Custom state
    $get_stauts_freez = $wpdb->get_var(
        $wpdb->prepare("SELECT state FROM {$table_agqa_manage_user} WHERE user_id = %d LIMIT 1", $get_user_id)
    );
    if (strtolower($get_stauts_freez) == 'freeze') {
        $disabledActionClass = 'table-body-disabled';
    } else {
        $disabledActionClass = '';
    }

    if ($add_faq_report == 0) {

        //         $sql = "
        //     SELECT
        //         id,
        //         user_id,
        //         report_type,
        //         status,
        //         issue_detail,
        //         issue_detail_reply,
        //         upload_attachments,
        //         answer,
        //         reporter,
        //         reply_time,
        //         create_time
        //     FROM $table_agqa_report_system
        // ";

        //         // Add WHERE clause only if status is provided
        //         if (!empty($add_report_status)) {
        //             // Escaping the value to prevent SQL injection
        //             $safe_status = esc_sql($add_report_status);
        //             $sql .= " WHERE status = '$safe_status Response'";
        //         }

        //         // Final ORDER BY
        //         $sql .= " ORDER BY id DESC";

        //         // Run the query
        //         $report_system_data = $wpdb->get_results($sql);


        // Base SELECT
        $sql  = "
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
";

        // WHERE parts + args
        $where = [];
        $args  = [];

        // Status filter (e.g., "Pending Response")
        if (! empty($add_report_status)) {
            $where[] = "status = %s";
            $args[]  = $add_report_status . ' Response';
        }

        // Viewer => only own records
        if ($getUserRole === 'viewer') {
            $where[] = "user_id = %d";
            $args[]  = (int) $user_id;
        }

        // Attach WHERE if any
        if (!empty($where)) {
            $sql .= " WHERE " . implode(' AND ', $where);
        }

        // Order by latest first
        $sql .= " ORDER BY id DESC";

        // Prepare (only if we have args)
        if (!empty($args)) {
            $sql = $wpdb->prepare($sql, $args);
        }

        // Run
        $report_system_data = $wpdb->get_results($sql);
        // $pending_response_count = $wpdb->get_var("
        //         SELECT COUNT(*) 
        //         FROM $table_agqa_report_system
        //         WHERE status = 'Pending Response'
        //     ");
        $count_sql  = "SELECT COUNT(*) FROM $table_agqa_report_system WHERE status = %s";
        $count_args = ['Pending Response'];

        // If viewer, only their own records
        if ($getUserRole === 'viewer') {
            $count_sql  .= " AND user_id = %d";
            $count_args[] = $user_id;
        }
        $pending_response_count = (int) $wpdb->get_var($wpdb->prepare($count_sql, $count_args));

        // Query to fetch the user role for the current user
        $report_get_current_user = $wpdb->get_results("
    SELECT user_role
    FROM $table_agqa_user
    WHERE user_id = $user_id
");

        // Check if we found a user
        if ($report_get_current_user) {
            // Assuming there's only one row returned for the current user
            $user_role = $report_get_current_user[0]->user_role;
            // You can now use $user_role as neede
        } else {
            $user_role = "";
        }


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
    $dataTimezone = "Asia/Karachi";
    $curl = curl_init();
    $ip = ipum_get_client_ip();  // The IP address you want to use
    if ($ip == '::1') {
        $ip = '39.61.50.216';
    }
    // Create the API request URL (dynamically pass the IP)
    $url = "https://get.geojs.io/v1/ip/geo/{$ip}.json";

    // Set cURL options
    curl_setopt_array($curl, array(
        CURLOPT_URL => $url, // Use the dynamically generated URL
        CURLOPT_RETURNTRANSFER => true, // Return the response as a string
        CURLOPT_ENCODING => '', // Handle all encodings
        CURLOPT_MAXREDIRS => 10, // Maximum redirects
        CURLOPT_TIMEOUT => 30, // Timeout in seconds
        CURLOPT_FOLLOWLOCATION => true, // Follow redirects
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1, // HTTP version
        CURLOPT_CUSTOMREQUEST => 'GET', // Custom request method (GET)
    ));

    // Execute cURL request and get the response
    $response = curl_exec($curl);

    if (curl_errno($curl)) {
        echo 'cURL Error: ' . curl_error($curl); // Handle any errors
    } else {
        // Decode the JSON response
        $data = json_decode($response, true);
        // print_r($data);

        // Check if the response contains the needed data and output it
        if (isset($data['timezone'])) {
            $dataTimezone = $data['timezone'];
        } else {
            echo "Error: Required data not found in the response.";
        }
    }
    // Close cURL session
    curl_close($curl);

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
                                <?php
                                if (strpos($_SERVER['REQUEST_URI'], '/zh/') !== false) { ?>
                                    <li data-value="all">全部</li>
                                    <li data-value="Functional issue / Operation not working as expected">
                                        功能問題／操作未如預期
                                    </li>
                                    <li data-value="UI display issue">
                                        介面顯示問題
                                    </li>
                                    <li data-value="Incorrect data display">
                                        資料顯示錯誤
                                    </li>
                                    <li data-value="System error message">
                                        系統錯誤訊息
                                    </li>
                                    <li data-value="Process interruption / Unable to complete operation">
                                        流程中斷／無法完成操作
                                    </li>
                                    <li data-value="Performance issue / System lag">效能問題／系統延遲</li>
                                    <li data-value="Permission or account-related issue">
                                        權限或帳號相關問題
                                    </li>
                                    <li data-value="Notification / Email / Task trigger issue">
                                        通知／電子郵件／任務觸發問題
                                    </li>
                                    <li data-value="Text / Language error">
                                        文字／語言錯誤
                                    </li>
                                    <li data-value="Other">
                                        其他
                                    </li>
                                <?php } else { ?>
                                    <li data-value="all">All</li>
                                    <li data-value="Functional issue / Operation not working as expected">
                                        Functional issue / Operation not working as expected
                                    </li>
                                    <li data-value="UI display issue">
                                        UI display issue
                                    </li>
                                    <li data-value="Incorrect data display">
                                        Incorrect data display
                                    </li>
                                    <li data-value="System error message">
                                        System error message
                                    </li>
                                    <li data-value="Process interruption / Unable to complete operation">
                                        Process interruption / Unable to complete operation
                                    </li>
                                    <li data-value="Performance issue / System lag">Performance issue / System lag</li>
                                    <li data-value="Permission or account-related issue">
                                        Permission or account-related issue
                                    </li>
                                    <li data-value="Notification / Email / Task trigger issue">
                                        Notification / Email / Task trigger issue
                                    </li>
                                    <li data-value="Text / Language error">
                                        Text / Language error
                                    </li>
                                    <li data-value="Other">
                                        Other
                                    </li>
                                <?php } ?>

                            </ul>
                        </div>
                    </div>
                    <input type="search" name="filter-search" id="report-filter-search" class="agqa-report-validation-100"
                        placeholder="Please Enter">
                    <div class="filter-select">
                        <input type="hidden" name="filter-report-states"
                            class="agqa-filter-select-hidden agqa-status-filter">
                        <button class="filter-select-title agqa-report-select-filter-button">
                            <span class="filter-default-text">Select States</span>
                            <span class="filter-selected-text report-filter-selected-text"></span>
                        </button>
                        <div class="filter-select-list agqa-report-cat-filter">
                            <ul>
                                <?php
                                if (strpos($_SERVER['REQUEST_URI'], '/zh/') !== false) { ?>
                                    <li data-value="Pending Response">未回覆</li>
                                    <li data-value="Responded">已回覆</li>
                                    <li data-value="No response Needed">不需回覆</li>
                                <?php } else { ?>
                                    <li data-value="Pending Response">Pending Response</li>
                                    <li data-value="Responded">Responded</li>
                                    <li data-value="No response Needed">No response Needed</li>
                                <?php } ?>
                            </ul>
                        </div>
                    </div>
                    <button type=" submit" class="filter-select-button" id="agqa-report-system-filter">
                        <span>Search</span></button>
                </form>
            </div>
        </div>
        <!-- <div class="filter-pending-responses">
            <button>Pending Response <span
                    class="pending-response-counting"><?php //echo esc_html($pending_response_count); 
                                                        ?></span>
            </button>
        </div> -->
        <?php
        $pendingLabel = (strpos($_SERVER['REQUEST_URI'], '/zh') !== false)
            ? '未回覆'
            : 'Pending Response';
        ?>
        <div class="filter-pending-responses">
            <button>
                <?php echo esc_html($pendingLabel); ?>
                <span class="pending-response-counting"><?php echo esc_html($pending_response_count); ?></span>
            </button>
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
                        <?php
                        $count = 1; // Initialize counter
                        foreach ($report_system_data as $report_value) { ?>
                            <?php // Check if the create_time is less than one year ago
                            $create_time = strtotime($report_value->create_time);
                            if ($create_time && (time() - $create_time) <= 365 * 24 * 60 * 60 || strtolower($user_role) == "admin") {  // 365 days in seconds
                            ?>
                                <div class="custom-table-row active">
                                    <div class="table-body report-row">
                                        <div class="report-row-head">
                                            <div class="table-body-col"><?php echo $count; ?></div>
                                            <div class="table-body-col agqa-report-type-search-text">
                                                <?php echo $report_value->report_type; ?>
                                            </div>
                                            <div class="table-body-col report-status-response">
                                                <span
                                                    class="<?php echo str_replace(' ', '-', strtolower($report_value->status)); ?><?php if ($report_value->status == 'Responded') echo '-status'; ?>">
                                                    <?php echo $report_value->status; ?>
                                                </span>
                                            </div>
                                            <div class=" table-body-col"><?php echo $report_value->reporter ?>
                                            </div>
                                            <div class="table-body-col">
                                                <?php echo date('Y/m/d', strtotime($report_value->create_time)); ?>
                                            </div>
                                            <div class="table-body-col">
                                                <?php
                                                if ($report_value->reply_time == "--") {
                                                    echo $report_value->reply_time;
                                                } else {
                                                    echo date('Y/m/d', strtotime($report_value->reply_time));
                                                }
                                                ?>
                                            </div>
                                            <?php if ($report_value->status === 'Pending Response') { ?>
                                                <?php if ($getUserRole !== 'viewer') { ?>
                                                    <div class="table-body-col report-action">
                                                        <button
                                                            class="respond-button pending-response-button <?php echo $disabledActionClass; ?>"></button>
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
                                                                                <!-- <input type="hidden" name="respond-report-type" required> -->
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-field required">
                                                                            <label for="respond-status-type"><span>* </span>Status</label>
                                                                            <div class="custom-select-dropdown">
                                                                                <div class="custom-select-dropdown-title">
                                                                                    <span class="custom-dropdown-default-value">Pending
                                                                                        Response
                                                                                    </span>
                                                                                    <span class="custom-dropdown-selected-value"></span>
                                                                                </div>
                                                                                <div class="custom-select-dropdown-lists">
                                                                                    <ul>
                                                                                        <li data-value="Pending Response">
                                                                                            Pending Response
                                                                                        </li>
                                                                                        <li data-value="No response needed">
                                                                                            No
                                                                                            response
                                                                                            needed
                                                                                        </li>
                                                                                    </ul>
                                                                                </div>
                                                                                <input type="hidden" name="respond-status-type"
                                                                                    value="Pending Response" required>
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-field">
                                                                            <label for="respond-disabled-textarea ag">Issue
                                                                                Detail</label>
                                                                            <textarea name="respond-disabled-textarea"
                                                                                class="respond-disabled-textarea"
                                                                                disabled><?php echo str_replace("\\", "", $report_value->issue_detail); ?></textarea>
                                                                        </div>
                                                                        <div class="uploaded-images">
                                                                            <span class="upload-image-label">Upload Attachments</span>
                                                                            <div class="uploaded-images-inner">
                                                                                <?php
                                                                                $reportUrl = $report_value->upload_attachments;
                                                                                $reportUrl = explode(",", $reportUrl);
                                                                                if (empty($reportUrl) || count($reportUrl) == 0 || (count($reportUrl) == 1 && $reportUrl[0] == '')) {
                                                                                    echo '<div class="agqa-no-attachments"> No attachments</div>';
                                                                                } else {
                                                                                    /*                                                                                    foreach ($reportUrl as $url) {
                                                                                ?>

                                                            <div class="uploaded-image">
                                                                <img src="<?php echo $url; ?>" alt=" Report Image"
                                                                    class="stretchable">
                                                                <div class="stretch-image-icon"></div>
                                                            </div>
                                                            <?php } */
                                                                                    foreach ($reportUrl as $url) {
                                                                                        $url = trim($url);
                                                                                        if (!$url) continue;

                                                                                        $upload_dir = wp_get_upload_dir();
                                                                                        $file_path = str_replace($upload_dir['baseurl'], $upload_dir['basedir'], $url);

                                                                                        if (!file_exists($file_path)) continue; // skip missing files
                                                                                ?>
                                                                                        <div class="uploaded-image">
                                                                                            <img src="<?php echo esc_url($url); ?>"
                                                                                                alt="Report Image" class="stretchable">
                                                                                            <div class="stretch-image-icon"></div>
                                                                                        </div>
                                                                                <?php
                                                                                    }
                                                                                } ?>
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
                                                                                <!-- <input type="hidden" name="respond-answer"
                                                                            class="respond-answer"> -->
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-field">
                                                                            <textarea name="respond-detail-textarea" maxlength="1000"
                                                                                class="respond-detail-textarea"
                                                                                placeholder="Please Enter"></textarea>
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-buttons d-flex agqa-respond-buttons">
                                                                        <div id=""
                                                                            class="cancel-form-confirmation report-cancel-popup-confirmation">
                                                                            <div class="cancel-form-confirmation-box">
                                                                                <h2>Cancel</h2>
                                                                                <div class="popup-form-cross-icon"></div>
                                                                                <div class="form-message">Are you sure you
                                                                                    want
                                                                                    to cancel?
                                                                                </div>
                                                                                <div class="form-buttons agqa-popup-form-buttons d-flex">
                                                                                    <button class="no-form-cancel" type="button">No
                                                                                    </button>
                                                                                    <a href="#"
                                                                                        class="back-button agqa-report-cancel-btn">Yes</a>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div id="confirm-submit-popup" class="confirm-submit-popup">
                                                                            <div class="confirm-submit-popup-box">
                                                                                <h2>Submit</h2>
                                                                                <div class="popup-form-cross-icon report-cancel-icon"></div>
                                                                                <div class="form-message">Are you sure you
                                                                                    want
                                                                                    to submit?
                                                                                </div>
                                                                                <div class="form-buttons agqa-popup-form-buttons d-flex">
                                                                                    <button class="no-confirm-submit" type="button">No
                                                                                    </button>
                                                                                    <input type="submit" value="Yes" id="confirm-submit">
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <a href="#"
                                                                            class="back-button report-cancel-button cancel-confirmation-button">Cancel</a>
                                                                        <!-- <button type="submit">Submit</button> -->
                                                                        <button type="button" class="respond-popup-button">
                                                                            Submit
                                                                        </button>
                                                                    </div>
                                                                    <input type="hidden" name="id" value="<?php echo $report_value->id; ?>">
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php } ?>
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
                                                                        <label for="respond-report-type">Report
                                                                            Type</label>
                                                                        <div class="custom-select-dropdown">
                                                                            <div class="custom-select-dropdown-title">
                                                                                <span
                                                                                    class="custom-dropdown-default-value"><?php echo $report_value->report_type; ?></span>
                                                                                <span class="custom-dropdown-selected-value"></span>
                                                                            </div>
                                                                            <div class="custom-select-dropdown-lists">
                                                                                <ul>
                                                                                    <li>All</li>
                                                                                    <li>Functional issue / Operation not
                                                                                        working as
                                                                                        expected
                                                                                    </li>
                                                                                    <li>UI display issue</li>
                                                                                    <li>Incorrect data display</li>
                                                                                    <li>System error message</li>
                                                                                    <li>Process interruption / Unable to
                                                                                        complete
                                                                                        operation
                                                                                    </li>
                                                                                    <li>Performance issue / System lag
                                                                                    </li>
                                                                                    <li>Permission or account-related
                                                                                        issue
                                                                                    </li>
                                                                                    <li>Notification / Email / Task
                                                                                        trigger
                                                                                        issue
                                                                                    </li>
                                                                                    <li>Text / Language error</li>
                                                                                    <li>Other (please specify)</li>
                                                                                </ul>
                                                                            </div>
                                                                            <!-- <input type="hidden" name="respond-report-type"> -->
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
                                                                                    <li data-value="Pending Response">
                                                                                        Pending Response
                                                                                    </li>
                                                                                    <li data-value="No response needed">
                                                                                        No
                                                                                        response
                                                                                        needed
                                                                                    </li>
                                                                                </ul>
                                                                            </div>
                                                                            <input type="hidden" name="respond-status-type">
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-field">
                                                                        <label for="respond-disabled-textarea">Issue
                                                                            Detail</label>
                                                                        <textarea name="respond-disabled-textarea"
                                                                            class="respond-disabled-textarea" placeholder="Please Enter"
                                                                            disabled><?php echo $report_value->issue_detail; ?></textarea>
                                                                    </div>
                                                                    <div class="uploaded-images">
                                                                        <span class="upload-image-label">Upload Attachments</span>
                                                                        <div class="uploaded-images-inner">
                                                                            <?php
                                                                            $reportUrl = $report_value->upload_attachments;
                                                                            $reportUrl = explode(",", $reportUrl);
                                                                            if (empty($reportUrl) || count($reportUrl) == 0 || (count($reportUrl) == 1 && $reportUrl[0] == '')) {
                                                                                echo '<div class="agqa-no-attachments"> No attachments</div>';
                                                                            } else {/*
                                                                                foreach ($reportUrl as $url) {
                                                                            ?>
                                                            <div class="uploaded-image">
                                                                <img src="<?php echo $url; ?>" alt="Report Image"
                                                                    class="stretchable">
                                                                <div class="stretch-image-icon"></div>
                                                            </div>
                                                            <?php } */
                                                                                foreach ($reportUrl as $url) {
                                                                                    $url = trim($url);
                                                                                    if (!$url) continue;
                                                                                    $upload_dir = wp_get_upload_dir();
                                                                                    $file_path = str_replace($upload_dir['baseurl'], $upload_dir['basedir'], $url);

                                                                                    if (!file_exists($file_path)) continue; // skip missing files
                                                                            ?>
                                                                                    <div class="uploaded-image">
                                                                                        <img src="<?php echo esc_url($url); ?>"
                                                                                            alt="Report Image" class="stretchable">
                                                                                        <div class="stretch-image-icon"></div>
                                                                                    </div>
                                                                            <?php }
                                                                            } ?>
                                                                        </div>
                                                                    </div>
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
                                                                                <span class="custom-dropdown-default-value">Import
                                                                                    Answer From FAQ</span>
                                                                                <!--                                                                            --><?php //foreach ($faq_data as $faq_value) { 
                                                                                                                                                                    ?>
                                                                                <!--                                                                                <span class="custom-dropdown-default-value">-->
                                                                                <!--                                                                                -->
                                                                                <?php //echo esc_html($faq_value->question); 
                                                                                ?>
                                                                                <!--</span>-->
                                                                                <!--                                                                            --><?php //} 
                                                                                                                                                                    ?>
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
                                                                            <!-- <input type="hidden" name="respond-answer"
                                                                            class="respond-answer"> -->
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-field">
                                                                        <textarea name="respond-detail-textarea"
                                                                            class="respond-detail-textarea" placeholder="--"
                                                                            disabled><?php echo $report_value->issue_detail_reply ?></textarea>
                                                                    </div>
                                                                </div>
                                                                <div class="form-buttons d-flex agqa-respond-buttons">
                                                                    <div class="approval-info">
                                                                        <span class="approval-time">

                                                                            <?php
                                                                            if ($report_value->reply_time == '--') {
                                                                            } else {
                                                                                // Get the current system time (local server time) as a string
                                                                                $date = new DateTime($report_value->reply_time); // Convert the string into DateTime object

                                                                                // Convert to the 'Asia/Kolkata' time zone (Indian Standard Time)
                                                                                $date->setTimezone(new DateTimeZone($dataTimezone));

                                                                                // Output the time in 'Y/m/d H:i' format
                                                                                echo $date->format('Y/m/d H:i');
                                                                            }
                                                                            ?>
                                                                        </span>
                                                                        <span
                                                                            class="approavl-account"><?php echo $report_value->answer; ?></span>
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
                                                    <div class="report-row-body-text agqa-report-search-box">
                                                        <p><?php echo str_replace("\\", "", $report_value->issue_detail); ?>
                                                        </p>
                                                    </div>
                                                    <div class="report-row-body-text agqa-report-search-box">
                                                        <p><?php echo str_replace("\\", "", $report_value->issue_detail_reply); ?></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php $count++;
                            } ?>
                        <?php } ?>
                    </div>

                </div>

            </div>
        </div>
        <div class="section-found">
            <div class="no-found-ctn">
                <div class="search-no-found">
                    <div class="search-no-found-icon">
                        <img src="<?php echo URIP_URL ?>assets/image/search-forund-icon.svg" alt="Search Icon">
                    </div>
                    <div class="search-no-found-text">
                        <h2>Nothing matched your search</h2>
                    </div>
                </div>
            </div>
        </div>
        <div class="pagination-ctn">
            <div id="pagination-demo"></div>
        </div>
    </div>
    <script>
        jQuery(document).ready(function() {
            // ==========================
            // 6. Pagination
            // ==========================

            var itemsPerPage = 10;
            var totalItems = jQuery(".report-system-template .custom-table-row").length;
            var totalPages = Math.ceil(totalItems / itemsPerPage);

            jQuery(".report-system-template #pagination-demo").twbsPagination({
                totalPages: totalPages,
                visiblePages: totalPages,
                onPageClick: function(event, page) {
                    jQuery(".report-system-template .custom-table-row").hide();
                    jQuery(
                        '.report-system-template .custom-table-row[data-page="' + page + '"]'
                    ).show();
                    var totalActiveItems = jQuery(".custom-table-row.active").length;
                    var totalActivePages = Math.ceil(totalActiveItems / itemsPerPage);

                    // Loop through each page <li> (exclude Prev/Next)
                    // Loop through each page <li> (exclude Prev/Next)
                    jQuery(".report-system-template .pagination-ctn ul li.page-item")
                        .nextAll()
                        .not(".next")
                        .show();
                    jQuery(".report-system-template .pagination-ctn ul li.page-item")
                        .not(".prev, .next")
                        .each(function() {
                            var pageNumberss = parseInt(jQuery(this)
                                .text()); // Get the number of the page

                            if (pageNumberss === totalActivePages && totalActivePages !== 0) {
                                // Remove all <li> items that come after this one
                                jQuery(this).nextAll().not(".next").hide();

                                // Check the <li> just before the Next button
                                var prevLi = jQuery(".pagination-ctn ul li.page-item.active").next();
                                var $nextBtn = jQuery(".pagination-ctn ul li.next");

                                // Disable if: no next item, next is hidden, or next IS the .next button (last page)
                                if (!prevLi.length || prevLi.is(":hidden") || prevLi.hasClass("next")) {
                                    $nextBtn.addClass("disabled");
                                } else {
                                    $nextBtn.removeClass("disabled");
                                }

                                // Break the loop since we found the match
                                // return false;
                            }
                        });
                    // ========= NEW CODE: 1 hamesha show + center dots =========
                    // Agar koi active page hi nahi to dots ka scene hi nahi
                    if (!totalActivePages) {
                        return;
                    }


                    var $pager = jQuery(".pagination-ctn ul");

                    // Pichle custom dots hata do (refresh ke liye)
                    $pager.find("li.page-item.cust-ellipsis").remove();

                    // Sirf number waale page items (prev/next/first/last ko hata ke)
                    var $numItems = $pager.find("li.page-item").not(".prev, .next, .first, .last");

                    // Pehle sab numeric pages ko hide kar dete hain
                    $numItems.each(function() {
                        var n = parseInt(jQuery(this).text(), 10);
                        if (isNaN(n)) return;

                        // Sirf unhi numbers ke sath kaam jahan n <= totalActivePages
                        if (n > totalActivePages) {
                            jQuery(this).hide();
                        }
                    });

                    // Ab decide karte hain kaun se page dikhane hain
                    var sideRange = 1; // current ke 1-1 neighbour

                    $numItems.each(function() {
                        var n = parseInt(jQuery(this).text(), 10);
                        if (isNaN(n) || n > totalActivePages) return;

                        // hamesha show:
                        // 1, lastActivePage, current, current-1, current+1
                        if (
                            n === 1 ||
                            n === totalActivePages ||
                            n === page ||
                            n === page - sideRange ||
                            n === page + sideRange
                        ) {
                            jQuery(this).show();
                        } else {
                            jQuery(this).hide();
                        }
                    });

                    // 1st page <li> aur lastActivePage <li> pakdo
                    var $page1 = $numItems.filter(function() {
                        return parseInt(jQuery(this).text(), 10) === 1;
                    });
                    var $lastPage = $numItems.filter(function() {
                        return parseInt(jQuery(this).text(), 10) === totalActivePages;
                    });

                    // Ensure page 1 visible
                    if ($page1.length) {
                        $page1.show();
                    }

                    // Dots after 1 (agar 1 ke baad direct 2 na ho visible mein)
                    if ($page1.length && $page1.is(":visible")) {
                        var $after1 = $page1.nextAll("li.page-item")
                            .not(".prev,.next,.first,.last")
                            .filter(":visible")
                            .first();

                        if ($after1.length) {
                            var nAfter = parseInt($after1.text(), 10);
                            if (!isNaN(nAfter) && nAfter > 2) {
                                jQuery(
                                        '<li class="page-item disabled cust-ellipsis"><span class="page-link">...</span></li>'
                                    )
                                    .insertAfter($page1);
                            }
                        }
                    }

                    // Ensure last active page visible
                    if ($lastPage.length) {
                        $lastPage.show();
                    }

                    // Dots before lastActivePage (agar us se pehle vala visible number lastActivePage - 1 na ho)
                    if ($lastPage.length && $lastPage.is(":visible")) {
                        var $beforeLast = $lastPage.prevAll("li.page-item")
                            .not(".prev,.next,.first,.last")
                            .filter(":visible")
                            .first();

                        if ($beforeLast.length) {
                            var nBefore = parseInt($beforeLast.text(), 10);
                            if (!isNaN(nBefore) && nBefore < (totalActivePages - 1)) {
                                jQuery(
                                        '<li class="page-item disabled cust-ellipsis"><span class="page-link">...</span></li>'
                                    )
                                    .insertBefore($lastPage);
                            }
                        }
                    }
                    // ========= NEW DOTS CODE END =========
                },
            });

            jQuery(".report-system-template .custom-table-row").each(function(index) {
                var page = Math.floor(index / itemsPerPage) + 1;
                jQuery(this).attr("data-page", page);
                if (page === 1) {
                    jQuery(this).show();
                } else {
                    jQuery(this).hide();
                }
            });
            setTimeout(function() {
                applyCustomDots(totalPages);
            }, 500);

            function applyCustomDots(totalPages) {
                var $pager = jQuery(".pagination-ctn ul");

                // Agar 1 hi page hai to dots ka koi faida nahi
                if (!totalPages || totalPages <= 1) {
                    $pager.find("li.page-item.cust-ellipsis").remove();
                    return;
                }

                // Purane wale custom dots hata do
                $pager.find("li.page-item.cust-ellipsis").remove();

                // Sirf number wali li (prev / next ko hata ke)
                var $numItems = $pager.find("li.page-item").not(".prev, .next");

                // Current active page nikaalo (jo tum nth-child(3) se active kar rahe ho)
                var current = parseInt($pager.find("li.page-item.active").text(), 10);
                if (isNaN(current) || current < 1) current = 1;
                if (current > totalPages) current = totalPages;

                // Pehle sab numeric pages ko base state mein hide karo / > totalPages hide
                $numItems.each(function() {
                    var n = parseInt(jQuery(this).text(), 10);
                    if (isNaN(n)) return;

                    if (n > totalPages) {
                        jQuery(this).hide();
                    } else {
                        jQuery(this).hide(); // baad mein select karke show karenge
                    }
                });

                var sideRange = 1; // current ke aas paas 1-1 page

                // 1, last, current, current-1, current+1 show karo
                $numItems.each(function() {
                    var n = parseInt(jQuery(this).text(), 10);
                    if (isNaN(n) || n > totalPages) return;

                    if (
                        n === 1 ||
                        n === totalPages ||
                        n === current ||
                        n === current - sideRange ||
                        n === current + sideRange
                    ) {
                        jQuery(this).show();
                    }
                });

                // 1st page li aur last page li find karo
                var $page1 = $numItems.filter(function() {
                    return parseInt(jQuery(this).text(), 10) === 1;
                });
                var $lastPage = $numItems.filter(function() {
                    return parseInt(jQuery(this).text(), 10) === totalPages;
                });

                if ($page1.length) $page1.show();
                if ($lastPage.length) $lastPage.show();

                // 1 ke baad dots (agar gap ho)
                if ($page1.length && $page1.is(":visible")) {
                    var $after1 = $page1.nextAll("li.page-item")
                        .not(".prev,.next")
                        .filter(":visible")
                        .first();

                    if ($after1.length) {
                        var nAfter = parseInt($after1.text(), 10);
                        if (!isNaN(nAfter) && nAfter > 2) {
                            jQuery(
                                '<li class="page-item disabled cust-ellipsis"><span class="page-link">...</span></li>'
                            ).insertAfter($page1);
                        }
                    }
                }

                // last se pehle dots (agar gap ho)
                if ($lastPage.length && $lastPage.is(":visible")) {
                    var $beforeLast = $lastPage.prevAll("li.page-item")
                        .not(".prev,.next")
                        .filter(":visible")
                        .first();

                    if ($beforeLast.length) {
                        var nBefore = parseInt($beforeLast.text(), 10);
                        if (!isNaN(nBefore) && nBefore < totalPages - 1) {
                            jQuery(
                                '<li class="page-item disabled cust-ellipsis"><span class="page-link">...</span></li>'
                            ).insertBefore($lastPage);
                        }
                    }
                }
            }
        });
    </script>
<?php

    return ob_get_clean(); // Get the content generated by the buffer and return it
}

// Register the shortcode
add_shortcode('report_system', 'report_system_shortcode');
?>