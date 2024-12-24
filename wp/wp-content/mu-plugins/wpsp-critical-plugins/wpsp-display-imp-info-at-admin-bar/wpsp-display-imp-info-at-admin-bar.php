<?php
/**
 * @package wpsp-display-imp-info-at-admin-bar.php
 */
/*
Plugin Name: wpsp-display-imp-info-at-admin-bar.php
Plugin URI: https://wpscalepro.com/
Description: Display current server private Ip, Site ID and Restriction ID at admin bar.
Version: 1.0.0
Author: Erfan Ilyas
Author URI: https://wpscalepro.com/
License: GPLv2 or later
*/


// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}



// add links/menus to the admin bar
add_action( 'admin_bar_menu', function(){
  
  global $wp_admin_bar;
  
  $wp_admin_bar->add_menu( array(
    'parent' => false, // use 'false' for a root menu, or pass the ID of the parent menu
    'id' => 'server_ip', // link ID, defaults to a sanitized title value
    'href' => '#',
    'title' => 'IP:'.$_SERVER['SERVER_ADDR']. '-ID:'.WPSP_SITE_ID.'-RID:'.WPSP_RESTRICTION_GROUP_ID,
  ));
  
}, 9999999999);