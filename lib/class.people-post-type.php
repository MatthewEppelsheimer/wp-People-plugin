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
	public static function setup() {
		self::init_layouts();

		self::register_shortcodes();
		self::register_taxonomy();
		self::register_people();

		add_action( 'admin_init', array( get_called_class(), 'admin_init' ) );

		/**
		 * Action hook for plugins and themes to register layouts after this plugin has initialized
		 */
		do_action( 'rli_people_init' );
	}

	/**
	 * Setup for wp-admin
	 */
	public static function admin_init() {
		add_filter( 'enter_title_here', array( get_called_class(), 'title_name' ) );
	}

	/**
	 * Prepare the $rli_people global to store registered layout templates
	 */
	private static function init_layouts() {
		global $rli_people;

		$rli_people = new stdClass();

		$rli_people->layout_templates = array();
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
			'taxonomies' => array( 'people_departments' ),
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
			
		// 'register_meta_box_cb' => array( get_class(), 'create_people_metaboxes'),
		'menu_icon' => 'dashicons-admin-users' 
		) );
	}

	/**
	 * Registers custom "Department" taxonomy
	 */
	static function register_taxonomy() {

		$labels = array(
			'name'                       => _x( 'Departments', 'Taxonomy General Name', 'rli_testimonial' ),
			'singular_name'              => _x( 'Department', 'Taxonomy Singular Name', 'rli_testimonial' ),
			'menu_name'                  => __( 'Departments', 'rli_testimonial' ),
			'all_items'                  => __( 'All Departments', 'rli_testimonial' ),
			'parent_item'                => __( 'Parent Departments', 'rli_testimonial' ),
			'parent_item_colon'          => __( 'Parent Departments:', 'rli_testimonial' ),
			'new_item_name'              => __( 'New Department Name', 'rli_testimonial' ),
			'add_new_item'               => __( 'Add New Department', 'rli_testimonial' ),
			'edit_item'                  => __( 'Edit Department', 'rli_testimonial' ),
			'update_item'                => __( 'Update Department', 'rli_testimonial' ),
			'view_item'                  => __( 'View Department', 'rli_testimonial' ),
			'separate_items_with_commas' => __( 'Separate departments with commas', 'rli_testimonial' ),
			'add_or_remove_items'        => __( 'Add or remove Departments', 'rli_testimonial' ),
			'choose_from_most_used'      => __( 'Choose from the most used', 'rli_testimonial' ),
			'popular_items'              => __( 'Popular Departments', 'rli_testimonial' ),
			'search_items'               => __( 'Search Departments', 'rli_testimonial' ),
			'not_found'                  => __( 'Not Found', 'rli_testimonial' ),
			'no_terms'                   => __( 'No Departments', 'rli_testimonial' ),
			'items_list'                 => __( 'Department list', 'rli_testimonial' ),
			'items_list_navigation'      => __( 'Department list navigation', 'rli_testimonial' ),
		);

		$args = array(
			'labels'                     => $labels,
			'hierarchical'               => true,
			'public'                     => true,
			'show_ui'                    => true,
			'show_admin_column'          => true,
			'show_in_nav_menus'          => true,
			'show_tagcloud'              => true,
		);

		register_taxonomy( 'people_departments', array( 'people' ), $args );
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

		wp_reset_query();

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
		if ( ! is_int( $id ) ) {
			$id = get_the_ID();
		}
		
		// check if the post type is correct
		if ( 'people' != get_post_type( $id ) ) {
			return false;
		}

		$person = new Person( $id );

		return $person;
	}

	/**
	 * The default function for listing a person in a list of people
	 * Called by list_people and can be overwritten by adding a filter to 'people_item_callback'
	 */
	static function list_item() {
		global $post;
		// get person info
		$person = self::get_person();
		$name = $person->get_name();
		$titles = $person->get_titles();
		$emails = $person->get_emails();
		$short_bio = $person->get_short_bio();
		$thumbnail_size = 'post-thumbnail';

		$output = "<div class='pp-person'>
			<div class='person-photo'>
			<a href='" . get_permalink() . "'>";
		$output .= get_the_post_thumbnail( $post->ID, $thumbnail_size, array( 'class' => "attachment-$thumbnail_size photo" ) ) . "</a> ";
		$output .= '</div>
			<h2><a href="' . get_permalink() . '" ><span class="person-name"> ' . esc_html( $name ) . '</span></a></h2>
			<p class="person-meta">';
			foreach ( $titles as $title ) {
				$output .= "<span class='person-title title'>" . esc_html( $title ) . "</span>";
			}
			$output .= "</p><p class='person-contact'>";
			foreach ( $emails as $email ) {
				$output .= '<a href="mailto:' . esc_attr( $email ) . ' class="email">' . esc_html( $email ) . '</a>';
			}
			$output .= '</p>
			<div class="person-short-bio note">' . $short_bio . '</div>
		</div>';
	return apply_filters( 'people_item_callback', $output );
	}
	
	/**
	 *	Takes query arguments for people and 
	 *	performs the query, manages a custom loop, and echoes html
	 *
	 *	@param $args an array of $args formatted for WP_Query to accept
	 *	
	 *	@return true if we output html with people; false if not
	 */
	public static function list_people( $args = null, $callback = null, $shortcode_atts = null ) {

		if ( empty( $args['orderby'] ) ) {
			$args['orderby'] = 'menu_order';
		}

		$people = self::query_people( $args );
	
		if ( $people->have_posts() ) {
			$out = apply_filters( 'people_list_before_loop', '', $args, $shortcode_atts );
			while ( $people->have_posts() ) {
				$people->the_post();
			
				// BUILD HTML
				// Give Priority to:
				// $callback variable, 
				// then the action hook,
				// then the default method self::list_item()
				if ( $callback ) {
					$out .= $callback( $args, $shortcode_atts );
				}
				elseif ( has_filter('people_item_callback' ) ) {
					$out .= apply_filters( 'people_item_callback', '', get_the_id(), $shortcode_atts );
				}
				else {
					$out .= self::list_item();
				}
			}
			wp_reset_query();
			$out .= apply_filters( 'people_list_after_loop', '', $args, $shortcode_atts );
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
			<p class='person-contact'><a href=\"tel:" . $person['phone-number'] . "\" class='phone-number'>" . $person['phone-number'] . "</a></p>
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
				'department' => '',
				'orderby'  => '',
				'class'    => '',
				'layout'   => ''
			),
			$atts,
			'people'
		);
		
		$query_args = array();
		
		if( $atts['department'] ) {
			$query_args['tax_query'] = array(
				array(
					'taxonomy' => 'people_departments',
					'terms' => $atts['department']
				)
			);
			if ( is_string( $atts['department'] ) ) {
				$query_args['tax_query'][0]['field'] = 'slug';
 			} else {
				$query_args['tax_query'][0]['field'] = 'id';
			}
		}

		$callback = null;

		// Set a callback if there is a valid layout parameter
		if ( ! empty( $atts['layout'] ) ) {
			$layout = self::get_layout_callback( $atts['layout'] );
			if ( $layout ) {
				$callback = $layout;
			}
		}

		if( $atts['orderby'] ) {
			$query_args['orderby'] = $atts['orderby'];
		}
		return self::list_people( apply_filters( 'people_shortcode_query_args', $query_args, $atts ), $callback, $atts );
	}

	/**
	 * Change the 'post title' field to "Enter Name" in the case of People
	 * @param $title
	 *
	 * @return string|void
	 */
	static function title_name( $title ) {
		global $post;
		if ( 'people' == $post->post_type ) {
			return __( 'Enter Name', 'people' );
		}
		return $title;
	}

	/**
	 * Register a People layout template
	 *
	 * This registers a callback function that renders a single person in a list
	 * to a layout name key, storing registered layouts to the private $layout_templates
	 * parameter.
	 *
	 * @uses stdClass $rli_people         Global variable for storing registered layouts
	 *
	 * @param string $template_name       A layout key name
	 * @param string $template_callback   A template rendering callback function name
	 *
	 * @return bool|WP_Error
	 */
	public static function register_layout_template( $template_name, $template_callback ) {
		global $rli_people;

		if ( empty( $template_name ) OR ! is_string( $template_name ) ) {
			return new WP_Error( 'Unable to register People layout template', "Unable to register a People layout without a valid template_name parameter.");
		}

		if ( empty( $template_callback ) OR ! is_string( $template_callback ) OR ! function_exists( $template_callback ) ) {
			return new WP_Error( 'Unable to register People layout template', "Unable to register a People layout without a valid template_callback parameter.");
		}

		if ( isset( $rli_people->layout_templates[$template_name] ) ) {
			return new WP_Error( 'Unable to register People layout template', "Unable to register People layout template $template_name, because a template with that name already exists." );
		}

		$rli_people->layout_templates[$template_name] = $template_callback;

		return true;
	}

	/**
	 * Retrieve callback function for a registered layout
	 *
	 * @param string $layout    A registered layout key
	 *
	 * @return string|bool      A layout callback function name, or false if none
	 *                          match the given $layout key
	 */
	private static function get_layout_callback( $layout ) {
		global $rli_people;

		if ( isset( $rli_people->layout_templates[$layout] ) ) {
			$callback = $rli_people->layout_templates[$layout];
			return $callback;
		}

		return false;
	}
}
