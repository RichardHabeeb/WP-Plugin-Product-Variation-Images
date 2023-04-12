jQuery(document).ready(function($) {
    // When a variation is selected
    $(document).on('found_variation', function(event, variation) {
        // If the variation has additional images
        if (variation.pvi_variation_images) {
            // Remove any existing additional images
            $('.pvi-variation-image').remove();
            // Loop through additional images and append them to the product gallery
            $.each(variation.pvi_variation_images, function(index, imageUrl) {
                $('.Specifications-draw').append('<img class="pvi-variation-image" src="' + imageUrl + '">');
            });
        } else {
            // If no additional images, remove any existing ones
            $('.pvi-variation-image').remove();
        }
    });
});