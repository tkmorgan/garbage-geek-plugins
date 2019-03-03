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

include('helpers/tipCategory.php');
include('helpers/recyclingCenterTotals.php');

//Save Custom Fields
	//Geek_tip - tip_category 
	function save_geek_tip_tip_category_fields_meta( $post_id, $post) {   
		// Return if the user doesn't have edit permissions.
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return $post_id;
		}
		// Verify this came from the our screen and with proper authorization,
		// because save_post can be triggered at other times.
		if ( ! isset( $_POST['tip_category'] ) || ! wp_verify_nonce( $_POST['tip_category_fields'], basename(__FILE__) ) ) {
			return $post_id;
		}
		// Now that we're authenticated, time to save the data.
		// This sanitizes the data from the field and saves it into an array $events_meta.
		$events_meta['tip_category'] = esc_textarea( $_POST['tip_category'] );
		// Cycle through the $events_meta array.
		// Note, in this example we just have one item, but this is helpful if you have multiple.
		foreach ( $events_meta as $key => $value ) :
			// Don't store custom data twice
			if ( 'revision' === $post->post_type ) {
				return;
			}
			if ( get_post_meta( $post_id, $key, false ) ) {
				// If the custom field already has a value, update it.
				update_post_meta( $post_id, $key, $value );
			} else {
				// If the custom field doesn't have a value, add it.
				add_post_meta( $post_id, $key, $value);
			}
			if ( ! $value ) {
				// Delete the meta key if there's no value
				delete_post_meta( $post_id, $key );
			}
		endforeach;
	}
	add_action( 'save_post', 'save_geek_tip_tip_category_fields_meta',1 ,2 );
	//Geek_tip - encoragement or critisizm


	function save_generic_category_fields_meta( $field_name, $post_id, $post) {   
		
		// Return if the user doesn't have edit permissions.
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return $post_id;
		}
		// Verify this came from the our screen and with proper authorization,
		// because save_post can be triggered at other times.
		if ( ! isset( $_POST[$field_name] ) || ! wp_verify_nonce( $_POST[$field_name . '_fields'], basename(__FILE__) ) ) {
			return $post_id;
		}
		// Now that we're authenticated, time to save the data.
		// This sanitizes the data from the field and saves it into an array $events_meta.
		$events_meta[$field_name] = esc_textarea( $_POST[$field_name] );
		// Cycle through the $events_meta array.
		// Note, in this example we just have one item, but this is helpful if you have multiple.
		foreach ( $events_meta as $key => $value ) :
			// Don't store custom data twice
			if ( 'revision' === $post->post_type ) {
				return;
			}
			if ( get_post_meta( $post_id, $key, false ) ) {
				// If the custom field already has a value, update it.
				update_post_meta( $post_id, $key, $value );
			} else {
				// If the custom field doesn't have a value, add it.
				add_post_meta( $post_id, $key, $value);
			}
			if ( ! $value ) {
				// Delete the meta key if there's no value
				delete_post_meta( $post_id, $key );
			}
		endforeach;
	}

	// rc_totals 
	function save_rc_totals_rc_totals_fields_meta( $post_id, $post) { 
		foreach(array_keys( recyclingCenterTotals::$types ) as $key)
			save_generic_category_fields_meta( $key, $post_id, $post );
	}

	add_action( 'save_post', 'save_rc_totals_rc_totals_fields_meta',1 ,2 );

//Metaboxes
function garbage_geek_plugin_geek_tip_post_metaboxes(){
	//Tip Catagory
	function garbage_geek_plugin_tip_category(){
		global $post;
		wp_nonce_field( basename( __FILE__ ), 'tip_category_fields' );
		// Get the location data if it's already been entered
		$pluginTip = get_post_meta( $post->ID, 'tip_category', true );
		//function to check if selected
		function selectedChk ($compare, $pluginTip){
			$compare = sanitize_text_field($compare);
			if($pluginTip == $compare){
				return 'value="'.$compare.'" Selected';
			}else{
				return 'value="'.$compare.'"';
			}
		}
		// Output the field
		echo'
			<label title='.esc_attr_e( $term->name ).'/>
			<select id="tip_category" name="tip_category">
				<option '.selectedChk('trash', $pluginTip).'>Trash</option>
				<option '.selectedChk('street-violations', $pluginTip).'>Street Violations</option>
				<option '.selectedChk('recyclable', $pluginTip).'>Recycling</option>
				<option '.selectedChk('non-recyclable', $pluginTip).'>Non Recyclables</option>
				<option '.selectedChk('general', $pluginTip).'>General</option>
			</select>';
		echo '<p>Category of Tip.</p>';
	}
	add_meta_box(
		'tip_category',
		'Category of Tip',
		'garbage_geek_plugin_tip_category',
		array('geek_tip'),
		'normal',
		'default',
		'high'
	);
	//Encouragement or Critizism
}

