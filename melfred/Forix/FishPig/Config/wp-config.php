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
define('DB_NAME', 'wp_melfredborzall');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define( 'DB_PASSWORD', 'root' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'zinoxhu25s6pukc7bq5nyglotadj4wskdvbb6ples7uavg1kpspwkgrfekwy77dl' );
define( 'SECURE_AUTH_KEY',  '5uzcotqes3icyfyq9u2u8gaddyuxxthla3pwgn0uyvttdqxyoflfsfbiolsex9hl' );
define( 'LOGGED_IN_KEY',    'ybmwmzpfqonzfytwrlvgdmqku70ztmln89xszupngpygwyqometmbnlt1xjgdspk' );
define( 'NONCE_KEY',        '2fxax1go2yocpsyvpbf6k8nlfxntbqimsj2eidyjuxivft1sgd2yabnes3j4z7fy' );
define( 'AUTH_SALT',        'ykprhd62pgwchqefvtzkhxbzwypzlxqnpanykno6o3ecsjppmwpepbnj5vkn4vfu' );
define( 'SECURE_AUTH_SALT', '9zzbozyti1vuqrbmuvg3minadc32hnaxpay1mu9x7qgflpmfbbne1zchpbduxom3' );
define( 'LOGGED_IN_SALT',   'bcmpii8cerlg1gqf7pivek8xktfnmptcskezvxzxqz1fqhaymzozazycmxhrg6ap' );
define( 'NONCE_SALT',       '4dsypofnokoq1zhdv7byvw6bqig2vmjjois9e9nl3zlqv1drplcett4x2yrz3yig' );

/**#@-*/

define( 'CONCATENATE_SCRIPTS', false );
/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

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
// WordPress debug settings in test and live environments.
if ( in_array( $_SERVER['HTTP_HOST'], [ 'melfredborzall.com', 'www.melfredborzall.com' ] ) || in_array( $_SERVER['PANTHEON_ENVIRONMENT'], [ 'dev', 'aivaras' ] ) ) {
	// Debugging disabled.
	ini_set( 'log_errors', 'On' );
	ini_set( 'display_errors', 'Off' );
	ini_set( 'error_reporting', E_ALL );
	define( 'WP_DEBUG', false );
	define( 'WP_DEBUG_LOG', true );
	define( 'WP_DEBUG_DISPLAY', false );
} // WordPress debug settings in development environments.
else {
	// Debugging enabled.
	if ( ! defined( 'WP_DEBUG' ) ) {
		define( 'WP_DEBUG', true );
	}
	define( 'WP_DEBUG_LOG', true ); // Stored in wp-content/debug.log
	define( 'WP_DEBUG_DISPLAY', true );
}

define( 'SCRIPT_DEBUG', true );

define( 'WP_POST_REVISIONS', 3 );
define( 'WP_MEMORY_LIMIT', '256M' );

// Configure directory & filename of cached autoptimize files
define( 'AUTOPTIMIZE_CACHE_CHILD_DIR', '/uploads/resources/' );
define( 'AUTOPTIMIZE_CACHEFILE_PREFIX', 'aggregated_' );

