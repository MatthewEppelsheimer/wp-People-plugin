<?php
/**
 * Include and setup custom metaboxes and fields.
 *
 * @category RLI People
 * @license  http://www.opensource.org/licenses/gpl-license.php GPL v2.0 (or later)
 * @link     https://github.com/WebDevStudios/CMB2
 */

/**
 * Get the bootstrap!
 */

if ( file_exists( dirname( __FILE__ ) . '/cmb/init.php' ) ) {
	require_once dirname( __FILE__ ) . '/cmb/init.php';
} elseif ( file_exists( dirname( __FILE__ ) . '/CMB/init.php' ) ) {
	require_once dirname( __FILE__ ) . '/CMB/init.php';
}

/**
 * Add People Details metabox.
 */
function rli_people_register_demo_metabox() {

	// Start with an underscore to hide fields from custom fields list
	$prefix = '_rli_people_demo_';

	/**
	 * Sample metabox to demonstrate each field type included
	 */
	$cmb_demo = new_cmb2_box( array(
		'id'            => $prefix . 'metabox',
		'title'         => __( 'Test Metabox', 'rli_people' ),
		'object_types'  => array( 'page', ), // Post type
		// 'show_on_cb' => 'rli_people_show_if_front_page', // function should return a bool value
		// 'context'    => 'normal',
		// 'priority'   => 'high',
		// 'show_names' => true, // Show field names on the left
		// 'cmb_styles' => false, // false to disable the CMB stylesheet
		// 'closed'     => true, // true to keep the metabox closed by default
	) );

	$cmb_demo->add_field( array(
		'name'       => __( 'Test Text', 'rli_people' ),
		'desc'       => __( 'field description (optional)', 'rli_people' ),
		'id'         => $prefix . 'text',
		'type'       => 'text',
		'show_on_cb' => 'rli_people_hide_if_no_cats', // function should return a bool value
		// 'sanitization_cb' => 'my_custom_sanitization', // custom sanitization callback parameter
		// 'escape_cb'       => 'my_custom_escaping',  // custom escaping callback parameter
		// 'on_front'        => false, // Optionally designate a field to wp-admin only
		// 'repeatable'      => true,
	) );

	$cmb_demo->add_field( array(
		'name' => __( 'Test Text Small', 'rli_people' ),
		'desc' => __( 'field description (optional)', 'rli_people' ),
		'id'   => $prefix . 'textsmall',
		'type' => 'text_small',
		// 'repeatable' => true,
	) );

	$cmb_demo->add_field( array(
		'name' => __( 'Test Text Medium', 'rli_people' ),
		'desc' => __( 'field description (optional)', 'rli_people' ),
		'id'   => $prefix . 'textmedium',
		'type' => 'text_medium',
		// 'repeatable' => true,
	) );

	$cmb_demo->add_field( array(
		'name' => __( 'Website URL', 'rli_people' ),
		'desc' => __( 'field description (optional)', 'rli_people' ),
		'id'   => $prefix . 'url',
		'type' => 'text_url',
		// 'protocols' => array('http', 'https', 'ftp', 'ftps', 'mailto', 'news', 'irc', 'gopher', 'nntp', 'feed', 'telnet'), // Array of allowed protocols
		// 'repeatable' => true,
	) );

	$cmb_demo->add_field( array(
		'name' => __( 'Test Text Email', 'rli_people' ),
		'desc' => __( 'field description (optional)', 'rli_people' ),
		'id'   => $prefix . 'email',
		'type' => 'text_email',
		// 'repeatable' => true,
	) );

	$cmb_demo->add_field( array(
		'name' => __( 'Test Time', 'rli_people' ),
		'desc' => __( 'field description (optional)', 'rli_people' ),
		'id'   => $prefix . 'time',
		'type' => 'text_time',
	) );

	$cmb_demo->add_field( array(
		'name' => __( 'Time zone', 'rli_people' ),
		'desc' => __( 'Time zone', 'rli_people' ),
		'id'   => $prefix . 'timezone',
		'type' => 'select_timezone',
	) );

	$cmb_demo->add_field( array(
		'name' => __( 'Test Date Picker', 'rli_people' ),
		'desc' => __( 'field description (optional)', 'rli_people' ),
		'id'   => $prefix . 'textdate',
		'type' => 'text_date',
	) );

	$cmb_demo->add_field( array(
		'name' => __( 'Test Date Picker (UNIX timestamp)', 'rli_people' ),
		'desc' => __( 'field description (optional)', 'rli_people' ),
		'id'   => $prefix . 'textdate_timestamp',
		'type' => 'text_date_timestamp',
		// 'timezone_meta_key' => $prefix . 'timezone', // Optionally make this field honor the timezone selected in the select_timezone specified above
	) );

	$cmb_demo->add_field( array(
		'name' => __( 'Test Date/Time Picker Combo (UNIX timestamp)', 'rli_people' ),
		'desc' => __( 'field description (optional)', 'rli_people' ),
		'id'   => $prefix . 'datetime_timestamp',
		'type' => 'text_datetime_timestamp',
	) );

	// This text_datetime_timestamp_timezone field type
	// is only compatible with PHP versions 5.3 or above.
	// Feel free to uncomment and use if your server meets the requirement
	// $cmb_demo->add_field( array(
	// 	'name' => __( 'Test Date/Time Picker/Time zone Combo (serialized DateTime object)', 'rli_people' ),
	// 	'desc' => __( 'field description (optional)', 'rli_people' ),
	// 	'id'   => $prefix . 'datetime_timestamp_timezone',
	// 	'type' => 'text_datetime_timestamp_timezone',
	// ) );

	$cmb_demo->add_field( array(
		'name' => __( 'Test Money', 'rli_people' ),
		'desc' => __( 'field description (optional)', 'rli_people' ),
		'id'   => $prefix . 'textmoney',
		'type' => 'text_money',
		// 'before_field' => '£', // override '$' symbol if needed
		// 'repeatable' => true,
	) );

	$cmb_demo->add_field( array(
		'name'    => __( 'Test Color Picker', 'rli_people' ),
		'desc'    => __( 'field description (optional)', 'rli_people' ),
		'id'      => $prefix . 'colorpicker',
		'type'    => 'colorpicker',
		'default' => '#ffffff',
	) );

	$cmb_demo->add_field( array(
		'name' => __( 'Test Text Area', 'rli_people' ),
		'desc' => __( 'field description (optional)', 'rli_people' ),
		'id'   => $prefix . 'textarea',
		'type' => 'textarea',
	) );

	$cmb_demo->add_field( array(
		'name' => __( 'Test Text Area Small', 'rli_people' ),
		'desc' => __( 'field description (optional)', 'rli_people' ),
		'id'   => $prefix . 'textareasmall',
		'type' => 'textarea_small',
	) );

	$cmb_demo->add_field( array(
		'name' => __( 'Test Text Area for Code', 'rli_people' ),
		'desc' => __( 'field description (optional)', 'rli_people' ),
		'id'   => $prefix . 'textarea_code',
		'type' => 'textarea_code',
	) );

	$cmb_demo->add_field( array(
		'name' => __( 'Test Title Weeeee', 'rli_people' ),
		'desc' => __( 'This is a title description', 'rli_people' ),
		'id'   => $prefix . 'title',
		'type' => 'title',
	) );

	$cmb_demo->add_field( array(
		'name'             => __( 'Test Select', 'rli_people' ),
		'desc'             => __( 'field description (optional)', 'rli_people' ),
		'id'               => $prefix . 'select',
		'type'             => 'select',
		'show_option_none' => true,
		'options'          => array(
			'standard' => __( 'Option One', 'rli_people' ),
			'custom'   => __( 'Option Two', 'rli_people' ),
			'none'     => __( 'Option Three', 'rli_people' ),
		),
	) );

	$cmb_demo->add_field( array(
		'name'             => __( 'Test Radio inline', 'rli_people' ),
		'desc'             => __( 'field description (optional)', 'rli_people' ),
		'id'               => $prefix . 'radio_inline',
		'type'             => 'radio_inline',
		'show_option_none' => 'No Selection',
		'options'          => array(
			'standard' => __( 'Option One', 'rli_people' ),
			'custom'   => __( 'Option Two', 'rli_people' ),
			'none'     => __( 'Option Three', 'rli_people' ),
		),
	) );

	$cmb_demo->add_field( array(
		'name'    => __( 'Test Radio', 'rli_people' ),
		'desc'    => __( 'field description (optional)', 'rli_people' ),
		'id'      => $prefix . 'radio',
		'type'    => 'radio',
		'options' => array(
			'option1' => __( 'Option One', 'rli_people' ),
			'option2' => __( 'Option Two', 'rli_people' ),
			'option3' => __( 'Option Three', 'rli_people' ),
		),
	) );

	$cmb_demo->add_field( array(
		'name'     => __( 'Test Taxonomy Radio', 'rli_people' ),
		'desc'     => __( 'field description (optional)', 'rli_people' ),
		'id'       => $prefix . 'text_taxonomy_radio',
		'type'     => 'taxonomy_radio',
		'taxonomy' => 'category', // Taxonomy Slug
		// 'inline'  => true, // Toggles display to inline
	) );

	$cmb_demo->add_field( array(
		'name'     => __( 'Test Taxonomy Select', 'rli_people' ),
		'desc'     => __( 'field description (optional)', 'rli_people' ),
		'id'       => $prefix . 'taxonomy_select',
		'type'     => 'taxonomy_select',
		'taxonomy' => 'category', // Taxonomy Slug
	) );

	$cmb_demo->add_field( array(
		'name'     => __( 'Test Taxonomy Multi Checkbox', 'rli_people' ),
		'desc'     => __( 'field description (optional)', 'rli_people' ),
		'id'       => $prefix . 'multitaxonomy',
		'type'     => 'taxonomy_multicheck',
		'taxonomy' => 'post_tag', // Taxonomy Slug
		// 'inline'  => true, // Toggles display to inline
	) );

	$cmb_demo->add_field( array(
		'name' => __( 'Test Checkbox', 'rli_people' ),
		'desc' => __( 'field description (optional)', 'rli_people' ),
		'id'   => $prefix . 'checkbox',
		'type' => 'checkbox',
	) );

	$cmb_demo->add_field( array(
		'name'    => __( 'Test Multi Checkbox', 'rli_people' ),
		'desc'    => __( 'field description (optional)', 'rli_people' ),
		'id'      => $prefix . 'multicheckbox',
		'type'    => 'multicheck',
		// 'multiple' => true, // Store values in individual rows
		'options' => array(
			'check1' => __( 'Check One', 'rli_people' ),
			'check2' => __( 'Check Two', 'rli_people' ),
			'check3' => __( 'Check Three', 'rli_people' ),
		),
		// 'inline'  => true, // Toggles display to inline
	) );

	$cmb_demo->add_field( array(
		'name'    => __( 'Test wysiwyg', 'rli_people' ),
		'desc'    => __( 'field description (optional)', 'rli_people' ),
		'id'      => $prefix . 'wysiwyg',
		'type'    => 'wysiwyg',
		'options' => array( 'textarea_rows' => 5, ),
	) );

	$cmb_demo->add_field( array(
		'name' => __( 'Test Image', 'rli_people' ),
		'desc' => __( 'Upload an image or enter a URL.', 'rli_people' ),
		'id'   => $prefix . 'image',
		'type' => 'file',
	) );

	$cmb_demo->add_field( array(
		'name'         => __( 'Multiple Files', 'rli_people' ),
		'desc'         => __( 'Upload or add multiple images/attachments.', 'rli_people' ),
		'id'           => $prefix . 'file_list',
		'type'         => 'file_list',
		'preview_size' => array( 100, 100 ), // Default: array( 50, 50 )
	) );

	$cmb_demo->add_field( array(
		'name' => __( 'oEmbed', 'rli_people' ),
		'desc' => __( 'Enter a youtube, twitter, or instagram URL. Supports services listed at <a href="http://codex.wordpress.org/Embeds">http://codex.wordpress.org/Embeds</a>.', 'rli_people' ),
		'id'   => $prefix . 'embed',
		'type' => 'oembed',
	) );

	$cmb_demo->add_field( array(
		'name'         => 'Testing Field Parameters',
		'id'           => $prefix . 'parameters',
		'type'         => 'text',
		'before_row'   => 'rli_people_before_row_if_2', // callback
		'before'       => '<p>Testing <b>"before"</b> parameter</p>',
		'before_field' => '<p>Testing <b>"before_field"</b> parameter</p>',
		'after_field'  => '<p>Testing <b>"after_field"</b> parameter</p>',
		'after'        => '<p>Testing <b>"after"</b> parameter</p>',
		'after_row'    => '<p>Testing <b>"after_row"</b> parameter</p>',
	) );

}
// add_action( 'cmb2_init', 'rli_people_register_demo_metabox' );

