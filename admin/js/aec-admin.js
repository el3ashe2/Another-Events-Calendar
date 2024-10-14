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
			zoom	  : parseInt( aec.zoom_level ),
			center	  : new google.maps.LatLng( 0, 0 ),
			mapTypeId : google.maps.MapTypeId.ROADMAP
		};
		
		// create map	        	
		var map = new google.maps.Map( $el[0], args );
		
		// add a marker reference
		map.marker = '';		
		
		// Add marker
		var latitude = $('#aec-latitude').val();
		var geocoder = new google.maps.Geocoder();
		
		if( '' == latitude ) {
						
			var default_location = $el.data('default_location');
			geocoder.geocode( { 'address': default_location }, function( results, status ) {
      			if( status == google.maps.GeocoderStatus.OK ) {
					aec_update_latlng( results[0].geometry.location.lat(), results[0].geometry.location.lng() );
					aec_add_marker( $marker, map );		
					aec_center_map( map );					
      			}
    		});
			
		} else {
			
			aec_add_marker( $marker, map );		
			aec_center_map( map );
			
		};
		
		// Update marker position on address change
		$('.aec-map-field').on('blur', function() {
			var address = [];
			
			if( $('#aec-venue-name').val() ) address.push( $('#aec-venue-name').val() );
			if( $('#aec-address').val() ) address.push( $('#aec-address').val() );
			if( $('#aec-city').val() ) address.push( $('#aec-city').val() );
			if( $('#aec-country').val() ) address.push( $("#aec-country option:selected").text() );
			if( $('#aec-state').val() ) address.push( $('#aec-state').val() );
			if( $('#aec-pincode').val() ) address.push( $('#aec-pincode').val() );
			
			address = address.join();

			geocoder.geocode( { 'address': address}, function( results, status ) {
      			if( status == google.maps.GeocoderStatus.OK) {
					map.marker.setPosition( results[0].geometry.location );
					aec_update_latlng( results[0].geometry.location.lat(), results[0].geometry.location.lng() );
					aec_center_map( map );					
      			}
    		});
			
		});
		
		// return
		return map;
		
	}
	
	/*
	*  This function will add a marker to the selected Google Map
	*
	*  @since	1.0.0
	*/	
	function aec_add_marker( $marker, map ) {
	
		// var
		var latlng = new google.maps.LatLng( $('#aec-latitude').val(), $('#aec-longitude').val() );
	
		// create marker
		var marker = new google.maps.Marker({
			position	: latlng,
			map			: map,
			draggable   : true
		});
	
		map.marker = marker;
		
		// if marker contains HTML, add it to an infoWindow
		if( $marker.html() ) {
			// create info window
			var infowindow = new google.maps.InfoWindow({
				content	: $marker.html()
			});
	
			// show info window when marker is clicked
			google.maps.event.addListener(marker, 'click', function() {
	
				infowindow.open( map, marker );
	
			});
			
		}
		
		// Update latlng values on marker dragend	
		google.maps.event.addListener( marker, "dragend", function(e) {
            var t = marker.getPosition();
            map.panTo(t);
           	aec_update_latlng( t.lat(), t.lng() );
        });
	
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
	*  Update latlng values in the custom post type 'aec_events'
	*
	*  @since	1.0.0
	*/	
	function aec_update_latlng( lat, lng ) {
		
		$('#aec-latitude').val( lat );
		$('#aec-longitude').val( lng );
		
	}
	
	/*
	*  On Document Ready
	*
	*  @since	1.0.0
	*/	
	$(function() {
		
		// Add datepicker field in custom post type 'aec_events'
		$( ".aec-date" ).datepicker({
			dateFormat: 'yy-mm-dd'
		});
		
		// Show or hide time selectors in custom post type 'aec_events'
		$( '#aec-all-day-event' ).on( 'change', function () {
											  
			if ( $("#aec-all-day-event").is(":checked") ) { 
				$( '.aec-event-time-fields' ).hide(); 
			} else {
				$( '.aec-event-time-fields' ).show();
			}
			
		}).trigger('change');	
		
		// Add new venue fields in custom post type 'aec_events'
		$( '#aec-venues' ).on( 'change', function() {											 
			
        	var value = $(this).val();
        	 if( value == -1 ) {
            	$( '#aec-new-venue-fields' ).show();
			} else {
				$( '#aec-new-venue-fields' ).hide();
			}
			
    	}).trigger('change');	
		
		// Add new organizer fields in custom post type 'aec_events'
		$( '#aec-add-new-organizer' ).on( 'click', function() {

			$( '#aec-organizer-fields' ).find( '.aec-organizer-fields p' ).html( '#' + $( '.aec-organizer-fields' ).length );
			var $clone = $( '#aec-organizer-fields' ).find( '.aec-organizer-fields' ).clone( false ); 
			$( '#aec-organizer-fields-container' ).append( $clone );
			
		});
		
		// Add map in custom post type 'aec_venues'
		$( '.aec-map' ).each(function() {
			aec_map( $(this) );
		});
		
	});

})( jQuery );
