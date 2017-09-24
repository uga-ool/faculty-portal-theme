<?php

// Filter the "no content matched your criteria" 404 error
add_filter( 'genesis_noposts_text', 'uga_noposts_text', 10, 2 );
function uga_noposts_text( $text ) {
	if ( is_search() ) {
		$text = esc_html__( "I'm sorry. I couldn't find any pages with that phrase. Try again?", 'uga-online' );
	} elseif ( is_archive() ) {
		$text = esc_html__( "There are no posts in this section.", 'uga-online' );
	}
	$text .= get_search_form( false );
	return $text;
}

//* Add excerpts to pages
add_post_type_support( 'page', 'excerpt' );

//* Use excerpts as Featured Page widget content
add_filter( 'the_content', 'uga_featured_page_widget_content' );
function uga_featured_page_widget_content( $content ) {
	if ( is_front_page() && is_active_widget( 'Genesis_Featured_Page' ) ) {
		$content = sprintf( '<p class="excerpt">%s <br /> %s</p>', get_post_field( 'post_excerpt', get_the_ID() ), $link );
	}
	return $content;
}

add_filter( 'get_the_content_limit', 'uga_genesis_content_limit', 9999, 4 );
function uga_genesis_content_limit( $output, $content, $link, $max_characters ) {
	return sprintf( '<p class="excerpt">%s <br /> %s</p>', get_post_field( 'post_excerpt', get_the_ID() ), $link );
}

function scl_child_pages_shortcode() {
   return '<ul class="childpages">'.wp_list_pages( 'echo=0&depth=1&title_li=&child_of='. get_the_ID() ).'</ul>';
}
add_shortcode( 'child-pages', 'scl_child_pages_shortcode' );
add_shortcode( 'children', 'scl_child_pages_shortcode' );
add_shortcode( 'subpages', 'scl_child_pages_shortcode' );

function scl_append_child_pages( $content ) {
   if ( is_singular() && is_page() && ( empty( $content ) ) )
      $content = '<ul class="childpages">'.wp_list_pages( 'echo=0&depth=3&title_li=&child_of='. get_the_ID() ).'</ul>';

   return $content;
}
add_filter( 'the_content', 'scl_append_child_pages' );