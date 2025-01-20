<?php
########################################
#####DO-NOT-REMOVE-THIS-BLOCK-START#####
########################################

require_once( '/var/www/WPScalePro/WPScalePro-Controller.php' );

//We have already defined the following constants in above WPScalePro-Controller.php file
//You can use the following constants in your mu-plugins/plugins/themes to further customize your Auto Scaled WordPress Network.

//WPSP_SITE_ID                            #this is current site ID.
//WPSP_RESTRICTION_GROUP_ID               #use this apply restrictions on per site basis.
//WPSP_PLATFORM_DOMAIN                    #contains this network primary domain name.
//WPSP_CURRENT_DOMAIN                     #current domain name. subdomain if it's a subdomain.
//WPSP_REGISTERED_DOMAIN                  #Current registered domain without subdomain if any.
//WPSP_CDN_URL                            #Reverse CDN URL e.g cfcdnsite{ID}-fc.WPSP_REGISTERED_DOMAIN
//WPSP_CURRENT_REQUEST_ON_PRIMARY_SERVER  #If the current request is on primary server it will be true. Important for plugin/theme install/update logic behind Auto Scaling.

//WPSP_WP_ERROR_LOG_PATH    #/mnt/network-share/wp-content//logs/site'.WPSP_SITE_ID.'/logs/wp-errors.log';
//WPSP_PHP_ERROR_LOG_PATH   #/mnt/network-share/wp-content/logs/site'.WPSP_SITE_ID.'/logs/php-errors.log';


//WPSP_DB_NAME       #site'.WPSP_SITE_ID.'
//WPSP_DB_USER       #site'.WPSP_SITE_ID.'
//WPSP_DB_PASSWORD   #DB password
//WPSP_DB_HOST       #DB host private ip address

//WPSP_FS_METHOD = direct;

//WPSP_AUTH_KEY
//WPSP_SECURE_AUTH_KEY
//WPSP_LOGGED_IN_KEY
//WPSP_NONCE_KEY
//WPSP_AUTH_SALT
//WPSP_SECURE_AUTH_SALT
//WPSP_LOGGED_IN_SALT
//WPSP_NONCE_SALT


########################################
#####DO-NOT-REMOVE-THIS-BLOCK-END#######
########################################





$domainExplode = explode( '.', WPSP_CURRENT_DOMAIN );
if( count($domainExplode) > 2 ){
  define( 'WPSP_CURRENT_SITE_IS_SUBDOMAIN', true );
}else{
  define( 'WPSP_CURRENT_SITE_IS_SUBDOMAIN', false );
}

if( WPSP_CURRENT_SITE_IS_SUBDOMAIN ){
  
  if (str_starts_with(WPSP_CURRENT_DOMAIN, 'stage')) {
    define( 'WPSP_CURRENT_SITE_IS_STAGE', true );
  }else{
    define( 'WPSP_CURRENT_SITE_IS_STAGE', false );
  }
  
}else{
  define( 'WPSP_CURRENT_SITE_IS_STAGE', false );
}





define( 'WP_REDIS_IGBINARY', true );
define( 'WP_REDIS_CLIENT', 'predis' );
define( 'WP_REDIS_SENTINEL', 'mymaster' );
define( 'WP_REDIS_SERVERS', ['tcp://127.0.0.1:26379'] );
define( 'WP_REDIS_PREFIX', WPSP_SITE_ID );
define( 'WP_REDIS_DISABLE_ADMINBAR', true );
define( 'WP_REDIS_DISABLE_METRICS', true );
define( 'WP_REDIS_DISABLE_DROPIN_CHECK', true );
define( 'WP_REDIS_DISABLE_BANNERS', true );
define( 'WP_REDIS_DISABLE_COMMENT', true );










//# errors will be logged at WPSP_WP_ERROR_LOG_PATH
//# but type of errors that are logged depends if the site is staged or production.
//# check the mu-plugins/wpsp-critical-plugins/error_reporting/error_reporting.php
define( 'WP_DEBUG', true ); // Enable WP_DEBUG mode
define( 'WP_DEBUG_LOG', WPSP_WP_ERROR_LOG_PATH ); // Enable error logging
define( 'WP_DEBUG_DISPLAY', false ); // Disable error display












//needed for cloudflare
if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
    $_SERVER['HTTPS'] = 'on';
}


//define custom constants
define( 'DISABLE_WP_CRON', true ); //Keep it disabled in Auto Scaling Environment.
define( 'FS_METHOD', WPSP_FS_METHOD ); //File System Write Method



