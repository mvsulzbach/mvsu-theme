<?php
/**
 * View: List Event
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events/v2/list/event.php
 *
 * See more documentation about our views templating system.
 *
 * @link http://evnt.is/1aiy
 *
 * @version 5.0.0
 *
 * @var WP_Post $event The event post object with properties added by the `tribe_get_event` function.
 *
 * @see tribe_get_event() For the format of the event object.
 */

$container_classes = [ 'tribe-common-g-row', 'tribe-events-calendar-list__event-row' ];
$container_classes['tribe-events-calendar-list__event-row--featured'] = $event->featured;


if( tribe_get_start_date( $event , false , "N") > 4 ){
    $container_classes[] = 'weekend';
}

$event_classes = tribe_get_post_class( [ 'tribe-events-calendar-list__event', 'tribe-common-g-row', 'tribe-common-g-row--gutters' ], $event->ID );
?>
<div <?php tribe_classes( $container_classes ); ?>>

	<?php $this->template( 'list/event/date-tag', [ 'event' => $event ] ); ?>

	<div class="tribe-events-calendar-list__event-wrapper tribe-common-g-col">
		<article <?php tribe_classes( $event_classes ) ?>>
			<?php $this->template( 'list/event/featured-image', [ 'event' => $event ] ); ?>

			<div class="tribe-events-calendar-list__event-details tribe-common-g-col">

				<header class="tribe-events-calendar-list__event-header">
					<?php $this->template( 'list/event/title', [ 'event' => $event ] ); ?>
					<?php $this->template( 'list/event/date', [ 'event' => $event ] ); ?>
					<?php $this->template( 'list/event/venue', [ 'event' => $event ] ); ?>
				</header>
			</div>

			<div class="teilnahme">

				<?php 
				$gemeinschaft = tribe_event_in_category ("gemeinschaft",get_the_ID());
				$probe = tribe_event_in_category ("probe",get_the_ID());
				$registerprobe = tribe_event_in_category ("registerprobe",get_the_ID());
				$staendchen = tribe_event_in_category ("staendchen",get_the_ID());
				$kirchlich = tribe_event_in_category("kirchlich",get_the_ID());
				$kommunal = tribe_event_in_category("kommunal",get_the_ID());
				$auftritt = tribe_event_in_category ("auftritt",get_the_ID());
				$jugendauftritt = tribe_event_in_category ("jugendauftritt",get_the_ID());
				$staendchen = tribe_event_in_category ("staendchen",get_the_ID());
				$cat=$auftritt||$gemeinschaft||$probe||$registerprobe||$staendchen||$kirchlich||$kommunal;
				$eventdate = tribe_get_start_date( get_the_ID(), true, "U" );
				include 'ajax/variables.php';
				$time = 0;
				if($gemeinschaft){
				$time = $timecat15;
				}
				if($staendchen){
				$time = $timecat14;
				}
				if($kommunal){
				$time = $timecat13;
				}
				if($kirchlich){
				$time = $timecat12;
				}
				if($auftritt){
				$time = $timecat10;
				}
				if($registerprobe){
				$time = $timecat8;
				}
				if($probe){
				$time = $timecat6;
				}
				$latestregistration  = time() + $time;
				if (is_user_logged_in()&&$cat&&$eventdate>$latestregistration) : ?>
					<?php
					$current_user = wp_get_current_user();
					$uid = $current_user->ID;
					?>

					<div id="teilnahme-<?php the_ID() ?>" class="teilnahme_buttons">
					<button class="teilnahme_button tribe-common-c-btn" type="button" onclick="teilnahme(<?php echo $uid ?>,1,<?php the_ID() ?>);">Teilnehmen</button>
					</div>
					<div id="absage-<?php the_ID() ?>" class="absage_buttons">
					<button class="teilnahme_button tribe-common-c-btn" type="button" onclick="absagen(<?php echo $uid ?>,<?php the_ID() ?>,false);">Absagen</button>
					</div>
					<div id="popup-<?php the_ID() ?>" class="overlayHidden tribe-common">
							
							
							<div id="event-name-<?php the_ID() ?>"><b><?php the_title() ?></b></div><div><?php echo tribe_events_event_schedule_details() ?></div><div>Grund</div>
							<textarea class="grund_area" id="grund-<?php the_ID() ?>" cols="50" rows="3"></textarea>
							<div class="hinweis" id="hinweis-<?php the_ID() ?>"></div>
							<button class="tribe-common-c-btn" id="absage-button" onclick="teilnahme(<?php echo $uid ?>,0,<?php the_ID() ?>);">Absagen</button>
							<button class="tribe-common-c-btn" id="schliessen" onclick="document.getElementById('popup-<?php the_ID() ?>').className='overlayHidden';">X</button>
						</div>
					<?php else: ?>
					<div id="teilnahme-<?php the_ID() ?>" class="teilnahme_buttons"></div>
					<div id="absage-<?php the_ID() ?>" class="absage_buttons"></div>
				<?php endif; ?>
			</div>
		</article>
	</div>

</div>
