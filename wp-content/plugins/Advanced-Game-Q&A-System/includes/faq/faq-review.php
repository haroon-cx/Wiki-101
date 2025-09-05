<?php


global $wpdb;
$table_agqa_faq_review = $wpdb->prefix . 'agqa_faq_review';

$faq_data_review = $wpdb->get_results("
            SELECT
                id,
                question,
                answer,
                faq_category,
                verified_answer,
                status
            FROM $table_agqa_faq_review
            ORDER BY id DESC
        ");


?>


<div class="faq-template">
    <div id="page-content">
        <!-- Content will be dynamically updated based on pagination -->
    </div>
    <div class="template-title">
        <h1>FAQ Review</h1>
    </div>


    <!-- Main Content Start -->
    <div class="faq-main-content">
        <div class="faq-accordions">
            <?php foreach ($faq_data_review as $faq_value) {
            ?>
            <div class="faq-accordion">
                <div class="faq-accodion-status"><?php echo $faq_value->faq_category; ?></div>
                <div class="faq-accordion-head">
                    <h2><?php echo $faq_value->question; ?></h2>
                    <button class="button agqa-status-toggle"
                        style="background: transparent !important; padding: 0; padding-left: 10px;">
                        <img src="<?php echo AGQA_URL ?>assets/images/accordian-arrow.svg" alt="Arrow">
                    </button>
                </div>
                <div class="faq-accordion-body">
                    <?php if ($faq_value->answer) { ?>
                    <p><?php echo $faq_value->answer; ?></p>
                    <?php } ?>
                </div>
                <div class="faq-accordion-bottom">

                    <a href="<?php echo esc_url(home_url('faq/') . '?edit-review=' . $faq_value->id); ?>"
                        class="faq-accordion-button edit-button">
                        <div class="faq-accordion-icon">
                            <img src="<?php echo AGQA_URL ?>assets/images/edit-icon.svg" alt="Edit Icon">
                        </div>
                        Edit
                    </a>

                    <button class="faq-accordion-button verified-button">
                        <?php if ($faq_value->status == 'approve') { ?>
                        <div class="faq-accordion-icon">
                            <img src="<?php echo AGQA_URL ?>assets/images/verified-icon.svg" alt="Verified Answer Icon">
                        </div>
                        <?php  } ?>

                        <span><?php echo ucwords($faq_value->status); ?></span>

                    </button>
                </div>
            </div>
            <?php } ?>
        </div>
    </div>
    <div class="pagination-ctn">
        <div id="pagination-demo"></div>
    </div>
    <!-- Main Content End -->
</div>