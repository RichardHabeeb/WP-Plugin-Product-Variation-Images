/* Open WordPress Media Library
 * https://wordpress.stackexchange.com/a/363944 */
const wpOpenGallery = function(o, callback) {
    const options = (typeof o === 'object') ? o : {};

    // Predefined settings
    const defaultOptions = {
        title: 'Select Media',
        fileType: 'image',
        multiple: false,
        currentValue: '',
    };

    const opt = { ...defaultOptions, ...options };

    let image_frame;

    if(image_frame){
        image_frame.open();
    }

    // Define image_frame as wp.media object
    image_frame = wp.media({
        title: opt.title,
        multiple : opt.multiple,
        library : {
            type : opt.fileType,
        }
    });

    image_frame.on('open',function() {
        // On open, get the id from the hidden input
        // and select the appropiate images in the media manager
        const selection =  image_frame.state().get('selection');
        const ids = opt.currentValue.split(',');

        ids.forEach(function(id) {
            const attachment = wp.media.attachment(id);
            attachment.fetch();
            selection.add( attachment ? [ attachment ] : [] );
        });
    });

    image_frame.on('close',function() {
        // On close, get selections and save to the hidden input
        // plus other AJAX stuff to refresh the image preview
        const selection =  image_frame.state().get('selection');
        const files = [];

        selection.each(function(attachment) {
            files.push({
                id: attachment.attributes.id,
                filename: attachment.attributes.filename,
                url: attachment.attributes.url,
                type: attachment.attributes.type,
                subtype: attachment.attributes.subtype,
                sizes: attachment.attributes.sizes,
            });
        });

        callback(files);
    });

    image_frame.open();
};

(function($) {
    $(document).ready(function() {
        $("body").on("click", ".pvi-btn-add-image", function(e) {
            e.preventDefault();

            const controlled = $("#" + $(e.target).attr("aria-controls"));
            wpOpenGallery(
                {
                    multiple:false,
                    currentValue: controlled.val() || "",
                },
                function(selected) {
                    controlled.val(selected.map((s) => s.id).join(",")).trigger("change");
                }
            );
        });
    });
}(jQuery));
