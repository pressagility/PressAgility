<?php
/**
 * @package wppa-critical-plugins-loader.php
 */
/*
Plugin Name: wppa-critical-plugins-loader.php
Plugin URI: https://pressagility.com/
Description: git cicd
Version: 1.0.0
Author: Erfan Ilyas
Author URI: https://pressagility.com/
License: GPLv2 or later
*/
if( !defined('ABSPATH') ){
  exit; // Prevent direct access.
}




###
#set superduper constant
#Use this constant to create logic for superduper only functions
###
if( !defined('WPPA_CURRENT_USER_IS_SUPER_DUPER') ){
  
  add_action( 'init', function(){
    
    $currentUser = wp_get_current_user();
    if( isset($currentUser->data->user_login) && $currentUser->data->user_login == 'superduper' ){
      
      define( 'WPPA_CURRENT_USER_IS_SUPER_DUPER', true );
      
      //only superduper is allowed to install plugins/themes
      define( 'DISALLOW_FILE_MODS', false );
      
      //only supduper is allowed to rollback plugins/themes
      require_once( WPPA_MU_DIR_PATH.'/wp-content/mu-plugins/wppa-critical-plugins/wp-rollback/wp-rollback.php' );
      
    }else{
      
      define( 'WPPA_CURRENT_USER_IS_SUPER_DUPER', false );
      define( 'DISALLOW_FILE_MODS', true );
      
    }

  }, 0 );
  
}



if( WPPA_CURRENT_SITE_IS_STAGE ){
  define( 'WPPA_MU_DIR_PATH', '/var/www/PressAgility/stage-branch' );
}else{
  define( 'WPPA_MU_DIR_PATH', '/var/www/PressAgility/main-branch' );
}



//Disable Admin Email Verification
//sometimes WordPress ask the administrator user to verify the admin email address. We do not need that.
add_filter( 'admin_email_check_interval', '__return_false' );















###
//Optional plugin that can be commented out based on your network requirements.
###




###
// #1 display some important info at wp admin bar you may want to disable it.
###
require_once( WPPA_MU_DIR_PATH.'/wp-content/mu-plugins/wppa-critical-plugins/wppa-display-imp-info-at-admin-bar/wppa-display-imp-info-at-admin-bar.php' );



###
// #2 Selective Full Page Cache.
###
if( !WPPA_CURRENT_SITE_IS_STAGE ){
	require_once( WPPA_MU_DIR_PATH.'/wp-content/mu-plugins/wppa-critical-plugins/wppa-selective-full-page-cache/wppa-selective-full-page-cache.php' );
}




###
// #3 cdn-enabler - CDN urls are hard coded. To disable the CDN simply comment out the following line.
###
require_once( WPPA_MU_DIR_PATH.'/wp-content/mu-plugins/wppa-critical-plugins/cdn-enabler/cdn-enabler.php' );



###
// #4 flyingpages - To disable comment out the following.
###
define( 
    'flying_pages_config_ignore_keywords', 
    [
      '/wp-admin', '/wp-login.php', '/cart', '/checkout', 'add-to-cart', 'logout', '#', '?',
      '.png', '.jpeg', '.jpg', '.gif', '.svg', '.webp', '.avif',
    ] 
  );
  
define( 'flying_pages_config_delay', 99999 ); //do not start pre loading the page automatically.
define( 'flying_pages_config_max_rps', 0 ); //max requests per second
define( 'flying_pages_config_hover_delay', 0 ); //mouse hover delay
define( 'flying_pages_config_disable_on_login', false );
//To dsiable flying pages comment out the following line.
require_once( WPPA_MU_DIR_PATH.'/wp-content/mu-plugins/wppa-critical-plugins/flying-pages/flying-pages.php' );












###
//Here load all the **critial** plugins here
###




###
// #1 to update and manage reporting error level edit the following file. - Do not Remove or Comment out.*****
###
require_once( WPPA_MU_DIR_PATH.'/wp-content/mu-plugins/wppa-critical-plugins/wppa-error-reporting/wppa-error-reporting.php' );



###
// #2 superduper related functions - Do not Remove or Comment out.*****
###
require_once( WPPA_MU_DIR_PATH.'/wp-content/mu-plugins/wppa-critical-plugins/wppa-superduper-related-functions/wppa-superduper-related-functions.php' );






###
// #3 all-in-one-wp-migration - We are using patched version all-in-one-wp-migration
###
define( 'REQUIRED_FILE_all_in_one_wp_migration', true );
require_once( WPPA_MU_DIR_PATH.'/wp-content/mu-plugins/wppa-critical-plugins/all-in-one-wp-migration/all-in-one-wp-migration.php' );


###
// #3.1 all-in-one-wp-migration-s3-client-extension - If you own this plugin files please add the files at the following path of your repo
// PressAgility/wp/wp-content/mu-plugins/wppa-critical-plugins/all-in-one-wp-migration-s3-client-extension
//and uncomment the following two lines:
###
#define( 'REQUIRED_FILE_all_in_one_wp_migration_s3_client_extension', true );
#require_once( WPPA_MU_DIR_PATH.'/wp-content/mu-plugins/wppa-critical-plugins/all-in-one-wp-migration-s3-client-extension/all-in-one-wp-migration-s3-client-extension.php' );


###
// #3.2 wppa-all-in-one-wp-migration-hooks - Do not Remove or Comment out.*****
###
require_once( WPPA_MU_DIR_PATH.'/wp-content/mu-plugins/wppa-critical-plugins/wppa-all-in-one-wp-migration-hooks/wppa-all-in-one-wp-migration-hooks.php' );











