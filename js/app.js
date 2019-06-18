jQuery(document).ready(function( $ ) {
    $('body').on('change', '.org-dropdown', function(){
        indshaAddLoading();
        var id = $(this).val();
        console.log(id);
        $.ajax({
            url:indsha_ajax.ajaxurl,
            dataType: 'text',
            method: 'POST',
            data: {
                action: 'indsha_get_org_form',
                id:id,
            },
            type: 'POST',
            success: function(e){
                console.log(e);
                e = JSON.parse(e);
                indshaDelLoading();
                $('.org-management-form-container').empty();
                $('.org-management-form-container').append(e['output']);
            }
        });
    })

    $('body').on('submit', '#cred_form_4803_1', function(e){
        e.preventDefault();
        indshaAddLoading();
        var org_id = $('.org-dropdown').val();
        var address = $('[name="wpcf-org-address"]').val();
        var title = $('[name="post_title"]').val();
        var content = $('[name="post_content"]').val();
        var email = $('[name="wpcf-org-email"]').val();
        var contact = $('[name="wpcf-org-point-of-contact"').val();
        var hours = $('[name="wpcf-org-hours-of-operation"]').val();
        var phone = $('[name="wpcf-org-phone"]').val();
        console.log(address);
        $.ajax({
            url:indsha_ajax.ajaxurl,
            dataType: 'text',
            method: 'POST',
            data: {
                action: 'indsha_save_org_form',
                org_id: org_id,
                title: title,
                content: content,
                email: email,
                contact: contact,
                hours: hours,
                phone: phone,
                address: address,
            },
            type: 'POST',
            success: function(e){
                console.log(e);
                indshaDelLoading();
            }
        });
    })

    function indshaAddLoading(location = 'body', primary = 'white', secondary = 'white', background = 'indsha-loading-background'){
        jQuery(location).append("<div class='indsha-loading-background'><div class=" + background + "><div id='indsha-loading-icon'><svg class='image' width='100' height='100'><path d='M5,50 a1,1 0 0,0 90,0' fill='none' stroke-opacity='0.9' stroke='" + primary + "' stroke-width='9'/></svg><svg class='image-rev' width='100' height='100'><path d='M2,50 a1,1 0 0,1 96,0' fill='none' stroke-opacity='0.7' stroke='" + secondary + "' stroke-width='3.6'/></svg><svg class='image-rev-2' width='100' height='100'><path d='M10,50 a40,40 0 0,0  40,40' stroke-width='6' stroke-opacity='0.7' stroke='" + secondary + "' fill='none'</></svg></div></div></div>");
    }
    
    function indshaDelLoading(){
        jQuery('.indsha-loading-background').remove();
    }

});

// start of stand alone functions