$countriesStates = [
	'United States',
	'Afghanistan',
	'Albania',
	'Algeria',
	'American Samoa',
	'Andorra',
	'Angola',
	'Antigua and Barbuda',
	'Argentina',
	'Armenia',
	'Australia',
	'Austria',
	'Azerbaijan',
	'Bahamas',
	'Bahrain',
	'Bangladesh',
	'Barbados',
	'Belarus',
	'Belgium',
	'Belize',
	'Benin',
	'Bermuda',
	'Bhutan',
	'Bolivia',
	'Bosnia and Herzegovina',
	'Botswana',
	'Brazil',
	'Brunei',
	'Bulgaria',
	'Burkina Faso',
	'Burundi',
	'Cambodia',
	'Cameroon',
	'Canada',
	'Cape Verde',
	'Cayman Islands',
	'Central African Republic',
	'Chad',
	'Chile',
	'China',
	'Colombia',
	'Comoros',
	'Congo, Democratic Republic of the',
	'Congo, Republic of the',
	'Costa Rica',
	'Côte d\'Ivoire',
	'Croatia',
	'Cuba',
	'Curaçao',
	'Cyprus',
	'Czech Republic',
	'Denmark',
	'Djibouti',
	'Dominica',
	'Dominican Republic',
	'East Timor',
	'Ecuador',
	'Egypt',
	'El Salvador',
	'Equatorial Guinea',
	'Eritrea',
	'Estonia',
	'Ethiopia',
	'Faroe Islands',
	'Fiji',
	'Finland',
	'France',
	'French Polynesia',
	'Gabon',
	'Gambia',
	'Georgia',
	'Germany',
	'Ghana',
	'Greece',
	'Greenland',
	'Grenada',
	'Guam',
	'Guatemala',
	'Guinea',
	'Guinea - Bissau',
	'Guyana',
	'Haiti',
	'Honduras',
	'Hong Kong',
	'Hungary',
	'Iceland',
	'India',
	'Indonesia',
	'Iran',
	'Iraq',
	'Ireland',
	'Israel',
	'Italy',
	'Jamaica',
	'Japan',
	'Jordan',
	'Kazakhstan',
	'Kenya',
	'Kiribati',
	'North Korea',
	'South Korea',
	'Kosovo',
	'Kuwait',
	'Kyrgyzstan',
	'Laos',
	'Latvia',
	'Lebanon',
	'Lesotho',
	'Liberia',
	'Libya',
	'Liechtenstein',
	'Lithuania',
	'Luxembourg',
	'Macedonia',
	'Madagascar',
	'Malawi',
	'Malaysia',
	'Maldives',
	'Mali',
	'Malta',
	'Marshall Islands',
	'Mauritania',
	'Mauritius',
	'Mexico',
	'Micronesia',
	'Moldova',
	'Monaco',
	'Mongolia',
	'Montenegro',
	'Morocco',
	'Mozambique',
	'Myanmar',
	'Namibia',
	'Nauru',
	'Nepal',
	'Netherlands',
	'New Zealand',
	'Nicaragua',
	'Niger',
	'Nigeria',
	'Northern Mariana Islands',
	'Norway',
	'Oman',
	'Pakistan',
	'Palau',
	'Palestine, State of',
	'Panama',
	'Papua New Guinea',
	'Paraguay',
	'Peru',
	'Philippines',
	'Poland',
	'Portugal',
	'Puerto Rico',
	'Qatar',
	'Romania',
	'Russia',
	'Rwanda',
	'Saint Kitts and Nevis',
	'Saint Lucia',
	'Saint Vincent and the Grenadines',
	'Samoa',
	'San Marino',
	'Sao Tome and Principe',
	'Saudi Arabia',
	'Senegal',
	'Serbia',
	'Seychelles',
	'Sierra Leone',
	'Singapore',
	'Sint Maarten',
	'Slovakia',
	'Slovenia',
	'Solomon Islands',
	'Somalia',
	'South Africa',
	'Spain',
	'Sri Lanka',
	'Sudan',
	'Sudan, South',
	'Suriname',
	'Swaziland',
	'Sweden',
	'Switzerland',
	'Syria',
	'Taiwan',
	'Tajikistan',
	'Tanzania',
	'Thailand',
	'Togo',
	'Tonga',
	'Trinidad and Tobago',
	'Tunisia',
	'Turkey',
	'Turkmenistan',
	'Tuvalu',
	'Uganda',
	'Ukraine',
	'United Arab Emirates',
	'United Kingdom',
	'Uruguay',
	'Uzbekistan',
	'Vanuatu',
	'Vatican City',
	'Venezuela',
	'Vietnam',
	'Virgin Islands, British',
	'Virgin Islands, U . S . Yemen',
	'Zambia',
	'Zimbabwe',
	'Alabama',
	'Alaska',
	'Arizona',
	'Arkansas',
	'California',
	'Colorado',
	'Connecticut',
	'Delaware',
	'District of Columbia',
	'Florida',
	'Georgia',
	'Hawaii',
	'Idaho',
	'Illinois',
	'Indiana',
	'Iowa',
	'Kansas',
	'Kentucky',
	'Louisiana',
	'Maine',
	'Maryland',
	'Massachusetts',
	'Michigan',
	'Minnesota',
	'Mississippi',
	'Missouri',
	'Montana',
	'Nebraska',
	'Nevada',
	'New Hampshire',
	'New Jersey',
	'New Mexico',
	'New York',
	'North Carolina',
	'North Dakota',
	'Ohio',
	'Oklahoma',
	'Oregon',
	'Pennsylvania',
	'Rhode Island',
	'South Carolina',
	'South Dakota',
	'Tennessee',
	'Texas',
	'Utah',
	'Vermont',
	'Virginia',
	'Washington',
	'West Virginia',
	'Wisconsin',
	'Wyoming',
	'Armed Forces Americas',
	'Armed Forces Europe',
	'Armed Forces Pacific',
	'united states',
	'afghanistan',
	'albania',
	'algeria',
	'american samoa',
	'andorra',
	'angola',
	'antigua and barbuda',
	'argentina',
	'armenia',
	'australia',
	'austria',
	'azerbaijan',
	'bahamas',
	'bahrain',
	'bangladesh',
	'barbados',
	'belarus',
	'belgium',
	'belize',
	'benin',
	'bermuda',
	'bhutan',
	'bolivia',
	'bosnia and herzegovina',
	'botswana',
	'brazil',
	'brunei',
	'bulgaria',
	'burkina faso',
	'burundi',
	'cambodia',
	'cameroon',
	'canada',
	'cape verde',
	'cayman islands',
	'central african republic',
	'chad',
	'chile',
	'china',
	'colombia',
	'comoros',
	'congo, democratic republic of the',
	'congo, republic of the',
	'costa rica',
	'côte d\'ivoire',
	'croatia',
	'cuba',
	'curaçao',
	'cyprus',
	'czech republic',
	'denmark',
	'djibouti',
	'dominica',
	'dominican republic',
	'east timor',
	'ecuador',
	'egypt',
	'el salvador',
	'equatorial guinea',
	'eritrea',
	'estonia',
	'ethiopia',
	'faroe islands',
	'fiji',
	'finland',
	'france',
	'french polynesia',
	'gabon',
	'gambia',
	'georgia',
	'germany',
	'ghana',
	'greece',
	'greenland',
	'grenada',
	'guam',
	'guatemala',
	'guinea',
	'guinea - bissau',
	'guyana',
	'haiti',
	'honduras',
	'hong kong',
	'hungary',
	'iceland',
	'india',
	'indonesia',
	'iran',
	'iraq',
	'ireland',
	'israel',
	'italy',
	'jamaica',
	'japan',
	'jordan',
	'kazakhstan',
	'kenya',
	'kiribati',
	'north korea',
	'south korea',
	'kosovo',
	'kuwait',
	'kyrgyzstan',
	'laos',
	'latvia',
	'lebanon',
	'lesotho',
	'liberia',
	'libya',
	'liechtenstein',
	'lithuania',
	'luxembourg',
	'macedonia',
	'madagascar',
	'malawi',
	'malaysia',
	'maldives',
	'mali',
	'malta',
	'marshall islands',
	'mauritania',
	'mauritius',
	'mexico',
	'micronesia',
	'moldova',
	'monaco',
	'mongolia',
	'montenegro',
	'morocco',
	'mozambique',
	'myanmar',
	'namibia',
	'nauru',
	'nepal',
	'netherlands',
	'new zealand',
	'nicaragua',
	'niger',
	'nigeria',
	'northern mariana islands',
	'norway',
	'oman',
	'pakistan',
	'palau',
	'palestine, state of',
	'panama',
	'papua new guinea',
	'paraguay',
	'peru',
	'philippines',
	'poland',
	'portugal',
	'puerto rico',
	'qatar',
	'romania',
	'russia',
	'rwanda',
	'saint kitts and nevis',
	'saint lucia',
	'saint vincent and the grenadines',
	'samoa',
	'san marino',
	'sao tome and principe',
	'saudi arabia',
	'senegal',
	'serbia',
	'seychelles',
	'sierra leone',
	'singapore',
	'sint maarten',
	'slovakia',
	'slovenia',
	'solomon islands',
	'somalia',
	'south africa',
	'spain',
	'sri lanka',
	'sudan',
	'sudan, south',
	'suriname',
	'swaziland',
	'sweden',
	'switzerland',
	'syria',
	'taiwan',
	'tajikistan',
	'tanzania',
	'thailand',
	'togo',
	'tonga',
	'trinidad and tobago',
	'tunisia',
	'turkey',
	'turkmenistan',
	'tuvalu',
	'uganda',
	'ukraine',
	'united arab emirates',
	'united kingdom',
	'uruguay',
	'uzbekistan',
	'vanuatu',
	'vatican city',
	'venezuela',
	'vietnam',
	'virgin islands, british',
	'virgin islands, u . s . yemen',
	'zambia',
	'zimbabwe',
	'alabama',
	'alaska',
	'arizona',
	'arkansas',
	'california',
	'colorado',
	'connecticut',
	'delaware',
	'district of columbia',
	'florida',
	'georgia',
	'hawaii',
	'idaho',
	'illinois',
	'indiana',
	'iowa',
	'kansas',
	'kentucky',
	'louisiana',
	'maine',
	'maryland',
	'massachusetts',
	'michigan',
	'minnesota',
	'mississippi',
	'missouri',
	'montana',
	'nebraska',
	'nevada',
	'new hampshire',
	'new jersey',
	'new mexico',
	'new york',
	'north carolina',
	'north dakota',
	'ohio',
	'oklahoma',
	'oregon',
	'pennsylvania',
	'rhode island',
	'south carolina',
	'south dakota',
	'tennessee',
	'texas',
	'utah',
	'vermont',
	'virginia',
	'washington',
	'west virginia',
	'wisconsin',
	'wyoming',
	'armed forces americas',
	'armed forces europe',
	'armed forces pacific',
];

$request_uri = $_SERVER['REQUEST_URI'];

if ( $request_uri !== '/' ) {
	$paths   = explode( '/', $request_uri );
	$count   = count( $paths );
	$endPath = $paths[ $count - 1 ];

	if ( ! empty( $endPath ) && in_array( $endPath, $countriesStates ) && $count > 2 ) {
		array_splice( $paths, $count - 1, 1);
		$return_uri = implode( '/', $paths ) . '/';

		header( 'HTTP/1.0 301 Moved Permanently' );
		header( 'Location: https://www.melfredborzall.com' . $return_uri );
		exit();
	}
}

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) )
	define( 'ABSPATH', dirname(__FILE__) . '/' );

/** Sets up WordPress vars and included files. */
require_once( ABSPATH . 'wp-settings.php' );



# Disables all core updates. Added by SiteGround Autoupdate:
define( 'WP_AUTO_UPDATE_CORE', false );
