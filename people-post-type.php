<?php
/*
Plugin Name: People
Version: 1.0
Plugin URI: http://rocketlift.com/software/people
Description: Manage and information on individual people within WordPress
Author: RocketLift
Author URI: http://rocketlift.com/
License: GPL 2
Text Domain: people
*/

/*  Copyright 2013 Rocket Lift (email : software@rocketlift.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

if ( ! defined( 'RLI_PEOPLE_PREFIX' ) ) {
	define( 'RLI_PEOPLE_PREFIX', 'rli_people_' );
}

/**
 * Initialization routine for the plugin.
 * - Registers custom post types.
 *
 * @since 0.2
 * @todo Register the default textdomain.
 */
function rli_people_init() {

	require_once( 'lib/cmb.php' );
	if ( ! class_exists( 'People_Post_Type' ) ) {
		require_once( 'lib/class.people-post-type.php' );
	}
	if ( ! class_exists( 'Person' ) ) {
		require_once( 'lib/class.Person.php' );
	}

	People_Post_Type::setup();
}
add_action( 'init', 'rli_people_init' );

/**
 * Plugin activation
 *
 * @since 0.2
 */
if ( ! function_exists( 'people_post_type_activation' ) ) {
	function people_post_type_activation() {
		people_post_type_init();
		flush_rewrite_rules();
	}
	register_activation_hook( __FILE__, 'people_post_type_activation' );
}


/**
 * Flush rewrite rules on plugin deactivation.
 *
 * @since 0.2
 */
if ( ! function_exists( 'people_post_type_deactivation' ) ) {
	function people_post_type_deactivation() {
		flush_rewrite_rules();
	}
	register_deactivation_hook( __FILE__, 'people_post_type_deactivation' );
}


/*********************** User Functions **************************/


/*
 *	people_list() takes query arguments for people and 
 *	performs the query, manages a custom loop, and echoes html
 *
 *	@param $args an array of $args formatted for WP_Query to accept
 *	
 *	@return true if we output html with people; false if not
 */

if ( ! function_exists( 'people_list_people' ) ) {
	function people_list_people( $args = null, $callback = null ) {
		People_Post_Type::list_people( $args, $callback );
	}
}

/**
 * Returns an array containing all the meta values attached to filter 'people_atts'
 *
 * Uses filter 'people_atts' to allow users to add their own meta fields
 *
 * @param $person The post id of the person
 * 
 * @return An associative array containing all the meta values for the person ; false if $person was invalid
 */
if ( ! function_exists( 'people_get_person' ) ) {
	function people_get_person() {
		return People_Post_Type::get_person();
	}
}

/**
 * Returns the html code for rendering a single person.
 * Must be called inside the loop to work. 
 *
 * To change how a person is rendered, add a filter to 'people_single_callback'
 */
if ( ! function_exists( 'people_render_single_person' ) ) {
	function people_render_single_person() {
		return People_Post_Type::render_single_person();
	}
}

/*************** Utility Functions *********************/

/*
 * Extend tinymce to the excerpt editor
 *
 * @kudos http://haet.at/add-tinymce-editor-wordpress-excerpt-field/
 */
if ( ! function_exists( 'people_tinymce_excerpt_js' ) ) {
	function people_tinymce_excerpt_js() {
		if ( 'people' != get_post_type() ) {
			return;
		}
	?>
		<script type="text/javascript">// <![CDATA[
			jQuery(document).ready( tinymce_excerpt );
			function tinymce_excerpt() {
			    jQuery("#excerpt").addClass("mceEditor");
			    tinyMCE.execCommand("mceAddControl", false, "excerpt");
			    tinyMCE.onAddEditor.add(function(mgr,ed) {
			        if(ed.id=="excerpt"){
			            ed.settings.theme_advanced_buttons2 ="";
			            ed.settings.theme_advanced_buttons1 = "bold,italic,underline,seperator,justifyleft,justifycenter,justifyright,separator,link,unlink,seperator,pastetext,pasteword,removeformat,seperator,undo,redo,seperator,spellchecker,";
			        }
			    });
			}
		// ]]></script>
	<?php
	}
}
add_action( 'admin_head-post.php', 'people_tinymce_excerpt_js');
add_action( 'admin_head-post-new.php', 'people_tinymce_excerpt_js');

/**
 * Handles the saving of a meta field
 */
if ( ! function_exists( 'people_save_meta' ) ) {
	function people_save_meta( $post_id, $post_type, $field_id ) {
		
		// Verify if this is an auto save routine. If it is our form has not been submitted, so we dont want
		// to do anything
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}
		// Check permissions to edit pages and/or posts		
		if ( ! current_user_can( 'edit_page', $post_id ) || ! current_user_can( 'edit_post', $post_id ) ) {
			return $post_id;
		}
		// save data in INVISIBLE custom field (note the "_" prefixing the custom fields' name
		update_post_meta( $post_id, '_' . $field_id, $_POST[$field_id] ); 
	}
}

