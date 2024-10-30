<span style="display:none"><?php echo $idcrm_event_timestring; ?></span>

<?php
if ( ! empty( $contact_events ) ) {
    foreach ( $contact_events as $contact_event ) {
        $event_type         = get_the_terms( $contact_event->ID, 'contact_events' );
        if ($event_type !== false) {
            $term_id = $event_type[0]->term_taxonomy_id;
            $custom_icon_type = !empty(get_term_meta($term_id,'custom_icon_type', true)) ? get_term_meta($term_id,'custom_icon_type', true) : false;
            $formatted_custom_icon_type = $custom_icon_type !== false ? $custom_icon_type : 'note';
            $formatted_custom_icon_type = str_replace("icon-", "", $formatted_custom_icon_type);
            $formatted_custom_icon_type = ($formatted_custom_icon_type) ?: 'note';
            echo '<i class="icon-' . esc_attr($formatted_custom_icon_type) . '"></i>&nbsp;';
            // foreach ( $event_type as $e_type ) {
            // 	if ( $event_type_call === $e_type->name ) {
            // 		echo '<i class="icon-phone"></i> ';
            // 	} elseif ( $event_type_bill === $e_type->name ) {
            // 		echo '<i class="icon-doc"></i> ';
            // 	} elseif ( $event_type_meeting === $e_type->name ) {
            // 		echo '<i class="icon-people"></i> ';
            // 	} elseif ( $event_type_mail === $e_type->name ) {
            // 		echo '<i class="icon-envelope-letter"></i> ';
            // 	} else {
            // 		echo '<i class="icon-clock"></i> ';
            // 	}
            // }
            // Display event date.
            $event_dates = get_post_meta( $contact_event->ID, 'idcrm_event_timestring', true );
                if ( ! empty( $event_dates ) ) {
                    echo date_i18n('d.m.Y H:i', $event_dates );
                }
            // Display event title.
            echo ' ' . esc_html( $contact_event->post_content );
        }

    }
}
?>
