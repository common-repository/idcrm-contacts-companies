<?php

use idcrm\idCRM;
use idcrm\includes\actions\idCRMActionLanguage;

/**
 * Comments loop template part
 */
$current_post_id = get_query_var( 'current_loop_id' ) ?: get_query_var( 'current_post_id' );
$idcrm_company_id = get_post_meta( $current_post_id, 'idcrm_company_id', true );
$idcrm_contact_user_id = get_post_meta( $current_post_id, 'idcrm_contact_user_id', true );
$is_company = $idcrm_company_id ? true : false;
$key = $idcrm_company_id ? 'idcrm_company_id' : 'idcrm_contact_user_id';
$client_id = $idcrm_company_id ?: $idcrm_contact_user_id;
$more = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-more-horizontal feather-sm"><circle cx="12" cy="12" r="1"></circle><circle cx="19" cy="12" r="1"></circle><circle cx="5" cy="12" r="1"></circle></svg>';
$contact_events = get_posts(
  array(
    'numberposts' => 20,
    'post_type'   => 'contact_event',
    'meta_query' => [
      [
      'key' => 'idcrm_event_status',
      'value' => 'finished',
      ],
      [
      'key' => 'idcrm_contact_user_id',
      'value' => $current_post_id,
      ],
    ],
    'orderby'     => 'post_date',
    'order'       => 'DESC',
  )
);
if ( ! empty( $contact_events ) ) {
  foreach ( $contact_events as $contact_event ) {
    $timestring = get_post_meta( $contact_event->ID, 'idcrm_event_timestring', true ) ?: array();
    $event_type = get_the_terms( $contact_event->ID, 'contact_events' );
    $term_id = $event_type[0]->term_id;
    $custom_icon_type = !empty(get_term_meta($term_id,'custom_icon_type', true)) ? get_term_meta($term_id,'custom_icon_type', true) : false;
    $formatted_custom_icon_type = $custom_icon_type !== false ? $custom_icon_type : 'note';
    $formatted_custom_icon_type = str_replace("icon-", "", $formatted_custom_icon_type);
    $formatted_custom_icon_type = ($formatted_custom_icon_type) ?: 'note';
    $user_id = $contact_event->post_author;
    $user_img = get_user_meta($user_id, 'userimg', true);
    $default_image = idCRM::$IDCRM_URL . 'templates/images/no-user.jpg';
    $manager = get_userdata($user_id);
    $comment_author = (isset($manager->user_firstname) && !empty($manager->user_firstname) && isset($manager->user_lastname)) ? $manager->user_firstname . ' ' . $manager->user_lastname : $manager->user_login;
    $post_content = (isset($contact_event->post_content) && !empty($contact_event->post_content)) ? '<span class="comment-text">' . esc_html($contact_event->post_content) . '</span>' : '';
    echo '<ul class="timeline timeline-left" data-timestring="' . strtotime($contact_event->post_date)*1000 . '">
      <li class="timeline-inverted timeline-item current-comment-' . $contact_event->ID . '">
        <div class="timeline-badge">';
          if (! empty($user_img) ) {
            echo '<img src="' . esc_html($user_img) . '" width="50" height="50" class="rounded-circle" style="margin-right: 10px">';
          } else {
            echo '<img src="' . esc_html($default_image) . '" width="50" height="50" class="rounded-circle" style="margin-right: 10px">';
          }
          echo '
        </div>
        <div class="timeline-panel">';
          if ($term_id) {
            echo '<div class="edit">
              <div class="dropdown dropstart">
                <a href="#" class="link" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-more-horizontal feather-sm"><circle cx="12" cy="12" r="1"></circle><circle cx="19" cy="12" r="1"></circle><circle cx="5" cy="12" r="1"></circle></svg>
                </a>
                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                  <li>
                    <span class="dropdown-item">
                      <a  class="edit-comment" data-is-comment="1" data-id="' . $contact_event->ID . '" data-event-id="' . $contact_event->ID . '" href="#">' . esc_html__( 'Edit', idCRMActionLanguage::TEXTDOMAIN ) . '</a>
                    </span>
                  </li>
                </ul>
              </div>
            </div>';
          }
          echo '<div class="timeline-heading">
            <span class="timeline-title">
              <small class="me-2 text-muted">
                <i class="icon-' . esc_attr($formatted_custom_icon_type) . ' events-item-inner"></i>
              </small>' . esc_html($comment_author) . '
            </span>
            <small class="text-muted">' . date("d.m.Y, H:i", strtotime($contact_event->post_date)) . '</small>
          </div>
          <div class="timeline-body">
            <!--<div class="timeline-title">' . esc_html( implode(' ', array_slice(explode(' ', $contact_event->post_content), 0, 3) ) ) . '</div>-->
            <div class="timeline-mail-short" contenteditable="false">' . wp_trim_words( strip_tags($post_content), 20, '<a class="show-mail-more" href="#">' . $more . '</a>' ) . '</div>
            <div class="timeline-mail-full display-none" contenteditable="false"><span class="comment-text">' . strip_tags($post_content) . '</span><a class="show-mail-less" href="#">' . $more . '</a></div>
          </div>
          <div class="comment-edit-area"></div>
        </div>
      </li>
    </ul>';
  }
} else {
  //echo esc_html__( 'There are no events in timeline yet', idCRMActionLanguage::TEXTDOMAIN );
}

?>
