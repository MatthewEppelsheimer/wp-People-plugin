<?php
/**
 * Core functionality for the Rocket Lift People plugin.
 *
 * All functions are static members of this class to allow for easy namespacing, 
 * so this class should not be instantiated.
 *
 * @module RLI_People_Post_Type
 * @author Matthew Eppelsheimer
 */

final class RLI_People_Post_Type {

	/**
	 * Register custom post type rli_people
	 *
	 * @author Matthew Eppelsheimer
	 * @since 0.2
	 */

	function register_people() {
		register_post_type( 'rli_people' , array( 
			'public' => true,
			'supports' =>  array(
				'title',
				'thumbnail',
				'editor'
			),
			'taxonomies' => array( 'post_tag', 'post_category' ),
			'query_var' => 'rli_people',
			'rewrite' =>  array(
				'slug' => 'people'
			),
			'labels' => array(
				'name' => "People",
				'all_items' => "All People",
				'singular_name' => "People",
				'add_new' => "Add a New Person",
				'add_new_item' => "Add a New Person",
				'edit_item' => "Edit Person",
				'new_item' => "Add New Person",
				'view_item' => "View Person",
				'search_items' => "Search People",
				'not_found' => "No People Found Matching Search",
				'not_found_in_trash' => "No People Found in Trash",
				'parent_item_colon' => "Parent Person:"
			)
		) );
	}

	/**
	 * Register meta box for the rli_people post editor screen.
	 *
	 * @uses render_people_detail_metabox()
	 */
	public static function create_people_detail_metabox() {
		add_meta_box( 'people-metabox', 'Person Details', array( 'RLI_People_Post_Type', 'render_people_detail_metabox' ), 'rli_people', 'normal', 'high' );
	}

	/**
	 * Render people detail meta box.
	 * 
	 * Calls 'rli_people_metabox_render' action.
	 */
	public static function render_people_detail_metabox( $post ) {
		do_action( 'rli_people_metabox_render', $post );
	}

	/**
	 * Save People Detail meta box data
	 *
	 * Calls 'rli_people_detail_meta_save' action.
	 */
	function people_detail_meta_save( $post_id ) {
		do_action( 'rli_people_detail_meta_save', $post_id );
	}

	/**
	 * Utility function to return a WP_Query object with posts of type RLI People
	 *
	 * @author Matthew Eppelsheimer
	 * @since 0.2
	 */
	public static function query_publications( $args ) {
		$defaults = array(
			'posts_per_page' => - 1,
			'order'          => 'ASC',
			'orderby'        => 'menu_order'
		);

		$query_args = wp_parse_args( $args, $defaults );
		$query_args['post_type'] = 'publication';

		$results = new WP_Query( $query_args );

		return $results;
	}

	/*
	 *	Shortcode Setup
	 */

	/**
	 *	Register shortcode 
	 */

	function register_shortcodes() {
		add_shortcode( 'rli-people', array( 'RLI_People_Post_Type', 'people_shortcode' ) );
	}

	/*
	 *	Creates a shortcode to display a list of people on demand
	 *
	 *	Supports the 'category' keyword.
	 */

	function people_shortcode( $atts ) {
		$atts = shortcode_atts( 
			array( 
				'category' => ''
			), 
			$atts 
		);
		
		$query_args = array();
		
		if( $atts['category'] != '' )
			$query_args['category'] = $atts['category'];	

		$this->list_people( $query_args );
	}

	/*
	 *	list_people() takes query arguments for rli-people and 
	 *	performs the query, manages a custom loop, and echoes html
	 *
	 *	@param $args an array of $args formatted for WP_Query to accept
	 *	
	 *	@return true if we output html with people; false if not
	 */

	function list_people( $args ) {

		global $post;

		$people = rli_people_query_people( $args );
		
		if ( $people->have_posts() ) {
			$output = "";
			while ( $people->have_posts() ) {
				$people->the_post();
				
				/*	BUILD HTML	*/
				/*	@TODO make filterable for theme use	*/
				
			}
			echo $output;
			wp_reset_query();
			return true;
		}
		
		wp_reset_query();
		return false;
	}

}