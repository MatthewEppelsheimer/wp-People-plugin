<?php
/*
Plugin Name: Rocket Lift People Post Type
Version: 1.
Plugin URI: http://rocketlift.com/software/rl-people
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

if ( ! class_exists( 'RLI_People_Post_Type' ) ) {
	require_once( 'lib/class.rli-people-post-type.php' );
}
/**
 * Initialization routine for the plugin.
 * - Registers custom post types.
 *
 * @since 0.2
 * @todo Register the default textdomain.
 */
if ( ! function_exists('rli_people_post_type_init') ) {
	function rli_people_post_type_init() {
		// @todo load_plugin_textdomain( 'rli_people_translate', false, dirname( dirname( plugin_basename( __FILE__) ) ) . '/lang/' );
		RLI_People_Post_Type::setup();
	}
	add_action( 'init', 'rli_people_post_type_init' );
}


/**
 * Plugin activation
 *
 * @since 0.2
 */
if ( ! function_exists('rli_people_post_type_activation') ) {
	function rli_people_post_type_activation() {
		rli_people_post_type_init();
		flush_rewrite_rules();
	}
	register_activation_hook( __FILE__, 'rli_people_post_type_activation' );
}


/**
 * Flush rewrite rules on plugin deactivation.
 *
 * @since 0.2
 */
if ( ! function_exists('rli_people_post_type_deactivation') ) {
	function rli_people_post_type_deactivation() {
		flush_rewrite_rules();
	}
	register_deactivation_hook( __FILE__, 'rli_people_post_type_deactivation' );
}


/*********************** User Functions **************************/


/*
 *	rli_people_list() takes query arguments for rli-people and 
 *	performs the query, manages a custom loop, and echoes html
 *
 *	@param $args an array of $args formatted for WP_Query to accept
 *	
 *	@return true if we output html with people; false if not
 */

if ( ! function_exists('rli_people_list') ) {
	function rli_people_list( $args = null, $callback = null ) {
		RLI_People_Post_Type::list_people( $args, $callback );
	}
}

/**
 * Returns an array containing all the meta values attached to filter 'rli_people_atts'
 *
 * Uses filter 'rli_people_atts' to allow users to add their own meta fields
 *
 * @param $person The post id of the person
 * 
 * @return An associative array containing all the meta values for the person ; false if $person was invalid
 */
if ( ! function_exists('rli_people_person') ) {
	function rli_people_person( $person = false ) {
		return RLI_People_Post_Type::get_person( $person );
	}
}

/*************** Utility Functions *********************/

/**
 * Creates a tinymce textarea 
 */
if ( ! function_exists('rli_people_metabox_basic_tinymce') ) {
	function rli_people_metabox_basic_tinymce( $post, $field_id, $height ){
		$text = esc_attr( get_post_meta( $post->ID, '_' . $field_id, true ) );
		echo <<<EOT
  	<script type="text/javascript">
		jQuery(document).ready(function() {
		  jQuery("#{$field_id}").addClass("mceEditor");
			if ( typeof( tinyMCE ) == "object" &&
				typeof( tinyMCE.execCommand ) == "function" ) {
				tinyMCE.execCommand("mceAddControl", false, "tinymce");
			}
		});
		</script>
			<textarea class="widefat" id="{$field_id}" name="{$field_id}" style="width: 100%; height:{$height};" >{$text}</textarea>
EOT;
	}
}

/**
 * Handles the saving of a 
 */
if ( ! function_exists('rli_save_meta') ) {
	function rli_save_meta( $post_id, $post_type, $nonce_name, $field_id ) {
		// Verify this came from the our screen and with proper authorization,
		// because save_post can be triggered at other times
		if ( ! wp_verify_nonce( $_POST[$nonce_name], 'rli-people' )) {
			return $post_id;
		}

		// Verify if this is an auto save routine. If it is our form has not been submitted, so we dont want
		// to do anything
		if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) 
			return $post_id;

		// Check permissions to edit pages and/or posts
		if ( $post_type != $_POST['post_type'] )
			return $post_id;
	
		if ( !current_user_can( 'edit_page', $post_id ) || !current_user_can( 'edit_post', $post_id ))
			return $post_id;

		// save data in INVISIBLE custom field (note the "_" prefixing the custom fields' name
		update_post_meta( $post_id, '_' . $field_id, $_POST[$field_id]); 
	}
}
// Add default fields
if ( ! defined( 'RLI_PEOPLE_DEFAULTS_FILE' ) )
	require_once( 'lib/defaults.php' );

