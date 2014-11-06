<?php
/**
 * Core functionality for the Rocket Lift People plugin.
 *
 * All functions are static members of this class to allow for easy namespacing, 
 * so this class should not be instantiated.
 *
 * @module People_Post_Type
 * @author Matthew Eppelsheimer
 */

class People_Post_Type {

	static function setup() {
		// Wire up actions/filters

		// Change 'enter title here' label in Person editor screen
		add_filter( 'enter_title_here', 'people_title_name' );
		
		function people_title_name( $title ) {
			global $post;
			if ( 'people' == $post->post_type ) {
		  		return __( 'Enter Name', 'people' );
		  	}
		  	return $title;
		}
		
		self::register_shortcodes();
		self::register_people();
	}
	
	/**
	 * Register custom post type people
	 *
	 * @author Matthew Eppelsheimer
	 * @since 0.2
	 */

	static function register_people() {
		register_post_type( 'people' , array( 
			'public' => true,
			'supports' =>  array(
				'title',
				'thumbnail',
				'editor',
				'excerpt',
				'page-attributes',
			),
			'taxonomies' => array( 'post_tag', 'post_category' ),
			'rewrite' =>  array(
				'slug' => 'people'
			),
			'labels' => array(
				'name' => __( 'People', 'people' ),
				'all_items' => __( 'All People', 'people' ),
				'singular_name' => __( 'People', 'people' ),
				'add_new' => __( 'Add a New Person', 'people' ),
				'add_new_item' => __( 'Add a New Person', 'people' ),
				'edit_item' => __( 'Edit Person', 'people' ),
				'new_item' => __( 'Add New Person', 'people' ),
				'view_item' => __( 'View Person', 'people' ),
				'search_items' => __( 'Search People', 'people' ),
				'not_found' => __( 'No People Found Matching Search', 'people' ),
				'not_found_in_trash' => __( 'No People Found in Trash', 'people' ),
				'parent_item_colon' => __( 'Parent Person:', 'people' )
			),
			
		'register_meta_box_cb' => array( get_class(), 'create_people_metaboxes'),
		'menu_icon' => 'dashicons-admin-users' 
		) );
	}

	/**
	 * Register meta box for the people post editor screen.
	 *
	 * @uses render_people_detail_metabox()
	 */
	public static function create_people_metaboxes() {
		// enables users to add more meta boxes
		do_action( 'people_create_metaboxes');
	}
	
	/**
	 * Utility function to return a WP_Query object with posts of type People
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
		$query_args['post_type'] = 'people';

		$results = new WP_Query( $query_args );

		return $results;
	}

	/**
	 * Returns an array containing all the meta values attached to filter 'people_atts'
	 *
	 * Uses filter 'people_atts' to allow users to add their own meta fields
	 *
	 * @return An associative array containing all the meta values for the current $post in the loop
	 * @return false if current $post is not of post type people
	 */
	static function get_person( $id = null ) {
	
		// if id is not given, set it to the_post id
		$person = $id;
		if ( ! is_int( $id ) ) {
			$person = get_the_ID();
		}
		
		// check if the post type is correct
		if ( 'people' != get_post_type( $person ) ) {
			return false;
		}
	
		$fields = array(
			'name' => get_the_title( $person ),
			// get_the_content() does not apply the filters the_content() does,
			// so manually add the filters
			'full_bio' => apply_filters( 'the_content', get_the_content() ),
			'brief_bio' => apply_filters( 'the_excerpt', get_the_excerpt() ),
		);
	
		// Users add to this filter to append their own fields to the array
		$fields = apply_filters( 'people_atts', $fields, $person );
		
		foreach( $fields as $key => $field ) {
			if ( 'full_bio' !== $key and 'brief_bio' !== $key ) {
				// if it is the full bio we don't want to escape the autop tags
				$field = esc_attr( $field );
			}
			$field = apply_filters( 'person_' . $key, $field, $person );
			$out[$key] = wp_kses( $field, wp_kses_allowed_html( 'post' ) );
		}
		return $out;
	}

