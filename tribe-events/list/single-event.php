<?php
/**
 * List View Single Event
 * This file contains one event in the list view
 *
 * Override this template in your own theme by creating a file at [your-theme]/tribe-events/list/single-event.php
 *
 * @package TribeEventsCalendar
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
} ?>

<?php

// Setup an array of venue details for use later in the template
$venue_details = array();

if ( $venue_name = tribe_get_meta( 'tribe_event_venue_name' ) ) {
	$venue_details[] = $venue_name;
}

//if ( $venue_address = tribe_get_meta( 'tribe_event_venue_address' ) ) {
//	$venue_details[] = $venue_address;
//}
// Venue microformats
$has_venue_address = ( $venue_address ) ? ' location' : '';

// Organizer
$organizer = tribe_get_organizer();

?>

<!-- Event Cost -->
<?php if ( tribe_get_cost() ) : ?>
	<div class="tribe-events-event-cost">
		<span><?php echo tribe_get_cost( null, true ); ?></span>
	</div>
<?php endif; ?>

<!-- Event Title -->
<?php do_action( 'tribe_events_before_the_event_title' ) ?>
<div class="event-title">
<?php
$auftritt = tribe_event_in_category ("auftritt",$eventid);
$jugendauftritt = tribe_event_in_category ("jugendauftritt",$eventid);
$staendchen = tribe_event_in_category ("staendchen",$eventid);
$class="";
if($auftritt||$jugendauftritt){
$class="-auftritt";
}
?>
<h2 class="tribe-events-list-event-title<?php echo $class ?> entry-title summary">
	<a class="url" href="<?php echo tribe_get_event_link() ?>" title="<?php the_title() ?>" rel="bookmark">
		<?php the_title() ?>
	</a>
</h2>
</div>
<?php do_action( 'tribe_events_after_the_event_title' ) ?>

<!-- Event Meta -->
<?php do_action( 'tribe_events_before_the_meta' ) ?>
<div class="tribe-events-event-meta vcard">
	<div class="author <?php echo $has_venue_address; ?>">

		<!-- Schedule & Recurrence Details -->
		<div class="updated published time-details">
			<?php echo tribe_events_event_schedule_details() ?>
		</div>

		<?php if ( $venue_details ) : ?>
			<!-- Venue Display Info -->
			<div class="tribe-events-venue-details">
				<?php echo implode( ', ', $venue_details ); ?>
			</div> <!-- .tribe-events-venue-details -->
		<?php endif; ?>
		<?php if(is_user_logged_in()&&$staendchen) : ?>
			<div class="tribe-events-list-description tribe-events-content entry-content description">
				<?php the_content(); ?>
			</div>
		<?php endif; ?>

	</div>
</div><!-- .tribe-events-event-meta -->

<div class="teilnahme">

<?php 
$gemeinschaft = tribe_event_in_category ("gemeinschaft",$eventid);
$probe = tribe_event_in_category ("probe",$eventid);
$registerprobe = tribe_event_in_category ("registerprobe",$eventid);
$staendchen = tribe_event_in_category ("staendchen",$eventid);
$kirchlich = tribe_event_in_category("kirchlich",$eventid);
$kommunal = tribe_event_in_category("kommunal",$eventid);
$cat=$auftritt||$gemeinschaft||$probe||$registerprobe||$staendchen||$kirchlich||$kommunal;
$eventdate = tribe_get_start_date( $eventid, true, "U" );
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
if(is_user_logged_in()&&$cat&&$eventdate>$latestregistration) : ?>
<?php
$current_user = wp_get_current_user();
$uid = $current_user->ID;
?>

<div id="teilnahme-<?php the_ID() ?>" class="teilnahme_buttons">
<button class="teilnahme_button" type="button" onclick="teilnahme(<?php echo $uid ?>,true,<?php the_ID() ?>);">Teilnehmen</button>
</div>
<div id="absage-<?php the_ID() ?>" class="absage_buttons">
<button class="teilnahme_button" type="button" onclick="absagen(<?php echo $uid ?>,<?php the_ID() ?>,false);">Absagen</button>
</div>
<div id="popup-<?php the_ID() ?>" class="overlayHidden">
		
		
		<div id="event-name-<?php the_ID() ?>"><b><?php the_title() ?></b></div><div><?php echo tribe_events_event_schedule_details() ?></div><div>Grund</div>
		<textarea class="grund_area" id="grund-<?php the_ID() ?>" cols="50" rows="3"></textarea>
		<div class="hinweis" id="hinweis-<?php the_ID() ?>"></div>
		<button id="absage-button" onclick="teilnahme(<?php echo $uid ?>,false,<?php the_ID() ?>);">Absagen</button>
		<button id="schliessen" onclick="document.getElementById('popup-<?php the_ID() ?>').className='overlayHidden';">X</button>
	</div>
<?php else: ?>
<div id="teilnahme-<?php the_ID() ?>" class="teilnahme_buttons"></div>
<div id="absage-<?php the_ID() ?>" class="absage_buttons"></div>
<?php endif; ?>
</div>
<?php do_action( 'tribe_events_after_the_meta' ) ?>

<!-- Event Image -->
<?php echo tribe_event_featured_image( null, 'medium' ) ?>

<!-- Event Content -->
<?php do_action( 'tribe_events_before_the_content' ) ?>
<?php do_action( 'tribe_events_after_the_content' ) ?>