function garbage_geek_plugin_rc_totals_post_metaboxes(){
	//Tip Catagory
	function garbage_geek_plugin_rc_totals(){
		global $post;
		foreach( recyclingCenterTotals::$types as $slug => $name ) {
			wp_nonce_field( basename( __FILE__ ), "${slug}_fields" );
			$meta_val = get_post_meta( $post->ID, $slug, true );
			?>
			<div>
				<label for="<?=$slug?>">
					<?=$name;?>
					<input 
						type='text' 
						value='<?=$meta_val?>' 
						id='<?=$slug?>' 
						name="<?=$slug?>" 
						/>
				</label>
			</div>
			<?php
		}
	}
	add_meta_box(
		'rc_totals',
		'Recycling Center Counts',
		'garbage_geek_plugin_rc_totals',
		array('rc_totals'),
		'normal',
		'default',
		'high'
	);
	//Encouragement or Critizism
}

//Custom Post Types
	//Geek_tip
	function garbage_geek_plugin_create_rc_totals_post_type() {

		$lbls = array(
			'name' => __('Recycling Center Totals'),
			'singular_name' => __('Recycling Center Total'),
			'add_new'            => __( 'Add New Recycling Center Total' ),
			'add_new_item'       => __( 'Add New Recycling Center Total' ),
			'edit_item'          => __( 'Edit Recycling Center Total' ),
			'new_item'           => __( 'Add New Recycling Center Total' ),
			'view_item'          => __( 'View Recycling Center Total' ),
			'search_items'       => __( 'Search Recycling Center Total' ),
			'not_found'          => __( 'No Recycling Center Totals found' ),
			'not_found_in_trash' => __( 'No Recycling Center Total found in trash' )
		);
		$supports = array('title', 'thumbnail','editor','catagories');
		register_post_type( 'rc_totals',
			array(
			'labels' => $lbls,
			'public' => true,
			'has_archive' => true,
			'description'=> __('Post type for Recycling Center Totals.'),
			'capability_type' => 'post',
			'rewrite'     => array( 'slug' => 'rc_totals'), // my custom slug
			'has_archive' => true,
			'supports' => $supports,
			'register_meta_box_cb' => 'garbage_geek_plugin_rc_totals_post_metaboxes'
			)
		);
	}

	//Geek_tip
	function garbage_geek_plugin_create_geek_tip_post_type() {
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
		$supports = array('title', 'thumbnail','editor','catagories');
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
			'register_meta_box_cb' => 'garbage_geek_plugin_geek_tip_post_metaboxes'
			)
		);
	}
	add_action( 'init', 'garbage_geek_init' );

	function garbage_geek_init() {
		garbage_geek_plugin_create_rc_totals_post_type();
		garbage_geek_plugin_create_geek_tip_post_type();
	}
//Activations
    function garbage_geek_plugin_application_activation(){
        garbage_geek_plugin_create_geek_tip_post_type();
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

function garbage_geek_add_tip_category_page() {
	$PageGuid = site_url() . "/tips";
	$my_post  = array( 'post_title'     => 'Tips',
					'post_type'      => 'page',
					'post_name'      => 'tips',
					'post_content'   => '',
					'post_status'    => 'publish',
					'comment_status' => 'closed',
					'ping_status'    => 'closed',
					'post_author'    => 1,
					'menu_order'     => 0,
					'guid'           => $PageGuid );

	$PageID = wp_insert_post( $my_post, FALSE );
}

register_activation_hook( __FILE__, 'garbage_geek_add_tip_category_page' );

function garbage_geek_add_subscribe_page() {
	$PageGuid = site_url() . "/subscribe";
	$my_post  = array( 'post_title'     => 'Subscribe',
					'post_type'      => 'page',
					'post_name'      => 'subscribe',
					'post_content'   => '',
					'post_status'    => 'publish',
					'comment_status' => 'closed',
					'ping_status'    => 'closed',
					'post_author'    => 1,
					'menu_order'     => 0,
					'guid'           => $PageGuid );

	$PageID = wp_insert_post( $my_post, FALSE );
}

register_activation_hook( __FILE__, 'garbage_geek_add_subscribe_page' );



add_action( 'admin_post_add_email', 'garbage_geek_admin_add_email' );

function garbage_geek_admin_add_email() {
	$email = $_REQUEST['email'];
    // Handle request then generate response using echo or leaving PHP and using HTML

	$user_name = base64_encode( $email );
	$user_id = username_exists( $user_name );

	if ( !$user_id and email_exists($user_email) == false ) {
		$random_password = wp_generate_password( $length=12, $include_standard_special_chars=false );
		$user_id = wp_create_user( $user_name, $random_password, $user_email );

		if( is_numeric( $user_id ) ) {
			echo $user_id;
		} else {
			$rv = [];
			foreach( $user_id->errors as $cat=>$msg ) {
				$tmp = new stdClass();
				$tmp->cat = $cat;
				$tmp->msg = $msg;
				$rv[] = $tmp;
			}
			echo json_encode( $rv );
		}
	} else {
		echo "";
	}
}


