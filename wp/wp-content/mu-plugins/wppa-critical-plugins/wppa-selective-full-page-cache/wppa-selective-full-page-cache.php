<?php
/**
 * @package wppa-selective-full-page-cache.php
 */
/*
Plugin Name:wppa-selective-full-page-cache.php
Plugin URI: https://pressagility.com/
Description: Adds admin bar controls for selective page caching.
Version: 1.0.0
Author: Erfan Ilyas
Author URI: https://pressagility.com/
License: GPLv2 or later
*/


// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


//Bail early if not a frontend request or not logged in
if ( is_admin() || defined( 'DOING_AJAX' ) ) {
	return;
}


add_action( 'wp_enqueue_scripts', function(){
  
  //only logged in users
  if( !is_user_logged_in() ){
		return;
	}
  
  wp_enqueue_script( 'pressagility-selective-page-cache',
                      '/wp-content/site'.WPPA_SITE_ID.'/mu-plugins/wppa-critical-plugins/wppa-selective-full-page-cache/wppa-selective-full-page-cache.js',
                      ['jquery'],
                      '1.0',
                      true );
                      
  wp_localize_script( 'pressagility-selective-page-cache', 'WPPA_PRESSAGILITY_DATA', [
      'nonce'   => wp_create_nonce( 'wp_rest' ),
      'api_url' => rest_url( 'wppa-pressagility/v1/generate-static-page/' ),
  ]);
  
});






add_action( 'admin_bar_menu', function( $wp_admin_bar ){
  
  //only logged in users
  if( !is_user_logged_in() ){
		return;
	}
  
  if( !is_admin_bar_showing() ) return;
  
  $wp_admin_bar->add_node([
    'id'    => 'pressagility-selective-page-cache',
    'title' => 'Page Cache: <span id="pressagility-selective-page-cache-check">🔄 checking...</span>',
    'href'  => '#',
  ]);
  
  $buttons = [
    ['id' => 'add', 'label' => 'Add Current Page'],
    ['id' => 'refresh', 'label' => 'Refresh Current Page'],
    ['id' => 'remove', 'label' => 'Remove Current Page'],
    ['id' => 'flush', 'label' => 'Flush Entire Cache']
  ];
  
  
  foreach ($buttons as $btn) {
    $wp_admin_bar->add_node([
        'id'     => 'pressagility-selective-page-cache-' . $btn['id'],
        'title'  => $btn['label'],
        'parent' => 'pressagility-selective-page-cache',
        'href'   => '#',
        'meta'   => ['class' => 'pressagility-selective-page-btn']
    ]);
  }
  
    
    
}, PHP_INT_MAX);






add_action('rest_api_init', function () {
  
  //only logged in users
  if( !is_user_logged_in() ){
		return;
	}
  
  register_rest_route('wppa-pressagility/v1', '/generate-static-page/', [
    'methods'  => 'POST',
    'callback' => 'wppa_pressagility_generate_static_page_callback',
    'permission_callback' => function () {
        return current_user_can('manage_options');
    }
  ]);
    
});






function wppa_pressagility_generate_static_page_callback( WP_REST_Request $request ) {
  
  //only logged in users
  if( !is_user_logged_in() ){
		return;
	}
  
  $action = $request->get_param( 'action' );
	if( $action == 'refresh' ){
		$action = 'add';
	}
  $url    = $request->get_param( 'url' );

  
  $apiUrl = 'https://ctrl.'.WPPA_PLATFORM_DOMAIN.'/api-v1/site/generate-static-page/';
  
  $dataToSend = [
    'user'    => WPPA_SITE_ID,
    'key'     => AUTH_KEY,
    'site-id' => WPPA_SITE_ID,
    'action'  => $action,
    'url'     => $url,
  ];
  
  $response = wp_remote_post( $apiUrl, [
     'method'    => 'POST',
      'body'      => $dataToSend,
      'timeout'   => 8,
      'headers'   => array(
          'Content-Type' => 'application/x-www-form-urlencoded'
      ),
  ]);
  
  if ( is_wp_error( $response ) ) {
    return new WP_Error(
      'api_request_failed',
      'Failed to connect to API: ' . $response->get_error_message(),
      [ 'status' => 500 ]
    );
  }
  
  $response_code = wp_remote_retrieve_response_code( $response );
  $response_body = wp_remote_retrieve_body( $response );
  

  return new WP_REST_Response([
      'success' => true,
      'body' => json_decode($response_body, true),
  ], 200);
    
}


add_action( 'set_auth_cookie', function($auth_cookie, $expire, $expiration, $user_id, $scheme){
	setcookie(
		'WPPA-PressAgility-Login',
		$_SERVER['HTTP_HOST'].'XdS944jr',     
		$expire,
		COOKIEPATH,
		COOKIE_DOMAIN,
		is_ssl(),
		true
	);
}, 10, 5);

add_action( 'clear_auth_cookie', function(){
	setcookie(
		'WPPA-PressAgility-Login',
		'',
		time() - 3600,
		COOKIEPATH,
		COOKIE_DOMAIN
	);
});