<?php
/**
 * Comments loop template part
*/

$current_post_id = get_query_var( 'current_post_id' );
$post_id = get_query_var( 'post_id' );
$timestring = get_post_meta( $current_post_id, 'idcrm_event_timestring', true ) ?: array();
$event_type = get_the_terms( $current_post_id, 'contact_events' );
$term_id = $event_type[0]->term_id;
$term_name = $event_type[0]->name;
$custom_icon_type = !empty(get_term_meta($term_id,'custom_icon_type', true)) ? get_term_meta($term_id,'custom_icon_type', true) : false;
$formatted_custom_icon_type = $custom_icon_type !== false ? $custom_icon_type : 'note';
$formatted_custom_icon_type = str_replace("icon-", "", $formatted_custom_icon_type);
$formatted_custom_icon_type = ($formatted_custom_icon_type) ?: 'note';
$schedulePost = get_post($current_post_id);

echo '<div class="events-item" data-timestring="' . $timestring . '" id="wrapper-event-' . $current_post_id . '">
    <input type="checkbox" class="check-delete-event form-check-input flex-shrink-0" data-author="' . get_post_meta( $current_post_id, 'idcrm_contact_user_id', true ) . '" data-comment-post-id="' . $post_id . '" data-current-user-id="' . get_current_user_id() . '" data-id="' . $current_post_id . '" id="event-' . $current_post_id . '">
    <i class="icon-pencil edit-event-icon d-none" data-bs-toggle="modal" data-bs-target="#editEventModal" data-date="' . gmdate('d.m.Y', $timestring ) . '" data-time="' . gmdate('H:i', $timestring ) . '" data-timestring="' . $timestring . '" data-event-id="' . $current_post_id . '" data-type="' . $term_id . '" data-title="' . esc_html( $schedulePost->post_content ) . '"></i>
    <label for="event-' . $current_post_id . '">
        <div class="event-icon" title="' . $term_name . '">
            <i class="icon-' . esc_attr($formatted_custom_icon_type) . ' events-item-inner"></i>
        </div>
        <div class="events-item-inner event-text">
            <span class="event-time small">' . gmdate('d.m.Y H:i', $timestring ) . '</span>
            <span class="event-title title-' . $current_post_id . '">' . esc_html( $schedulePost->post_content ) . '</span>
        </div>
    </label>
</div>';
