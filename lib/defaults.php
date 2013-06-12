<?php
//This sets the default fields and serves as an example for users to add their own fields

// Prevents this file being loaded more than once
define( 'RLI_PEOPLE_DEFAULTS_FILE', __FILE__ );
/******************* Default Fields ********************/

/******************** Title Field **********************/

// Add title meta box
add_action( 'rli_people_create_metaboxes', function() { 
	add_meta_box( 'title', __( 'Title' ), 'render_people_title_metabox', 'rli-people', 'normal', 'high' );
} );

/**
 * Render people title meta box.
 * 
 * Calls 'rli_people_title_metabox_render' action.
 */
if ( ! function_exists('render_people_title_metabox') ) {
	function render_people_title_metabox( $post ) {
		add_action( 'rli_people_title_metabox_render' , 
			function( $post ) {
				wp_nonce_field( 'rli-people', 'rli_people_title_nonce' ); ?>
				<p>
					<input class="widefat" type="text" name="title" id="title" value="<?php echo esc_attr( get_post_meta( $post->ID, '_title', true ) ); ?>" size="30" />
				</p>
			<?php
			}
		);
		do_action( 'rli_people_title_metabox_render', $post );
	}
}

// Add save action
add_action('save_post',
	function( $post_id ) {
		rli_save_meta( $post_id, 'rli-people', 'rli_people_title_nonce', 'title' );
	}
);

// Add rli_people_person hook
add_filter( 'rli_people_atts', 
	function( $arr, $id ) {
		$arr['title'] = get_post_meta( $id, '_title', true );
		return $arr;
	},
	2,
	2
);

/******************** Email Field **********************/

// Add Email meta box
add_action( 'rli_people_create_metaboxes', function() {
	add_meta_box( 'email', __( 'Email' ), 'render_people_email_metabox', 'rli-people', 'normal', 'high' );
} );

/**
 * Render people email meta box.
 * 
 * Calls 'rli_people_email_metabox_render' action.
 */
if ( ! function_exists('render_people_email_metabox') ) {
	function render_people_email_metabox( $post ) {
		add_action( 'rli_people_email_metabox_render', 
			function( $post ) {
				wp_nonce_field( 'rli-people', 'rli_people_email_nonce' ); ?>
				<p>
					<input class="widefat" type="text" name="email" id="email" value="<?php echo esc_attr( get_post_meta( $post->ID, '_email', true ) ); ?>" size="30" />
				</p>
			<?php
			}
		);
		do_action( 'rli_people_email_metabox_render', $post );
	}
}

//save_action
add_action('save_post',
	function( $post_id ) {
		rli_save_meta( $post_id, 'rli-people', 'rli_people_email_nonce', 'email' );
	}
);

// Add rli_people_person hook
add_filter( 'rli_people_atts', 
	function( $arr, $id ) {
		$arr['email'] = get_post_meta( $id, '_email', true );
		return $arr;
	},
	2,
	2
);

/****************** Brief Bio Field *********************/

// Add Brief Bio meta box
add_action( 'rli_people_create_metaboxes', function() {
	add_meta_box( 'brief', __( 'Brief Bio' ), 'render_people_brief_metabox', 'rli-people', 'normal', 'high' );
} );

/**
 * Render people full bio meta box.
 * 
 * Calls 'rli_people_full_bio_metabox_render' action.
 */
if ( ! function_exists('render_people_brief_metabox') ) {
	function render_people_brief_metabox( $post ) {
		add_action( 'rli_people_brief_metabox_render' , 
			function( $post ) {
				wp_nonce_field( 'rli-people', 'rli_people_brief_nonce' );
				rli_people_metabox_basic_tinymce( $post, 'brief', '150px' );
			}
		);
		do_action( 'rli_people_brief_metabox_render', $post );
	}
}
// save action
add_action('save_post',
	function( $post_id ) {
		rli_save_meta( $post_id, 'rli-people', 'rli_people_brief_nonce', 'brief' );
	}
);

// Add rli_people_person hook
add_filter( 'rli_people_atts', 
	function( $arr, $id ) {
		$arr['brief_bio'] = get_post_meta( $id, '_brief', true );
		return $arr;
	},
	2,
	2
);