	/**
	 * The default function for listing a person in a list of people
	 * Called by list_people and can be overwritten by adding a filter to 'people_item_callback'
	 */
	static function list_item() {
		global $post;
		// get person info
		$person = self::get_person();
		
		$bio_text = sprintf( __( 'View %s\'s full bio', 'people' ), $person['name'] ) ;
		
		$output = "<div class='pp-person'>
			<div class='person-photo'>
			<a href=\"" . get_permalink() . "\" alt=\"$bio_text\">";
				
		$size = 'post-thumbnail';
		$output .= get_the_post_thumbnail( $post->ID, $size, array( 'class' => "attachment-$size photo" ) ) . "</a> ";
		$output .= '</div><!-- .person-photo -->
			<h2><a href="' . get_permalink() . "\" alt=$bio_text><span class='person-name'> " . $person['name'] . "</span></a></h2>
			<p class='person-meta'><span class='person-title title'>" . $person['title'] . "</span></p>
			<p class='person-contact'><a href=\"mailto:" . $person['email'] . "\" class='email'>" . $person['email'] . '</a></p>
			<div class="person-short-bio note">' . $person['brief_bio'] . '</div>
		</div><!-- .pp-person -->';
	return $output;
	}
	
	/**
	 *	Takes query arguments for people and 
	 *	performs the query, manages a custom loop, and echoes html
	 *
	 *	@param $args an array of $args formatted for WP_Query to accept
	 *	
	 *	@return true if we output html with people; false if not
	 */
	public static function list_people( $args = null, $callback = null ) {
		global $post;
		
		if ( empty( $args['orderby'] ) ) {
			$args['orderby'] = 'menu_order';
		}
		
		$people = self::query_people( $args );
	
		if ( $people->have_posts() ) {
			$out = '';
			while ( $people->have_posts() ) {
				$people->the_post();
			
				// BUILD HTML
				// Give Priority to:
				// $callback variable, 
				// then the action hook,
				// then the default method self::list_item()
				if ( $callback ) {
					$out .= $callback( $post );
				}
				elseif ( has_filter('people_item_callback' ) ) {
					$out .= apply_filters( 'people_item_callback', '' );
				}
				else {
					$out .= self::list_item();
				}
			}
			wp_reset_query();
			return $out;
		}
	
		wp_reset_query();
		return false;
	}

	/**
	 *
	 * Returns the html code for a single person. Will use a default format unless something has been added to the action: 'people_single_callback'
	 *
	 * NOTE: Must be called inside the loop.
	 */
	public static function render_single_person() {
		global $post;
		
		$person = self::get_person();
		
		// If something has been hooked to this action, use that instead of the default below
		if ( has_filter( 'people_single_callback' ) ){
			return apply_filters( 'people_single_callback', '', $person );
		}
		
		$out = "<div class='pp-person'>
			<div class='person-photo'>";
				
		$size = 'post-thumbnail';
		$out .= get_the_post_thumbnail( $post->ID, $size, array( 'class' => "attachment-$size photo" ) );
		$out .= "</div><!-- .person-photo -->
			<h2><span class='person-name'>" . $person['name'] . "</span></h2>
			<p class='person-meta'><span class='person-title title'>" . $person['title'] . "</span></p>
			<p class='person-contact'><a href=\"mailto:" . $person['email'] . "\" class='email'>" . $person['email'] . "</a></p>
		</div>
		<div class='person-long-bio'>" . $person['full_bio'] . "</div><!-- .pp-person -->";
		return $out;
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
				'category' => '',
				'orderby'  => ''
			),
			$atts
		);
		
		$query_args = array();
		
		if( $atts['category'] ) {
			$query_args['category'] = $atts['category'];
		}
		if( $atts['orderby'] ) {
			$query_args['orderby'] = $atts['orderby'];
		}
		return self::list_people( $query_args );
	}
}

