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

	function setup() {
		// Wire up actions/filters

		// Change 'enter title here' label in Person editor screen
		add_filter( 'enter_title_here', 
			function() {
				global $post;
				if ( 'rli-people' == $post->post_type )
			  	return __( 'Enter Name' );
			} 
		);
		
		self::register_shortcodes();
		self::register_people();
	}
	
	/**
	 * Register custom post type rli-people
	 *
	 * @author Matthew Eppelsheimer
	 * @since 0.2
	 */

	static function register_people() {
		register_post_type( 'rli-people' , array( 
			'public' => true,
			'supports' =>  array(
				'title',
				'thumbnail',
				'editor',
				'page-attributes',
			),
			'taxonomies' => array( 'post_tag', 'post_category' ),
			'query_var' => 'rli-people',
			'rewrite' =>  array(
				'slug' => 'people'
			),
			'labels' => array(
				'name' => __( 'People', 'rli_people' ),
				'all_items' => __( 'All People', 'rli_people' ),
				'singular_name' => __( 'People', 'rli_people' ),
				'add_new' => __( 'Add a New Person', 'rli_people' ),
				'add_new_item' => __( 'Add a New Person', 'rli_people' ),
				'edit_item' => __( 'Edit Person', 'rli_people' ),
				'new_item' => __( 'Add New Person', 'rli_people' ),
				'view_item' => __( 'View Person', 'rli_people' ),
				'search_items' => __( 'Search People', 'rli_people' ),
				'not_found' => __( 'No People Found Matching Search', 'rli_people' ),
				'not_found_in_trash' => __( 'No People Found in Trash', 'rli_people' ),
				'parent_item_colon' => __( 'Parent Person:', 'rli_people' )
			),
			
		'register_meta_box_cb' => array( get_class(), 'create_people_metaboxes')
		) );
	}

	/**
	 * Register meta box for the rli-people post editor screen.
	 *
	 * @uses render_people_detail_metabox()
	 */
	public static function create_people_metaboxes() {
		// enables users to add more meta boxes
		do_action( 'rli_people_create_metaboxes');
	}
	
	/**
	 * Utility function to return a WP_Query object with posts of type RLI People
	 *
	 * @author Matthew Eppelsheimer
	 * @since 0.2
	 */
	public static function query_people( $args ) {
		$defaults = array(
			'posts_per_page' => - 1,
			'order'          => 'ASC',
			'orderby'        => 'menu_order'
		);

		$query_args = wp_parse_args( $args, $defaults );
		$query_args['post_type'] = 'rli-people';

		$results = new WP_Query( $query_args );

		return $results;
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
	static function get_person( $person = false ) {
		// Convert Post object to post id 
		if ( is_object( $person ) )
			$person = $person->ID;
	
		// check if the post type is correct
		if ( 'rli-people' != get_post_type( $person ) )
			return false;
	
		// if  id is not given, set it to the_post id
		if ( false === $person )
			$person = get_the_ID();
	
		$fields = array(
			'name' => get_the_title( $person ),
			'full_bio' => get_the_content( $person ),
		);
	
		// Users add to this filter to append their own fields to the array
		$fields = apply_filters( 'rli_people_atts', $fields, $person );	
		
		foreach( $fields as $key => $field ) {
			$out[$key] = wp_kses( $field, wp_kses_allowed_html( 'post' ) );
		}
		return $out;
	}

	static function list_item( $post ) {
		// get person info
		$person = self::get_person( $post );
		
		$size = 'post-thumbnail';
		$bio_text = sprintf( __( 'View %s\'s full bio', 'rli_people' ), $person['name'] ) ;
		
		$output = "<div class='vcard'>
			<div class='person-photo'>
			<a href=\"" . get_permalink() . "\" alt=\"$bio_text\">";
				 
		$output .= get_the_post_thumbnail( $post->ID, $size, array( 'class' => "attachment-$size photo" ) ) . "</a> ";
		$output .= '</div>
			<h2><a href="' . get_permalink() . "\" alt=$bio_text><span class='fn'> " . $person['name'] . "</span></a></h2>
			<p class='person-meta'><span class='person-title title'>" . $person['title'] . "</span></p>
			<p class='person-contact'><a href=\"mailto:" . $person['email'] . "\" class='email'>" . $person['email'] . '</a></p>
			<div class="person-short-bio note">' . $person['brief_bio'] . '</div>
		</div>';
	return $output;
	}
	
	/*
	 *	rli_people_list() takes query arguments for rli-people and 
	 *	performs the query, manages a custom loop, and echoes html
	 *
	 *	@param $args an array of $args formatted for WP_Query to accept
	 *	
	 *	@return true if we output html with people; false if not
	 */
	public static function list_people( $args = null, $callback = null ) {
		global $post;
		
		$people = self::query_people( $args );
	
		if ( $people->have_posts() ) {
			$out = '';
			while ( $people->have_posts() ) {
				$people->the_post();
			
				// BUILD HTML
				// Give Priority to:
				// $callback variable, 
				// then the action hook,
				// then the default function
				if ( $callback )
					$out .= $callback( $post );
				elseif ( has_filter('rli_people_item_callback' ) )
					$out .= apply_filters( 'rli_people_item_callback', '', $post );
				else
					$out .= self::list_item( $post );
			}
			wp_reset_query();
			return $out;
		}
	
		wp_reset_query();
		return false;
	}

	/*
	 *	Shortcode Setup
	 */

	/**
	 *	Register shortcode 
	 */

	static function register_shortcodes() {
		add_shortcode( 'people', array( get_class(), 'people_shortcode' ) );
	}

	/*
	 *	Creates a shortcode to display a list of people on demand
	 *
	 *	Supports the 'category' keyword.
	 */

	static function people_shortcode( $atts ) {
		$atts = shortcode_atts( 
			array( 
				'category' => ''
			),
			$atts
		);
		
		$query_args = array();
		
		if( $atts['category'] != '' )
			$query_args['category'] = $atts['category'];
		return self::list_people( $query_args );
	}
}

