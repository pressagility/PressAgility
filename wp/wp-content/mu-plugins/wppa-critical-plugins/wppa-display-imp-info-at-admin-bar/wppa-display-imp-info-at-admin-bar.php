<?php
/**
 * @package wppa-display-imp-info-at-admin-bar.php
 */
/*
Plugin Name: wppa-display-imp-info-at-admin-bar.php
Plugin URI: https://pressagility.com/
Description: Display current server private Ip, Site ID and Restriction ID at admin bar.
Version: 1.0.0
Author: Erfan Ilyas
Author URI: https://pressagility.com/
License: GPLv2 or later
*/


// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}




// add links/menus to the admin bar
add_action( 'admin_bar_menu', function(){
  
  if( WPPA_CURRENT_USER_IS_SUPER_DUPER == false ){
    return;
  }
  
  global $wp_admin_bar;
  
  $titleText = 'ID:'.WPPA_SITE_ID.' - Restrict-ID:'.WPPA_RESTRICTION_GROUP_ID.' - IP:'.$_SERVER['SERVER_ADDR'];
  if( WPPA_CURRENT_SITE_IS_STAGE ){
    $titleText .= '-STAGED-SITE';
  }
  
  $wp_admin_bar->add_menu( array(
    'parent' => false, // use 'false' for a root menu, or pass the ID of the parent menu
    'id' => 'server_ip', // link ID, defaults to a sanitized title value
    'href' => '#',
    'title' => $titleText,
  ));
  
}, 9999999999);

