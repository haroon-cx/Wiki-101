<?php
global $wpdb;
$table_approval_review = $wpdb->prefix . 'agqa_approval_review_page';
$add_username = isset($_GET['username']) ? $_GET['username'] : '';
// echo $add_username;
$get_data_approval = $wpdb->get_results("
            SELECT
                id,
                question,
                type_name,
                status,
                provider_name,
                created_at
            FROM $table_approval_review
             ORDER BY
            id DESC
            ");


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
?>

<style>

</style>

<div class="approval-template">
    <div class="template-title">
        <h1>Login Records</h1>
    </div>
</div>
<div class="filter-container">
    <div class="approval-filter filter-area">
        <form action="#" autocomplete="off">
            <div class="filter-select">
                <input type="hidden" name="filter-select-hidden" class="agqa-filter-select-hidden">
                <button class="filter-select-title">
                    <span class="filter-default-text">Select A Type</span>
                    <span class="filter-selected-text"></span>
                </button>
                <div class="filter-select-list">
                    <ul>
                        <li>All</li>
                        <li>API Add</li>
                        <li>API Edit</li>
                        <li>FAQ Add</li>
                        <li>FAQ Edit</li>
                    </ul>
                </div>
            </div>
            <div class="filter-select">
                <input type="hidden" name="filter-select-hidden" class="agqa-filter-select-hidden">
                <button class="filter-select-title select-states">
                    <span class="filter-default-text">Select A Status</span>
                    <span class="filter-selected-text"></span>
                </button>
                <div class="filter-select-list">
                    <ul>
                        <li>All</li>
                        <li>Pending</li>
                        <li>Approved</li>
                        <li>Rejected</li>
                    </ul>
                </div>
            </div>
            <button type="submit" class="filter-select-button" id="agqa-game-filter"><span>Search</span></button>
        </form>
    </div>
</div>
<div class="custom-table-ctn">
    <div class="custom-table-ctn-inner">
        <div class="approval-table custom-table">
            <div class="custom-table-head">
                <div class="table-head-col">Type</div>
                <div class="table-head-col">Summary</div>
                <div class="table-head-col">Status</div>
                <div class="table-head-col">Submission Time</div>
                <div class="table-head-col">Action</div>
            </div>
            <div class="custom-table-body">
                <?php foreach ($get_data_approval as $value_approval) {
                    // var_dump($value_approval);
                ?>
                    <div class="custom-table-row">
                        <div class="table-body-col"><?php echo $value_approval->type_name; ?></div>
                        <div class="table-body-col"><?php echo $value_approval->question; ?></div>
                        <div class="table-body-col table-row-status <?php echo strtolower($value_approval->status); ?>"><span><?php echo $value_approval->status; ?></span></div>
                        <div class="table-body-col">
                            <?php
                            // Get the current system time (local server time) as a string
                            $date = new DateTime($value_approval->created_at); // Convert the string into DateTime object

                            // Convert to the 'Asia/Kolkata' time zone (Indian Standard Time)
                            $date->setTimezone(new DateTimeZone($dataTimezone));

                            // Output the time in 'Y/m/d H:i' format
                            echo $date->format('Y/m/d H:i');
                            ?>
                        </div>
                        <div class="table-body-col table-body-col-buttons">
                            <a href="#" class="approval-view-button"></a>
                            <?php 

                            if($value_approval->type_name == 'FAQ Edit' || $value_approval->type_name == 'FAQ Add'){
                                $cuim_page_value = 'faq/?edit-review=' . $value_approval->id; 
                            }else{
                                $cuim_page_value = '';
                            }
                            ?>
                            <a href="<?php echo home_url('/' . $cuim_page_value); ?>" class="approval-edit-button"></a>
                        </div>
                    </div>
                <?php  } ?>

            </div>
        </div>
    </div>
</div>