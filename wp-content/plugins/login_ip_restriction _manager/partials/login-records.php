<?php
$getUserRole = get_user_role_simple();
if ($getUserRole == 'viewer') {
    echo "Permission not allowed";
    return;
}
global $wpdb;
$table_login_records_list = $wpdb->prefix . 'agqa_wiki_login_records';
$add_username = isset($_GET['username']) ? $_GET['username'] : '';
// echo $add_username;
$get_login_records_list = $wpdb->get_results("
            SELECT
                id,
                user_id,
                account,
                login_ip,
                created_at
            FROM $table_login_records_list
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
    .login-records-filter input[type="search"] {
        min-width: 409px;
    }

    .login-records-filter form {
        gap: 16px;
    }

    .login-records-filter .date-field {
        min-width: 416px;
    }

    .login-records-filter input[type="search"]:focus,
    .login-records-filter input[type="date"]:focus {
        background-color: #2b2937 !important;
    }

    .table-head-col:nth-child(1),
    .table-body-col:nth-child(1) {
        width: 37.888%;
    }

    .table-head-col:nth-child(2),
    .table-body-col:nth-child(2) {
        width: 30.31%;
    }

    .table-head-col:nth-child(3) {
        text-align: left;
    }

    .table-head-col:nth-child(3),
    .table-body-col:nth-child(3) {
        width: 31.7%;
    }
    select:not(.esg-sorting-select):not([class*="trx_addons_attrib_"]):not([size]) {
        visibility: visible !important;
        width: 88% !important;
        margin: 0 10px;
    }

    .hourselect,
    .minuteselect {
        background-color: #6e6e6e !important;
        border: none !important;
        width: 30%;
        margin: unset;

    }

    .daterangepicker select {
        background: #747474 url('<?php echo URIP_URL; ?>assets/image/arrow-down-icon.svg') no-repeat right 21px center / 13px !important;
    }
    .daterangepicker .calendar-time {
        display: flex;
        justify-content: center;
        margin-top: 20px;
    }


    @media (max-width: 1760px) {
        .login-records-table {
            width: 100%;
        }
    }

    @media (max-width: 1735px) and (min-width: 768px) {
        .login-records-filter .date-field {
            min-width: clamp(18.75rem, 16.315vw + 8.308rem, 26rem);
        }

        .login-records-filter input[type="search"] {
            min-width: clamp(14.375rem, 25.176vw + -1.738rem, 25.563rem);
        }

        .login-records-filter form {
            flex-wrap: nowrap;
        }
    }
</style>

<div class="login-records-template">
    <div class="template-title">
        <h1>Login Records</h1>
    </div>
    <div class="login-records-filter filter-area">
        <form action="#">
            <div class="filter-search-field">
                <input type="search" value="<?php echo $add_username;  ?>" class="cuim-login-records-search-validation-254" maxlength="254" name="login-records-search" id="login-records-search" placeholder="Search By Account">
            </div>
            <div class="login-records-filter-date">
                <div class="filter-select date-field">
                    <input type="text" name="daterange" class="select-date-picker" value="" id="daterange"
                        placeholder="YYYY/MM/DD 00:00 - YYYY/MM/DD 00:00">
                    <span class="calendar-icon"></span>
                </div>
            </div>
            <button type="submit" class="filter-select-button" id="agqa-login-records-filters"><span>Search</span></button>
        </form>
    </div>
    <div class="custom-table-ctn">
        <div class="custom-table-ctn-inner">
            <div class="login-records-table custom-table">
                <div class="custom-table-head">
                    <div class="table-head-col">Account</div>
                    <div class="table-head-col"> Login Time</div>
                    <div class="table-head-col">Login IP Address</div>
                </div>
                <div class="custom-table-body">
                    <?php foreach ($get_login_records_list as  $records_value) { ?>
                        <div class="custom-table-row">
                            <div class="table-body-col login-record-account"><?php echo $records_value->account; ?></div>
                            <div class="table-body-col table-body-col-date">
                                <?php
                                // Get the current system time (local server time) as a string
                                $date = new DateTime($records_value->created_at); // Convert the string into DateTime object

                                // Convert to the 'Asia/Kolkata' time zone (Indian Standard Time)
                                $date->setTimezone(new DateTimeZone($dataTimezone));

                                // Output the time in 'Y/m/d H:i' format
                                echo $date->format('Y/m/d H:i');
                                ?>
                            </div>
                            <div class="table-body-col"><?php echo $records_value->login_ip; ?></div>
                        </div>
                    <?php  } ?>
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
        var itemsPerPage = 15;
        var totalItems = jQuery(".login-records-template .custom-table-row").length;
        var totalPages = Math.ceil(totalItems / itemsPerPage);

        // If no rows exist, disable pagination and return
        if (totalItems === 0) {
            jQuery(".login-records-template #pagination-demo").hide(); // Hide pagination if no items
            return;
        }

        jQuery(".login-records-template #pagination-demo").twbsPagination({
            totalPages: totalPages,
            visiblePages: 3,
            onPageClick: function(event, page) {
                // Hide all rows first
                jQuery(".login-records-template .custom-table-row").hide();

                // Show the rows for the current page
                jQuery('.login-records-template .custom-table-row[data-page="' + page + '"]').show();

                // Calculate the active items on the current page
                var totalActiveItems = jQuery(".custom-table-row.active").length;
                var totalActivePages = Math.ceil(totalActiveItems / itemsPerPage);

                // Show/hide pagination links based on the active pages
                jQuery(".login-records-template .pagination-ctn ul li.page-item").nextAll().not(".next").show();

                jQuery(".login-records-template .pagination-ctn ul li.page-item").not(".prev, .next").each(function() {
                    var pageNumberss = parseInt(jQuery(this).text()); // Get the number of the page

                    if (pageNumberss === totalActivePages && totalActivePages !== 0) {
                        // Hide all <li> items that come after the last active page
                        jQuery(this).nextAll().not(".next").hide();

                        // Check if the "Next" button should be disabled
                        var prevLi = jQuery(".login-records-template .pagination-ctn ul li.page-item.active").next();

                        // Disable or enable the "Next" button based on the visibility of the next page
                        if (prevLi.is(":hidden")) {
                            jQuery(".login-records-template .pagination-ctn ul li.next").addClass("disabled"); // Disable Next button
                        } else {
                            jQuery(".login-records-template .pagination-ctn ul li.next").removeClass("disabled"); // Enable Next button
                        }
                    }
                });
            },
        });

        // Loop through each row and assign a page number based on its index
        jQuery(".login-records-template .custom-table-row").each(function(index) {
            var page = Math.floor(index / itemsPerPage) + 1;
            jQuery(this).attr("data-page", page); // Assign page data attribute

            // Initially show or hide based on the page
            if (page === 1) {
                jQuery(this).show();
            } else {
                jQuery(this).hide();
            }
        });

        jQuery('input[name="daterange"]').daterangepicker({
            opens: 'right',
            timePicker: true,
            timePicker24Hour: true,
            locale: {
                format: 'YYYY/MM/DD HH:mm'
            },
            startDate: moment().startOf('month').startOf('day'),
            endDate: moment().endOf('month').endOf('day'),
            minDate: moment().startOf('month').startOf('day'),
            maxDate: moment().endOf('month').endOf('day'),
            autoUpdateInput: false // page load par input empty rahega
        });

// jab user range select kare
        jQuery('input[name="daterange"]').on('apply.daterangepicker', function(ev, picker) {
            jQuery(this).val(picker.startDate.format('YYYY/MM/DD HH:mm') + ' - ' + picker.endDate.format('YYYY/MM/DD HH:mm'));
        });



        // Handle the cancel or clear action
        jQuery('input[name="daterange"]').on(
            "cancel.daterangepicker",
            function(ev, picker) {
                jQuery(this).val(""); // Reset the input field to empty when the user cancels or clears the date range
            }
        );
    <?php if($add_username !== ''){ ?>    
         setTimeout(function() {
            var $btn = jQuery('#agqa-login-records-filters');
            const btn = $btn.get(0);
            // Create and dispatch mouse events
            ['mousedown', 'mouseup', 'click'].forEach(eventType => {
                btn.dispatchEvent(
                    new MouseEvent(eventType, {
                        view: window,
                        bubbles: true,
                        cancelable: true
                    })
                );
            });

        }, 2000);
    <?php } ?>
    });
</script>