###
// #4 one-time-login - critical plugin. Do not Remove or Comment out.*****
###
require_once( WPPA_MU_DIR_PATH.'/wp-content/mu-plugins/wppa-critical-plugins/one-time-login/one-time-login.php' );


###
// #5 redis-cache - Cache is hard coded. Do not Remove or Comment out.*****
###
require_once( WPPA_MU_DIR_PATH.'/wp-content/mu-plugins/wppa-critical-plugins/redis-cache/redis-cache.php' );



###
// #6 wppa lifecyle hooks. Do not Remove or Comment out.****
###
require_once( WPPA_MU_DIR_PATH.'/wp-content/mu-plugins/wppa-critical-plugins/wppa-lifesycle-hooks/wppa-lifesycle-hooks.php' );



###
// #7 Git integration - critical plugin. Do not Remove or Comment out.*****
###
require_once( WPPA_MU_DIR_PATH.'/wp-content/mu-plugins/wppa-critical-plugins/wppa-magic-functions/wppa-git-cicd-magic-deploy.php' );





###
// #9 upload media to s3/r2/spaces/ - critical plugin. Do not Remove or Comment out.*****
###

if( !defined('NETWORK_SELF_HOSTED_FILE_STORE') ){
  define( 'NETWORK_SELF_HOSTED_FILE_STORE', false ); //provide the file store access url e.g. https://our-files.yournetwork.com/ https and ending forward slash is important
}

if( NETWORK_SELF_HOSTED_FILE_STORE === false ){
  if( !defined('WP_INSTALLING') ){
    require_once( WPPA_MU_DIR_PATH.'/wp-content/mu-plugins/wppa-critical-plugins/amazon-s3-and-cloudfront/wordpress-s3.php' );
    require_once( WPPA_MU_DIR_PATH.'/wp-content/mu-plugins/wppa-critical-plugins/wppa-magic-functions/wppa-offload-files-magic.php' );
  }
}



###
// #10 Generate Images on the fly - critical plugin. Do not Remove or Comment out.*****
//order is imporant must come after wppa-offload-files-magic.php
###
define( 'WPPA_DISABLE_IMAGE_SIZES', true ); //this will stop generating WP Image Thumbnails

if( defined('WPPA_DISABLE_IMAGE_SIZES') && WPPA_DISABLE_IMAGE_SIZES == true ){
  
  //setup image sizes that you want to generate on the fly!
  //WP by-default generate some images
  //Thumbnail - 150x150
  //Medium - 300x300
  //Large 1024x1024
  
  define( 'WPPA_VIRTUAL_IMAGES_SIZES', array(
      'v_60x60'=>array(  'name'=>'V 60x60',     'w'=>60,  'h'=>60,  'crop'=>true,   'q'=>60 ),
      'v_100x100'=>array(  'name'=>'V 100x100',   'w'=>100, 'h'=>100, 'crop'=>true,   'q'=>60 ),
      'v_200x200'=>array(  'name'=>'V 200x200',   'w'=>200, 'h'=>200, 'crop'=>false,  'q'=>60 ), //we can skip 150 as it will be created by default
      'v_250x250'=>array(  'name'=>'V 250x250',   'w'=>250, 'h'=>250, 'crop'=>false,  'q'=>60 ),
      'v_350x350'=>array(  'name'=>'V 350x350',   'w'=>350, 'h'=>350, 'crop'=>false,  'q'=>60 ), //we can skip 300 as it will be created by default
      'v_400x400'=>array(  'name'=>'V 400x400',   'w'=>400, 'h'=>400, 'crop'=>false,  'q'=>70 ),
      'v_450x450'=>array(  'name'=>'V 450x450',   'w'=>450, 'h'=>450, 'crop'=>false,  'q'=>70 ),
      'v_500x500'=>array(  'name'=>'V 500x500',   'w'=>500, 'h'=>500, 'crop'=>false,  'q'=>70 ),
      'v_550x550'=>array(  'name'=>'V 550x550',   'w'=>550, 'h'=>550, 'crop'=>false,  'q'=>70 ),
      'v_60x600'=>array(  'name'=>'V 600x600',   'w'=>600, 'h'=>600, 'crop'=>false,  'q'=>70 ),
      'v_650x650'=>array( 'name'=>'V 650x650',   'w'=>650, 'h'=>650, 'crop'=>false,  'q'=>70 ),
      'v_700x700'=>array( 'name'=>'V 700x700',   'w'=>700, 'h'=>700, 'crop'=>false,  'q'=>75 ),
      'v_750x750'=>array( 'name'=>'V 750x750',   'w'=>750, 'h'=>750, 'crop'=>false,  'q'=>75 ),
      'v_800x800'=>array( 'name'=>'V 800x800',   'w'=>800, 'h'=>800, 'crop'=>false,  'q'=>75 ),
      'v_850x850'=>array( 'name'=>'V 850x850',   'w'=>850, 'h'=>850, 'crop'=>false,  'q'=>75 ),
      'v_900x900'=>array( 'name'=>'V 900x900',   'w'=>900, 'h'=>900, 'crop'=>false,  'q'=>80 ),
      'v_950'=>array( 'name'=>'V 950x950',   'w'=>950, 'h'=>950, 'crop'=>false,  'q'=>80 ),
    )
  );
  
  require_once( WPPA_MU_DIR_PATH.'/wp-content/mu-plugins/wppa-critical-plugins/wppa-magic-functions/wppa-generate-image-magic.php' );
}