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
    wp_enqueue_style('indppl-style', INDPPL_ROOT_URL . 'css/style.css');
}
add_action('admin_enqueue_scripts', 'indppl_admin_enqueue');