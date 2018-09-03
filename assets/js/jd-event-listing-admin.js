/**
 * Event Listing Admin JS.
 */

jQuery( document ).ready( function ( $ ) {

	var map_section = $( '#js_event_location_map' ),
		is_map_show = $( '#jd_event_show_google_map' ).prop( 'checked' );

	// Hide map section by default.
	map_section.hide();

	// Show Map if checkbox checked.
	if ( is_map_show ) {
		map_section.show();
	}

	// Event start date.
	$( "#jd_event_start_date" ).datetimepicker( {
	} );

	// Event end date.
	$( "#jd_event_end_date" ).datetimepicker( {
	} );

	/**
	 *  Show/Hide Map based on action.
	 */
	$( document ).on( 'click', '#jd_event_show_google_map', function () {

		var $this = $( this );
		if ( $this.is( ':checked' ) ) {
			map_section.show();
		} else {
			map_section.hide();
		}

	} );

	/**
	 * Get Geo Position based on address and update lat and long.
	 */
	$( document ).on( 'focusout', '#jd_event_address', function () {

		var $this = $( this ),
			address = $this.val();

		getLatLongFromAddress( address );

	} );

	/**
	 * Map Initialize.
	 */
	function initialize() {
		var map_canvas = document.getElementById( 'js_event_location_map' );

		var my_lat_long = new google.maps.LatLng( getLatLong.lat, getLatLong.long );

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

	/**
	 * Get Position and new lat long based on address.
	 * @param jd_event_address
	 */
	function getLatLongFromAddress( jd_event_address ) {
		var geocoder;
		geocoder = new google.maps.Geocoder();

		geocoder.geocode( { 'address': jd_event_address }, function ( results, status ) {
			var map_canvas = document.getElementById( 'js_event_location_map' );
			var my_lat_long = new google.maps.LatLng( getLatLong.lat, getLatLong.long );

			var map_options = {
				center: my_lat_long,
				zoom: 8,
				mapTypeId: google.maps.MapTypeId.ROADMAP
			};

			var map = new google.maps.Map( map_canvas, map_options );

			if ( status === google.maps.GeocoderStatus.OK ) {
				map.setCenter( results[ 0 ].geometry.location );

				var marker = new google.maps.Marker( {
					map: map,
					position: results[ 0 ].geometry.location
				} );

				document.getElementById( "jd_event_lat" ).value = results[ 0 ].geometry.location.lat();
				document.getElementById( "jd_event_long" ).value = results[ 0 ].geometry.location.lng();
				document.getElementById( "jd_event_address" ).value = results[ 0 ].formatted_address;

			} else {
				alert( getLatLong.geo_code_error_msg + status );
			}
		} );
	}

	// Initialize Map if Admin want to show.
	google.maps.event.addDomListener( window, 'load', initialize );

} );
