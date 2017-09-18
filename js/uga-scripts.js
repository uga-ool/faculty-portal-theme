// jQuery
jQuery(document).ready(function( $ ) {
	
	$( 'table' ).each( function() {
    	var parentTable = $(this);

		// add responsive class if headers are not empty
		var headertext = $( 'thead th', parentTable ).text();
		if( $.trim( headertext ) !== '' ) {
			parentTable.addClass( 'responsive-headers' );
		}
    
		// create array of header text
	    var headings = [];
		$( 'thead th', parentTable ).each( function() {
			headings.push( $(this).text() );
		});
    
		// set title attribute to corresponding header text
    	$( 'tbody th, tbody td', parentTable ).each( function() {
			$(this).attr( 'title', headings[ $(this).index() ] );
	    });
	});
	

	// Move header widget search into primary sidebar on small screens
    if ( $( 'body' ).width() < 860 ) {
		$( ".header-widget-area .widget_search" ).appendTo( "#mobile-sidebar-widgets" );
	}
});