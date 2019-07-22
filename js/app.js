jQuery(document).ready(function( $ ) {
    $('body').on('change', '.org-dropdown', function(){
        var id = $(this).val();
        console.log(id);
        var base = $('#org-form-go').data('url');
        $('#org-form-go').attr('href', base + id);
    });
    
    $('body').on('click', '#complete-form-go', function(e){
        e.preventDefault();
        var num = $('.complete-dropdown').find(":selected").val();
        if(num == 1){
            var url = $('#meeting-creation-url').data('url');
            console.log(url);
            window.location.href = url;
        }else{
            indshaAddLoading();
            $.ajax({
                url: indsha_ajax.ajaxurl,
                dataType: 'text',
                method: 'POST',
                data: {
                    action: 'indsha_return_complete_select_ajax',
                    num: num,
                    nonce: indsha_ajax.sharon_nonce,
                },
                type: 'POST',
                success: function(e){
                    console.log(e);
                    $('.complete-management-form-container').empty();
                    $('.complete-management-form-container').append(e);
                    indshaDelLoading();
                }
            });
        }

    });

    $('body').on('change', '#upload-doc-file', function(e){
        var fileName = e.target.files[0].name;
        var split = fileName.split(".");
        type = split[split.length-1];
        console.log(type);
        if(type == 'pdf'){
            $(this).parent().find('.doc-upload-warning').remove();
        }else{
            $(this).parent().find('.doc-upload-warning').remove();
            $(this).parent().append('<div class="doc-upload-warning">This file type must be a PDF</div>');
        }
    });

    $('body').on('click', '#doc-form-save', function(e){
        e.preventDefault();
        $('.required-form-text').each(function(){
            $(this).remove();
        })
        $('.upload-required').each(function(){
            if($(this).val() == ""){
                $(this).parent().append('<div class="required-form-text">This field is required</div>');
            }
        })
        if($('body').find('.doc-upload-warning').length > 0 || $('.required-form-text').length > 0){
            alert('You must fill out all required fields and follow the upload requirements.');
        }else{
            indshaAddLoading();
            var fd = new FormData();
            var file = $('#upload-doc-file').prop('files')[0];
            fd.append('file', file);
            // fd.append('form_data', data);
            fd.append('doc-title', $('#upload-doc-title').val());
            fd.append('doc-date', $('#upload-doc-date').val());
            fd.append('org', $('#doc-organization').val());
            fd.append('cat', $('#doc-category').val());
            fd.append('nonce', indsha_ajax.sharon_nonce);
            fd.append('action', 'indsha_upload_doc_ajax');
            console.log(fd);
            $.ajax({
                url: indsha_ajax.ajaxurl,
                method: 'POST',
                contentType: false,
                processData: false,
                data: fd,
                type: 'POST',
                success: function (response) {
                    console.log(response);
                }
            });
            indshaDelLoading();
        }
    })

    $('body').on('change', '#doc-category', function(){
        if($(this).val() > 0 && !$('#doc-organization').hasClass('upload-required')){
            $('#doc-organization').addClass('upload-required');
        }else{
            $('#doc-organization').removeClass('upload-required');
        }
    })

    $('body').on('click', '.event-add-doc-btn', function(e){
        e.preventDefault();
        var terms = $('#get_current_terms').html();
        console.log(terms);
        $('.event-doc-container').append("<span class='ind-add-doc-container'><span class='event-doc-file-title-text'>Title: </span><input type='text' class='event-doc-file-title'><input type='file' class='event-doc-file' name='my_file_upload[]'><select name='my_file_cat[]' class='my-file-cat doc-org-dropdown'>" + terms + "</select><a href='#' class='remove-add-document red-text'>Remove</a></span>");
    })

    $('body').on('click', '.remove-add-document', function(e){
        e.preventDefault();
        $(this).parent().remove();
    })

    $('body').on('click', '#event-form-save', function(e){
        e.preventDefault();
        $('.required-form-text').each(function(){
            $(this).remove();
        })
        $('.meeting-required').each(function(){
            if($(this).val() == ""){
                $(this).parent().append('<div class="required-form-text">This field is required</div>');
            }
        })
        if($('.required-form-text').length > 0 || $('body').find('.doc-upload-warning').length > 0){
            alert('You must fill out all required fields and follow the upload requirements.');
        }else{
            indshaAddLoading();
            var agenda = $('#event-doc-agenda').prop('files')[0];
            var file_array = [];
            var fd = new FormData();
            var count = 1;
            var title_array = [];
            $('.event-doc-file').each(function(){
                if($.inArray($(this).prev().val(), title_array) >= 0){
                    var title = $(this).prev().val() + count;
                    count++;
                }else{
                    var title = $(this).prev().val();
                }
                fd.append(title, $(this).prop('files')[0]);
                title_array.push($(this).prev().val());

            });
            var cat_array = [];
            $('.my-file-cat').each(function(){
                cat_array.push($(this).val());
            })
            console.log(tinymce.editors['event-doc-content'].getContent());
            fd.append('agenda', agenda);
            // fd.append('file_array', file_array);
            fd.append('org', $('#doc-organization').val());
            fd.append('date', $('#event-doc-date').val());
            fd.append('content', tinymce.editors['event-doc-content'].getContent());
            fd.append('special', $('#event-doc-special').is(':checked'));
            fd.append('nonce', indsha_ajax.sharon_nonce);
            fd.append('cat', cat_array);
            fd.append('action', 'indsha_save_event_ajax');
            $.ajax({
                url: indsha_ajax.ajaxurl,
                method: 'POST',
                contentType: false,
                processData: false,
                data: fd,
                type: 'POST',
                success: function(response){
                    console.log(response);
                    indshaDelLoading();
                }
            });        
            console.log(agenda);
        }
        
    })

    $('body').on('change', '#event-doc-agenda', function(e){
        var fileName = e.target.files[0].name;
        var split = fileName.split(".");
        type = split[split.length-1];
        console.log(type);
        if(type == 'pdf'){
            $(this).parent().find('.doc-upload-warning').remove();
        }else{
            $(this).parent().find('.doc-upload-warning').remove();
            $(this).parent().append('<div class="doc-upload-warning">This file type must be a PDF</div>');
        }
    });

    $('body').on('change', '#meeting-organization', function(){
        indshaAddLoading();
        var org = $(this).val();
        $.ajax({
            url: indsha_ajax.ajaxurl,
            dataType: 'text',
            method: 'POST',
            data: {
                action: 'indsha_get_meetings_ajax',
                nonce: indsha_ajax.sharon_nonce,
                org: org,
            },
            type: 'POST',
            success: function(e){
                console.log(e);

                $('#meeting-meeting').empty();
                $('#meeting-meeting').append('<option value="" dissabled="" selected="">Select an event</option>' + e);
                indshaDelLoading();
            }
        });
    })

    $('body').on('change', '#meeting-category', function(){
        if($(this).val() == 122){
            console.log($(this).val());
            $("#minutes-override-label").removeClass('hide');
        }else{
            if(!$('#minutes-override-label').hasClass('hide')){
                $('#minutes-override-label').addClass('hide');
            }
        }
    });

    $('body').on('click', '#meeting-form-save', function(e){
        e.preventDefault();
        $('.required-form-text').each(function(){
            $(this).remove();
        })
        $('.upload-required').each(function(){
            if($(this).val() == ""){
                $(this).parent().append('<div class="required-form-text">This field is required</div>');
            }
        })
        if($('body').find('.meeting-upload-warning').length > 0 || $('.required-form-text').length > 0){
            alert('You must fill out all required fields and follow the upload requirements.');
        }else{
            indshaAddLoading();
            var fd = new FormData();
            var file = $('#upload-meeting-file').prop('files')[0];
            fd.append('file', file);
            // fd.append('form_data', data);
            fd.append('override', $('#minutes-override').is(":checked"));
            fd.append('meeting', $('#meeting-meeting').val());
            fd.append('org', $('#meeting-organization').val());
            fd.append('cat', $('#meeting-category').val());
            fd.append('nonce', indsha_ajax.sharon_nonce);
            fd.append('action', 'indsha_upload_meeting_ajax');
            // console.log($('#minutes-override').is(':checked'));
            $.ajax({
                url: indsha_ajax.ajaxurl,
                method: 'POST',
                contentType: false,
                processData: false,
                data: fd,
                type: 'POST',
                success: function (response) {
                    console.log(response);
                }
            });
            indshaDelLoading();
        }
    })


    // report a concern menu button
    $('body').on('click', '#menu-global-menu .alert-button', function(e){
        e.preventDefault();
        indshaAddLoading();
        $.ajax({
            url: indsha_ajax.ajaxurl,
            dataType: 'text',
            method: 'POST',
            data: {
                action: 'indsha_report_a_concern_ajax',
                nonce: indsha_ajax.sharon_nonce,
            },
            type: 'POST',
            success: function(e){
                e = JSON.parse(e);
                console.log(e['filename']);
                var script = document.createElement( 'script' );
                script.type = 'text/javascript';
                script.src = e['filename'];
                $( "head" ).prepend( script );
                $('body').prepend(e['modal']);
                indshaDelLoading();
            }
        });
    });

    $('body').on('click', '.ind-modal-x', function(){
        $('.ind-modal-container').remove();
    });
    $('body').on('click', '.ind-modal-bg', function(){
        $('.ind-modal-container').remove();
    });
});

function indshaAddLoading(){
    var location='body';
    var primary='white';
    var secondary='white';
    var background='indsha-loading-background';
    jQuery(location).append("<div class='indsha-loading-background-container'><div class=" + background + "><div id='indsha-loading-icon'><svg class='ind-image' width='100' height='100'><path d='M5,50 a1,1 0 0,0 90,0' fill='none' stroke-opacity='0.9' stroke='" + primary + "' stroke-width='9'/></svg><svg class='ind-image-rev' width='100' height='100'><path d='M2,50 a1,1 0 0,1 96,0' fill='none' stroke-opacity='0.7' stroke='" + secondary + "' stroke-width='3.6'/></svg><svg class='ind-image-rev-2' width='100' height='100'><path d='M10,50 a40,40 0 0,0  40,40' stroke-width='6' stroke-opacity='0.7' stroke='" + secondary + "' fill='none'</></svg></div></div></div>");
}
function indshaDelLoading(){
    jQuery('.indsha-loading-background-container').remove();
}

// start of stand alone functions