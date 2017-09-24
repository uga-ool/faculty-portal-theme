<?php

//* TinyMCE CSS
add_action( 'after_setup_theme', 'uga_editor_styles' );

function uga_editor_styles() {
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

/*
Define the styleselect ("Formats") dropdown menu options. To be used as PHP in uga_mce_style_options() and as JSON in uga_rich_text_widget_buttons_enqueue()
/**/

function uga_mce_styleselect_options() {
	return array(
        // Each array child is a format with its own settings
        array(  
            'title' => __( 'Button Link', 'uga-online' ),
            'selector' => 'a',  
            'classes' => 'button'             
        ),
		array(  
            'title' => __( 'Uppercase', 'uga-online' ),
            'selector' => 'h1, h2, h3, h4, h5, h6, p, span, li, dt',
			'inline' => 'span',
            'classes' => 'uppercase'             
        ),
		array(  
            'title' => __( 'Bulldog Red', 'uga-online' ),
            'selector' => 'h1, h2, h3, h4, h5, h6, p, blockquote, span, li, dt',
			'inline' => 'span',
            'classes' => 'red'             
        ),
		array(  
            'title' => __( 'Commit To... heading (auto prefixed)', 'uga-online' ),
            'selector' => 'h1, h2, h3, h4, h5, h6',
            'classes' => 'commit-to',
            'exact' => true,
        ),
		array(  
	        'title' => __( 'Trade Gothic headline', 'uga-online' ),
	        'selector' => 'h1, h2, h3, h4, h5, h6',
	        'classes' => 'trade-gothic',
			'exact' => true,             
	    ),
		array(  
	        'title' => __( 'Merriweather Sans headline', 'uga-online' ),
	        'selector' => 'h1, h2, h3, h4, h5, h6',
	        'classes' => 'merriweathersans',
			'exact' => true,             
	    ),
		array(  
	        'title' => __( 'Merriweather headline', 'uga-online' ),
	        'selector' => 'h1, h2, h3, h4, h5, h6',
	        'classes' => 'merriweather',
	        'exact' => true,
	    )
    );  
}

// Callback function to filter the MCE settings
function uga_mce_style_options( $init_array ) {  
    // Define the style_formats array
    $style_formats = uga_mce_styleselect_options();
    // Insert the array, JSON ENCODED, into 'style_formats'
    $init_array['style_formats'] = json_encode( $style_formats );  
	$init_array['preview_styles'] = false;
    return $init_array;  

} 
add_filter( 'tiny_mce_before_init', 'uga_mce_style_options' );

// New TinyMCE button for blockquotes with cite
function uga_pullquote_mce_button() {
	// check if WYSIWYG is enabled
	if ( user_can_richedit() ) {
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

// Declare script for main TinyMCE buttons
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

// Declare script for rich text widget buttons
function uga_rich_text_widget_buttons_enqueue( $hook ) {
	if ( 'widgets.php' !== $hook )
    	return;

    wp_enqueue_script( 'uga_rich_text_widget_buttons', get_stylesheet_directory_uri() . '/js/rich-text-widget-buttons.js', array( 'jquery' ), '1.0', true  );
	wp_localize_script(
		'uga_rich_text_widget_buttons',
		'UGATinyMCE_formats',
		uga_mce_styleselect_options()
	);
}
add_action( 'admin_enqueue_scripts', 'uga_rich_text_widget_buttons_enqueue' );