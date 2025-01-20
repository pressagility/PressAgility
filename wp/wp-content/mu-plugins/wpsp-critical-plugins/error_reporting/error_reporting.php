<?php
/**
 * @package error_reporting.php
 */
/*
Plugin Name: error_reporting.php
Plugin URI: https://wpscalepro.com/
Description: Log all type of error only for staged site
Version: 1.0.0
Author: Erfan Ilyas
Author URI: https://wpscalepro.com/
License: GPLv2 or later
*/


// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


//if this is a staged site
if( WPSP_CURRENT_SITE_IS_STAGE ){ 
  error_reporting( E_ALL ); // Log all types of errors warnings including notices
}else{
  error_reporting( E_ERROR ); // Log only fatal runtime errors
}