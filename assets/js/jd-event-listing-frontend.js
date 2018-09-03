/**
 * Event Listing Frontend JS.
 */

jQuery( document ).ready( function ( $ ) {

	var $body = $( 'body' );

	// Show Google Map.
	$body.on( 'click', '.jd-event-show-google-map', function () {
		var $this = $( this ),
			event_id = $this.attr( 'id' ),
			article = $this.parents( 'article.jd-event-listing' ),
			jd_event_address = article.find( '.jd_event_address' ).text(),
			google_map_element = article.find( '#jd-event-google-map-modal' );

		showMap( jd_event_address, event_id );

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

	// Insert Event on Google Calendar.
	$body.on( 'click', '.jd-event-google-calendar', function () {

		var $this = $( this ),
			article = $this.parents( 'article.jd-event-listing' ),
			summary = article.find( '.event-title-summary' ).data( 'summary' ),
			jd_event_address = article.find( '.jd_event_address' ).text(),
			event_start_date = article.find( '.event_date' ).data( 'start_time' ),
			event_end_date = article.find( '.event_date' ).data( 'end_time' );

		var event_details = {
			summary: summary,
			location: jd_event_address,
			startDateTime: event_start_date,
			endDateTime: event_end_date
		};

		var auth_status = gapi.auth2.getAuthInstance().isSignedIn.get();

		if ( auth_status ) {
			insertEvents( event_details );
		}

	} );

	// Client ID and API key from the Developer Console.
	var CLIENT_ID = JDCalendarEventObject.client_id;
	var API_KEY = JDCalendarEventObject.api_key;

	// Array of API discovery doc URLs.
	var DISCOVERY_DOCS = [ "https://www.googleapis.com/discovery/v1/apis/calendar/v3/rest" ];

	// Authorization scopes required by the API; multiple scopes can be
	// included, separated by spaces.
	var SCOPES = "https://www.googleapis.com/auth/calendar";

	// Handle oAuth.
	window.onload = function () {
		handleClientLoad();
	};

	/**
	 *  On load, called to load the auth2 library and API client library.
	 */
	function handleClientLoad() {
		gapi.load( 'client:auth2', initClient );
	}

	/**
	 *  Initializes the API client library and sets up sign-in state
	 *  listeners.
	 */
	function initClient() {
		gapi.client.init( {
			apiKey: API_KEY,
			clientId: CLIENT_ID,
			discoveryDocs: DISCOVERY_DOCS,
			scope: SCOPES
		} ).then( function () {
			// Listen for sign-in state changes.
			gapi.auth2.getAuthInstance().isSignedIn.listen( updateSigninStatus() );

			// Handle the initial sign-in state.
			updateSigninStatus();

		} );
	}

	/**
	 *  Called when the signed in status changes, to update the UI
	 *  appropriately. After a sign-in, the API is called.
	 */
	function updateSigninStatus() {
		var auth_status = gapi.auth2.getAuthInstance().isSignedIn.get();

		if ( !auth_status ) {
			handleAuthClick();
		}
	}

	/**
	 *  Sign in the user upon button click.
	 */
	function handleAuthClick() {
		gapi.auth2.getAuthInstance().signIn();
	}

	/**
	 * Print the summary and start datetime/date of the next ten events in
	 * the authorized user's calendar. If no events are found an
	 * appropriate message is printed.
	 */
	function insertEvents( event_details ) {
		var request = gapi.client.calendar.events.insert( {
			'calendarId': 'primary',
			'summary': event_details.summary,
			'location': event_details.location,
			'start': {
				'dateTime': event_details.startDateTime
			},
			'end': {
				'dateTime': event_details.endDateTime
			}
		} );

		request.execute( function ( event ) {
			if ( event.htmlLink ) {
				alert( JDCalendarEventObject.event_inserted );
			} else {
				alert( JDCalendarEventObject.event_failed + ' ' + event.message );
			}
		} );
	}

} );