/**
 * Hook in and add a metabox to demonstrate repeatable grouped fields
 */
function rli_people_register_details_metabox() {

	// Start with an underscore to hide fields from custom fields list
	$prefix = '_rli_people_group_';

	/**
	 * Repeatable Field Groups
	 */
	$cmb_group = new_cmb2_box( array(
		'id'           => $prefix . 'metabox',
		'title'        => __( 'Details for the Person', 'rli_people' ),
		'object_types' => array( 'people' ),
	) );

	/**
	 * Titles (repeating)
	 */

	$title_group_field_id = $cmb_group->add_field( array(
		'id'          => $prefix . 'title',
		'type'        => 'group',
		'description' => __( 'Position Titles', 'rli_people' ),
		'options'     => array(
			'group_title'   => __( 'Title {#}', 'rli_people' ), // {#} gets replaced by row number
			'add_button'    => __( 'Add another Title', 'rli_people' ),
			'remove_button' => __( 'Remove this Title', 'rli_people' ),
			'sortable'      => true, // beta
		)
	) );

	// Group fields.
	$cmb_group->add_group_field( $title_group_field_id, array(
		'name'       => __( 'Title', 'rli_people' ),
		'id'         => 'title',
		'type'       => 'text',
	) );

	$cmb_group->add_group_field( $title_group_field_id, array(
		'name'        => __( 'Notes', 'rli_people' ),
		'description' => __( 'These are for editorial purposes only and do not display on the site.', 'rli_people' ),
		'id'          => 'notes',
		'type'        => 'textarea_small',
	) );
	/**
	 * Email addresses (repeating)
	 */

	$email_group_field_id = $cmb_group->add_field( array(
		'id'          => $prefix . 'email',
		'type'        => 'group',
		'description' => __( 'Email addresses', 'rli_people' ),
		'options'     => array(
			'group_title'   => __( 'Email {#}', 'rli_people' ), // {#} gets replaced by row number
			'add_button'    => __( 'Add another Email', 'rli_people' ),
			'remove_button' => __( 'Remove this address', 'rli_people' ),
			'sortable'      => true, // beta
		)
	) );

	// Group fields.
	// The parent field's id needs to be passed as the first argument.
	$cmb_group->add_group_field( $email_group_field_id, array(
		'name'       => __( 'Email Address', 'rli_people' ),
		'id'         => 'email',
		'type'       => 'text_email',
	) );

	$cmb_group->add_group_field( $email_group_field_id, array(
		'name'        => __( 'Notes', 'rli_people' ),
		'description' => __( 'These are for editorial purposes only and do not display on the site.', 'rli_people' ),
		'id'          => 'notes',
		'type'        => 'textarea_small',
	) );

	/**
	 * Phone numbers (repeating)
	 */

	$phone_group_field_id = $cmb_group->add_field( array(
		'id'          => $prefix . 'phone',
		'type'        => 'group',
		'description' => __( 'Phone numbers', 'rli_people' ),
		'options'     => array(
			'group_title'   => __( 'Phone number {#}', 'rli_people' ), // {#} gets replaced by row number
			'add_button'    => __( 'Add another Number', 'rli_people' ),
			'remove_button' => __( 'Remove this Number', 'rli_people' ),
			'sortable'      => true, // beta
		)
	) );

	// Group fields.
	// The parent field's id needs to be passed as the first argument.
	$cmb_group->add_group_field( $phone_group_field_id, array(
		'name'       => __( 'Phone Number', 'rli_people' ),
		'id'         => 'phone',
		'type'       => 'text',
	) );

	$cmb_group->add_group_field( $phone_group_field_id, array(
		'name'       => __( 'Extension', 'rli_people' ),
		'id'         => 'extension',
		'type'       => 'text_small',
	) );

	$cmb_group->add_group_field( $phone_group_field_id, array(
		'name'        => __( 'Notes', 'rli_people' ),
		'description' => __( 'These are for editorial purposes only and do not display on the site.', 'rli_people' ),
		'id'          => 'notes',
		'type'        => 'textarea_small',
	) );

	/**
	 * Social media profiles (repeating)
	 */

	$socail_group_field_id = $cmb_group->add_field( array(
		'id'          => $prefix . 'social',
		'type'        => 'group',
		'description' => __( 'Social media profiles', 'rli_people' ),
		'options'     => array(
			'group_title'   => __( 'Social media profile {#}', 'rli_people' ), // {#} gets replaced by row number
			'add_button'    => __( 'Add another profile', 'rli_people' ),
			'remove_button' => __( 'Remove this profile', 'rli_people' ),
			'sortable'      => true, // beta
		)
	) );

	// Group fields.
	// The parent field's id needs to be passed as the first argument.
	$cmb_group->add_group_field( $socail_group_field_id, array(
		'name'       => __( 'Profile URL', 'rli_people' ),
		'id'         => 'profile_url',
		'type'       => 'text_url',
	) );

	$cmb_group->add_group_field( $socail_group_field_id, array(
		'name'        => __( 'Notes', 'rli_people' ),
		'description' => __( 'These are for editorial purposes only and do not display on the site.', 'rli_people' ),
		'id'          => 'notes',
		'type'        => 'textarea_small',
	) );

}
add_action( 'cmb2_init', 'rli_people_register_details_metabox' );
