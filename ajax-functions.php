<?php

function indsha_return_complete_select_ajax(){
    $security = check_ajax_referer( 'public_nonce', 'nonce');
    if(!$security){
        die();
    }
    if(isset($_POST['num'])){
        $num = $_POST['num'];
    }
    switch($num){
        case 0:
        //    edit department/committee page
            echo do_shortcode( '[ind-organization-management]' );
            break;
        case 1:
            // schedule a meeting or event
            echo do_shortcode('[ind-edit-meeting-event]');
            break;
        case 2:
            // post minutes
            break;
        case 3:
            // Upload document unrelated to a meeting
            break;
    }
    die();
}
add_action( 'wp_ajax_indsha_return_complete_select_ajax', 'indsha_return_complete_select_ajax' );
add_action('wp_ajax_nopriv_indsha_return_complete_select_ajax', 'indsha_return_complete_select_ajax');
