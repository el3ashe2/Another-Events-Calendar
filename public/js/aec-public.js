(function( $ ) {
	'use strict';

	/*
	 *  This function will render a Google Map onto the selected jQuery element
	 *
	 *  @since	1.0.0
	 */
	function aec_map( $el ) {
		
		// var
		var $marker = $el.find('.marker');

		// vars
		var args = {
			zoom		: parseInt( aec.zoom ),
			center		: new google.maps.LatLng( 0, 0 ),
			mapTypeId	: google.maps.MapTypeId.ROADMAP
		};
		
		// create map	        	
		var map = new google.maps.Map( $el[0], args );
		
		// add a marker reference
		map.marker = '';		
		
		// Add marker		
		aec_add_marker( $marker, map );		
		aec_center_map( map );
		
		// When modal window is open, this script resizes the map and resets the map center
		$( '#aec-map-modal' ).on( "shown.bs.modal", function() {
			google.maps.event.trigger( map, "resize" );
      		return aec_center_map( map );
		});
		
					
	}
	
	/*
	*  This function will add a marker to the selected Google Map
	*
	*  @since	1.0.0
	*/	
	function aec_add_marker( $marker, map ) {
	
		// var
		var latlng = new google.maps.LatLng( $marker.data('latitude'), $marker.data('longitude') );
	
		// create marker
		var marker = new google.maps.Marker({
			position	: latlng,
			map			: map,
			draggable   : false
		});
	
		map.marker = marker;
		
		// if marker contains HTML, add it to an infoWindow
		if( $marker.html() ) {
			// create info window
			var infowindow = new google.maps.InfoWindow({
				content		: $marker.html()
														
			});
	
			// show info window when marker is clicked
			google.maps.event.addListener(marker, 'click', function() {
	
				infowindow.open( map, marker );
	
			});
			
		}
	
	}		  
	
	/*
	 *  This function will center the map, showing all markers attached to this map
	 *	
	 *  @since	1.0.0
	 */	
	function aec_center_map( map ) {
	
		// vars
		var bounds = new google.maps.LatLngBounds();
	
		// loop through all markers and create bounds
		var latlng = new google.maps.LatLng( map.marker.position.lat(), map.marker.position.lng() );
		bounds.extend( latlng );
	
		map.setCenter( bounds.getCenter() );
	
	}
	
	/*
	 *  On Document Ready
	 *
	 *  @since	1.0.0
	 */	
	$(function() {
	
		// Add date picker in the search widget(s)
		$( ".aec-widget-date-picker" ).datepicker({
			dateFormat: 'yy-mm-dd'
		});	
		
		// Add map in custom post type 'aec_venues'
		$( '.aec-map' ).each(function() {
			aec_map( $(this) );
		});
		
		//Add Mini-calendar
		$( 'body' ).on( 'click', '.aec-mini-calendar-nav', function( e ) {
			e.preventDefault();

			var data = {
				'action'	: 'aec_mini_calendar',
				'mo'        : $(this).data('month'),
				'yr'    	: $(this).data('year'),
				'widget_id' : $(this).data('id'),
			};	
			
			$( '.aec-spinner-container', '#' + data['widget_id'] ).html( '<div class="aec-spinner"></div>' );
			
			jQuery.post( aec.ajaxurl, data, function( response ) {
				$( '#' + data['widget_id'] ).replaceWith( response );
			});
		
		});
					
	});

})( jQuery );
