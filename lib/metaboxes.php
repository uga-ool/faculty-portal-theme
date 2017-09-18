<?php

// Replace Excerpt meta box so we can edit the descriptive text below to be more informative

add_action( 'add_meta_boxes', 'uga_custom_meta_boxes', 99 );

function uga_custom_meta_boxes() {
	remove_meta_box( 'postexcerpt', array( 'post', 'page' ), 'side' );
	add_meta_box( 'postexcerpt', __( 'Excerpt' ), 'uga_custom_excerpt_meta_box', array( 'post', 'page' ), 'normal', 'high' );
}

function uga_custom_excerpt_meta_box( $post ) { ?>
	<label class="screen-reader-text" for="excerpt"><?php _e( 'Excerpt' ) ?></label>
	<textarea rows="1" cols="40" name="excerpt" id="excerpt"><?php echo $post->post_excerpt; // textarea_escaped ?></textarea>
	<p><?php
		_e( 'This summary will appear on archive pages (e.g. lists of posts by category), in search results, and in Featured Post or Page widgets.', 'uga-online' );
	?></p>
	<?php
}

// Hide some meta boxes by default

add_filter( 'default_hidden_meta_boxes', 'uga_hidden_meta_boxes', 10, 2 );

function uga_hidden_meta_boxes( $hidden, $screen ) {
	
	$show_these = array(
		'postexcerpt',
	);
	$hidden = array_diff( $hidden, $show_these );
	
    $hide_these = array( 
		'genesis-theme-settings-version', 
		'genesis-theme-settings-feeds',
		'genesis_inpost_scripts_box',
		'commentstatusdiv',
		'slugdiv',
		'authordiv',
		'postcustom',
		'trackbacksdiv',
		'sharing_meta'
	);
	
	return array_merge( $hidden, $hide_these );
}

// Close some meta boxes by default
// The dynamic portions of the hook name, `$page` and `$id`, refer to the screen and metabox ID, respectively.
// add_filter( "postbox_classes_{$page}_{$id}", 'uga_closed_meta_boxes' );

add_action( 'admin_init', 'uga_close_meta_boxes', 99 );

function uga_close_meta_boxes() {
	$post_types = get_post_types( array( 'public' => true ) );
	foreach ( $post_types as $type ) {
		// Close Genesis SEO on all post types
		add_filter( "postbox_classes_{$type}_genesis_inpost_seo_box", 'uga_closed_meta_boxes' );
		// Close Genesis Layout on all post types
		add_filter( "postbox_classes_{$type}_genesis_inpost_layout_box", 'uga_closed_meta_boxes' );
		
		// Close Genesis Layout on all CPT Archive Settings
		add_filter( "postbox_classes_{$type}_page_genesis-cpt-archive-{$type}_genesis-cpt-archives-seo-settings", 		'uga_closed_meta_boxes' );
		// Close Genesis SEO on all CPT Archive Settings
		add_filter( "postbox_classes_{$type}_page_genesis-cpt-archive-{$type}_genesis-cpt-archives-layout-settings", 		'uga_closed_meta_boxes' );
	}
}

function uga_closed_meta_boxes( $classes ) {
    array_push( $classes, 'closed' );
    return $classes;
}