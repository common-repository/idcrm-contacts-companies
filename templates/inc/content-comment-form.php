<?php

use idcrm\includes\actions\idCRMActionLanguage;

/**
 * Add schedule template part
 */
?>
<div class="p-3">
  <div id="respond" class="comment-respond">
    <h3 id="reply-title" class="comment-reply-title"><?php esc_html_e( 'Write a Comment', idCRMActionLanguage::TEXTDOMAIN ); ?></h3>
    <form action="" method="post" id="commentform" class="comment-form" novalidate="">
      <div>
        <br />
        <textarea class="form-control" id="comment-textarea" placeholder="<?php esc_html_e( 'Comment text', idCRMActionLanguage::TEXTDOMAIN ); ?>" name="comment" aria-required="true" style="margin: -15px 0px 20px 0px;"></textarea>
      </div>
      <p class="form-submit wp-block-button">
        <i class="btn1 waves-effect waves-light btn-rounded btn-outline-info wp-block-button__link waves-input-wrapper" style="">
          <input id="send-comment-button" name="submit" type="button" class="waves-button-input"
            data-post-type="<?php echo get_post_type(); ?>"
            data-current-user-id="<?php echo esc_attr($post->ID); ?>"
            data-post-id="<?php echo esc_attr($post->ID); ?>"
            value="<?php esc_html_e( 'Send', idCRMActionLanguage::TEXTDOMAIN ); ?>">
        </i>
      </p>
    </form>
  </div>

<?php
$comments_args = array(
  'must_log_in'         => '',
  'logged_in_as'        => '',
  // Change the title of send button.
  'label_submit'        => esc_html__( 'Send', idCRMActionLanguage::TEXTDOMAIN ),
  'class_submit'        => 'btn1 waves-effect waves-light btn-rounded btn-outline-info',
  // Change the title of the reply section.
  'title_reply'         => esc_html__( 'Write a Comment', idCRMActionLanguage::TEXTDOMAIN ),
  // Remove "Text or HTML to be displayed after the set of comment fields".
  'comment_notes_after' => '',
  // Redefine your own textarea (the comment body).
  'comment_field'       => '<div>
<br />
<textarea class="form-control" id="comment" placeholder="' . esc_html__( 'Comment text', idCRMActionLanguage::TEXTDOMAIN ) . '" name="comment" aria-required="true" style="margin: -15px 0px 15px 0px;"></textarea>
</div>',
);

// add_action( 'comment_form_logged_in_after', 'post_type_comment_custom_fields' );

function post_type_comment_custom_fields() {
  $post_type = get_post_type();
  echo '<input type="hidden" name="post_type" value="' . $post_type . '">';
  echo '<input type="hidden" name="idcrm_comment_type" value="comment">';
}

// comment_form( $comments_args );

?>
</div>