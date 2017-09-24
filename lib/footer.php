<?php

// replace footer text
remove_action( 'genesis_footer', 'genesis_do_footer' );
add_action( 'genesis_footer', 'uga_custom_footer' );

function uga_custom_footer() {

	do_action( 'uga_before_footer_text' );

	echo '<div class="uga-logo one-half first">';
	printf( '<img src="%s/images/uga-online-learning-formal.png" alt="%s" />', get_stylesheet_directory_uri(), __( 'University of Georgia Online Learning logo', 'uga-online' ) );
	echo '</div><!-- end .uga-logo -->';
	
	echo '<div class="uga-address one-half">';
	echo wpautop( get_theme_mod( 'uga_footer_text', '&copy; ' . date( 'Y' ) . ' ' . get_option( 'blogname' ) ) );
	echo '</div><!-- end .uga-address -->';

	do_action( 'uga_after_footer_text' );
}



add_action( 'customize_register', 'scl_customizer_sections' );
function scl_customizer_sections( $wp_customize ) {

	$wp_customize->add_section( 'uga_footer', array(
		'title' => __( 'Footer Text', 'uga-online' ),
		'priority' => 105,
		'capability' => 'manage_options',
	) );
 
	$wp_customize->add_setting( 'uga_footer_text', array(
		'default' => '',
		'sanitize_callback' => 'uga_sanitize_footer_text',
		'transport' => 'postMessage',
	) );
 
	$wp_customize->add_control( 'uga_footer_text', array(
		'label' => __( 'Footer text (copyright, etc.)', 'uga-online' ),
		'section' => 'uga_footer',
		'type' => 'textarea',
	) );

}

function uga_sanitize_footer_text( $input ) {
    return wp_kses_post( force_balance_tags( $input ) );
}