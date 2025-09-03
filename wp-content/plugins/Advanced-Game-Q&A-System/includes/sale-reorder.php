<div class="api-filter-buttons">
    <div class="reorder-ctn">
        <div class="reorder-popup">
            <div class="reorder-popup-inner">
                <div class="reorder-popup-close"></div>
            <div class="reorder-popup-title">
                <h2>Reorder</h2>
            </div>
            <div class="sort-by-dropdown">
                <button class="sort-by-dropdown-button">
                    <img src="<?php echo AGQA_URL ?>assets/images/sort-by-icon.svg" alt="Sort By Icon">
                    <div class="default-text">
                      <?php if($sort_order == "asc") { ?>
                                    Lowest to Highest
                            <?php } ?>
                            <?php if($sort_order == "desc") { ?>
                                    Highest to Lowest
                            <?php } ?>
                              <?php if(empty($sort_order)) { ?>
                                    Sort By
                            <?php } ?>    
                    </div>
                    <div class="sortby-selected-text"></div>
                </button>
                <div class="sort-by-dropdown-lists">
                     <ul>
                        <li data-sort="desc">Highest to Lowest</li>
                        <li data-sort="asc">Lowest to Highest</li>
                    </ul>
                    <button type="button" class="sort-by-reset-button">Reset to Default</button>
                </div>
            </div>
            <?php if ($revenu_data) {?>
                <div class="reorder-selling-price-tiles">
                    <?php foreach ($revenu_data as $rev_item) {?>
                        <div class="reorder-selling-price-tile" data-sale-id="<?php echo $rev_item->id; ?>">
                            <div class="selling-price-tile-item">
                                <span>Provider Name</span>
                                <div class="selling-price-tile-logo">
                                    <img src="<?php echo esc_url($rev_item->image_url); ?>" alt="Game Provider Logo">
                                </div>
                            </div>
                            <div class="selling-price-tile-item agqa-selling-price-title">
                                <span>Selling Price (%)</span>
                                <h2><?php echo esc_html(number_format($rev_item->min_revenue_share, 0)); ?>%</h2>
                            </div>
                            <div class="selling-price-tile-item">
                                <span>API Cost (%)</span>
                                <h2><?php echo esc_html(number_format($rev_item->max_resale_share, 0)); ?>%</h2>
                            </div>
                        </div>
                    <?php }?>
                </div>
            <?php }?>
            <div class="agqa-popup-form-field agqa-popup-form-buttons d-flex">
                  <form id="agqa-sort-sales">
                    <input type="hidden" name="sort-by-sales">
                    <button class="cancel-button">Cancel</button>
                    <input type="submit" value="Submit" class="">
                </form>
            </div>
            </div>
        </div>
    </div>
</div>
<script>
    jQuery(document).ready(function($) {
    // Listen for the sorting option clicks
    $('.sort-by-dropdown-lists ul li').on('click', function() {
        // Get the sorting order: ascending or descending
        var sortOrder = $(this).data('sort');

        // Get the items to sort
        var $tiles = $('.reorder-selling-price-tile');

        $tiles.sort(function(a, b) {
            var priceA = parseFloat($(a).find('.agqa-selling-price-title h2').text().replace('%', '').trim());
            var priceB = parseFloat($(b).find('.agqa-selling-price-title h2').text().replace('%', '').trim());

            // Sort based on the order (ascending or descending)
            if (sortOrder === 'desc') {
                return priceB - priceA; 
            } else {
                return priceA - priceB; 
            }
        });

        // Append the sorted tiles back to the container
        $('.reorder-selling-price-tiles').html($tiles);
        $('input[type="hidden"]').val(sortOrder);
    });

    $('.sort-by-reset-button').on('click', function() {
        // Get all the elements with the class '.reorder-selling-price-tile'
        var items = $('.reorder-selling-price-tile');

        // Sort the elements based on 'data-sale-id' (from high to low)
        items.sort(function(a, b) {
            var idA = $(a).data('sale-id');
            var idB = $(b).data('sale-id');
            
            
            // Sorting in descending order (high to low)
            return idB - idA;
        });

        // Reorder the elements in the DOM
    $('.reorder-selling-price-tiles').append(items); // Append the sorted items to the parent container

        // Reset button functionality
        $('.default-text').text('Sort By');
        $('input[type="hidden"]').val('');
    });

});

</script>