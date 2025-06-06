<?php
/**
 * @package wppa-all-in-one-wp-migration-hooks
 */
/*
Plugin Name: wppa-all-in-one-wp-migration-hooks
Plugin URI: https://pressagility.com/
Description: Custom hooks for all in one wp migration plugin
Version: 1.0.0
Author: Erfan Ilyas
Author URI: https://pressagility.com/
License: GPLv2 or later
*/



####
#This file controls everything about all in one wp migration plugin.
###



###
#Run the following when main all-in-one-wp-migration plugin is loaded
###
if( defined('REQUIRED_FILE_all_in_one_wp_migration') && REQUIRED_FILE_all_in_one_wp_migration ){
}else{
  return;
}









###
#show all-in-one-wp-migration menu only to a superduper user
###
add_action( 'admin_init', function(){
  
  //hide for all users - even for superduper user
  remove_submenu_page( 'ai1wm_export', 'ai1wm_schedules' );
  remove_submenu_page( 'ai1wm_export', 'ai1wm_reset' );
  remove_submenu_page( 'ai1wm_export', 'ai1wmve_reset' );
  remove_submenu_page( 'ai1wm_export', 'ai1wmve_schedules' );
  
  
  
  //hide for all users except superduper
	if( WPPA_CURRENT_USER_IS_SUPER_DUPER ){
    return true;
  }
  
  remove_menu_page( 'ai1wm_export' );
  
}, 9999, 1 ); //add_action( 'admin_init', function(){




###
#harden security for non superduper user
###
add_action( 'init', function(){
  
  if( WPPA_CURRENT_USER_IS_SUPER_DUPER ){
    return true;
  }
	
  
	global $pagenow;
	if( !isset($pagenow) ){ //make sure $pageNow is set
		return;
	}
  
  
  $restrictedSubMenu = array(
    'ai1wmne_settings', 'ai1wm_backups', 'ai1wm_import', 'ai1wm_export',
  );

  if( $pagenow == 'admin.php' ){
    if( isset($_GET['page']) ){
			if( in_array($_GET['page'], $restrictedSubMenu) ) {
				wp_redirect( admin_url() );
			}
		}
  }

}, 9999 ); //add_action( 'init', function(){










###
#ovewrite/default settings main plugin - Do not allow to export any content except database and media that is not in yearly format.
###

#Main plugin hook - ai1wm_exclude_themes_from_export
add_filter( 'ai1wm_exclude_themes_from_export', function( $options ){
  
  $allInstalledThemes = wp_get_themes();
	
	//skip disabled themes
	foreach( $allInstalledThemes as $installedTheme ){
    $options[] = $installedTheme->template;
	}
  
  return $options;
});




#Main plugin hook - ai1wm_exclude_plugins_from_export
add_filter( 'ai1wm_exclude_plugins_from_export', function( $options ){
  
  // Check if get_plugins() function exists. This is required on the front end of the
	// site, since it is in a file that is normally only loaded in the admin.
	if ( ! function_exists( 'get_plugins' ) ) {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
	}
  
	$allInstalledPlugins 	= get_plugins();
  foreach( $allInstalledPlugins as $pluginFullName=>$allInstalledPlugin ){
		$exploded = explode( '/', $pluginFullName );
		$options[] = $exploded[0];
	}
  
  return $options;
});




#Main plugin hook - ai1wm_exclude_content_from_export
add_filter( 'ai1wm_exclude_content_from_export', function( $options ){
  
  //add any other directory we do not need to backup
  $options[] = 'ai1wm-storage';
  $options[] = 'cache';
  $options[] = 'mu-plugins';
  $options[] = 'logs';
  $options[] = 'languages';
  $options[] = 'object-cache.php';

  return $options;
});




#Main plugin hook - ai1wm_exclude_media_from_export
add_filter( 'ai1wm_exclude_media_from_export', function( $options ){
  
  $upload_dir = wp_upload_dir(); // Get the WordPress uploads directory
  $upload_path = $upload_dir['basedir']; // Get the absolute path

  if (is_dir($upload_path)) {
    
    $folders = scandir($upload_path); // Scan the uploads directory

    foreach ($folders as $folder) {
      
      if ($folder !== '.' && $folder !== '..' && is_dir($upload_path . '/' . $folder)) {
        // Exclude only directories that are 4-digit years (e.g., 2010, 2022, 2030)
        if (preg_match('/^\d{4}$/', $folder)) {
            $options[] = $folder;
        }
      }
      
    }
    
  }

  return $options;
});









