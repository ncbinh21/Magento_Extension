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
//define('DB_NAME', 'forix_sss');

/** MySQL database username */
//define('DB_USER', 'root');

/** MySQL database password */
//define('DB_PASSWORD', 'root');

/** MySQL hostname */
//define('DB_HOST', 'localhost');

/** The name of the database for WordPress */
define('DB_NAME', 'm2dev_ssoftstarshoes');

/** MySQL database username */
define('DB_USER', 'dssoftstarshoes');

/** MySQL database password */
define('DB_PASSWORD', 'NTA5ZWNkMmVi');

/** MySQL hostname */
define('DB_HOST', 'database.internal');

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
define('AUTH_KEY',         '&%f|ss{f@>!]zGC5bj*(Jb9Wz}46UN|n!8b>PU,7>lHv`2)/!ACM}9`,.q4wb:Ih');
define('SECURE_AUTH_KEY',  'QYe:9uG]h#[{n6~0Kfl_)D1Tm-%Y`` 0iP4fK 0@~qHcyau,2lu!nT}p_u3R6E4P');
define('LOGGED_IN_KEY',    ' iLhS4(CE%5@L>k*}^YY.f*6z;.,Bp/Tp&9IM?MHYUZp1LYG|kMI~mVl:WdQt-;|');
define('NONCE_KEY',        'ixm^e_bd<&W-4IGsSxXSjI`!:^sPlv3h(SuTr jlupr(z1,/aAKvf?.=W5z3>iin');
define('AUTH_SALT',        '9u6bNQCS>-dFW5s=p000HuuP|I-#:OS_gI9C[&A*&NvJSeF2!>aX?[Gptw.11<=G');
define('SECURE_AUTH_SALT', 'Ifm#^|0qPzG#{b _TStTr~Fzq,?&HzyUH*HKiY^~ex:-Euz-MJZ41FgK|[f@]m61');
define('LOGGED_IN_SALT',   'u7jmRB^.4rwdEmp>LP7(&K;W2%y56+|C7R1,n(nX,ND{si@v:P$*+f?*jvWE|5g@');
define('NONCE_SALT',       'An0(x;`+W]i=;#bpwc{KsW-WhaWVV5}?0}1}^6+i(D]H~keDK|R0P@-YRAmXsPcw');

define('FS_METHOD', 'direct');
define('FTP_BASE', '/usr/home/username/public_html/my-site.example.com/wordpress/');
define('FTP_CONTENT_DIR', '/usr/home/username/public_html/my-site.example.com/wordpress/wp-content/');
define('FTP_PLUGIN_DIR ', '/usr/home/username/public_html/my-site.example.com/wordpress/wp-content/plugins/');
// define('FTP_PUBKEY', '/home/username/.ssh/id_rsa.pub');
// define('FTP_PRIKEY', '/home/username/.ssh/id_rsa');
define('FTP_USER', 'my-ftp-username');
define('FTP_PASS', 'my-ftp-password');
define('FTP_HOST', 'ftp.my-site.example.com');
// define('FTP_SSL', false);
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
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
    define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
