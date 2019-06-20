jQuery(document).ready(function( $ ) {
    $('body').on('change', '.org-dropdown', function(){
        var id = $(this).val();
        console.log(id);
        var base = $('#org-form-go').data('url');
        $('#org-form-go').attr('href', base + id);
    })

    function indshaAddLoading(location = 'body', primary = 'white', secondary = 'white', background = 'indsha-loading-background'){
        jQuery(location).append("<div class='indsha-loading-background'><div class=" + background + "><div id='indsha-loading-icon'><svg class='ind-image' width='100' height='100'><path d='M5,50 a1,1 0 0,0 90,0' fill='none' stroke-opacity='0.9' stroke='" + primary + "' stroke-width='9'/></svg><svg class='ind-image-rev' width='100' height='100'><path d='M2,50 a1,1 0 0,1 96,0' fill='none' stroke-opacity='0.7' stroke='" + secondary + "' stroke-width='3.6'/></svg><svg class='ind-image-rev-2' width='100' height='100'><path d='M10,50 a40,40 0 0,0  40,40' stroke-width='6' stroke-opacity='0.7' stroke='" + secondary + "' fill='none'</></svg></div></div></div>");
    }
    
    function indshaDelLoading(){
        jQuery('.indsha-loading-background').remove();
    }

});

// start of stand alone functions