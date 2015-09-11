<?php
/**
 * Get an HTML img element representing an image attachment
 * Based on wp_get_attachment_image(), without height and width.
 *
 * While $size will accept an array, it is better to register a size with
 * add_image_size() so that a cropped version is generated. It's much more
 * efficient than having to find the closest-sized image and then having the
 * browser scale down the image.
 *
 * @since 2.0
 * @see add_image_size()
 * @uses apply_filters() Calls 'wp_get_attachment_image_attributes' hook on attributes array
 * @uses wp_get_attachment_image_src() Gets attachment file URL and dimensions
 * @since 2.5.0
 * @package Rocket Lift Parent Theme
 *
 * @param int $attachment_id Optional, defaults to null, which will use current post image ID. Image attachment ID.
 * @param string $size Optional, default is 'thumbnail'.
 * @param bool $icon Optional, default is false. Whether it is an icon.
 * @param array $attr Optional, default is empty. Attributes to pass (e.g. src, class, alt, title).
 * @return string HTML img element or empty string on failure.
 */
function get_post_thumbnail_without_dimensions($attachment_id = '', $size = 'thumbnail', $icon = false, $attr = '') {

	global $post;

	if ( '' == $attachment_id ) {
		if ( has_post_thumbnail( $post->ID ) ) {
			$attachment_id = get_post_thumbnail_id( $post->ID );
		} else {
			return false;
		}
	}

	$html = '';
	$image = wp_get_attachment_image_src($attachment_id, $size, $icon);
	if ( $image ) {
		list($src, $width, $height) = $image;
		if ( is_array($size) )
			$size = join('x', $size);
		$attachment =& get_post($attachment_id);
		$default_attr = array(
			'src'   => $src,
			'class' => "attachment-$size",
			'alt'   => trim(strip_tags( get_post_meta($attachment_id, '_wp_attachment_image_alt', true) )), // Use Alt field first
			'title' => trim(strip_tags( $attachment->post_title )),
		);
		if ( empty($default_attr['alt']) )
			$default_attr['alt'] = trim(strip_tags( $attachment->post_excerpt )); // If not, Use the Caption
		if ( empty($default_attr['alt']) )
			$default_attr['alt'] = trim(strip_tags( $attachment->post_title )); // Finally, use the title

		$attr = wp_parse_args($attr, $default_attr);
		$attr = apply_filters( 'wp_get_attachment_image_attributes', $attr, $attachment );
		$attr = array_map( 'esc_attr', $attr );
		$html = rtrim("<img ");
		foreach ( $attr as $name => $value ) {
			$html .= " $name=" . '"' . $value . '"';
		}
		$html .= ' />';
	}

	return $html;
}