<?php

use idcrm\idCRM;
use idcrm\includes\actions\idCRMActionLanguage;

/**
 * Comments loop template part
 */

// $current_post_id = $post_id ? $post_id : $post->ID;
$current_post_id = get_query_var( 'current_loop_id' ) ?: get_query_var( 'current_post_id' );
$post_type = get_query_var( 'post_type' ) ?: get_query_var( 'post_type' );
include idCRM::$IDCRM_PATH . 'templates/inc/icons.php';
/* $idcrm_company_id = get_post_meta( $current_post_id, 'idcrm_company_id', true ); */
/* $idcrm_contact_user_id = get_post_meta( $current_post_id, 'idcrm_contact_user_id', true ); */

/* $is_company = $idcrm_company_id ? true : false; */
/* $key = $idcrm_company_id ? 'idcrm_company_id' : 'idcrm_contact_user_id'; */
/* $client_id = $idcrm_company_id ?: $idcrm_contact_user_id; */

$current_mailbox = isset($_COOKIE["mailbox"]) && !empty($_COOKIE["mailbox"]) ? $_COOKIE["mailbox"] : 0;
$meta_query = !empty($post_type) ? [['meta_key' => 'idcrm_post_type', 'value' => $post_type]] : '';

$idcrm_comments = get_posts(
  array(
    'numberposts' => -1,
    'post_type'   => 'idcrm_comments',
    'meta_query' => [
        [
        'key' => 'idcrm_contact_user_id',
        'value' => $current_post_id,
        ],
    ],
    'orderby'     => 'post_date',
    'order'       => 'DESC',
    'fields' => 'ids',
    // 'meta_query' => $meta_query
  )
);

function idcrm_trunc($phrase, $max_words, $id) {
  $position = strpos($phrase, '<blockquote');

  if ($position !== false) {
      $result = substr($phrase, 0, $position);
      return $result;
  } else {
    $phrase_array = explode(' ', $phrase);
    $allowed_tags = array(
        'p' => array(),
        'br' => array(),
        'blockquote' => array(),
    );

    $phrase = wp_kses($phrase, $allowed_tags);
    if (count($phrase_array) > $max_words && $max_words > 0) {
       $phrase = implode(' ', array_slice($phrase_array, 0, $max_words)) . '...';
     }
    return $phrase;
  }
}

function wrap_urls_with_links($text) {
  $pattern = '/(?<!href=["\'])\bhttps?:\/\/\S+\b(?!<\/a>)/i';

  // Replace URLs with links
  $text_with_links = preg_replace($pattern, '<a target="_blank" href="$0">$0</a>', $text);

  return $text_with_links;
}

