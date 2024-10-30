<?php

use idcrm\includes\actions\idCRMActionLanguage;
use idcrm\includes\api\idCRMApiSchedule;

$idcrm_company_id = get_post_meta( $post->ID, 'idcrm_company_id', true );
$idcrm_contact_user_id = get_post_meta( $post->ID, 'idcrm_contact_user_id', true );

$is_company = $idcrm_company_id ? true : false;

// $key = $idcrm_company_id ? 'idcrm_company_id' : 'idcrm_contact_user_id';

$key = 'idcrm_contact_user_id';

// $client_id = $idcrm_company_id ?: $idcrm_contact_user_id;

$client_id = $post->ID;

$events = get_posts(
    [
        'numberposts' => -1,
        'post_type'   => 'contact_event',
        'meta_query' => [
            'relation' => 'AND',
            [
                'key' => 'idcrm_event_status',
                'value' => 'active',
            ],
            [
                'key' => 'idcrm_contact_user_id',
                'value' => $post->ID,
            ],
        ]
    ]
);

?>
<div class="card schedule-card <?php if ( !$events ) { echo 'd-none'; } ?>">
  <div class="card-body">
    <div class="edit">
      <div class="dropdown dropstart">
        <a href="#" class="link" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
          <i data-feather="more-horizontal" class="feather-sm" ></i>
        </a>
        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
          <li>
            <span class="dropdown-item">
              <?php echo '<a id="edit-author-events" href="#">' . esc_html__( 'Edit', idCRMActionLanguage::TEXTDOMAIN ) . '</a>'; ?>
            </span>
          </li>
        </ul>
      </div>
    </div>
    <h3><?php esc_html_e( 'Schedule', idCRMActionLanguage::TEXTDOMAIN ); ?></h3>
    <div id="schedule-container">
      <?php

      idCRMApiSchedule::idcrmAjaxRefreshSchedule($post->ID);

      ?>
    </div>
  </div>
</div>

<div class="modal fade" id="editEventModal" tabindex="-1" aria-labelledby="editEventModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="editEventModalLabel"><?php esc_html_e( 'Event Editing', idCRMActionLanguage::TEXTDOMAIN ); ?></h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form>
          <div class="mb-3">
            <input type="text" class="form-control" name="edit_event_topic" id="edit_event_topic" maxlength="300" placeholder="<?php esc_html_e( 'Topic', idCRMActionLanguage::TEXTDOMAIN ); ?>" value="" required tabindex="1" />
          </div>
          <div class="mb-3">
            <input type="text" class="form-control" id="edit_event_date" name="edit_event_date" placeholder="<?php esc_html_e( 'Check date', idCRMActionLanguage::TEXTDOMAIN ); ?>" />
          </div>

          <div class="mb-3">
            <input type="text" class="form-control" id="edit_event_time" name="edit_event_time" placeholder="<?php esc_html_e( 'Check time', idCRMActionLanguage::TEXTDOMAIN ); ?>" />
          </div>

          <div class="mb-3">
            <?php $args = array(
                    'taxonomy' => 'contact_events',
                    'hierarchical'    => false,
                    'hide_empty' => false,
                    'class' => 'form-control',
                    'name' => 'edit_contact_events',
                    'id' => 'edit_contact_events',
                    'required' => true,
                    'show_option_none'   => esc_html__( 'Event type', idCRMActionLanguage::TEXTDOMAIN ),
                  );
            wp_dropdown_categories( $args ); ?>
          </div>

        </form>
      </div>
      <div class="modal-footer border-0">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php esc_html_e( 'Cancel', idCRMActionLanguage::TEXTDOMAIN ); ?></button>
        <button type="button" data-event-id="" data-post-id="<?php echo $post->ID; ?>" class="btn btn-primary edit-save-event"><?php esc_html_e( 'Save', idCRMActionLanguage::TEXTDOMAIN ); ?></button>
      </div>
    </div>
  </div>
</div>
