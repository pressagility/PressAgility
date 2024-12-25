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


//#1 display some important info at wp admin bar you may want to disable it.
require_once( '/var/www/WPScalePro/wp/wp-content/mu-plugins/wpsp-critical-plugins/wpsp-display-imp-info-at-admin-bar/wpsp-display-imp-info-at-admin-bar.php' );

//#2 cdn-enabler - CDN urls are hard coded. To disable the CDN simply comment out the following line.
require_once( '/var/www/WPScalePro/wp/wp-content/mu-plugins/wpsp-critical-plugins/cdn-enabler/cdn-enabler.php' );

//#3 redis-cache - Cache is hard coded. To disable the CDN simply comment out the following line.
require_once( '/var/www/WPScalePro/wp/wp-content/mu-plugins/wpsp-critical-plugins/redis-cache/redis-cache.php' );

//#4 wp-rollback - 
define( 'WP_ROLLBACK_PLUGIN_FILE', '/mnt/network-share/wp-content/site'.WPSP_SITE_ID.'/mu-plugins/wpsp-critical-plugins/wp-rollback/wp-rollback/' );
require_once( '/var/www/WPScalePro/wp/wp-content/mu-plugins/wpsp-critical-plugins/wp-rollback/wp-rollback.php' );



//#5 one-time-login - critical plugin. Do not Remove or Comment out.*****
require_once( '/var/www/WPScalePro/wp/wp-content/mu-plugins/wpsp-critical-plugins/one-time-login/one-time-login.php' );

//#6 WPScalePro - critical plugin. Do not Remove or Comment out.*****
require_once( '/var/www/WPScalePro/wp/wp-content/mu-plugins/wpsp-critical-plugins/wpsp-git-cicd-magic-deploy/wpsp-git-cicd-magic-deploy.php' );