<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * Localized language
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'local' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', 'root' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

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
define( 'AUTH_KEY',          'aP,XTzbNJq,GBr*^e;_{uOie-]*gV97km4=0ws )83U-[aq35LZ)k9x9pLas4_x4' );
define( 'SECURE_AUTH_KEY',   '5cT4c)-yL;lNs&9Grs:/ ^pnJ8W[IZ+j:_oIa,d5@l5tuuz,Ajw21uZu|pt%kkXK' );
define( 'LOGGED_IN_KEY',     '=l]g=8dT,,9KKckfkn:aNI1!QTQ +|L]bhgN_r,q9*e)F0?L9R4g:8u.:6xyEx~i' );
define( 'NONCE_KEY',         '/v%c&eWEh`e=Bxv6i[YV?=,Q3I[>I[RMOOEMwWPe`N8@gf2e[,RoGA{/s+Jez#D~' );
define( 'AUTH_SALT',         'NPSu#zy~f2{ -4k$tqZwR@4zsC3V>_kR_tb$e>!NAC>36s^H$w%O>fxbJ-{9_i.C' );
define( 'SECURE_AUTH_SALT',  '>o%_;d$6YjS[E)qrBBMzPR@bUX@zCMUVw8t+uR+l_h9J%g=+PdpAzs*B:k8EWA-$' );
define( 'LOGGED_IN_SALT',    '!#0JO`;[0SAWK]#;.w6b7sW[~sE;H/,fi~~rG+xye@9UJ(~&%(d{ixFi?L^5udks' );
define( 'NONCE_SALT',        'h=+vlr0:6S#AL%m+lOJ<,QLJ~e9W0W=E[XJ%+<]&-UDu=BVYmbsR9?OZYLsbM Wf' );
define( 'WP_CACHE_KEY_SALT', '9:{7Sh>fB]K2oRqvXX;4P~uM3gAP+Htp|C,[j_t0sADthRY p[:Cd:+&Dk*nx/_Y' );


/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';


/* Add any custom values between this line and the "stop editing" line. */



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
if ( ! defined( 'WP_DEBUG' ) ) {
	define( 'WP_DEBUG', false );
}

define( 'WP_ENVIRONMENT_TYPE', 'local' );
/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