if ( !empty( $idcrm_comments ) ) {
  foreach ( $idcrm_comments as $contact_event ) {
    $timestring = get_post_meta( $contact_event, 'idcrm_event_timestring', true ) ?: array();
    $is_email = get_post_meta( $contact_event, 'idcrm_is_email', true ) ?: 0;
    $idcrm_mailbox = get_post_meta( $contact_event, 'idcrm_mailbox', true ) ?: 0;
    $has_attachment = get_post_meta( $contact_event, 'idcrm_has_attachment', true ) ?: 0;
    $idcrm_comment_editable = get_post_meta( $contact_event, 'idcrm_comment_editable', true );
    $is_editable = isset($idcrm_comment_editable) && !empty($idcrm_comment_editable) ? $idcrm_comment_editable : true;
    $user_id = get_post_field( 'post_author', $contact_event );

    $is_comment_seen = get_post_meta($contact_event, 'idcrm_is_seen_' . get_current_user_id(), true);

    $is_removable = get_post_meta( $contact_event, 'idcrm_comment_removable', true ) ?: 1;
    $is_removable = current_user_can( 'manage_options' ) || get_current_user_id() == $user_id || get_post_field( 'post_author', $current_post_id ) == get_current_user_id() ? $is_removable : 0;


    $icons_list = '';
    if ($post_type == 'idcrm_task') {

      $filenames = get_post_meta( $contact_event, 'idcrm_document_filenames', true ) ?: '';

      if (isset($filenames)) {
        $filenames = unserialize($filenames);

        if (is_array($filenames)) {
          foreach ($filenames as $name) {
            $filetype = wp_check_filetype($name[0]);
            // echo $filetype['ext'];
            $svg_icon = array_key_exists($filetype['ext'], $icons_array) ? $icons_array[$filetype['ext']] : $icons_array['other'];
            $icons_list .= '<a target="_blank" title="' . $name[0] . '" href="' . $name[1] . '">' . $svg_icon . '</a> ';
          }
        }
      }
    }



    $comment_type = get_post_meta( $contact_event, 'idcrm_comment_type', true ) ?: '';

    $hide_class = '';


    $user_img  = get_user_meta($user_id, 'userimg', true);

    if ($is_email == 1) {
        $idcrm_email_user_id = get_post_meta( $contact_event, 'idcrm_user_image', true ) ?: '';
        $user_img = $idcrm_email_user_id ?: $user_img;
        //$hide_class = $is_email == 1 && $current_mailbox == $idcrm_mailbox ? '' : 'd-none';
    }

    if ( empty( $user_img ) ) {
      $user_img = idCRM::$IDCRM_URL . 'templates/images/no-user.jpg';
    }

    $attachment_svg = '<svg width="13" height="13" class="attachment-svg" version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
       viewBox="0 0 280.067 280.067" xml:space="preserve"><g><path style="fill:#90a4ae;" d="M149.823,257.142c-31.398,30.698-81.882,30.576-113.105-0.429
        c-31.214-30.987-31.337-81.129-0.42-112.308l-0.026-0.018L149.841,31.615l14.203-14.098c23.522-23.356,61.65-23.356,85.172,0
        s23.522,61.221,0,84.586l-125.19,123.02l-0.044-0.035c-15.428,14.771-40.018,14.666-55.262-0.394
        c-15.244-15.069-15.34-39.361-0.394-54.588l-0.044-0.053l13.94-13.756l69.701-68.843l13.931,13.774l-83.632,82.599
        c-7.701,7.596-7.701,19.926,0,27.53s20.188,7.604,27.88,0L235.02,87.987l-0.035-0.026l0.473-0.403
        c15.682-15.568,15.682-40.823,0-56.39s-41.094-15.568-56.776,0l-0.42,0.473l-0.026-0.018l-14.194,14.089L50.466,158.485
        c-23.522,23.356-23.522,61.221,0,84.577s61.659,23.356,85.163,0l99.375-98.675l14.194-14.089l14.194,14.089l-14.194,14.098
        l-99.357,98.675C149.841,257.159,149.823,257.142,149.823,257.142z"/></g></svg>';
    $attachment_dropdown = '';
    $words_count = 30;
    $post_content = '';

    $p_data = get_post($contact_event);
    if ($p_data) {
      $post_content = $p_data->post_content;
    }

    $cut_post_content = idcrm_trunc($post_content, $words_count, $contact_event);
    $has_readmore = count(explode(' ', $post_content)) >= $words_count ? true : false;
    $add_readmore = '<span data-id="' . $contact_event . '" class="show-more-text"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-more-horizontal feather-sm"><circle cx="12" cy="12" r="1"></circle><circle cx="19" cy="12" r="1"></circle><circle cx="5" cy="12" r="1"></circle></svg><span>';

    $formatted_custom_icon_type = $is_email != 1 ? 'note' : 'envelope-letter';
    $formatted_custom_icon_type = $comment_type === 'checklist' ? 'check' : $formatted_custom_icon_type;


    $current_user_id = get_current_user_id();
    // $incoming_mail = $user_id == $current_user_id ? false : true;
    $incoming_mail = true;
    $idcrm_contact_email = get_post_meta( get_the_ID(), 'idcrm_contact_email', true );

    /* echo '$user_id: ' . print_r($user_id, true) . '^<br />'; */
    $manager = get_userdata($user_id);
    /* echo '$manager: ' . print_r($manager, true) . '^<br />'; */
    $comment_author = esc_html__( 'Deleted user', idCRMActionLanguage::TEXTDOMAIN );

    $idcrm_contact_surname = get_post_meta( $user_id, 'idcrm_contact_surname', true );
    $idcrm_use_surname = get_post_meta( $user_id, 'idcrm_use_surname', true );
    $surname = $idcrm_contact_surname && $idcrm_use_surname == 'yes' ? $idcrm_contact_surname : '';

    $username = '';

    if ( $manager !== false ) {
      $first_name = isset($manager->first_name) ? $manager->first_name : '';
      $last_name = isset($manager->last_name) ? $manager->last_name : '';
      $username = isset($manager->first_name) ? $manager->first_name : $manager->display_name;
      $comment_author = !$first_name && !$last_name ? $manager->display_name : $first_name . ' ' . $last_name;
    } ?>

    <ul data-is-seen="<?php echo $is_comment_seen || get_post_field( 'post_author', $contact_event ) == get_current_user_id() ? 'yes' : ''; ?>" data-id="<?php echo  $contact_event; ?>" class="timeline timeline-left comment-item <?php echo $hide_class; ?> <?php echo !$is_comment_seen && get_post_field( 'post_author', $contact_event ) != get_current_user_id() ? 'not-seen' : ''; ?>" data-type="<?php echo $comment_type; ?>" data-timestring="<?php echo strtotime(get_the_date('Y-m-d H:i:s', $contact_event)) * 1000; ?>">
      <li class="timeline-inverted timeline-item current-comment-<?php echo $contact_event; ?>">
        <div class="timeline-badge">
          <img src="<?php echo esc_url($user_img); ?>" width="50" height="50" class="rounded-circle" style="margin-right: 10px">
        </div>
        <div class="timeline-panel">

        <?php if ( $is_email != 1 ) { ?>
          <?php if ( $is_editable === 1 || $is_removable === 1 ) { ?>
          <div class="edit">
            <div class="dropdown dropstart">
              <a href="#" class="link" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-more-horizontal feather-sm"><circle cx="12" cy="12" r="1"></circle><circle cx="19" cy="12" r="1"></circle><circle cx="5" cy="12" r="1"></circle></svg>
              </a>
              <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">

              <?php if ($is_editable === 1) {
                echo '<li>
                  <span class="dropdown-item">
                    <a  class="edit-comment" data-id="' . $contact_event . '" data-is-comment="1" data-event-id="' . $contact_event . '" href="#">' . esc_html__( 'Edit', idCRMActionLanguage::TEXTDOMAIN ) . '</a>
                  </span>
                </li>';
              }

              if ($is_removable === 1) {
                echo '<li>
                  <span class="dropdown-item">
                    <a  class="delete-comment" data-id="' . $contact_event . '" href="#">' . esc_html__( 'Delete', idCRMActionLanguage::TEXTDOMAIN ) . '</a>
                  </span>
                </li>';
              }


              echo '</ul>
            </div>
          </div>';
          }
        } else {

          if ($incoming_mail) { ?>
            <div class="edit">
              <div class="dropdown dropstart">
                <a href="#" class="link" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-more-horizontal feather-sm"><circle cx="12" cy="12" r="1"></circle><circle cx="19" cy="12" r="1"></circle><circle cx="5" cy="12" r="1"></circle></svg>
                </a>
                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                  <li>
                    <span class="dropdown-item">
                      <a class="reply-from-timeline"
                        data-time="<?php echo esc_attr(date("d.m.Y, H:i", strtotime(get_the_date('Y-m-d H:i:s', $contact_event)))); ?>"
                        data-fullname="<?php echo esc_attr($comment_author); ?>"
                        data-mailbox="<?php echo esc_attr($current_mailbox); ?>"
                        data-email="<?php echo esc_attr($idcrm_contact_email); ?>"
                        data-subject="<?php echo esc_attr(get_the_title($contact_event)); ?>"
                        data-name="<?php echo esc_attr($username); ?>"
                        data-surname="<?php echo esc_attr($surname); ?>"
                        data-id="<?php echo esc_attr($contact_event); ?>"
                        data-current-user-id="<?php echo esc_attr__($current_user_id); ?>"
                        href="#">
                          <?php echo esc_html__( 'Reply', idCRMActionLanguage::TEXTDOMAIN ); ?>
                      </a>
                    </span>
                  </li>
                  <li>
                    <span class="dropdown-item">
                      <a class="forward-from-timeline"
                        data-time="<?php echo esc_attr(date("d.m.Y, H:i", strtotime(get_the_date('Y-m-d H:i:s', $contact_event)))); ?>"
                        data-fullname="<?php echo esc_attr($comment_author); ?>"
                        data-mailbox="<?php echo esc_attr($current_mailbox); ?>"
                        data-email="<?php echo esc_attr($idcrm_contact_email); ?>"
                        data-subject="<?php echo esc_attr(get_the_title($contact_event)); ?>"
                        data-name="<?php echo esc_attr($username); ?>"
                        data-surname="<?php echo esc_attr($surname); ?>"
                        data-id="<?php echo esc_attr($contact_event); ?>"
                        data-current-user-id="<?php echo esc_attr($current_user_id); ?>"
                        href="#">
                          <?php echo esc_html__( 'Forward', idCRMActionLanguage::TEXTDOMAIN ); ?>
                      </a>
                    </span>
                  </li>
                </ul>
              </div>
            </div>
          <?php }

          if ($has_attachment == 1) {

          $idcrm_files = get_post_meta( $contact_event, 'idcrm_files', true ) ? json_decode(get_post_meta( $contact_event, 'idcrm_files', true )) : [];

          // require_once(ABSPATH . 'wp-admin/includes/file.php');

          $attachment_dropdown .= '<div class="edit show-attachment">
            <div class="dropdown dropstart">
              <a href="#" class="link" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">'
                . $attachment_svg
              . '</a>
              <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">';

              foreach ($idcrm_files as $file) {

                $file_url = $file->file_url;
                $filename = $file->name;

                $attachment_dropdown .= '<li>
                  <span class="dropdown-item">
                    <a target="_blank" href="' . $file_url . '">' . $filename . '</a>
                  </span>
                </li>';

              }


              $attachment_dropdown .= '</ul>
            </div>
          </div>';
          }
        }

          echo '<div class="timeline-heading">
            <span class="timeline-title"><small class="me-2 text-muted"><i class="icon-' . esc_attr($formatted_custom_icon_type) . ' events-item-inner"></i></small>' . esc_html($comment_author) . '</span>
            <small class="text-muted">' . date("d.m.Y, H:i", strtotime(get_the_date('Y-m-d H:i:s', $contact_event)))
            . '</small>' . ($is_email == 1 ? ' - ' . get_the_title($contact_event) : '')
            . ' <small class="text-muted">'
            . ($has_attachment ? $attachment_dropdown : "")
            . '</small></div>
          <div class="timeline-body">
            <div class="timeline-title">
              <div data-id="' . $contact_event . '" class="comment-text cut-comment-text" contenteditable="false">' . ($is_email != 1 ? nl2br(wrap_urls_with_links($post_content)) : $post_content) . '</div>';

              if ($icons_list) {
                echo '<div class="comment-text icons-list">' . $icons_list . '</div>';
              }

              echo ($has_readmore ? $add_readmore : "") . '
            </div>
          </div>
          <div class="comment-edit-area"></div>';

          if ($post_type == 'idcrm_task') {

            echo '<div class="comment-likes">';
            $idcrm_likes = unserialize(get_post_meta($contact_event, 'idcrm_likes', true) ?: 'a:0:{}');
            $liked = '';

            if (!empty($idcrm_likes)) {

              echo '<div class="comment-likes--list">';
              foreach ($idcrm_likes as $like) {

                $liked = $like == get_current_user_id() ? 'liked' : $liked;

                  $user_img = get_user_meta($like, 'userimg', true);
                  $default_image = idCRM::$IDCRM_URL . 'templates/images/no-user.jpg';

                  $userdata = get_userdata($like);
                  $responsible_name = '';
                  if ( $userdata !== false ) {
                    $responsible_name = isset($userdata->first_name) && !empty($userdata->first_name) ? $userdata->first_name : $userdata->display_name;
                    $responsible_name = isset($userdata->last_name) && !empty($userdata->last_name) ? $responsible_name . ' ' . $userdata->last_name : $responsible_name;
                  }

                  echo '<img src="' . (empty($user_img) ? esc_html($default_image) : esc_html($user_img)) . '" width="25" height="25" class="rounded-circle me-1 object-fit-cover comment-likes--item" title="' . $responsible_name . '">';

              }

              echo '</div>';
            }

            echo '<div class="comment-likes-add ms-1 ' . $liked . '" data-comment-id="' . $contact_event . '" title="' . esc_html__( 'Like', idCRMActionLanguage::TEXTDOMAIN ) . '"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-thumbs-up feather-icon"><path d="M14 9V5a3 3 0 0 0-3-3l-4 9v11h11.28a2 2 0 0 0 2-1.7l1.38-9a2 2 0 0 0-2-2.3zM7 22H4a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h3"></path></svg></div>';

            echo '</div>';
          }

        echo '</div>
      </li>
    </ul>';
  }
}

include_once ABSPATH . 'wp-admin/includes/plugin.php';

if ( is_plugin_active( 'idcrm-contacts-companies-pro/idcrm-contacts-companies-pro.php' ) ) {
  // include WP_PLUGIN_DIR . '/idcrm-contacts-companies-pro/templates/inc/mail-compose-button.php';
  include WP_PLUGIN_DIR . '/idcrm-contacts-companies-pro/templates/inc/mail-compose-part.php';
}