/**
 * Adds the metabox for the details
 */
if ( ! function_exists( 'people_details_box') ) {
	function people_details_box() {
		add_meta_box( 'details', __( 'Person Details', 'people' ), 'render_people_details_metabox', 'people', 'normal', 'high' );
	}
	add_action( 'people_create_metaboxes', 'people_details_box' );
}
/**
 * Actual rendering of the details metabox
 */
if ( ! function_exists( 'render_people_details_metabox' ) ) {
	function render_people_details_metabox( $post ) {
		wp_nonce_field( 'people', 'people_details_nonce' ); 
		do_action( 'people_details_metabox', $post );
	}
}
/**
 * Verify that the detail nonce has been defined and this is the poeple CPT before saving
 */
function people_verify_detail_nonce( $post_id ) {
	
	// only call action if save is for the people post type
	if ( 'people' != get_post_type( $post_id ) ) {
		return $post_id;
	}
	
	// Verify this came from the our screen and with proper authorization,
	// because save_post can be triggered at other times
	if ( ! wp_verify_nonce( $_POST['people_details_nonce'], 'people' )) {
		return $post_id;
	}
	
	// if verified then save the subfields
	do_action( 'people_save_details', $post_id );
}
add_action( 'save_post', 'people_verify_detail_nonce' );

/**
 * MetaBox that connects users entity to the People CPT
 */
if ( ! function_exists( 'people_user_link' ) ) {
	function people_user_link() {
		add_meta_box( 'people_pu_metabox', __( 'User Account', 'people' ), 'people_user_box', 'people', 'side' );
	}
	add_action( 'add_meta_boxes', 'people_user_link' );

	function people_user_box( $post ){
		wp_nonce_field( 'people', 'people_user_nonce' );
	
		$user_id = get_post_meta( $post->ID, '_user', true );
		wp_dropdown_users( array(
			'show_option_none' => 'Not a User',
			'selected' => $user_id
		) );
	}
}

/*
 * Add save action
 */
if ( ! function_exists( 'people_save_user' ) ) {
	function people_save_user( $post_id ) {

		if ( 'people' != get_post_type( $post_id ) ) { 
			return $post_id;
		}
		// manually verify this nonce
		if ( ! wp_verify_nonce( $_POST['people_user_nonce'], 'people' )) {
			return $post_id;
		}
	
		people_save_meta( $post_id, 'people', 'user' );
	
	}
	add_action('save_post', 'people_save_user' );
}

/* 
 * Add people_atts hook
 */
if ( ! function_exists( 'people_user_atts_hook' ) ) {
	function people_user_atts_hook( $arr, $id ) {
		$arr['user'] = get_post_meta( $id, '_user', true );
		return $arr;
	}
	add_filter( 'people_atts', 'people_user_atts_hook', 2, 2 );
}

// Add default fields
require_once( 'lib/defaults.php' );

/**
 * One Time Script to convert People metadata to CMB2 fields
 */
function rli_people_update_people_meta_for_cmb2() {
	// Get all of the People posts
	$args = array(
		'post_type' => 'people',
		'posts_per_page' => -1
	);
	$people = new WP_Query( $args );

	foreach ( $people->posts as $post ) {

		// Get existing values

		$title = get_post_meta( $post->ID, '_title', true );
		$phone_num = get_post_meta( $post->ID, '_phone-number', true );
		$phone_extension = get_post_meta( $post->ID, '_phone_extension', true );
		$email = get_post_meta( $post->ID, '_email', true );

		// Set new values

		if ( ! empty( $title ) ) {
			$title_group = get_post_meta( $post->ID, '_rli_people_group_title' );
			$title_group[0]['title'] = $title;
			update_post_meta( $post->ID, '_rli_people_group_title', $title_group );
		}

		$phone_group = get_post_meta( $post->ID, '_rli_people_group_phone' );
		if ( ! empty( $phone_num ) ) {
			$phone_group[0]['phone'] = $phone_num;
		}
		if ( ! empty( $phone_extension ) ) {
			$phone_group[0]['extension'] = $phone_extension;
		}
		update_post_meta( $post->ID, '_rli_people_group_phone', $phone_group );

		if ( ! empty( $email ) ) {
			$email_group = get_post_meta( $post->ID, '_rli_people_group_email' );
			$email_group[0]['email'] = $email;
			update_post_meta( $post->ID, '_rli_people_group_email', $email_group );
		}

		// Cleanup: delete existing values
		if ( true ) { // false when debugging to avoid losing test data
			delete_post_meta( $post->ID, '_title' );
			delete_post_meta( $post->ID, '_phone-number' );
			delete_post_meta( $post->ID, '_phone_extension' );
			delete_post_meta( $post->ID, '_email' );
		}

	}
}

add_action( 'rli_one_time_scripts', 'rli_people_update_people_meta_for_cmb2' );
