<?php
//This sets the default fields and serves as an example for users to add their own fields

/******************** Title Field **********************/

/**
 * Render people title meta box.
 * 
 * Calls 'people_title_metabox_render' action.
 */
function render_people_title_field( $post ) { ?>
	<label for="title"><?php echo __( 'Title:', 'people' ); ?></label>
	<p>
		<input class="widefat" type="text" name="title" id="title" value="<?php echo esc_attr( get_post_meta( $post->ID, '_title', true ) ); ?>" size="30" />
	</p>
	<?php
}
add_action( 'people_details_metabox', 'render_people_title_field');

// Add save action
function people_title_save_hook( $post_id ) {
	if ( 'people' == get_post_type( $post_id ) )
		people_save_meta( $post_id, 'people', 'title' );
}
add_action( 'people_save_details', 'people_title_save_hook' );

// Add people_atts hook
function people_title_atts_hook( $arr, $id ) {
	$arr['title'] = get_post_meta( $id, '_title', true );
	return $arr;
}
add_filter( 'people_atts', 'people_title_atts_hook', 2, 2 );

/******************** Email Field **********************/

/**
 * Render people email meta box.
 * 
 */
function render_people_email_field( $post ) { ?>
	<label for="title"><?php echo __( 'Email:', 'people' ); ?></label>
	<p>
		<input class="widefat" type="text" name="email" id="email" value="<?php echo esc_attr( get_post_meta( $post->ID, '_email', true ) ); ?>" size="30" />
	</p>
	<?php
}
add_action( 'people_details_metabox', 'render_people_email_field');

//save_action
function people_email_save_hook( $post_id ) {
	// Verify that the input is an actual email address
	if ( ! ( isset(  $_POST['email'] ) and is_email( $_POST['email'] ) ) )
		return $post_id;
	people_save_meta( $post_id, 'people', 'email' );
}
add_action( 'people_save_details', 'people_email_save_hook' );

// Add people_atts hook
function people_email_atts_hook( $arr, $id ) {
	$arr['email'] = get_post_meta( $id, '_email', true );
	return $arr;
}
add_filter( 'people_atts', 'people_email_atts_hook', 2, 2 );

