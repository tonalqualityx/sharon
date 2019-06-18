<?php
function indsha_get_org_form(){
    if(isset($_POST['id'])){
        $id = $_POST['id'];
    }
    if($id){
        cred_form(4803,$id);
        // echo do_shortcode( '[cred_form form=4803, name="Organization Form", post=' . $id . ']' );
    }else{
        echo "Please select an organization you have access to.";
        // echo json_encode(array('output' => $output));
    }
    die();
}
add_action( 'wp_ajax_indsha_get_org_form', 'indsha_get_org_form' );
add_action('wp_ajax_nopriv_indsha_get_org_form', 'indsha_get_org_form');

function indsha_save_org_form(){
    if(isset($_POST['org_id'])){
        $org_id = $_POST['org_id'];
    }
    if(isset($_POST['content'])){
        $args = array(
            'ID' => $org_id,
            // 'post_title' => $_POST['title'],
            'post_content' => $_POST['content'],
        );
        $new_id = wp_update_post($args, true);
    }
    if(isset($_POST['email'])){
        $email = $_POST['email'];
        update_post_meta($org_id, 'wpcf-org-email', $email);
    }
    if(isset($_POST['contact'])){
        $contact = $_POST['contact'];
        update_post_meta($org_id, 'wpcf-org-point-of-contact', $contact);
    }
    if(isset($_POST['hours'])){
        $hours = $_POST['hours'];
        update_post_meta($org_id, 'wpcf-org-hours-of-operation', $hours);
    }
    if(isset($_POST['phone'])){
        $phone = $_POST['phone'];
        update_post_meta($org_id, 'wpcf-org-phone', $phone);
    }
    if(isset($_POST['address'])){
        $address = $_POST['address'];
        update_post_meta($org_id, 'wpcf-org-address', $address);
    }
    die();
}
add_action( 'wp_ajax_indsha_save_org_form', 'indsha_save_org_form' );
add_action('wp_ajax_nopriv_indsha_save_org_form', 'indsha_save_org_form');
