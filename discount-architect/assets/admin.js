jQuery(document).ready(function($) {
    // Tab Switching Logic
    $('.nav-tab').on('click', function(e) {
        e.preventDefault();
        
        const target = $(this).attr('href').substring(1); // operation or documentation
        
        // Update Tabs
        $('.nav-tab').removeClass('nav-tab-active');
        $(this).addClass('nav-tab-active');
        
        // Update Sections
        $('.da-tab-content').hide();
        $('#section-' + target).fadeIn(300);
        
        // Update URL hash without jumping
        history.pushState(null, null, '#' + target);
    });

    // Handle Hash on Load
    const currentHash = window.location.hash;
    if (currentHash && $('.nav-tab[href="' + currentHash + '"]').length) {
        $('.nav-tab[href="' + currentHash + '"]').trigger('click');
    }

    function initSelect2() {
        if ($.fn.select2) {
            $('.da-select2').select2({
                ajax: {
                    url: da_vars.ajax_url,
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            action: 'da_search_targets',
                            nonce: da_vars.nonce,
                            q: params.term,
                            type: $('#da_scope').val().includes('cat') ? 'category' : 'product'
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data.data
                        };
                    },
                    cache: true
                },
                placeholder: 'Type to search (Product Name or SKU)...',
                minimumInputLength: 2,
                width: '100%',
                language: {
                    inputTooShort: function() {
                        return "Please enter 2 or more characters to search...";
                    }
                }
            });
        }
    }

    initSelect2();

    // Toggle rows based on scope
    $('#da_scope').on('change', function() {
        const scope = $(this).val();
        
        if (scope === 'global') {
            $('#da_targets_row').hide();
            $('#da_price_threshold_row').hide();
            $('#da_price_threshold_max_row').hide();
        } else if (scope === 'product' || scope === 'category') {
            $('#da_targets_row').show();
            $('#da_price_threshold_row').hide();
            $('#da_price_threshold_max_row').hide();
        } else if (scope === 'price_gt' || scope === 'price_lt') {
            $('#da_targets_row').hide();
            $('#da_price_threshold_row').show();
            $('#da_price_threshold_max_row').hide();
            $('#da_price_label_min').text('Price Threshold');
            $('#da_price_desc_min').text('Enter the price limit.');
        } else if (scope === 'price_between') {
            $('#da_targets_row').hide();
            $('#da_price_threshold_row').show();
            $('#da_price_threshold_max_row').show();
            $('#da_price_label_min').text('Min Price');
            $('#da_price_desc_min').text('Enter the minimum price.');
        } else if (scope === 'cat_price_gt' || scope === 'cat_price_lt') {
            $('#da_targets_row').show();
            $('#da_price_threshold_row').show();
            $('#da_price_threshold_max_row').hide();
            $('#da_price_label_min').text('Price Threshold');
            $('#da_price_desc_min').text('Enter the price limit.');
        } else if (scope === 'cat_price_between') {
            $('#da_targets_row').show();
            $('#da_price_threshold_row').show();
            $('#da_price_threshold_max_row').show();
            $('#da_price_label_min').text('Min Price');
            $('#da_price_desc_min').text('Enter the minimum price.');
        }

        // Reset select2 search type and clear if needed
        $('#da_targets').val(null).trigger('change');
    }).trigger('change');

    // SweetAlert2 Toast Configuration
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer)
            toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
    });

    // Handle Delete Confirmation with SweetAlert2
    $('.da-delete-rule').on('click', function(e) {
        e.preventDefault();
        const deleteUrl = $(this).attr('href');

        Swal.fire({
            title: 'Are you sure?',
            text: "This discount rule will be permanently removed!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!',
            background: '#fff',
            borderRadius: '12px'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = deleteUrl;
            }
        });
    });

    // Show Toasts based on URL messages
    const urlParams = new URLSearchParams(window.location.search);
    const message = urlParams.get('message');

    if (message === 'saved') {
        Toast.fire({
            icon: 'success',
            title: 'Rule saved successfully'
        });
    } else if (message === 'deleted') {
        Toast.fire({
            icon: 'success',
            title: 'Rule deleted successfully'
        });
    }

    // Hide original WP notices if they exist
    $('.notice.is-dismissible').hide();
});
