<?php

function indsha_doc_upload($type='file'){
    $filename = $_FILES[$type];
    $wp_upload_dir = wp_upload_dir();
    $upload_overrides = array( 'test_form' => false );
    $return = wp_handle_upload($filename, $upload_overrides);
    return $return;
}

add_filter('relevanssi_content_to_index', 'rlv_add_filenames', 10, 2);
function rlv_add_filenames($content, $post) {
    if ($post->post_type == 'attachment') {
        $content .= " " . basename($post->guid);
    }
    return $content;
}