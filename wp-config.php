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
define( 'DB_NAME', 'purple_simplep' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', '' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

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
define( 'AUTH_KEY',         ' he^$WGoiyvNQr&oJ}<UP@ZjF.?iQS^xE!jA5*#_7INGA.v}B]rFg~mkTyC$#P7a' );
define( 'SECURE_AUTH_KEY',  'T,>;=>H$axB4YGunxKPLf^LA#y,i*+dkW9{_?+--:5YLCt5A)u+k+dD.RO_|rEPG' );
define( 'LOGGED_IN_KEY',    'D{%~a/6&&RS1/=U/7%FtM9JE&QVBqR63t!7m$= 7S^vD=pAjbd%B)19;r7aM*A}|' );
define( 'NONCE_KEY',        'H^F,+Cx<28zNN} y 8O|JVAjR{c`~]^yZP],B%DW,+{Ih+?1P!bGD5Lv m9t:wV(' );
define( 'AUTH_SALT',        'q?vg@cu$a5u(LO0izCge`Ydl0pDLJG-;G3he5`nNOPnQB^>5vJ.DZee]Npl?*:pl' );
define( 'SECURE_AUTH_SALT', ')|OjbZd<vRykgFN&zj1j)pTgU|K9TB$a[JZod}cOD0%lN!#5f[KF33{hSGD==x^A' );
define( 'LOGGED_IN_SALT',   '{IoRoL*G?4}%}DF.d7DE}/?X|um*~^4OSe4&mG#>B_f=+:K7sg5df~<xc#,TC0vs' );
define( 'NONCE_SALT',       't{0$|F2v3JNsxoh.AwI{gL,ozopm9%}Fa1>:)!{DBtc5 ?oK:g;S#!`Q?gTpJV^@' );

/**#@-*/

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
define( 'WP_DEBUG', false );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

/** Sets up WordPress vars and included files. */
require_once( ABSPATH . 'wp-settings.php' );
