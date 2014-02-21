/*global simpleresponsiveslider_admin_params */
/**
 * Theme Options and Metaboxes.
 */
jQuery(document).ready(function($) {
    /**
     * Image plupload adds.
     */
    $(".simpleresponsiveslider-gallery-container").on("click", ".simpleresponsiveslider-gallery-add", function(e) {
        e.preventDefault();
        var gallery_frame,
            gallery_wrap = $(this).parent(".simpleresponsiveslider-gallery-container"),
            image_gallery_ids = $(".simpleresponsiveslider-gallery-field", gallery_wrap),
            images = $("ul.simpleresponsiveslider-gallery-images", gallery_wrap),
            attachment_ids = image_gallery_ids.val();
        // If the media frame already exists, reopen it.
        if (gallery_frame) {
            gallery_frame.open();
            return;
        }
        // Create the media frame.
        gallery_frame = wp.media.frames.downloadable_file = wp.media({
            title: simpleresponsiveslider_admin_params.gallery_title,
            button: {
                text: simpleresponsiveslider_admin_params.gallery_button
            },
            multiple: true,
            library: {
                type: "image"
            }
        });
        // When an image is selected, run a callback.
        gallery_frame.on("select", function() {
            var selection = gallery_frame.state().get("selection");
            selection.map(function(attachment) {
                attachment = attachment.toJSON();
                if (attachment.id) {
                    attachment_ids = attachment_ids ? attachment_ids + "," + attachment.id : attachment.id;
                    images.append('<li id="image-new" class="image image-new" data-attachment_id="' + attachment.id + '"><img src="' + attachment.url + '" /><ul class="actions"><li><a href="#" class="button delete" title="' + simpleresponsiveslider_admin_params.gallery_remove + '">X</a></li></ul></li>');
                }
            });
            image_gallery_ids.val(attachment_ids);			$('html, body').animate({				scrollTop: jQuery("#image-new").offset().top			}, 2000);
        });
        // Finally, open the modal.
        gallery_frame.open();
    });
    /**
     * Image plupload ordering.
     */
    $(".simpleresponsiveslider-gallery-container").on("mouseover", "ul.simpleresponsiveslider-gallery-images", function() {
        var gallery_wrap = $(this).parent(".simpleresponsiveslider-gallery-container"),
            image_gallery_ids = $(".simpleresponsiveslider-gallery-field", gallery_wrap);			
        // Call the sortable action.
        $(this).sortable({
            items: "li.image",
            cursor: "move",
            scrollSensitivity: 40,
            forcePlaceholderSize: true,
            forceHelperSize: false,
            helper: "clone",
            opacity: 0.65,
            placeholder: "wc-metabox-sortable-placeholder",
            start: function(event, ui) {
                ui.item.css("background-color","#f6f6f6");
            },
            stop: function(event, ui) {
                ui.item.removeAttr("style");
            },
            update: function(event, ui) {
                var attachment_ids = "";
                // Gets the current ids.
                $("li.image", $(this)).css("cursor", "default").each(function() {
                    var attachment_id = $(this).attr("data-attachment_id");
                    attachment_ids = attachment_ids + attachment_id + ",";
                });
                // Return the new value.
                image_gallery_ids.val(attachment_ids);
            }
        });
    });
    /**
     * Image plupload remove link.
     */
    $(".simpleresponsiveslider-gallery-container").on("click", "a.delete", function(e) {
        e.preventDefault();
        var gallery_wrap = $(this).parents(".simpleresponsiveslider-gallery-container"),
            image_gallery_ids = $(".simpleresponsiveslider-gallery-field", gallery_wrap);
        // Remove the item.
        $(this).closest("li.image").remove();
		$('.alert-delete').show('slow');
        var attachment_ids = "";
        // Gets the current ids.
        $("ul li.image", gallery_wrap).css("cursor","default").each(function() {
            var attachment_id = $(this).attr("data-attachment_id");
            attachment_ids = attachment_ids + attachment_id + ",";
        });
        // Return the new value.
        image_gallery_ids.val(attachment_ids);
    });
});