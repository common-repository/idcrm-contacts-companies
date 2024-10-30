<?php

use idcrm\idCRM;
use idcrm\includes\actions\idCRMActionLanguage;

?>
<?php
function idcrm_comments2() {
    $args                = get_comment(get_comment_ID());
    $user_id             = $args->user_id;
    $user_img            = get_user_meta($user_id, 'userimg', true);
    $comment_author_name = $args->comment_author;
    $default_image       = idCRM::$IDCRM_URL . 'templates/images/no-user.jpg';
    $comment_type = get_comment_meta( get_comment_ID(), 'idcrm_comment_type', true ) ?: '';
    $icon_type = $comment_type_text = $idcrm_comment_event_id = '';
    $is_event = false;
    print_r($user_id);
    switch ($comment_type) {
        case 'email':
            $icon_type = "icon-envelope-letter";
            break;
        case 'comment':
            $icon_type = "icon-note";
            break;
        default:
            $icon_type = "icon-note";
    }
    switch ($comment_type) {
        case 'email':
            $comment_type_text = esc_html__( 'E-mail', idCRMActionLanguage::TEXTDOMAIN );
            break;
        case 'comment':
            $comment_type_text = esc_html__( 'Comment', idCRMActionLanguage::TEXTDOMAIN );
            break;
        default:
            $comment_type_text = esc_html__( 'Comment', idCRMActionLanguage::TEXTDOMAIN );
    }
    if (is_numeric($comment_type)) {
        $custom_icon_type = get_option( "taxonomy_$comment_type" );
        $formatted_custom_icon_type = str_replace(".icon-", "", $custom_icon_type['custom_icon_type']);
        $formatted_custom_icon_type = str_replace("icon-", "", $formatted_custom_icon_type);
        $formatted_custom_icon_type = ($formatted_custom_icon_type) ?: 'note';
        $icon_type = "icon-" . $formatted_custom_icon_type;
        $type_text = get_term_by('id', $comment_type, 'contact_events');
        $comment_type_text = $type_text->name;
        $is_event = true;
        $idcrm_comment_event_id = get_comment_meta( get_comment_ID(), 'idcrm_comment_event_id', true ) ?: '';
    }
    echo '<ul class="timeline timeline-left">
        <li class="timeline-inverted timeline-item current-comment-' . get_comment_ID() . '">
            <div class="timeline-badge">';
            if (! empty($user_img) ) {
                echo '<img src="' . esc_html($user_img) . '" width="50" height="50" class="rounded-circle" style="margin-right: 10px">';
            } else {
                echo '<img src="' . esc_html($default_image) . '" width="50" height="50" class="rounded-circle" style="margin-right: 10px">';
            }
            echo '</div>
                <div class="timeline-panel">
                    <div class="edit">
                        <div class="dropdown dropstart">
                        <a href="#" class="link" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                            <i data-feather="more-horizontal" class="feather-sm"></i>
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <li>
                                <span class="dropdown-item">
                                    <a  class="edit-comment" data-id="' . get_comment_ID() . '" data-event-id="' . $idcrm_comment_event_id . '" href="#">' . esc_html__( 'Edit', idCRMActionLanguage::TEXTDOMAIN ) . '</a>
                                </span>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="timeline-heading">
                    <span class="timeline-title">' . esc_html($comment_author_name) . '</span>
                    <small class="text-muted">
                        <!--i class="icon-clock"> </i-->' . esc_html(get_comment_date('j F Y, H:i')) . '
                    </small>
                </div>
                <div class="timeline-body">
                    <p><small class="me-2 text-muted"><i class="' . esc_attr($icon_type) . '"></i></small><span class="comment-text">' . esc_html(get_comment_text()) . '</span></p>
                </div>
                <div class="comment-edit-area"></div>
            </div>
        </li>
    </ul>';
}
?>
<!-- <div id="comments" class="comments-area default-max-width <?php echo get_option('show_avatars') ? 'show-avatars' : ''; ?>"> -->
<?php if (have_comments() ) : ; ?>
    <?php
        $args = array(
            'short_ping' => true,
            'reverse_top_level' => true,
            'callback' => 'idcrm_comments2',
        );
        wp_list_comments($args);
    ?>
    <!-- .comment-list -->
    <?php if ( !comments_open() ) : ?>
        <p class="no-comments">
            <?php esc_html_e('Comments are closed.', idCRMActionLanguage::TEXTDOMAIN); ?>
        </p>
    <?php endif; ?>
<?php endif; ?>

<!-- </div> -->
<!-- #comments -->
