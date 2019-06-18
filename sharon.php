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

define('IND_ROOT_PATH', plugin_dir_path(__FILE__));
define('IND_ROOT_URL', plugin_dir_url(__FILE__));

require_once(IND_ROOT_PATH . "/functions.php");
require_once(IND_ROOT_PATH . "/admin-functions.php");
require_once(IND_ROOT_PATH . "/ajax-functions.php");
require_once(IND_ROOT_PATH . "/shortcodes.php");

function indsha_enqueue(){
    wp_enqueue_style('indsha-style', IND_ROOT_URL . 'css/style.css');
    wp_register_script( 'indsha-js', IND_ROOT_URL . 'js/app.js', array( 'jquery' ), true);
    wp_localize_script( 'indsha-js', 'indsha_ajax',
      array(
         'ajaxurl' => admin_url( 'admin-ajax.php' ),
         'pluginDirectory' => plugins_url(),
         'guide_nonce' => wp_create_nonce(),
      )
   );
   wp_enqueue_script('indsha-js');
}
add_action('wp_enqueue_scripts', 'indsha_enqueue');

function indsha_admin_enqueue(){
    wp_enqueue_style('indsha-style', IND_ROOT_URL . 'css/admin-style.css');
}
add_action('admin_enqueue_scripts', 'indsha_admin_enqueue');

