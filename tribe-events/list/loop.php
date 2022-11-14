<?php
/**
 * List View Loop
 * This file sets up the structure for the list loop
 *
 * Override this template in your own theme by creating a file at [your-theme]/tribe-events/list/loop.php
 *
 * @package TribeEventsCalendar
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
} ?>

<?php
global $more;
$more = false;
?>

<script type="text/javascript" src="/wp-content/themes/mvsu/mvsu.min.js"></script>
<div class="tribe-events-loop vcalendar">
	<!--<?php $pids = array(); ?>-->
	<?php while ( have_posts() ) : the_post(); ?>
		<?php do_action( 'tribe_events_inside_before_loop' ); ?>
		<?php 
		$pid = get_the_ID();
		$auftritt = tribe_event_in_category ("auftritt",$pid);
		$gemeinschaft = tribe_event_in_category ("gemeinschaft",$pid);
		$probe = tribe_event_in_category ("probe",$pid);
		$registerprobe = tribe_event_in_category ("registerprobe",$pid);
		$staendchen = tribe_event_in_category ("staendchen",$pid);
		$kirchlich = tribe_event_in_category("kirchlich",$pid);
		$kommunal = tribe_event_in_category("kommunal",$pid);
		$cat=$auftritt||$gemeinschaft||$probe||$registerprobe||$staendchen||$kirchlich||$kommunal;
		if($cat){ $pids[] = $pid;}
		?>
		<!-- Month / Year Headers -->
		<?php tribe_events_list_the_date_headers(); ?>

		<!-- Event  -->
		<!--<?php
			$class = "";
			if( tribe_get_start_date( the_ID() , false , "N") > 4 ){
				$class = " weekend";
			}
		?>-->
		<div id="post-<?php the_ID() ?>" class="<?php tribe_events_event_classes() ?> <?php echo $class ?>">
			<?php tribe_get_template_part( 'list/single', 'event' ) ?>
		</div><!-- .hentry .vevent -->


		<?php do_action( 'tribe_events_inside_after_loop' ); ?>
	<?php endwhile; ?>
	<?php
	
	 if(is_user_logged_in()) : ?>
<?php
$ids = implode(",",$pids);
$current_user = wp_get_current_user();
$uid = $current_user->ID;
?>
<script type="text/javascript">get_participations(<?php echo $uid; ?>,<?php echo json_encode($ids); ?>);</script>
<?php endif; ?>
</div><!-- .tribe-events-loop -->
