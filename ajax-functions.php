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
            echo do_shortcode('[ind-add-event]');
            break;
        case 2:
            // post minutes
            echo do_shortcode('[ind-add-document]');
            break;
        // case 3:
        //     // Upload document unrelated to a meeting
        //     echo do_shortcode('[ind-add-document]');
        //     break;
    }
    die();
}
add_action( 'wp_ajax_indsha_return_complete_select_ajax', 'indsha_return_complete_select_ajax' );
add_action('wp_ajax_nopriv_indsha_return_complete_select_ajax', 'indsha_return_complete_select_ajax');

function indsha_upload_doc_ajax(){
    $security = check_ajax_referer( 'public_nonce', 'nonce');
    if(!$security){
        die();
    }
    if(isset($_POST['doc-title'])){
        $title = $_POST['doc-title'];
    }
    if(isset($_POST['doc-date'])){
        $date = $_POST['doc-date'];
    }
    if(isset($_POST['org'])){
        $org_id = $_POST['org'];
    }
    if(isset($_POST['cat'])){
        $cat = $_POST['cat'];
    }
    $doc = indsha_doc_upload();
    // var_dump($doc['url']);
    $date = strtotime($date);
    var_dump($cat);
    $postarr = array(
        "ID" => 0,
        "post_author" => get_current_user_id(),
        "post_title" => $title,
        'post_type' => 'document',
        'post_status' => "publish",
        'meta_input' => array(
            'wpcf-document-file' => $doc['url'],
            'wpcf-document-date' => $date,
        ),
    );
    $doc_id = wp_insert_post($postarr);
    wp_set_object_terms($doc_id, intval($cat), 'document-category');
    // var_dump($doc_id);
    $connection = toolset_connect_posts('organization-document', $org_id, $doc_id);
    // var_dump($connection);
    die();
}
add_action( 'wp_ajax_indsha_upload_doc_ajax', 'indsha_upload_doc_ajax' );
add_action('wp_ajax_nopriv_indsha_upload_doc_ajax', 'indsha_upload_doc_ajax');

function indsha_save_event_ajax(){
    $security = check_ajax_referer( 'public_nonce', 'nonce');
    if(!$security){
        die();
    }
    if(isset($_POST['date'])){
        $date = $_POST['date'];
    }
    if(isset($_POST['content'])){
        $content = $_POST['content'];
    }
    if(isset($_POST['special'])){
        $special = $_POST['special'];
    }
    if(isset($_POST['file_array'])){
        $file_array = $_POST['file_array'];
    }
    if(isset($_POST['org'])){
        $org = $_POST['org'];
    }
    if($special){
        $special = "Special ";
    }else{
        $special = '';
    }
    $org_name = get_the_title($org);
    $date_field = strtotime($date);
    $date_short = date('m-d-Y', $date_field);
    $title = $org_name . " " . $special . $date_short;
    $doc_id_array = [];
    $doc_id = '';
    $time = date("g:i a",$date_field);
    // var_dump($_FILES);
    if(isset($_FILES['agenda'])){
        foreach($_FILES as $key => $value){
            if($key == 'agenda'){
                $agenda = indsha_doc_upload($key);
                $postarr = array(
                    "ID" => 0,
                    "post_author" => get_current_user_id(),
                    "post_title" => $title . " " . $key,
                    'post_type' => 'document',
                    'post_status' => "publish",
                    'post_category' => array(
                        120
                    ),
                    'meta_input' => array(
                        'wpcf-document-file' => $agenda['url'],
                        'wpcf-document-date' => $date_field,
                    ),
                );
                $doc_id = wp_insert_post($postarr);
                toolset_connect_posts('organization-document', $org, $doc_id);
            }else{
                $url = indsha_doc_upload($key);
                $postarr = array(
                    "ID" => 0,
                    "post_author" => get_current_user_id(),
                    "post_title" => $title . " " . $key,
                    'post_type' => 'document',
                    'post_status' => "publish",
                    'post_category' => array(
                        121
                    ),
                    'meta_input' => array(
                        'wpcf-document-file' => $url['url'],
                        'wpcf-document-date' => $date_field,
                    ),
                );
                $doc_id = wp_insert_post($postarr);
                $doc_id_array[] = $doc_id;
                toolset_connect_posts('organization-document', $org, $doc_id);
            }
        }
    }
    $postarr = array(
        "ID" => 0,
        "post_author" => get_current_user_id(),
        "post_title" => $title,
        'post_type' => 'event',
        'post_content' => $content,
        'post_status' => "publish",
        'post_category' => array(
            119
        ),
        'meta_input' => array(
            'wpcf-event-date' => $date_field,
            'wpcf-time' => $time, //set as text field in toolset
        ),
    );
    $event_id = wp_insert_post($postarr);
    if($doc_id){
        $connection = toolset_connect_posts('document-event', $doc_id, $event_id);
    }
    var_dump($doc_id_array);
    if(!empty($doc_id_array)){
        foreach($doc_id_array as $key => $value){
            toolset_connect_posts('document-event', $value, $event_id);
        }
    }
    toolset_connect_posts('organization-event', $org, $event_id);

    die();
}
add_action( 'wp_ajax_indsha_save_event_ajax', 'indsha_save_event_ajax' );
add_action('wp_ajax_nopriv_indsha_save_event_ajax', 'indsha_save_event_ajax');