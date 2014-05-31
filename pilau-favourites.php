<?php

/**
 * Pilau Favourites
 *
 * @package   Pilau_Favourites
 * @author    Steve Taylor <steve@sltaylor.co.uk>
 * @license   GPL-2.0+
 * @copyright 2014 Public Life
 *
 * @wordpress-plugin
 * Plugin Name:			Pilau Favourites
 * Description:			Basic management of favourite posts and pages.
 * Version:				0.1
 * Author:				Steve Taylor
 * Text Domain:			pilau-favourites-locale
 * License:				GPL-2.0+
 * License URI:			http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:			/languages
 * GitHub Plugin URI:	https://github.com/pilau/favourites
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

require_once( plugin_dir_path( __FILE__ ) . 'class-pilau-favourites.php' );

// Register hooks that are fired when the plugin is activated, deactivated, and uninstalled, respectively.
register_activation_hook( __FILE__, array( 'Pilau_Favourites', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'Pilau_Favourites', 'deactivate' ) );

add_action( 'plugins_loaded', array( 'Pilau_Favourites', 'get_instance' ) );
