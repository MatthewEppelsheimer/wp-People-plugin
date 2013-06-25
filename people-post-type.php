<?php
/*
Plugin Name: People
Version: 1.0
Plugin URI: http://rocketlift.com/software/people
Description: Manage and information on individual people within WordPress
Author: Rocket Lift via Matthew Eppelsheimer and Kevin Lenihan
Author URI: http://rocketlift.com/
License: GPL 2
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

if ( ! class_exists( 'People_Post_Type' ) ) {
	require_once( 'lib/class.people-post-type.php' );
}
/**
 * Initialization routine for the plugin.
 * - Registers custom post types.
 *
 * @since 0.2
 * @todo Register the default textdomain.
 */
if ( ! function_exists( 'people_post_type_init' ) ) {
	function people_post_type_init() {
		// @todo load_plugin_textdomain( 'people_translate', false, dirname( dirname( plugin_basename( __FILE__) ) ) . '/lang/' );
		People_Post_Type::setup();
	}
	add_action( 'init', 'people_post_type_init' );
}


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
function tinymce_excerpt_js(){?>
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
add_action( 'admin_head-post.php', 'tinymce_excerpt_js');
add_action( 'admin_head-post-new.php', 'tinymce_excerpt_js');

/**
 * Handles the saving of a meta field
 */
if ( ! function_exists( 'people_save_meta' ) ) {
	function people_save_meta( $post_id, $post_type, $nonce_name, $field_id ) {
		// Verify this came from the our screen and with proper authorization,
		// because save_post can be triggered at other times
		if ( ! wp_verify_nonce( $_POST[$nonce_name], 'people' )) {
			return $post_id;
		}

		// Verify if this is an auto save routine. If it is our form has not been submitted, so we dont want
		// to do anything
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
			return $post_id;

		// Check permissions to edit pages and/or posts
		if ( $post_type != $_POST['post_type'] )
			return $post_id;
	
		if ( ! current_user_can( 'edit_page', $post_id ) || ! current_user_can( 'edit_post', $post_id ))
			return $post_id;

		// save data in INVISIBLE custom field (note the "_" prefixing the custom fields' name
		update_post_meta( $post_id, '_' . $field_id, $_POST[$field_id] ); 
	}
}
// Add default fields
require_once( 'lib/defaults.php' );

