<?php
/*
 * Plugin Name: Sharon
 * Plugin URI: https://becomeindelible.com
 * Description: Sets up custom data specific to the Sharon VT site.
 * Author: Indelible Inc.
 * Version: 0.1.0
 * Author URI: https://becomeindelible.com
 * License: GPL2+
 * Github Plugin URI: tonalqualityx/sharon
 */

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );//For security

define('INDPPL_ROOT_PATH', plugin_dir_path(__FILE__));
define('INDPPL_ROOT_URL', plugin_dir_url(__FILE__));

function indppl_enqueue(){
    wp_enqueue_style('indppl-style', INDPPL_ROOT_URL . 'css/style.css');
//     wp_register_script( 'indppl-js', INDPPL_ROOT_URL . 'js/app.js', array( 'jquery' ), true);
//     wp_localize_script( 'indppl-js', 'indppl_ajax',
//       array(
//          'ajaxurl' => admin_url( 'admin-ajax.php' ),
//          'pluginDirectory' => plugins_url(),
//          'guide_nonce' => wp_create_nonce(),
//       )
//    );
//    wp_enqueue_script('indppl-js');
}
add_action('wp_enqueue_scripts', 'indppl_enqueue');

function indppl_admin_enqueue(){
    wp_enqueue_style('indppl-style', INDPPL_ROOT_URL . 'css/admin-style.css');
}
add_action('admin_enqueue_scripts', 'indppl_admin_enqueue');

function sharon_user_roles($user){
    var_dump($_GET);
    ?>
    <h3>User's Organizations</h3>
    <?php
    $args = array(
        'post_type' => 'organization',
        'posts_per_page' => -1,
    );
    // var_dump($user);
    var_dump(get_user_meta($user->ID));
    $the_query = new WP_Query($args);
    if($the_query->have_posts() ){
        while($the_query->have_posts()){
            $the_query->the_post();
            $id = $the_query->post->ID;
            $title = get_the_title();
            $option = get_user_option($title . "_" . $id);
            ob_start();
            echo $option;
            ?>
            <input name='<?php echo $title . "_" . $id; ?>' type='checkbox' value='1' <?php checked($option, '1'); ?> /><?php echo $title; ?>
            <?php
            $return .= ob_get_clean();
        }
    }
    echo $return;
    ?>
    <?php
}
add_action( 'show_user_profile', 'sharon_user_roles' );
add_action( 'edit_user_profile', 'sharon_user_roles' );