//i thinks now we are using some logic in magic-deploy we cannot really use network-share-main because then we will not be able to run unison - may keep it only for the cli requests.
$makeRequestOnNFSDrive = false;
//if( isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST' ){
//  $makeRequestOnNFSDrive = true;
//}
if( php_sapi_name() == "cli" ){
	$makeRequestOnNFSDrive = true;
}

if( $makeRequestOnNFSDrive ){
  define( 'WP_CONTENT_DIR', '/mnt/network-share-main/wp-content/site'.WPSP_SITE_ID ); //wp-content on NFS dirve.
}else{
  define( 'WP_CONTENT_DIR', '/mnt/network-share/wp-content/site'.WPSP_SITE_ID ); //wp-content on local dirve.
}







if( WPSP_CURRENT_SITE_IS_STAGE ){
  define( 'WP_PLUGIN_DIR',  '/var/www/WPScalePro.stage/wp/wp-content/plugins' ); //ensure plugins are loaded from local SSD drive.
  define( 'WPMU_PLUGIN_DIR',  '/var/www/WPScalePro.stage/wp/wp-content/mu-plugins' ); //ensure plugins are loaded from local SSD drive.
}else{
  define( 'WP_PLUGIN_DIR',  '/var/www/WPScalePro/wp/wp-content/plugins' ); //ensure plugins are loaded from local SSD drive.
  define( 'WPMU_PLUGIN_DIR',  '/var/www/WPScalePro/wp/wp-content/mu-plugins' ); //ensure plugins are loaded from local SSD drive.
}


define( 'WP_CONTENT_URL', 'https://'.WPSP_CURRENT_DOMAIN.'/wp-content/site'.WPSP_SITE_ID ); //custom wp-content URL in Auto Scaling Environment.
define( 'WP_PLUGIN_URL',  'https://'.WPSP_CURRENT_DOMAIN.'/wp-content/plugins' ); //custom plugins URL in Auto Scaling Environment.
define( 'WPMU_PLUGIN_URL',  'https://'.WPSP_CURRENT_DOMAIN.'/wp-content/mu-plugins' ); //custom plugins URL in Auto Scaling Environment.



define( 'DISALLOW_FILE_EDIT', true ); //prohibit editing themes and plugins using the WordPress editor.

//Only allow plugins/themes install/update/delete on primary server.
if( WPSP_CURRENT_DOMAIN == WPSP_PLATFORM_DOMAIN ){
  
  define( 'AUTOMATIC_UPDATER_DISABLED', false ); //Allow automatic updates
  define( 'WP_AUTO_UPDATE_CORE', 'minor' ); //Allow core updates
  define( 'DISALLOW_FILE_MODS', false ); //Allow plugin/theme installs
  
}else{
  
  define( 'AUTOMATIC_UPDATER_DISABLED', true ); //only allow automatic updates on primary server
  define( 'WP_AUTO_UPDATE_CORE', false );
  define( 'DISALLOW_FILE_MODS', true );
}




//some optimizations
define( 'EMPTY_TRASH_DAYS', false );  // turn off the recycle garbage can;
define( 'WP_POST_REVISIONS', false ); // disable record revisions;
define( 'AUTOSAVE_INTERVAL', 120 );   // increase the autosave interval;






/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 *
 * @package WordPress
 */
 

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', WPSP_DB_NAME );

/** Database username */
define( 'DB_USER', WPSP_DB_USER  );

/** Database password */
define( 'DB_PASSWORD', WPSP_DB_PASSWORD  );

/** Database hostname */
define( 'DB_HOST', WPSP_DB_HOST );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         WPSP_AUTH_KEY.WPSP_SITE_ID );
define( 'SECURE_AUTH_KEY',  WPSP_SECURE_AUTH_KEY.WPSP_SITE_ID );
define( 'LOGGED_IN_KEY',    WPSP_LOGGED_IN_KEY.WPSP_SITE_ID );
define( 'NONCE_KEY',        WPSP_NONCE_KEY.WPSP_SITE_ID );
define( 'AUTH_SALT',        WPSP_AUTH_SALT.WPSP_SITE_ID );
define( 'SECURE_AUTH_SALT', WPSP_SECURE_AUTH_SALT.WPSP_SITE_ID );
define( 'LOGGED_IN_SALT',   WPSP_LOGGED_IN_SALT.WPSP_SITE_ID );
define( 'NONCE_SALT',       WPSP_NONCE_SALT.WPSP_SITE_ID );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
