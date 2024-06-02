<?php
include_once("mail.php");

function mvsu_create_user($email_address,$display_name,$register){
	if( null == username_exists( $email_address ) ) {
	 	$login = $email_address;
		if($register==""){
			$login = $display_name;
		}
		// Generate the password and create the user
		$password = wp_generate_password( 12, false );
		$user_id = wp_create_user( $login, $password, $email_address );
	 
		// Set the nickname
		wp_update_user(
			array(
	       			'ID'          =>    $user_id,
	       			'display_name'    =>    $display_name,
				'role'	=>	'subscriber',
				'show_admin_bar_front'	=>	'false'
	    		)
		);
		if($register!=""){
			add_user_meta($user_id,"register",$register,true);
		}
	 
		// Email the user
		smtpmailer($email_address, 'wordpress@mv-sulzbach.de', 'MV Sulzbach', 'MV Edelweiss Sulzbach Homepage', 'Hallo '.$display_name.', <br /><br /> f&uuml;r dich wurde soeben auf <a href="http://mv-sulzbach.de">http://mv-sulzbach.de</a> ein Account erstellt. Der Benutzername ist ' .$login. ', das Passwort ist: ' . $password .'.<br />Bitte &auml;ndere das Passwort schnellstm&ouml;glich, indem du dich auf der Seite einloggst und dann unten links auf "Passwort &auml;ndern" klickst!<br /><br />Dein Wordpress (i.A. MV "Edelweiss" Sulzbach)');
	} // end if
}
function mvsu_change_pass($pass){
	wp_update_user( array( 'ID' => get_current_user_id(), 'user_pass' => $pass));
}
function mvsu_reset_pass($id){
	$user = get_user_by('id',$id);
	$password = wp_generate_password( 12, false );
	wp_set_password($password,$user->ID);
	smtpmailer($user->user_email, 'wordpress@mv-sulzbach.de', 'MV Sulzbach', 'MV Edelweiss Sulzbach Homepage', 'Hallo '.$user->display_name.', <br /><br /> dein Passwort auf <a href="http://mv-sulzbach.de">http://mv-sulzbach.de</a> wurde soeben zur&uumlckgesetzt. Der Benutzername ist ' .$user->user_login. ', das Passwort ist: ' . $password .'.<br />Bitte &auml;ndere das Passwort schnellstm&ouml;glich, indem du dich auf der Seite einloggst und dann unten links auf "Passwort &auml;ndern" klickst!<br /><br />Dein Wordpress (i.A. MV "Edelweiss" Sulzbach)');
}
function my_generate_ical_feed( $post = null ) {

		$tec         = Tribe__Events__Main::instance();
		$events      = '';
		$blogHome    = get_bloginfo( 'url' );
		$blogName    = get_bloginfo( 'name' );

		if ( $post ) {
			$events_posts = is_array( $post ) ? $post : array( $post );
		} else {
			if ( tribe_is_month() ) {
				$events_posts = $tec::get_month_view_events();
			} else {
				global $wp_query;
				$events_posts = $wp_query->posts;
			}
		}

		$event_ids = wp_list_pluck( $events_posts, 'ID' );

		foreach ( $events_posts as $event_post ) {
			// add fields to iCal output
			$item = array();

			$full_format = 'Ymd\THis';
			$time = (object) array(
				'start' => tribe_get_start_date( $event_post->ID, false, 'U' ),
				'end' => tribe_get_end_date( $event_post->ID, false, 'U' ),
				'modified' => strtotime( $event_post->post_modified ),
				'created' => strtotime( $event_post->post_date ),
			);

			if ( 'yes' == get_post_meta( $event_post->ID, '_EventAllDay', true ) ) {
				$type = 'DATE';
				$format = 'Ymd';
			} else {
				$type = 'DATE-TIME';
				$format = $full_format;
			}

			$tzoned = (object) array(
				'start'    => date( $format, $time->start ),
				'end'      => date( $format, $time->end ),
				'modified' => date( $format, $time->modified ),
				'created'  => date( $format, $time->created ),
			);

			if ( 'DATE' === $type ){
				$item[] = "DTSTART;VALUE=$type:" . $tzoned->start;
				$item[] = "DTEND;VALUE=$type:" . $tzoned->end;
			} else {
				// Are we using the sitewide timezone or the local event timezone?
				$tz = Tribe__Events__Timezones::EVENT_TIMEZONE === Tribe__Events__Timezones::mode()
					? Tribe__Events__Timezones::get_event_timezone_string( $event_post->ID )
					: Tribe__Events__Timezones::wp_timezone_string();

				$item[] = 'DTSTART;TZID=' . $tz . ':' . $tzoned->start;
				$item[] = 'DTEND;TZID=' . $tz . ':' . $tzoned->end;
			}

			$item[] = 'DTSTAMP:' . date( $full_format, time() );
			$item[] = 'CREATED:' . $tzoned->created;
			$item[] = 'LAST-MODIFIED:' . $tzoned->modified;
			$item[] = 'UID:' . $event_post->ID . '-' . $time->start . '-' . $time->end . '@' . parse_url( home_url( '/' ), PHP_URL_HOST );
			$item[] = 'SUMMARY:' . str_replace( array( ',', "\n", "\r", "\t" ), array( '\,', '\n', '', '\t' ), html_entity_decode( strip_tags( $event_post->post_title ), ENT_QUOTES ) );
			$item[] = 'DESCRIPTION:' . str_replace( array( ',', "\n", "\r", "\t" ), array( '\,', '\n', '', '\t' ), html_entity_decode( strip_tags( $event_post->post_content ), ENT_QUOTES ) );
			$item[] = 'URL:' . get_permalink( $event_post->ID );

			// add location if available
			$location = $tec->fullAddressString( $event_post->ID );
			if ( ! empty( $location ) ) {
				$str_location = str_replace( array( ',', "\n" ), array( '\,', '\n' ), html_entity_decode( $location, ENT_QUOTES ) );

				$item[] = 'LOCATION:' .  $str_location;
			}

			// add geo coordinates if available
			if ( class_exists( 'Tribe__Events__Pro__Geo_Loc' ) ) {
				$long = Tribe__Events__Pro__Geo_Loc::instance()->get_lng_for_event( $event_post->ID );
				$lat  = Tribe__Events__Pro__Geo_Loc::instance()->get_lat_for_event( $event_post->ID );
				if ( ! empty( $long ) && ! empty( $lat ) ) {
					$item[] = sprintf( 'GEO:%s;%s', $lat, $long );

					$str_title = str_replace( array( ',', "\n" ), array( '\,', '\n' ), html_entity_decode( tribe_get_address( $event_post->ID ), ENT_QUOTES ) );

					if ( ! empty( $str_title ) && ! empty( $str_location ) ) {
						$item[] =
							'X-APPLE-STRUCTURED-LOCATION;VALUE=URI;X-ADDRESS=' . str_replace( '\,', '', trim( $str_location ) ) . ';' .
							'X-APPLE-RADIUS=500;' .
							'X-TITLE=' . trim( $str_title ) . ':geo:' . $long . ',' . $lat;
					}
				}
			}

			// add categories if available
			$event_cats = (array) wp_get_object_terms( $event_post->ID, Tribe__Events__Main::TAXONOMY, array( 'fields' => 'names' ) );
			if ( ! empty( $event_cats ) ) {
				$item[] = 'CATEGORIES:' . html_entity_decode( join( ',', $event_cats ), ENT_QUOTES );
			}

			// add featured image if available
			if ( has_post_thumbnail( $event_post->ID ) ) {
				$thumbnail_id        = get_post_thumbnail_id( $event_post->ID );
				$thumbnail_url       = wp_get_attachment_url( $thumbnail_id );
				$thumbnail_mime_type = get_post_mime_type( $thumbnail_id );
				$item[]              = apply_filters( 'tribe_ical_feed_item_thumbnail', sprintf( 'ATTACH;FMTTYPE=%s:%s', $thumbnail_mime_type, $thumbnail_url ), $event_post->ID );
			}

			// add organizer if available
			$organizer_email = tribe_get_organizer_email( $event_post->ID );
			if ( $organizer_email ) {
				$organizer_id = tribe_get_organizer_id( $event_post->ID );
				$organizer = get_post( $organizer_id );

				if ( $organizer_id ) {
					$item[] = sprintf( 'ORGANIZER;CN="%s":MAILTO:%s', rawurlencode( $organizer->post_title ), $organizer_email );
				} else {
					$item[] = sprintf( 'ORGANIZER:MAILTO:%s', $organizer_email );
				}
			}

			$item = apply_filters( 'tribe_ical_feed_item', $item, $event_post );

			$events .= "BEGIN:VEVENT\r\n" . implode( "\r\n", $item ) . "\r\nEND:VEVENT\r\n";
		}

		$site = sanitize_title( get_bloginfo( 'name' ) );
		$hash = substr( md5( implode( $event_ids ) ), 0, 11 );

		/**
		 * Modifies the filename provided in the Content-Disposition header for iCal feeds.
		 *
		 * @var string       $ical_feed_filename
		 * @var WP_Post|null $post
		 */
		$filename = apply_filters( 'tribe_events_ical_feed_filename', $site . '-' . $hash . '.ics', $post );

		$content = "BEGIN:VCALENDAR\r\n";
		$content .= "VERSION:2.0\r\n";
		$content .= 'PRODID:-//' . $blogName . ' - ECPv' . Tribe__Events__Main::VERSION . "//NONSGML v1.0//EN\r\n";
		$content .= "CALSCALE:GREGORIAN\r\n";
		$content .= "METHOD:PUBLISH\r\n";
		$content .= 'X-WR-CALNAME:' . apply_filters( 'tribe_ical_feed_calname', $blogName ) . "\r\n";
		$content .= 'X-ORIGINAL-URL:' . $blogHome . "\r\n";
		$content .= 'X-WR-CALDESC:Events for ' . $blogName . "\r\n";
		$content = apply_filters( 'tribe_ical_properties', $content );
		$content .= $events;
		$content .= 'END:VCALENDAR';
		return $content;


	}

	add_filter("script_loader_tag", "add_module_to_my_script", 10, 3);
	function add_module_to_my_script($tag, $handle, $src)
	{
		if ('mvsu-components' === $handle) {
			$tag = '<script type="module" src="' . esc_url($src) . '"></script>';
		}

		return $tag;
	}



	function mvsu_scripts() {
		$content = array(
			'root' => esc_url_raw( rest_url() ),
			'nonce' => wp_create_nonce( 'wp_rest' )
		);

		wp_enqueue_script( 'mvsu-script', get_template_directory_uri() . '/../mvsu/mvsu.min.js', array('jquery'), '20230925', true );
		wp_localize_script( 'mvsu-script', 'mvsuSettings', $content);
		wp_enqueue_script( 'mvsu-components-polyfills', get_template_directory_uri() . '/../mvsu/mvsu-components/polyfills.js', array('jquery'), '20230925', true );
		wp_enqueue_script( 'mvsu-components', get_template_directory_uri() . '/../mvsu/mvsu-components/main.js', array('jquery', 'mvsu-components-polyfills'), '20230925', true );
		wp_localize_script( 'mvsu-components', 'mvsuSettings', $content);

	}
	add_action( 'wp_enqueue_scripts', 'mvsu_scripts' );
?>