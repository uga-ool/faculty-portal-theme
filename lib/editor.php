<?php

//* TinyMCE CSS
add_action( 'after_setup_theme', 'berkeley_editor_styles' );

function berkeley_editor_styles() {
	// add base editor stylesheet and fonts
	add_editor_style( array( '/css/editor-style.css', '//fonts.googleapis.com/css?family=Merriweather|Merriweather+Sans|Oswald' ) );
}

// Callback function to insert 'styleselect' (Formats) into the $buttons array
function uga_mce_buttons( $buttons ) {
    array_unshift( $buttons, 'styleselect' );
	$remove = array( 'underline','alignjustify','forecolor' );
	return array_diff( $buttons, $remove );
}

add_filter( 'mce_buttons_2', 'uga_mce_buttons' );

// Callback function to filter the MCE settings
function uga_mce_style_options( $init_array ) {  
    // Define the style_formats array
    $style_formats = array(  
        // Each array child is a format with its own settings
        array(  
            'title' => __( 'Button Link', 'uga-online' ),
            'selector' => 'a',  
            'classes' => 'button'             
        )
    );  
    // Insert the array, JSON ENCODED, into 'style_formats'
    $init_array['style_formats'] = json_encode( $style_formats );  

    return $init_array;  

} 
add_filter( 'tiny_mce_before_init', 'uga_mce_style_options' );

// New TinyMCE button for blockquotes with cite

function uga_pullquote_mce_button() {
	// check if WYSIWYG is enabled
	if ( 'true' == get_user_option( 'rich_editing' ) ) {
		add_filter( 'mce_external_plugins', 'uga_pullquote_add_tinymce_plugin' );
		add_filter( 'mce_buttons', 'uga_pullquote_register_mce_button' );
		// enforce Dashicon font
		echo '<style>
		    .dashicons-testimonial::before {
		      font-family: dashicons,tinymce;
		    } 
		  </style>';
	}
}
add_action('admin_head', 'uga_pullquote_mce_button');

// Declare script for new button
function uga_pullquote_add_tinymce_plugin( $plugin_array ) {
	$plugin_array['blockquote_cite'] = get_stylesheet_directory_uri() .'/js/mce-buttons.js';
	return $plugin_array;
}

// Register new button in the editor
function uga_pullquote_register_mce_button( $buttons ) {
	$first = array_slice( $buttons, 0, 6 );
	array_push( $first, 'blockquote_cite' );
	$buttons = array_splice( $buttons, 6, count( $buttons ) );
	return array_merge( $first, $buttons );
}