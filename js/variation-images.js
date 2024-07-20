(function($) {
    $(document).ready(function() {
        // Set default image
        $('.Specifications-draw').append('<img class="pvi-variation-image" src="/wp-content/uploads/Artboard-1-1.png">');

        // Append .pvi-variation-image-wrap div
        $('.images').append('<div class="pvi-variation-image-wrap"></div>');

        // When a variation is selected
        $(document).on('found_variation', function(event, variation) {
            // Remove any existing additional images and <a> tags
            $('.pvi-variation-image').remove();
            $('.pvi-variation-image-top').remove();
            $('.pvi-variation-image-wrap a').remove();
            $('.Specifications-draw a').remove();

            // If the variation has additional images for top section
            if (variation.pvi_variation_images_top) {
                // Loop through additional images and append them to the product gallery
                $.each(variation.pvi_variation_images_top, function(index, imageUrlTop) {
                    $('.pvi-variation-image-wrap').append('<a href="' + imageUrlTop + '" target="_blank"><img class="pvi-variation-image-top" src="' + imageUrlTop + '"></a>');
                });
            }

            // If the variation has additional images for spec section
            if (variation.pvi_variation_images) {
                // Loop through additional images and append them to the product gallery
                $.each(variation.pvi_variation_images, function(index, imageUrl) {
                    $('.Specifications-draw').append('<a href="' + imageUrl + '" target="_blank"><img class="pvi-variation-image" src="' + imageUrl + '"></a>');
                });
            }

            // If no additional images
            if (!variation.pvi_variation_images) {
                // Set default image
                $('.Specifications-draw').append('<img class="pvi-variation-image-top" src="/wp-content/uploads/Artboard-1-1.png">');
            }
        });
    });
})(jQuery);
