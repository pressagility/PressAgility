<?php
/**
 * @package wppa-error-reporting.php
 */
/*
Plugin Name: wppa-error-reporting.php
Plugin URI: https://pressagility.com/
Description: Log all type of error only for staged site
Version: 1.0.0
Author: Erfan Ilyas
Author URI: https://pressagility.com/
License: GPLv2 or later
*/


// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


//if this is a staged site
if( WPPA_CURRENT_SITE_IS_STAGE ){ 
  error_reporting( E_ALL ); // Log all types of errors warnings including notices
}else{
  error_reporting( E_ERROR ); // Log only fatal runtime errors
}