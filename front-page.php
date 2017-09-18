<?php

add_action( 'genesis_meta', 'uga_front_page_genesis_meta' );
/**
 * Add widget support for homepage. If no widgets active, display the default loop.
 *
 */
function uga_front_page_genesis_meta() {

	if ( is_active_sidebar( 'front-page-1' ) || is_active_sidebar( 'front-page-2' ) || is_active_sidebar( 'front-page-3' ) || is_active_sidebar( 'front-page-4' ) ) {

		//* Add front-page body class
		add_filter( 'body_class', 'uga_body_class' );
		function uga_body_class( $classes ) {

   			$classes[] = 'front-page';

  			return $classes;
  
		}

		//* Force full width content layout
		add_filter( 'genesis_pre_get_option_site_layout', '__genesis_return_full_width_content' );

		//* Remove breadcrumbs
		remove_action( 'genesis_before_loop', 'genesis_do_breadcrumbs');

		//* Remove the default Genesis loop
		remove_action( 'genesis_loop', 'genesis_do_loop' );

		//* Add the rest of front page widgets
		add_action( 'genesis_loop', 'uga_front_page_widgets' );

	}

}

function uga_front_page_widgets() {
	
	do_action( 'uga_before_front_page_widgets' );
	
	$desc = __( sprintf( 'This is a flexible widget area. <a href="%s">See the Front Page Widget Layout guide.</a>', 'admin.php?page=wp-help-documents' ), 'uga-online' );

	genesis_widget_area( 'front-page-1', array(
		'before' => '<div id="front-page-1" class="front-page-1">
			<div class="flexible-widgets widget-area wrap' . uga_widget_area_class( 'front-page-1' ) . '">',
		'after'  => '</div></div>',
		'description' => $desc,
	) );

	genesis_widget_area( 'front-page-2', array(
		'before' => '<div id="front-page-2" class="front-page-2">
			<div class="flexible-widgets widget-area wrap' . uga_widget_area_class( 'front-page-2' ) . '">',
		'after'  => '</div></div>',
		'description' => $desc,
	) );

	genesis_widget_area( 'front-page-3', array(
		'before' => '<div id="front-page-3" class="front-page-3">
			<div class="flexible-widgets widget-area wrap' . uga_widget_area_class( 'front-page-3' ) . '">',
		'after'  => '</div></div>',
		'description' => $desc,
	) );
	
	do_action( 'uga_after_front_page_widgets' );
	

}

genesis();