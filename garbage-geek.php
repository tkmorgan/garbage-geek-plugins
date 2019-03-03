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
include('helpers/landfillClasses.php');
include('helpers/swmfs.php');

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

	function garbage_geek_save_post( $post_id, $post ) {
		landfillClasses::save_landfill_classes_fields_meta( $post_id, $post );
		recyclingCenterTotals::save_rc_totals_rc_totals_fields_meta( $post_id, $post );
		save_geek_tip_tip_category_fields_meta( $post_id, $post );
		swmfs::save_fields_meta( $post_id, $post );
	}

	add_action( 'save_post', 'garbage_geek_save_post',1 ,2 );

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

function garbage_geek_plugin_landfill_classes_post_metaboxes() {
	//Commodity Recycling Centers
	function garbage_geek_plugin_landfill_classes(){
		global $post;
		landfillClasses::get_metaboxes_for_text_fields($post);
		landfillClasses::get_metabox_for_category_field($post);
	}

	add_meta_box(
		'landfill_classes',
		'Waste Types (in tons)',
		'garbage_geek_plugin_landfill_classes',
		array('landfill_classes'),
		'normal',
		'default',
		'high'
	);
}

function garbage_geek_plugin_swmf_post_metaboxes() {
	//Commodity Recycling Centers
	function garbage_geek_plugin_swmf(){
		global $post;
		swmfs::get_metaboxes_for_text_fields($post);
		swmfs::get_metabox_for_category_field($post);
	}

	add_meta_box(
		'swmfs',
		'SWMF RECYCLED/DIVERTED (in Tons)',
		'garbage_geek_plugin_swmf',
		array('swmf'),
		'normal',
		'default',
		'high'
	);
}

function garbage_geek_plugin_rc_totals_post_metaboxes(){
	//Commodity Recycling Centers
	function garbage_geek_plugin_rc_totals(){
		global $post;
		recyclingCenterTotals::get_metaboxes_for_text_fields($post);
		recyclingCenterTotals::get_metabox_for_rc_center_category_field($post);
	}

	add_meta_box(
		'rc_totals',
		'Recycling Center Counts (in pounds)',
		'garbage_geek_plugin_rc_totals',
		array('rc_totals'),
		'normal',
		'default',
		'high'
	);
}

//Custom Post Types
	//Commodity Recycling
	function garbage_geek_plugin_create_rc_totals_post_type() {

		$lbls = array(
			'name' => __('Commodity Recycling'),
			'singular_name' => __('Commodity Recycling'),
			'add_new'            => __( 'Add New Recycling Center' ),
			'add_new_item'       => __( 'Add New Recycling Center' ),
			'edit_item'          => __( 'Edit Recycling Center' ),
			'new_item'           => __( 'Add New Recycling Center' ),
			'view_item'          => __( 'View Recycling Center Total' ),
			'search_items'       => __( 'Search Recycling Center Total' ),
			'not_found'          => __( 'No Recycling Center Totals found' ),
			'not_found_in_trash' => __( 'No Recycling Center Total found in trash' )
		);
		$supports = array('title'/*, 'thumbnail','editor'*/,'catagories');
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

	//Landfills
	function garbage_geek_plugin_create_landfill_class_post_type() {

		$lbls = array(
			'name' => __('Landfills'),
			'singular_name' => __('Landfill'),
			'add_new'            => __( 'Add New Landfill Class' ),
			'add_new_item'       => __( 'Add New Landfill Class' ),
			'edit_item'          => __( 'Edit Landfill Class' ),
			'new_item'           => __( 'Add New Landfill Class' ),
			'view_item'          => __( 'View Landfill Class' ),
			'search_items'       => __( 'Search Landfill Cla0ss' ),
			'not_found'          => __( 'No Landfill Classes found' ),
			'not_found_in_trash' => __( 'No Landfill Classes found in trash' )
		);
		$supports = array('title'/*, 'thumbnail','editor'*/,'catagories');
		register_post_type( 'landfill_classes',
			array(
			'labels' => $lbls,
			'public' => true,
			'has_archive' => true,
			'description'=> __('Post type for Landfill Classes.'),
			'capability_type' => 'post',
			'rewrite'     => array( 'slug' => 'landfill_classes'), // my custom slug
			'has_archive' => true,
			'supports' => $supports,
			'register_meta_box_cb' => 'garbage_geek_plugin_landfill_classes_post_metaboxes'
			)
		);
	}

		//SWMF (Solid Waste Management Facility)
		function garbage_geek_plugin_create_swmf_post_type() {

			$lbls = array(
				'name' => __('SWMFs'),
				'singular_name' => __('SWMF'),
				'add_new'            => __( 'Add New SWMF Class' ),
				'add_new_item'       => __( 'Add New SWMF Class' ),
				'edit_item'          => __( 'Edit SMWF Class' ),
				'new_item'           => __( 'Add New SWMF Class' ),
				'view_item'          => __( 'View SWMF Class' ),
				'search_items'       => __( 'Search SWMF Cla0ss' ),
				'not_found'          => __( 'No SWMF Classes found' ),
				'not_found_in_trash' => __( 'No SWMF Classes found in trash' )
			);
			$supports = array('title'/*, 'thumbnail','editor'*/,'catagories');
			register_post_type( 'swmf',
				array(
				'labels' => $lbls,
				'public' => true,
				'has_archive' => true,
				'description'=> __('Post type for SWMF.'),
				'capability_type' => 'post',
				'rewrite'     => array( 'slug' => 'smwf'), // my custom slug
				'has_archive' => true,
				'supports' => $supports,
				'register_meta_box_cb' => 'garbage_geek_plugin_swmf_post_metaboxes'
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
		garbage_geek_plugin_create_landfill_class_post_type();
		garbage_geek_plugin_create_swmf_post_type();
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

# register_activation_hook( __FILE__, 'garbage_geek_add_tip_category_page' );

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

# register_activation_hook( __FILE__, 'garbage_geek_add_subscribe_page' );



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





















// Mail Handeling:
    //mailgun request
    function sendMailgun ($subject, $msg){
        //Send Email
        $response = wp_remote_post('https://us-central1-garbage-geek.cloudfunctions.net/mailgun-send', array(
            'headers' => array(
                'from' => "Garbage Geeks",
                'subject' => $subject
            ),  
            'body'=> array(
                'name' => $msg
            )
        ));
    }   
    //Main Tasks
    function whispermail_send_email_to_admin() {
        if (isset($_POST['form-submited'])){
            $frm = $_POST['form-submited'];
        }else{
            return;
        }
        //form Selection
        if($frm == "hm-pg-contact"){
            $reqName = sanitize_user($_POST['name']);
            $subject = "Home Page - Contact - Form Fill For: ".$reqName;
            $reqEmail = sanitize_email($_POST['email']);
            $reqMsg = sanitize_textarea_field($_POST['msg']); 
            sendMailgun($subject, "Sender: $reqName Email: $reqEmail Msg: $reqMsg");
            wp_redirect( get_home_url()."/contact-response/");
        }else{
            wp_redirect( get_home_url()."/404/");
        }
    }
    add_action( 'admin_post_nopriv_contact_form', 'whispermail_send_email_to_admin' );
    add_action( 'admin_post_contact_form', 'whispermail_send_email_to_admin' );
?>
