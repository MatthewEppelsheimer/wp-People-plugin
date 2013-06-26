<?php
//This sets the default fields and serves as an example for users to add their own fields

/******************* Default Fields ********************/

/******************** Title Field **********************/

// Add title meta box
add_action( 'people_create_metaboxes', function() { 
	add_meta_box( 'title', __( 'Title', 'people' ), 'render_people_title_metabox', 'people', 'normal', 'high' );
} );

/**
 * Render people title meta box.
 * 
 * Creates a lambda function, for rendering the metabox, and adds it to the 'people_title_metabox_render' action.
 * This action is useful for adding additional fields to this metabox
 * Note: If you add a field to this metabox, remember to also add a filter to 'save_post' and 'people_atts'.
 *
 * Calls 'people_title_metabox_render' action.
 */
if ( ! function_exists('render_people_title_metabox') ) {
	function render_people_title_metabox( $post ) {
		add_action( 'people_title_metabox_render' , 
			function( $post ) {
				wp_nonce_field( 'people', 'people_title_nonce' ); ?>
				<p>
					<input class="widefat" type="text" name="title" id="title" value="<?php echo esc_attr( get_post_meta( $post->ID, '_title', true ) ); ?>" size="30" />
				</p>
			<?php
			}
		);
		do_action( 'people_title_metabox_render', $post );
	}
}

// Add save action
add_action('save_post',
	function( $post_id ) {
		if ( 'people' == get_post_type( $post_id ) )
			people_save_meta( $post_id, 'people', 'people_title_nonce', 'title' );
	}
);

// Add people_atts hook
add_filter( 'people_atts', 
	function( $arr, $id ) {
		$arr['title'] = get_post_meta( $id, '_title', true );
		return $arr;
	},
	2,
	2
);

/******************** Email Field **********************/

// Add Email meta box
add_action( 'people_create_metaboxes', function() {
	add_meta_box( 'email', __( 'Email', 'people' ), 'render_people_email_metabox', 'people', 'normal', 'high' );
} );

/**
 * Render people email meta box.
 * 
 * Creates a lambda function, for rendering the metabox, and adds it to the 'people_email_metabox_render' action.
 * This action is useful for adding additional fields to this metabox
 * Note: If you add a field to this metabox, remember to also add a filter to the save_post.
 *
 * Calls 'people_email_metabox_render' action.
 */
if ( ! function_exists('render_people_email_metabox') ) {
	function render_people_email_metabox( $post ) {
		add_action( 'people_email_metabox_render', 
			function( $post ) {
				wp_nonce_field( 'people', 'people_email_nonce' ); ?>
				<p>
					<input class="widefat" type="text" name="email" id="email" value="<?php echo esc_attr( get_post_meta( $post->ID, '_email', true ) ); ?>" size="30" />
				</p>
			<?php
			}
		);
		do_action( 'people_email_metabox_render', $post );
	}
}

//save_action
add_action('save_post',
	function( $post_id ) {
		// Validate if sting is and email or not set
		if ( 'people' != get_post_type( $post_id ) )
			return $post_id;
		if ( ! is_email( $_POST['email'] ) and $_POST['email'] )
			return $post_id;
		people_save_meta( $post_id, 'people', 'people_email_nonce', 'email' );
	}
);

// Add people_atts hook
add_filter( 'people_atts', 
	function( $arr, $id ) {
		$arr['email'] = get_post_meta( $id, '_email', true );
		return $arr;
	},
	2,
	2
);

