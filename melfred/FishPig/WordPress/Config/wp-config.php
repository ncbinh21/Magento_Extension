<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'wordpress');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', 'root');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'Y(4&`F/3*},&e{6vnrJs Y95[weN<M,%%S}Uz;bcYF6X1T|PDko5&(Vb.Zc/by<;');
define('SECURE_AUTH_KEY',  'Y_2jt--?bZ^18X`0t Mm/V&>^Ju H;Kkxm.vtOP!4$d.8U_B,heX$X%5;|enm]W<');
define('LOGGED_IN_KEY',    'Kd{1~n765=Ph$]W:.ZHE[J%lph7td;#u,#iTgQYl)r||jemU3Y&3LWk#Lv#T53W@');
define('NONCE_KEY',        'D9^fSGgf|k2_jfeg^V@W[Mpej}WfPDiD:X<.H-W/den(mhPr<t;6-[4s,nP:B,j0');
define('AUTH_SALT',        '#,!$pOZfuN&/EeP++0zhhtAZ{8PvsEx##dYdYlIQ*vX{Et>GP%hfA7gr-}m^)+F%');
define('SECURE_AUTH_SALT', 'p~_H~H$/phhP$%Z~GznJ2QdP!MIBkqP];!PG<OfuU|82G,_}0-~9)q904?q){4F?');
define('LOGGED_IN_SALT',   'IW%:+!i(lexga}SO>jyAy>rXu>b!^|%l%RKY$vh}dg4VeEyEh~<*wr95Wff4E|5~');
define('NONCE_SALT',       'Fv~7VqdR8b#@|7!oAz+NYztaBXQ,A,hA;L_f7aw]cZL0Yq3{[VU-=s+Y/D<W5%ha');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', true);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
