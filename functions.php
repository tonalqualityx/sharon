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


function ind_display_notice($notice = false){
    $today = strtotime('now');
    $args = array(
        'post_type' => 'notice',
        'meta_query' => array(
            array(
                'key' => 'wpcf-expiration',
                'value' => $today,
                'compare' => '>=',
                'type' => 'number',
            ),
        ),
    );
    // var_dump(strtotime('now'));

    $notices = new WP_Query($args);
    $notice_array = [];
    $alert_array = [];
    if($notices->have_posts()){
        while($notices->have_posts()){
            $notices->the_post();
            $title = get_the_title();
            $id = get_the_id();
            $content = get_the_content();
            $alert = get_post_meta($id, 'wpcf-alert', true);
            if($alert){
                $alert_array[] = $content;
            }else{
                $notice_array[] = array('title' => $title, 'content' => $content);
            }
        }
    }
    if($notice == false){
        ?>
        <script>
        <?php
            echo 'var alert_array = ' . json_encode($alert_array) . ';';
            ?>
            jQuery(document).ready(function( $ ) {
                $(alert_array).each(function(index, value){
                    console.log(value);
                    $('body').prepend("<div class='alert-bg'><div class='alert-container'><span class='alert-header'>ALERT: </span><span class='alert-text'>" + value + "</span></div></div>");
                })
                
            });
            </script>
        <?php
    }else{
        return $notice_array;
    }
}
add_action('wp_footer', 'ind_display_notice');

function org_header_hero(){
    $default_img = home_url() . '/wp-content/uploads/2019/05/sharon-vt-ATEF6D.jpg';
    if(has_post_thumbnail()){
        $default_img = get_the_post_thumbnail_url(get_the_id(), 'full');
    }
    ob_start();
    ?>
    <div class='org-header-hero' style="background-image:url('<?php echo $default_img; ?>')"></div>
    <div class='org-header-hero-text'><div class='org-header-hero-second-container'><?php echo do_shortcode( "[ind-page-title]"); ?></div></div>
    <?php
    $return = ob_get_clean();
    return $return;
}