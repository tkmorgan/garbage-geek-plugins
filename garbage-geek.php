<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://34.73.97.67/
 * @since             1.0.0
 * @package           Garbage_Geek
 *
 * @wordpress-plugin
 * Plugin Name:       Garbage Geek
 * Plugin URI:        http://34.73.97.67/
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            Knox Hacks 2019 Garbage Geeks Team
 * Author URI:        http://34.73.97.67/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       garbage-geek
 * Domain Path:       /languages
 */

function create_geek_post_type() {
	register_post_type( 'geek_tip',
		array(
		'labels' => array(
			'name' => __( 'Tips' ),
			'singular_name' => __( 'Tip' )
		),
		'public' => true,
		'has_archive' => true,
		)
	);
}

function remove_geek_post_type() {
	unregister_post_type('geek_tip');
}
add_action( 'init', 'create_geek_post_type' );

register_deactivation_hook( __FILE__, 'remove_geek_post_type' );