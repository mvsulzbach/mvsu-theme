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
				$event_id = get_the_ID();
				if (mvsu_event_has_registration($event_id)) : ?>
					<mvsu-registration initial="<?php echo getParticipation(get_current_user_id(), $event_id) ?>" divid="<?php the_ID() ?>" title="<?php the_title() ?>" schedule_details="<?php echo tribe_events_event_schedule_details(null, '', '', false) ?>" ></mvsu-registration>
				<?php endif; ?>
			</div>
		</article>
	</div>

</div>