###
#Main plugin hook - when backup is imported.
###
add_action( 'ai1wm_status_import_done', function($params) {
  
  //all-in-one-wp-migration/lib/controller/class-ai1wm-main-controller.php - 198
  if( !isset($params['priority']) ){
    return;
  }
  
  if( $params['priority'] != 400 ){
    return;
  }
  
  error_log( 'All in One WP Migration import completed. Priority: '.$params['priority'] );



  $apiUrl = 'https://ctrl.'.WPPA_PLATFORM_DOMAIN.'/api-v1/site/update/';
  
  ###
  #update core db
  #regenerate media - this will also upload media to the cloudflare r2 bucket.
  #delete local images
  ###
  $dataToSend = [
      'user'       => WPPA_SITE_ID,
      'key'        => AUTH_KEY,
      'site-id'    => WPPA_SITE_ID,
      'task-name'  => 'run_wpcli_cmd',
      'wp-cli-cmd' => 'wp core update-db --skip-packages --skip-plugins --skip-themes --url='.WPPA_CURRENT_DOMAIN.' && 
                       wp media regenerate --yes --skip-packages --skip-plugins --skip-themes --url='.WPPA_CURRENT_DOMAIN.' && 
                       find /mnt/network-share-lower/network-share/wp-content/site'.WPPA_SITE_ID.'/uploads/ -type f \( -iname "*.jpg" -o -iname "*.jpeg" -o -iname "*.png" -o -iname "*.gif" -o -iname "*.bmp" -o -iname "*.tiff" -o -iname "*.tif" -o -iname "*.webp" -o -iname "*.svg" -o -iname "*.ico" -o -iname "*.heif" -o -iname "*.heic" -o -iname "*.j2k" -o -iname "*.jp2" -o -iname "*.jpf" -o -iname "*.jpx" -o -iname "*.jpm" -o -iname "*.avif" -o -iname "*.raw" -o -iname "*.cr2" -o -iname "*.nef" -o -iname "*.orf" -o -iname "*.sr2" \) -delete &&
                       find /mnt/network-share/wp-content/site'.WPPA_SITE_ID.'/uploads/ -type f \( -iname "*.jpg" -o -iname "*.jpeg" -o -iname "*.png" -o -iname "*.gif" -o -iname "*.bmp" -o -iname "*.tiff" -o -iname "*.tif" -o -iname "*.webp" -o -iname "*.svg" -o -iname "*.ico" -o -iname "*.heif" -o -iname "*.heic" -o -iname "*.j2k" -o -iname "*.jp2" -o -iname "*.jpf" -o -iname "*.jpx" -o -iname "*.jpm" -o -iname "*.avif" -o -iname "*.raw" -o -iname "*.cr2" -o -iname "*.nef" -o -iname "*.orf" -o -iname "*.sr2" \) -delete &&
                       wp cache flush --skip-packages --skip-plugins --skip-themes'
  ];
  
  //send request
  $response = wp_remote_post( $apiUrl, [
     'method'    => 'POST',
      'body'      => $dataToSend,
      'timeout'   => 8,
      'headers'   => array(
          'Content-Type' => 'application/x-www-form-urlencoded'
      ),
  ]);
  

  
  
  
  
  ###
  #add/update superduper user again
  ###
  $dataToSend = [
      'user'       => WPPA_SITE_ID,
      'key'        => AUTH_KEY,
      'site-id'    => WPPA_SITE_ID,
      'task-name'  => 'update_site'
  ];
  
  //send request
  $response = wp_remote_post( $apiUrl, [
     'method'    => 'POST',
      'body'      => $dataToSend,
      'timeout'   => 8,
      'headers'   => array(
          'Content-Type' => 'application/x-www-form-urlencoded'
      ),
  ]);
  
  
  
}); //add_action( 'ai1wm_status_import_done', function($params) {










