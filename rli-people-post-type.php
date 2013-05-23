<?php
/*
Plugin Name: Rocket Lift People Post Type
Version: 0.1
Plugin URI: http://rocketlift.com/software/rl-people
Description: Manage and information on individual people within WordPress
Author: Rocket Lift and Matthew
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

require_once( 'lib/class.rli-people-post-type.php' );

/**
 * Initialization routine for the plugin.
 * - Registers custom post types.
 *
 * @since 0.2
 * @todo Register the default textdomain.
 */

function rli_people_post_type_init() {
	// @todo load_plugin_textdomain( 'rli_people_translate', false, dirname( dirname( plugin_basename( __FILE__) ) ) . '/lang/' );

	RLI_People_Post_Type::register_people();
}
add_action( 'init', 'rli_people_post_type_init' );

/**
 * Plugin activation
 *
 * @since 0.2
 */
function rli_people_post_type_activation() {
	rli_people_post_type_init();
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'rli_people_post_type_activation' );

/**
 * Flush rewrite rules on plugin deactivation.
 *
 * @since 0.2
 */
function rli_people_post_type_deactivation() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'rli_people_post_type_deactivation' );

// Wire up actions

add_action( 'add_meta_boxes', array( 'RLI_People_Post_Type', 'create_people_detail_metabox' ) );
add_action( 'save_post', array( 'RLI_People_Post_Type', 'people_detail_meta_save' ) ;
add_action( 'init', array( 'RLI_People_Post_Type', 'register_shortcodes' ) );