jQuery(document).ready(function($){
    $(document).on('click', '.js-upload-image', function(e) {
        e.preventDefault();
        var button = $(this);
        var targetInputId = button.data('target');
        
        var uploader = wp.media({
            title: '选择图片',
            button: {
                text: '使用此图片'
            },
            multiple: false
        });

        uploader.on('select', function() {
            var attachment = uploader.state().get('selection').first().toJSON();
            $(targetInputId).val(attachment.url).trigger('change');
            
            // Robust Preview Handling
            // 1. Try to find existing preview by class
            var previewDiv = button.siblings('.less-image-preview');
            
            // 2. Fallback for invalid HTML structure (where P tag was split by browser)
            // If button is inside a P, and preview div was inserted, it might be outside the P
            if (previewDiv.length === 0) {
                var parentNext = button.parent().next();
                if (parentNext.hasClass('less-image-preview') || (parentNext.is('div') && parentNext.find('img').length > 0)) {
                    previewDiv = previewDiv.add(parentNext);
                }
            }

            // 3. Fallback: Find next siblings that are divs with images (Backward compatibility)
            // This handles cases where the class is missing or multiple previews exist
            var nextPreviews = button.nextAll('div').filter(function() {
                return $(this).find('img').length > 0;
            });
            
            // Merge collections
            previewDiv = previewDiv.add(nextPreviews);

            if (previewDiv.length > 0) {
                // Update the first one
                var first = previewDiv.first();
                first.find('img').attr('src', attachment.url);
                if (!first.hasClass('less-image-preview')) {
                    first.addClass('less-image-preview');
                }
                
                // Remove any duplicates
                previewDiv.not(first).remove();
            } else {
                button.after('<div class="less-image-preview" style="margin-top:10px;"><img src="' + attachment.url + '" style="max-height: 100px;"></div>');
            }
        });

        uploader.open();
    });

    // Social Sortable
    if ($('#social-sort-list').length) {
        $('#social-sort-list').sortable({
            update: function(event, ui) {
                var order = [];
                $('#social-sort-list li').each(function() {
                    order.push($(this).data('key'));
                });
                $('#social_sort_order').val(order.join(','));
            }
        });
    }
});