###
#Run the following when all-in-one-wp-migration-s3-client-extension is loaded
###
if( defined( 'REQUIRED_FILE_all_in_one_wp_migration_s3_client_extension' ) &&  REQUIRED_FILE_all_in_one_wp_migration_s3_client_extension ){
  
  
  add_action( 'init', function(){
    
    ###
    # hourly/twicedaily/daily/weekly
    ###
    if ( !wp_next_scheduled('wppa_take_ai1wm_backup_on_schedule') ){
      wp_schedule_event( time(), 'twicedaily', 'wppa_take_ai1wm_backup_on_schedule' );
    }
    
  });
  
  
  
  add_action( 'wppa_take_ai1wm_backup_on_schedule', function(){
    wppa_take_ai1wm_backup_now();
  });
  
  ###
  #ovewrite/default settings for s3 client extension
  ###

  //** set how many backups you want to keep at all times.
  add_filter( 'pre_option_ai1wmne_s3_backups', function($default){ return '8'; }); //***
  //**

  add_filter( 'pre_option_ai1wmne_s3_api_endpoint', function($default){ return WPPA_CLOUDFLARE_R2_ACCOUNT_ID.'.r2.cloudflarestorage.com'; });
  add_filter( 'pre_option_ai1wmne_s3_bucket_template', function($default){ return WPPA_CLOUDFLARE_R2_BUCKET.'.'.WPPA_CLOUDFLARE_R2_ACCOUNT_ID.'.r2.cloudflarestorage.com'; });
  add_filter( 'pre_option_ai1wmne_s3_region_name', function($default){ return 'auto'; });
  add_filter( 'pre_option_ai1wmne_s3_access_key', function($default){ return WPPA_CLOUDFLARE_R2_API_KEY; });
  add_filter( 'pre_option_ai1wmne_s3_secret_key', function($default){ return WPPA_CLOUDFLARE_R2_API_VALUE_S3; });
  add_filter( 'pre_option_ai1wmne_s3_https_protocol', function($default){ return '1'; });

  add_filter( 'pre_option_ai1wmne_s3_bucket_name', function($default){ return WPPA_CLOUDFLARE_R2_BUCKET; });
  add_filter( 'pre_option_ai1wmne_s3_folder_name', function($default){ return WPPA_SITE_ID.'-wppa/_ai1wm-backups'; });
  add_filter( 'pre_option_ai1wmne_s3_storage_class', function($default){ return 'STANDARD'; });
  add_filter( 'pre_option_ai1wmne_s3_encryption', function($default){ return '0'; });
  add_filter( 'pre_option_ai1wmne_s3_cron', function($default){ return array(); });
  add_filter( 'pre_option_ai1wmne_s3_incremental', function($default){ return '0'; });
  add_filter( 'pre_option_ai1wmne_s3_notify_toggle', function($default){ return '0'; });
  add_filter( 'pre_option_ai1wmne_s3_notify_error_toggle', function($default){ return '0'; });
  add_filter( 'pre_option_ai1wmne_s3_notify_email', function($default){ return ''; });
  add_filter( 'pre_option_ai1wmne_s3_days', function($default){ return '0'; });
  add_filter( 'pre_option_ai1wmne_s3_total', function($default){ return '0'; });
  add_filter( 'pre_option_ai1wmne_s3_total_unit', function($default){ return 'GB'; });
  add_filter( 'pre_option_ai1wmne_s3_file_chunk_size', function($default){ return '20971520'; });
  
  
  
  
  function wppa_take_ai1wm_backup_now(){
  
    $apiUrl = 'https://ctrl.'.WPPA_PLATFORM_DOMAIN.'/api-v1/site/update/';
    
    $dataToSend = [
      'user'       => WPPA_SITE_ID,
      'key'        => AUTH_KEY,
      'site-id'    => WPPA_SITE_ID,
      'task-name'  => 'run_wpcli_cmd',
      'wp-cli-cmd' => 'wp ai1wm s3-client backup'
    ];
    
    $response = wp_remote_post( $apiUrl, [
     'method'    => 'POST',
      'body'      => $dataToSend,
      'timeout'   => 8,
      'headers'   => array(
          'Content-Type' => 'application/x-www-form-urlencoded'
      ),
    ]);
    
  }
  
  
 
} //if( REQUIRED_FILE_all_in_one_wp_migration_s3_client_extension ){