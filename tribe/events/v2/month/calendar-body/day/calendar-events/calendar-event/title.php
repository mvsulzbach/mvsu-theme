<?php
/**
 * View: Month View - Calendar Event Title
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events/v2/month/calendar-body/day/calendar-events/calendar-event/title.php
 *
 * See more documentation about our views templating system.
 *
 * @link {INSERT_ARTICLE_LINK_HERE}
 *
 * @version 5.0.0
 *
 * @var WP_Post $event The event post object with properties added by the `tribe_get_event` function.
 *
 * @see tribe_get_event() For the format of the event object.
 */

$eventJson = json_encode($event, JSON_UNESCAPED_UNICODE);
$eventJsonEscaped = str_replace('"', '&quot;', $eventJson);
use Tribe__Date_Utils as Dates;
$event_date_attr = $event->dates->start->format( Dates::DBDATEFORMAT );
$has_registration = mvsu_event_has_registration($event->ID);
$initial = "";
if ($has_registration) {
    $initial = getParticipation(get_current_user_id(), $event->ID);
}
?>

<mvsu-month-event
    event_in="<?php echo $eventJsonEscaped; ?>"
    date_attr="<?php echo esc_attr($event_date_attr); ?>"
    has_registration="<?php echo $has_registration ?>"
    initial="<?php echo $initial ?>"
    classes="tribe-events-calendar-month__calendar-event-title-link tribe-common-anchor-thin"
>
