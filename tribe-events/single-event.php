<?php
/**
 * Single Event Template
 * A single event. This displays the event title, description, meta, and
 * optionally, the Google map for the event.
 *
 * Override this template in your own theme by creating a file at [your-theme]/tribe-events/single-event.php
 *
 * @package TribeEventsCalendar
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$event_id = get_the_ID();

?>
<script type="text/javascript" src="/wp-content/themes/mvsu/mvsu.min.js"></script>
<div id="tribe-events-content" class="tribe-events-single vevent hentry">

	<p class="tribe-events-back">
		<a href="<?php echo tribe_get_events_link() ?>"> <?php _e( '&laquo; All Events', 'tribe-events-calendar' ) ?></a>
	</p>

	<!-- Notices -->
	<?php tribe_events_the_notices() ?>

	<?php the_title( '<h2 class="tribe-events-single-event-title summary entry-title">', '</h2>' ); ?>
	

	<div class="tribe-events-schedule updated published tribe-clearfix">
		<?php echo tribe_events_event_schedule_details( $event_id, '<h3>', '</h3>' ); ?>
		<?php echo ('<div class="tribe-events-venue-details"><h3>'.tribe_get_meta( 'tribe_event_venue_name' ).'</h3></div>'); ?>
		<?php if ( tribe_get_cost() ) : ?>
			<span class="tribe-events-divider">|</span>
			<span class="tribe-events-cost"><?php echo tribe_get_cost( null, true ) ?></span>
		<?php endif; ?>
	</div>

	<div class="teilnahme">
	<?php 
	$gemeinschaft = tribe_event_in_category ("gemeinschaft",$eventid);
	$probe = tribe_event_in_category ("probe",$eventid);
	$registerprobe = tribe_event_in_category ("registerprobe",$eventid);
	$staendchen = tribe_event_in_category ("staendchen",$eventid);
	$auftritt = tribe_event_in_category ("auftritt",$eventid);
	$kirchlich = tribe_event_in_category("kirchlich",$eventid);
	$kommunal = tribe_event_in_category("kommunal",$eventid);
	$cat=$auftritt||$gemeinschaft||$probe||$registerprobe||$staendchen||$kirchlich||$kommunal;
	$zugriff=0;
	if(current_user_can("update_core")){
		$zugriff=1;
	}
	$current_user = wp_get_current_user();
	$uid = $current_user->ID;
    global $userAccessManager;

    if (isset($userAccessManager)) {
        $userGroupHandler = $userAccessManager->getUserGroupHandler();
        $userGroupsForUser = $userGroupHandler->getUserGroupsForObject(\UserAccessManager\Object\ObjectHandler::GENERAL_USER_OBJECT_TYPE,$uid);
        foreach($userGroupsForUser as $element){
            if($element->getId()==2){
				$zugriff=1;
			}
       	}
    }

	$eventdate = tribe_get_start_date( $eventid, true, "U" );
	include "ajax/variables.php";
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
	
	<script type="text/javascript">get_participation(<?php echo $uid ?>,<?php the_ID() ?>);</script>
	<div id="teilnahme-<?php the_ID() ?>" class="teilnahme_buttons">
	<button class="teilnahme_button" type="button" onclick="teilnahme(<?php echo $uid ?>,true,<?php the_ID() ?>,<?php echo $zugriff ?>);">Teilnehmen</button>
	</div>
	<div id="absage-<?php the_ID() ?>" class="absage_buttons">
	<button class="teilnahme_button" type="button" onclick="absagen(<?php echo $uid ?>,<?php the_ID() ?>,<?php echo $zugriff ?>);">Absagen</button>
	</div>
	<div id="popup-<?php the_ID() ?>" class="overlayHidden">
		
		
		<label for="grund">Grund </label>
		<textarea class="grund_area" id="grund-<?php the_ID() ?>" cols="50" rows="3"></textarea>
		<div class="hinweis" id="hinweis-<?php the_ID() ?>"></div>
		<button id="absage-button" onclick="teilnahme(<?php echo $uid ?>,false,<?php the_ID() ?>,true);">Absagen</button>
		<button id="schliessen" onclick="document.getElementById('popup-<?php the_ID() ?>').className='overlayHidden';">X</button>
	</div>

	<?php endif; ?>

	</div>
	<div class="clear"></div>
	<?php while ( have_posts() ) :  the_post(); ?>
		<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<!-- Event featured image, but exclude link -->
			<?php echo tribe_event_featured_image( $event_id, 'full', false ); ?>

			<!-- Event content -->
			<?php do_action( 'tribe_events_single_event_before_the_content' ) ?>
			<?php if(is_user_logged_in()||!$staendchen) : ?>
			<div class="tribe-events-single-event-description tribe-events-content entry-content description">
				<?php the_content(); ?>
			</div>
			<?php endif; ?>
			<!-- .tribe-events-single-event-description -->
			<?php do_action( 'tribe_events_single_event_after_the_content' ) ?>

			<!-- Event meta -->
			<?php do_action( 'tribe_events_single_event_before_the_meta' ) ?>
			<?php
			/**
			 * The tribe_events_single_event_meta() function has been deprecated and has been
			 * left in place only to help customers with existing meta factory customizations
			 * to transition: if you are one of those users, please review the new meta templates
			 * and make the switch!
			 */
			if ( ! apply_filters( 'tribe_events_single_event_meta_legacy_mode', false ) ) {
				tribe_get_template_part( 'modules/meta' );
			} else {
				echo tribe_events_single_event_meta();
			}
			?>
			<?php do_action( 'tribe_events_single_event_after_the_meta' ) ?>
		</div> <!-- #post-x -->
		<?php if ( get_post_type() == TribeEvents::POSTTYPE && tribe_get_option( 'showComments', false ) ) comments_template() ?>
	<?php endwhile; ?>

	<div id="teilnahme_stats" class="teilnahme_stats">
	<?php 
	if(is_user_logged_in()&&$cat&&$zugriff) : ?>
	<script type="text/javascript">get_num_participations(<?php the_ID() ?>);</script>
	<div class="teilnahme_stat">
	<div class="teilnahme_stat_text">Anmeldungen: </div><div id="zusagen" class="teilnahme_stat_num"></div>
	</div>
	<div class="teilnahme_stat">
	<div class="teilnahme_stat_text">Absagen: </div><div id="absagen" class="teilnahme_stat_num"></div>
	</div>
	<br />
	<div class="exp-col-content-holder"><a class="expand-cnt-link" href="#" onclick="get_participating_names(<?php the_ID() ?>);">Details</a>
	<div class="hidden-content"><div id="teilnahme_detail">Loading....</div>
	</div>	
	</div>

	<div id="popup" class="overlayHidden">
		<div id="teilnahme-name"></div>
		<button id="zusage-button" onclick="teilnahme(<?php echo $uid ?>,true,<?php the_ID() ?>,true,1);">Zusagen</button>
		<button id="absage-button" onclick="teilnahme(<?php echo $uid ?>,false,<?php the_ID() ?>,true,1);">Absagen</button>
		<button id="schliessen" onclick="document.getElementById('popup').className='overlayHidden';">X</button>
	</div>

	<?php endif; ?>
	</div>
	</br>
	<!-- Event footer -->
	<div id="tribe-events-footer">
		<!-- Navigation -->
		<!-- Navigation -->
		<h3 class="tribe-events-visuallyhidden"><?php _e( 'Event Navigation', 'tribe-events-calendar' ) ?></h3>
		<ul class="tribe-events-sub-nav">
			<li class="tribe-events-nav-previous"><?php tribe_the_prev_event_link( '<span>&laquo;</span> %title%' ) ?></li>
			<li class="tribe-events-nav-next"><?php tribe_the_next_event_link( '%title% <span>&raquo;</span>' ) ?></li>
		</ul>
		<!-- .tribe-events-sub-nav -->
	</div>
	<!-- #tribe-events-footer -->

</div><!-- #tribe-events-content -->
