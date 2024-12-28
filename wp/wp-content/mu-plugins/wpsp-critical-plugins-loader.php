<?php
/**
 * @package wpsp-critical-plugins-loader.php
 */
/*
Plugin Name: wpsp-critical-plugins-loader.php
Plugin URI: https://wpscalepro.com/
Description: git cicd
Version: 1.0.0
Author: Erfan Ilyas
Author URI: https://wpscalepro.com/
License: GPLv2 or later
*/


if( WPSP_CURRENT_SITE_IS_STAGE ){
  $muDirPath = '/var/www/WPScalePro.stage';
}else{
  $muDirPath = '/var/www/WPScalePro';

}




// #1 display some important info at wp admin bar you may want to disable it.
require_once( $muDirPath.'/wp/wp-content/mu-plugins/wpsp-critical-plugins/wpsp-display-imp-info-at-admin-bar/wpsp-display-imp-info-at-admin-bar.php' );



// #2 cdn-enabler - CDN urls are hard coded. To disable the CDN simply comment out the following line.
require_once( $muDirPath.'/wp/wp-content/mu-plugins/wpsp-critical-plugins/cdn-enabler/cdn-enabler.php' );



// #3 redis-cache - Cache is hard coded. To disable the CDN simply comment out the following line.
require_once( $muDirPath.'/wp/wp-content/mu-plugins/wpsp-critical-plugins/redis-cache/redis-cache.php' );



// #4 flyingpages -
define( 'flying_pages_config_ignore_keywords', [
  '/wp-admin', '/wp-login.php', '/cart', '/checkout', 'add-to-cart', 'logout',
  '#', '?', '.png', '.jpeg', '.jpg', '.gif', '.svg', '.webp'] );
define( 'flying_pages_config_delay', 99999 );
define( 'flying_pages_config_max_rps', 0 );
define( 'flying_pages_config_hover_delay', 0 );
define( 'flying_pages_config_disable_on_login', true );
//To dsiable flying pages comment out the following line.

require_once( $muDirPath.'/wp/wp-content/mu-plugins/wpsp-critical-plugins/flying-pages/flying-pages.php' );



// #5 one-time-login - critical plugin. Do not Remove or Comment out.*****
require_once( $muDirPath.'/wp/wp-content/mu-plugins/wpsp-critical-plugins/one-time-login/one-time-login.php' );




if( !WPSP_CURRENT_SITE_IS_STAGE ){
  
  //#4 wp-rollback - 
  require_once( $muDirPath.'/wp/wp-content/mu-plugins/wpsp-critical-plugins/wp-rollback/wp-rollback.php' );
  
  //#6 WPScalePro - critical plugin. Do not Remove or Comment out.*****
  require_once( $muDirPath.'/wp/wp-content/mu-plugins/wpsp-critical-plugins/wpsp-git-cicd-magic-deploy/wpsp-git-cicd-magic-deploy.php' );
}

