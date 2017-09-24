<?php
define( 'CHILD_THEME_NAME', 'UGA Online Learning' );
define( 'CHILD_THEME_URL', 'http://www.stephanieleary.com/' );
define( 'CHILD_THEME_VERSION', '1.0' );

// Shut up, Genesis
remove_action( 'genesis_upgrade', 'genesis_upgrade_redirect' );

include_once( get_template_directory() . '/lib/init.php' );
include_once( get_stylesheet_directory() . '/lib/editor.php' );
include_once( get_stylesheet_directory() . '/lib/featured-page-widget-plus.php' );
include_once( get_stylesheet_directory() . '/lib/footer.php' );
include_once( get_stylesheet_directory() . '/lib/images.php' );
include_once( get_stylesheet_directory() . '/lib/metaboxes.php' );
include_once( get_stylesheet_directory() . '/lib/menus.php' );
include_once( get_stylesheet_directory() . '/lib/pagination.php' );
include_once( get_stylesheet_directory() . '/lib/text.php' );
include_once( get_stylesheet_directory() . '/lib/widget-areas.php' );

add_action( 'after_setup_theme', 'uga_localization_setup' );
function uga_localization_setup(){
	load_child_theme_textdomain( 'uga-online', get_stylesheet_directory() . '/languages' );
}

remove_action( 'genesis_meta', 'genesis_load_stylesheet' );
add_action( 'wp_enqueue_scripts', 'uga_enqueue_scripts_styles', 15 );
function uga_enqueue_scripts_styles() {
	// redo main script enqueue so we can specify media types
	$version = defined( 'CHILD_THEME_VERSION' ) && CHILD_THEME_VERSION ? CHILD_THEME_VERSION : PARENT_THEME_VERSION;
	$handle  = defined( 'CHILD_THEME_NAME' ) && CHILD_THEME_NAME ? sanitize_title_with_dashes( CHILD_THEME_NAME ) : 'child-theme';
	wp_enqueue_style( $handle, get_stylesheet_uri(), false, $version, 'screen' );
	//wp_enqueue_style( 'uga-print-styles', get_stylesheet_directory_uri() . '/css/print.css', false, $version, 'print' );
	wp_enqueue_style( 'uga-fonts', '//fonts.googleapis.com/css?family=Merriweather|Merriweather+Sans|Oswald', array(), $version );
	// Enqueue front page stylesheet
	if ( is_active_sidebar( 'front-page-1' ) || is_active_sidebar( 'front-page-2' ) || is_active_sidebar( 'front-page-3' ) ) {
		wp_enqueue_style( 'uga-frontpage-styles', get_stylesheet_directory_uri() . '/css/frontpage.css', false, $version, 'screen' );
	}
	wp_enqueue_style( 'dashicons' );
	
	$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
	wp_enqueue_script( 'fontfaceobserver', get_stylesheet_directory_uri() . "/js/fontfaceobserver.standalone.js", false, $version, true );
	wp_enqueue_script( 'uga-font-loader', get_stylesheet_directory_uri() . "/js/uga-fonts{$suffix}.js", 'fontfaceobserver', $version, true );
	wp_enqueue_script( 'uga-responsive-menu', get_stylesheet_directory_uri() . "/js/responsive-menus{$suffix}.js", array( 'jquery' ), $version, true );
	wp_enqueue_script( 'uga-jquery-scripts', get_stylesheet_directory_uri() . "/js/uga-scripts{$suffix}.js", array( 'jquery' ), $version, true );
	wp_localize_script(
		'uga-responsive-menu',
		'genesis_responsive_menu',
		uga_responsive_menu_settings()
	);
}

add_filter( 'script_loader_tag', 'uga_async_scripts', 10, 2 );
function uga_async_scripts( $tag, $handle ) {
    if ( !in_array( $handle, array( 'jquery', 'uga-responsive-menu' ) ) )
        return $tag;
    return str_replace( ' src', ' async="async" src', $tag );
}

add_action( 'wp_head', 'uga_inline_head' );
function uga_inline_head() {
	$theme_uri = get_stylesheet_directory_uri();
	echo '<link rel="preload" href="'.$theme_uri.'/css/print.css" as="style">';
}

add_theme_support( 'html5', array( 'caption', 'comment-form', 'comment-list', 'gallery', 'search-form' ) );
add_theme_support( 'genesis-accessibility', array( '404-page', 'drop-down-menu', 'headings', 'rems', 'search-form', 'skip-links' ) );
add_theme_support( 'genesis-responsive-viewport' );
add_theme_support( 'custom-background' );
add_theme_support( 'genesis-menus', array( 'primary' => __( 'Primary Navigation', 'uga-online' ), 'secondary' => __( 'Footer Navigation', 'uga-online' ) ) );
add_theme_support( 'title-tag' );
add_theme_support( 'genesis-after-entry-widget-area' );
add_theme_support( 'genesis-footer-widgets', 3 );

//* Body classes
add_filter( 'body_class', 'uga_body_classes' );

function uga_body_classes( $classes ) {
		
	if ( is_active_sidebar( 'header-right' ) )
		$classes[] = 'header-right-active';
	
	if ( has_nav_menu( 'secondary' ) )
	     $classes[] = 'nav-secondary-active';
	
	return $classes;
}

/* Localize CSS generated content */

add_action( 'wp_enqueue_scripts', 'uga_localized_css' );
function uga_localized_css() {

	$content = __( 'Commit To ', 'uga-online' );
	
	$css .= ( sprintf( '

		.fonts-loaded .commit-to {
			font-family: "TradeGothicLTStd-BdCn20", "Trade Gothic LT Std Cn", "Oswald", sans-serif;
			letter-spacing: .15em;
			text-transform: uppercase;
		}

		.fonts-loaded .commit-to:before {
			content: "%s \A";
			font-family: "Merriweather", serif;
			font-size: 60%;
			letter-spacing: 0;
			white-space: pre;
		}

		', $content );


	if ( $css ) {
		wp_add_inline_style( 'uga_localized_css', $css );
	}

}