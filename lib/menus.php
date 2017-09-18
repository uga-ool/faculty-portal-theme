<?php

// Define our responsive menu settings.
function uga_responsive_menu_settings() {

	$settings = array(
		'mainMenu'          => __( 'Menu', 'uga-online' ),
		'menuIconClass'     => 'dashicons-before dashicons-menu',
		'subMenu'           => __( 'Submenu', 'uga-online' ),
		'subMenuIconsClass' => 'dashicons-before dashicons-arrow-down-alt2',
		'menuClasses'       => array(
			'combine' => array(
				'.nav-primary',
				'.nav-header',
			),
			'others'  => array(),
		),
	);

	return $settings;

}

// Reposition the secondary navigation menu.
remove_action( 'genesis_after_header', 'genesis_do_subnav' );
add_action( 'genesis_footer', 'genesis_do_subnav', 5 );

// Reduce the secondary navigation menu to one level depth.
add_filter( 'wp_nav_menu_args', 'uga_secondary_menu_args' );
function uga_secondary_menu_args( $args ) {

	if ( 'secondary' != $args['theme_location'] ) {
		return $args;
	}

	$args['depth'] = 1;

	return $args;

}

// Replace primary navigation to remove unnecessary "Main navigation" heading
// And reposition to before header
remove_action( 'genesis_after_header', 'genesis_do_nav' );
add_action( 'genesis_header_right', 'uga_do_nav' );
function uga_do_nav() {

	//* Do nothing if menu not supported
	if ( ! genesis_nav_menu_supported( 'primary' ) || ! has_nav_menu( 'primary' ) )
		return;

	$class = 'menu genesis-nav-menu menu-primary';
	if ( genesis_superfish_enabled() ) {
		$class .= ' js-superfish';
	}

	genesis_nav_menu( array(
		'theme_location' => 'primary',
		'menu_class'     => $class,
	) );

}

// Filter Skip link text
add_filter( 'genesis_skip_links_output', 'uga_skip_links_output' );

function uga_skip_links_output( $links ) {
	$links['genesis-content'] = esc_html__( 'Skip to main content', 'uga-online' );
	return $links;
}