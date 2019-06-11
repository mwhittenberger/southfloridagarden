<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, WordPress Language, and ABSPATH. You can find more information
 * by visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'garden_mikethetechninja_');

/** MySQL database username */
define('DB_USER', 'gardenmikethetec');

/** MySQL database password */
define('DB_PASSWORD', 'pR-PqntN');

/** MySQL hostname */
define('DB_HOST', 'mysql.garden.mikethetechninja.com');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

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
define('AUTH_KEY',         'o$RTlptwj92QwvH(*jUm&_e;Oy2SKT0XqK%:AfvR/86_P3frA/zpFc"O2SWIwW1r');
define('SECURE_AUTH_KEY',  'HCP*~74KUx_K:5^No:S?N8YvB!4uL#0^jeJtuhyBW1?Eb&cq%dlz/e0@_YBqxz+0');
define('LOGGED_IN_KEY',    'a!XG!MeBD26Zlr|~Aq|p+r/lbqEYa*+tSVLI5m)|Cy$%r)A@DnOfUgkSii?R+U!L');
define('NONCE_KEY',        '9W&YkjZo9gT_I1U9+^dEOl3`MsDa81:qttek*DkNa:&z0i9Qgq?(_F;VRb:TfrCZ');
define('AUTH_SALT',        '0y)Rv#8bl:RCB;^Np#%;3h4$pBC;WTJkH4u@4^7qEItCUIB7co@~%(bOD4luW(RS');
define('SECURE_AUTH_SALT', 'yZKR!4x`*FKb1md!uMijp+dPaEXmvAvD?hFAlupJ5y;MwFno`OBGzo8j;sRQmYlf');
define('LOGGED_IN_SALT',   '9Cy89`XZ5cDb%DjH`/z|mZ&aJIFXZ9YieX9i0l*_l^N7^VP6i_4_%RSCCk4~sP5@');
define('NONCE_SALT',       'Zo~|$?@&5IC!LJwTK862?*hKc_BQJ7JR3?oD18y6p`UtBMs@gzeVyK0Y;WT)W2Y5');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_fssupk_';

/**
 * Limits total Post Revisions saved per Post/Page.
 * Change or comment this line out if you would like to increase or remove the limit.
 */
define('WP_POST_REVISIONS',  10);

/**
 * WordPress Localized Language, defaults to English.
 *
 * Change this to localize WordPress. A corresponding MO file for the chosen
 * language must be installed to wp-content/languages. For example, install
 * de_DE.mo to wp-content/languages and set WPLANG to 'de_DE' to enable German
 * language support.
 */
define('WPLANG', '');

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', true);

/**
 * Removing this could cause issues with your experience in the DreamHost panel
 */

if (preg_match("/^(.*)\.dream\.website$/", $_SERVER['HTTP_HOST'])) {
        $proto = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
        define('WP_SITEURL', $proto . '://' . $_SERVER['HTTP_HOST']);
        define('WP_HOME',    $proto . '://' . $_SERVER['HTTP_HOST']);
}

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');

