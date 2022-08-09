<?php
define( 'WP_CACHE', false ); // Added by WP Rocket
// Added by WP Rocket


// Begin Really Simple SSL session cookie settings
@ini_set( 'session.cookie_httponly', true );
@ini_set( 'session.cookie_secure', true );
@ini_set( 'session.use_only_cookies', true );
// END Really Simple SSL
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'supherbdooble_newdb' );
/** MySQL database username */
define( 'DB_USER', 'supherbdooble_newdb' );
/** MySQL database password */
define( 'DB_PASSWORD', 't6sJ7CFwsUXYWwAm' );
/** MySQL hostname */
define( 'DB_HOST', 'localhost' );
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
define( 'AUTH_KEY', 'j093izn0sbhp4t9z4yrqvvgxhcfpyztphkh7pppto1svht1evf3urqzymlxtvnmt' );
define( 'SECURE_AUTH_KEY', 'bujep2lacejkgadab3qvautrafzfwkdvmpdbqnmw2u6nkkexvrtujyrpjyn0dlng' );
define( 'LOGGED_IN_KEY', 'ydmr1zsvrmtozt590b16spgwxn1pryabfafjml5dzy1rpd1plmggoc5pkqfdzvjg' );
define( 'NONCE_KEY', 'upsgcsrz2p5m5sc5bubgbbmgmlvc4q1irqeiolf4etbkqtd5c46d3r47vheo0dvm' );
define( 'AUTH_SALT', 'pzxxkcb6htkhpb9kfb6tplwsjgopl6bjw3uouvaielggcmj4teehga9rhwdl7lcw' );
define( 'SECURE_AUTH_SALT', '0oj6av3qdn1l2svszpp4wza3g4qd45dnxd32j0t2olh4gatxe0n0p9zlnwryoxhc' );
define( 'LOGGED_IN_SALT', 'cak03v0wrnw2avd2rwljoonyb6hghra6xhk6qewq1b4l2wdjklfcdqzfdxvuj6vb' );
define( 'NONCE_SALT', 'bajaqftcrjg1viriniybqy04jj13hezx6gz0wetl6bmydnrupulbmvf8vbcxfigk' );
/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wplh_';
/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );
define( 'WP_DEBUG_LOG', true );
define( 'WPCF7_AUTOP', false );
define( 'WP_MEMORY_LIMIT', '512M' );
define( 'WP_MAX_MEMORY_LIMIT', '512M' );
/* Add any custom values between this line and the "stop editing" line. */

/*
 Multisite */
// define( 'WP_ALLOW_MULTISITE', true );
// define( 'MULTISITE', true );
// define( 'SUBDOMAIN_INSTALL', false );
// define( 'DOMAIN_CURRENT_SITE', 'supherb.dooble.us' );
// define( 'PATH_CURRENT_SITE', '/' );
// define( 'SITE_ID_CURRENT_SITE', 1 );
// define( 'BLOG_ID_CURRENT_SITE', 1 );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
