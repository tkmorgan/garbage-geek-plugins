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

function garbage_geek_plugin_create_geek_post_type() {
	$lbls = array(
		'name' => __('Trash Tips'),
		'singular_name' => __('Trash Tip'),
		'add_new'            => __( 'Add New Trash Tip' ),
		'add_new_item'       => __( 'Add New Trash Tip' ),
		'edit_item'          => __( 'Edit Trash Tip' ),
		'new_item'           => __( 'Add New Trash Tip' ),
		'view_item'          => __( 'View Trash Tip' ),
		'search_items'       => __( 'Search Trash Tip' ),
		'not_found'          => __( 'No Trash Tips found' ),
		'not_found_in_trash' => __( 'No Trash Tip found in trash' )
	);
	$supports = array('title', 'thumbnail','editor');
	register_post_type( 'geek_tip',
		array(
		'labels' => $lbls,
		'public' => true,
		'has_archive' => true,
		'description'=> __('Post type for Trash Tips.'),
		'capability_type' => 'post',
		'rewrite'     => array( 'slug' => 'geek_tip'), // my custom slug
		'has_archive' => true,
		'supports' => $supports,
		// 'register_meta_box_cb' => 'metaboxes_function'
		)
	);
}
add_action( 'init', 'garbage_geek_plugin_create_geek_post_type' );

//Activations
    function garbage_geek_plugin_application_activation(){
        garbage_geek_plugin_create_geek_post_type();
        flush_rewrite_rules();
    }
    register_activation_hook( __FILE__, 'garbage_geek_plugin_application_activation' );
//Deactivations
    function garbage_geek_plugin_de_activation() {
        unregister_post_type( 'geek_tip');
        flush_rewrite_rules();
    }
    // register_deactivation_hook( __FILE__, 'whisper_room_plugin_application_de_activation' );
//Unsinstallation
?>
