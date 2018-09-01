/**
 * Event Listing Frontend JS.
 */

jQuery( document ).ready( function ( $ ) {

	$( '.jd-event-listing' ).each( function ( index ) {

		var $this = $( this ),
			event_id = $this.attr( 'id' ),
			jd_event_address = $this.find( '.jd_event_address' ).text();

		showMap( jd_event_address, event_id );

	} );


	$( 'body' ).on( 'click', '.show_google_map', function () {

		var $this = $( this ),
			article = $this.parents( 'article.jd-event-listing' ),
			google_map_element = article.find( '#jd-event-google-map-modal' );

		google_map_element.dialog( { width: 500, height: 400 } );

	} );

	/**
	 * Get Position and new lat long based on address.
	 * @param jd_event_address
	 * @param post_id
	 */
	function showMap( jd_event_address, post_id ) {
		var geocoder;
		geocoder = new google.maps.Geocoder();

		geocoder.geocode( { 'address': jd_event_address }, function ( results, status ) {
			var map_canvas = document.getElementById( 'js_event_location_map_' + post_id );
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
			}
		} );
	}

} );
