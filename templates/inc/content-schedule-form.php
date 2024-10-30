<?php

use idcrm\includes\actions\idCRMActionLanguage;

/**
 * Add schedule template part
 */

/* echo '$post->ID: ' . $post->ID . '^<br />'; */
$idcrm_company_id = get_post_meta( $post->ID, 'idcrm_company_id', true );
/* echo '$idcrm_company_id: ' . $idcrm_company_id . '^<br />'; */
$idcrm_contact_user_id = get_post_meta( $post->ID, 'idcrm_contact_user_id', true );
/* echo '$idcrm_contact_user_id: ' . $idcrm_contact_user_id . '^<br />'; */

$is_company = $idcrm_company_id ? true : false;
/* echo '$is_company: ' . ($is_company ? 'true' : 'false') . '^<br />'; */
$key = $idcrm_company_id ? 'idcrm_company_id' : 'idcrm_contact_user_id';
/* echo '$key: ' . $key . '^<br />'; */
$client_id = $idcrm_company_id ? : $idcrm_contact_user_id;
/* echo '$client_id: ' . $client_id . '^<br />'; */

?>

<h3><?php esc_html_e( 'Make a Schedule', idCRMActionLanguage::TEXTDOMAIN ); ?></h3>
<div class="pt-3 pb-3">
  <form class="form-horizontal form-material"	method="post" id="add_event">
    <div class="form-group">
      <div class="col-md-6 mb-3">
      <label><h4><?php esc_html_e( 'Event type', idCRMActionLanguage::TEXTDOMAIN ); ?></h4></label>
        <?php

        $event_types = get_terms( array( 'contact_events' ), array( 'hide_empty' => false ) );

        if ( ! empty( $event_types ) ) {
          foreach ( $event_types as $event_type ) {
            // print_r($event_type);
            echo '<div class="form-check form-check-inline">';
            echo '<input class="form-check-input success check-outline outline-success event-type-radio" data-type-id="' . esc_html( $event_type->term_id ) . '"
            type="radio" required id="' . esc_html( $event_type->name ) . '" name="event_type" value="' . esc_html( $event_type->name ) . '">';
            echo '<label class="form-check-label" for="' . esc_html( $event_type->name ) . '">' . esc_html( $event_type->name ) . ' </label>';
            echo '</div>';
          }
        }
        ?>
      </div>
      <div class="col-md-6 d-flex mb-3 gap-3 flex-column flex-lg-row">
        <div class="input-group me-0">
          <input type="text" class="form-control event_date" id="mdate" name="event_date" placeholder="<?php esc_html_e( 'Check date', idCRMActionLanguage::TEXTDOMAIN ); ?>"/>
          <label for="mdate">
            <span class="input-group-text event_date-span">
              <i data-feather="calendar" class="feather-sm"></i>
            </span>
          </label>
        </div>
        <div class="input-group">
          <input class="form-control event_date" id="timepicker" name="event_time" placeholder="<?php esc_html_e( 'Check time', idCRMActionLanguage::TEXTDOMAIN ); ?>"/>
          <label for="timepicker">
            <span class="input-group-text event_date-span">
              <i data-feather="clock" class="feather-sm"></i>
            </span>
          </label>
        </div>
      </div>
      <div class="col-md-12 mb-3">
        <input type="text" class="form-control" name="event_topic" id="event_topic" maxlength="300" placeholder="<?php esc_html_e( 'Topic', idCRMActionLanguage::TEXTDOMAIN ); ?>"
        value=""
        required
        tabindex="1"
        />
      </div>
      <?php wp_nonce_field( 'submit_event', 'event_nonce' ); ?>
      <input type="button" id="add-event-button" name="submit"
      value="<?php esc_html_e( 'Add Event', idCRMActionLanguage::TEXTDOMAIN ); ?>"
      class="btn-sml btn-info btn-rounded waves-effect"
      data-post-id="<?php echo esc_attr($post->ID); ?>"
      data-contact-id="<?php echo esc_attr($post->ID); ?>"
      data-current-user-id="<?php echo esc_attr($current_user->ID); ?>" />
      <input type="hidden" name="action" value="idcrm_contacts_add_event">
    </div>
  </form>
</div>
