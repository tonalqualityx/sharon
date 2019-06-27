<?php

function indsha_doc_upload($type='file'){
    $filename = $_FILES[$type];
    $wp_upload_dir = wp_upload_dir();
    $upload_overrides = array( 'test_form' => false );
    $return = wp_handle_upload($filename, $upload_overrides);
    return $return;
}