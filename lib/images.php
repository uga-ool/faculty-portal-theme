<?php

// Disable WordPress's terrible image compression function. We'll handle this with a plugin.
add_filter( 'jpeg_quality', function( $arg ){ return 100; } );

// Add Image Sizes.
add_action( 'after_setup_theme', 'uga_image_sizes' );
function uga_image_sizes() {
	add_image_size( 'uga-small', 180, 120, true );
	add_image_size( 'uga-featured-image', 400, 250, true );
	add_image_size( 'uga-featured-image-thumbnail', 90, 90, true );
}

// Make custom image size available in Insert Media
add_filter( 'image_size_names_choose', 'uga_image_size_names_choose' );
function uga_image_size_names_choose( $sizes ) {
    return array_merge( $sizes, array(
        'uga-small' => __( 'Small', 'uga-online' ),
		'uga-featured-image' => __( 'Featured Image', 'uga-online' ),
		'uga-featured-image-thumbnail' => __( 'Featured Thumbnail', 'uga-online' ),
    ) );
}

// Modify size of the Gravatar in the author box.
add_filter( 'genesis_author_box_gravatar_size', 'uga_author_box_gravatar' );
function uga_author_box_gravatar( $size ) {
	return 90;
}

// Disable Genesis's default first-uploaded image fallback
add_filter( 'genesis_get_image_default_args', 'uga_image_default_args' );
function uga_image_default_args( $args ) {
	$args['fallback'] = '';
	return $args;
}

// Make custom image size available in Insert Media
add_filter( 'image_size_names_choose', 'uga_image_size_names_choose' );
function uga_image_size_names_choose( $sizes ) {
    return array_merge( $sizes, array(
        'small' => __( 'Small', 'uga-online' ),
		'featured-image' => __( 'Featured Image', 'uga-online' ),
		'featured-image-thumbnail' => __( 'Featured Thumbnail', 'uga-online' ),
    ) );
}

// Remove built-in inline style attribute on div.wp-caption
add_filter( 'img_caption_shortcode', 'uga_img_caption_shortcode', 10, 3 );
function uga_img_caption_shortcode( $empty, $attr, $content ){
	$attr = shortcode_atts( array(
		'id'      => '',
		'align'   => 'alignnone',
		'width'   => '',
		'caption' => ''
	), $attr );

	if ( 1 > (int) $attr['width'] || empty( $attr['caption'] ) ) {
		return '';
	}

	if ( $attr['id'] ) {
		$attr['id'] = 'id="' . esc_attr( $attr['id'] ) . '" ';
	}

	return '<div ' . $attr['id']
	. 'class="wp-caption ' . esc_attr( $attr['align'] ) . '" '
	. 'style="max-width: ' . ( (int) $attr['width'] ) . 'px;">'
	. do_shortcode( $content )
	. '<p class="wp-caption-text">' . $attr['caption'] . '</p>'
	. '</div>';

}