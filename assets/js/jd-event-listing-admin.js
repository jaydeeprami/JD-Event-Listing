/**
 * Event Listing Admin JS.
 */

jQuery( document ).ready( function ( $ ) {

	// Event start date.
	$( "#jd_event_start_date" ).datepicker({
		dateFormat : "dd-mm-yy"
	});

	// Event end date.
	$( "#jd_event_end_date" ).datepicker({
		dateFormat : "dd-mm-yy"
	});

	/**
	 * Map Initialize.
	 */
	function initialize() {
		var map_canvas = document.getElementById( 'js_event_location_map' );

		var my_lat_long = new google.maps.LatLng( 40.713956, -74.006653 );

		var map_options = {
			center: my_lat_long,
			zoom: 8,
			mapTypeId: google.maps.MapTypeId.ROADMAP
		};

		var map = new google.maps.Map( map_canvas, map_options );
		var marker = new google.maps.Marker( {
			draggable: true,
			position: my_lat_long,
			map: map
		} );

		google.maps.event.addListener( marker, 'dragend', function ( event ) {

			document.getElementById( "jd_event_lat" ).value = event.latLng.lat();
			document.getElementById( "jd_event_long" ).value = event.latLng.lng();
			geocodePosition( marker.getPosition() );

		} );
	}

	function geocodePosition( pos ) {
		var geocoder;
		geocoder = new google.maps.Geocoder();

		var address = '';

		geocoder.geocode( {
			latLng: pos
		}, function ( responses ) {
			if ( responses && responses.length > 0 ) {
				address = responses[ 0 ].formatted_address;
			}

			document.getElementById( "jd_event_address" ).value = address;
		} );
	}

	google.maps.event.addDomListener( window, 'load', initialize );

} );
