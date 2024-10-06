jQuery(document).ready(function($) {
    $('#post_type').on('change', function() {
        var postType = $(this).val();
        
        // Hide all meta fields containers
        $('.meta-fields').hide();
        
        // Show meta fields for the selected post type
        if (postType) {
            $('.meta-fields[data-post-type="' + postType + '"]').show();
        }
    });

    $('#include_metadata').on('change', function() {
        if ($(this).is(':checked')) {
            $('#meta_fields_container').show();
        } else {
            $('#meta_fields_container').hide();
        }
    });
});
