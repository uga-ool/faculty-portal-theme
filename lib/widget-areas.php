<?php

//* Setup widget counts
function uga_count_widgets( $id ) {
	global $sidebars_widgets;

	if ( isset( $sidebars_widgets[ $id ] ) ) {
		return count( $sidebars_widgets[ $id ] );
	}
}

function uga_widget_area_class( $id ) {
	$count = uga_count_widgets( $id );
	return uga_get_widget_count_classes( $count );
}

function uga_get_widget_count_classes( $count ) {
	$class = '';

	if( $count == 1 ) {
		$class .= ' widget-full ';
	} elseif( $count % 3 == 0 ) {
		$class .= ' widget-thirds ';
	} elseif( $count % 4 == 0 ) {
		$class .= ' widget-fourths ';
	} elseif( $count % 5 == 0 ) {
		$class .= ' widget-fifths ';
	} elseif( $count % 6 == 0 ) {
		$class .= ' widget-thirds ';
	} elseif( $count % 2 == 0 ) {
		$class .= ' widget-halves ';
	} else {	
		$class .= ' widget-halves uneven ';
	}

	return $class;
}

//* Add support for after-entry widget area to all content types
add_action( 'genesis_entry_footer', 'uga_after_entry_widget'  );
 
function uga_after_entry_widget() {
	genesis_widget_area( 'after-entry', array(
		'before' => '<div class="after-entry widget-area"><div class="wrap">',
		'after'  => '</div></div>',
	) );
}

//* Remove sidebar headings
function uga_sidebar_title_output( $heading, $sidebar_id ) {
	return '';
}

add_filter( 'genesis_sidebar_title_output', 'uga_sidebar_title_output', 10, 2 );

//* Create empty div to house header widgets on mobile
// above primary sidebar on 2- and 3-column pages; above footer text on home page
add_action( 'genesis_before_sidebar_widget_area', 'uga_before_sidebar_widgets' );
add_action( 'uga_before_footer_text', 'uga_before_sidebar_widgets' );
function uga_before_sidebar_widgets() {
	echo '<div id="mobile-sidebar-widgets"></div>';
}


//* register Front Page widget areas
genesis_register_sidebar( array(
	'id'          => 'front-page-1',
	'name'        => esc_html__( 'Front Page Top Section', 'uga-online' ),
	'description' => esc_html__( 'This is the front page top section.', 'uga-online' ),
) );
genesis_register_sidebar( array(
	'id'          => 'front-page-2',
	'name'        => esc_html__( 'Front Page Middle Section', 'uga-online' ),
	'description' => esc_html__( 'This is the front page middle section.', 'uga-online' ),
) );
genesis_register_sidebar( array(
	'id'          => 'front-page-3',
	'name'        => esc_html__( 'Front Page Bottom Section', 'uga-online' ),
	'description' => esc_html__( 'This is the front bottom section.', 'uga-online' ),
) );