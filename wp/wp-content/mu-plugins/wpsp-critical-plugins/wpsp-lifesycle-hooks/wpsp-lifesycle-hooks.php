<?php
/**
 * @package wpsp-lifesycle-hooks
 */
/*
Plugin Name: wpsp-lifesycle-hooks
Plugin URI: https://wpscalepro.com/
Description: Tenant Lifecycle hooks
Version: 1.0.0
Author: Erfan
Author URI: https://wpscalepro.com/
License: GPLv2 or later
*/


// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


###
#To run the lifecycle hooks test please uncomment the following and configure the test-runner from the script
###
#require_once ( __dir__.'/wpsp-lifesycle-hooks-test-runner.php' );



//these hooks will only run from wp cli
if ( !defined('WP_CLI') ){
	return;
}



###
#run after site is created
###
WP_CLI::add_command( 'wpsp-lifecycle after-site-created', function( $args, $assoc_args ) {
    
  if ( isset( $assoc_args['data-collection'] ) ) {
    
    $dataCollection = json_decode( $assoc_args['data-collection'], true );
    if ( json_last_error() !== JSON_ERROR_NONE ) {
        WP_CLI::error( "Invalid JSON passed in --data-collection: " . json_last_error_msg() );
    }
    
  }else{
    $dataCollection = [];
  }

  do_action( 'wpsp_lifecycle_after_site_created', $dataCollection );
});



###
#run before site is removed
###
WP_CLI::add_command( 'wpsp-lifecycle before-site-removed', function( $args, $assoc_args ) {
    
  if ( isset( $assoc_args['data-collection'] ) ) {
    
    $dataCollection = json_decode( $assoc_args['data-collection'], true );
    if ( json_last_error() !== JSON_ERROR_NONE ) {
        WP_CLI::error( "Invalid JSON passed in --data-collection: " . json_last_error_msg() );
    }
    
  }else{
    $dataCollection = [];
  }

  do_action( 'wpsp_lifecycle_before_site_removed', $dataCollection );
});









###
#run before site is cloned - Note: This hook will run on the new site. It will not run on the source site.
###
WP_CLI::add_command( 'wpsp-lifecycle before-site-cloned', function( $args, $assoc_args ) {
    
  if ( isset( $assoc_args['data-collection'] ) ) {
    
    $dataCollection = json_decode( $assoc_args['data-collection'], true );
    if ( json_last_error() !== JSON_ERROR_NONE ) {
        WP_CLI::error( "Invalid JSON passed in --data-collection: " . json_last_error_msg() );
    }
    
  }else{
    $dataCollection = [];
  }

  do_action( 'wpsp_lifecycle_before_site_cloned', $dataCollection );
});





###
#run after site is cloned - Note: This hook will run on the new site. It will not run on the source site.
###
WP_CLI::add_command( 'wpsp-lifecycle after-site-cloned', function( $args, $assoc_args ){
  
  if ( isset( $assoc_args['data-collection'] ) ) {
    
    $dataCollection = json_decode( $assoc_args['data-collection'], true );
    if ( json_last_error() !== JSON_ERROR_NONE ) {
        WP_CLI::error( "Invalid JSON passed in --data-collection: " . json_last_error_msg() );
    }
    
  }else{
    $dataCollection = [];
  }
  
	do_action( 'wpsp_lifecycle_after_site_cloned', $dataCollection );
});







###
#run before site is updated
###
WP_CLI::add_command( 'wpsp-lifecycle before-site-updated', function( $args, $assoc_args ) {
    
  if ( isset( $assoc_args['data-collection'] ) ) {
    
    $dataCollection = json_decode( $assoc_args['data-collection'], true );
    if ( json_last_error() !== JSON_ERROR_NONE ) {
        WP_CLI::error( "Invalid JSON passed in --data-collection: " . json_last_error_msg() );
    }
    
  }else{
    $dataCollection = [];
  }

  do_action( 'wpsp_lifecycle_before_site_updated', $dataCollection );
});





###
#run after site is updated
###
WP_CLI::add_command( 'wpsp-lifecycle after-site-updated', function( $args, $assoc_args ){
  
  if ( isset( $assoc_args['data-collection'] ) ) {
    
    $dataCollection = json_decode( $assoc_args['data-collection'], true );
    if ( json_last_error() !== JSON_ERROR_NONE ) {
        WP_CLI::error( "Invalid JSON passed in --data-collection: " . json_last_error_msg() );
    }
    
  }else{
    $dataCollection = [];
  }
  
	do_action( 'wpsp_lifecycle_after_site_updated', $dataCollection );
});





###
#run before site is deactivated
###
WP_CLI::add_command( 'wpsp-lifecycle before-site-deactivated', function( $args, $assoc_args ) {
    
  if ( isset( $assoc_args['data-collection'] ) ) {
    
    $dataCollection = json_decode( $assoc_args['data-collection'], true );
    if ( json_last_error() !== JSON_ERROR_NONE ) {
        WP_CLI::error( "Invalid JSON passed in --data-collection: " . json_last_error_msg() );
    }
    
  }else{
    $dataCollection = [];
  }

  do_action( 'wpsp_lifecycle_before_site_deactivated', $dataCollection );
});






###
#run after site is activated
###
WP_CLI::add_command( 'wpsp-lifecycle after-site-activated', function( $args, $assoc_args ){
  
  if ( isset( $assoc_args['data-collection'] ) ) {
    
    $dataCollection = json_decode( $assoc_args['data-collection'], true );
    if ( json_last_error() !== JSON_ERROR_NONE ) {
        WP_CLI::error( "Invalid JSON passed in --data-collection: " . json_last_error_msg() );
    }
    
  }else{
    $dataCollection = [];
  }
  
	do_action( 'wpsp_lifecycle_after_site_activated', $dataCollection );
});



