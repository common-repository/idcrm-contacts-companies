<?php

use idcrm\includes\actions\idCRMActionLanguage;

$current_user_id = get_current_user_id();
$author = is_super_admin( $current_user_id ) ? "" : $current_user_id;
?>


<?php $tax_id = 0;
function idcrm_zadarma_image_validation( $file_name ) {
	$valid_extensions = array( 'jpg', 'jpeg', 'gif', 'png', 'webp' );
	$exploded_array   = explode( '.', $file_name );
	if ( ! empty( $exploded_array ) && is_array( $exploded_array ) ) {
		$ext = array_pop( $exploded_array );
		return in_array( $ext, $valid_extensions );
	} else {
		return false;
	}
}

function idcrm_zadarma_insert_attachments( $file_handler, $post_id, $setthumb = false ) {

	if ( $_FILES[ $file_handler ]['error'] !== UPLOAD_ERR_OK ) {
		__return_false();
	}

	require_once ABSPATH . 'wp-admin/includes/image.php';
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';

	$attach_id = media_handle_upload( $file_handler, $post_id );

	if ( $setthumb ) {
		update_post_meta( $post_id, '_thumbnail_id', $attach_id );
	}

	return $attach_id;
}

function disposition_text($status) {

	$disposition_text = [
		'answered' => esc_html__( 'Answered', idCRMActionLanguage::TEXTDOMAIN ),
		'busy' => esc_html__( 'Busy', idCRMActionLanguage::TEXTDOMAIN ),
		'cancel' => esc_html__( 'Cancel', idCRMActionLanguage::TEXTDOMAIN ),
		'no answer' => esc_html__( 'No answer', idCRMActionLanguage::TEXTDOMAIN ),
		'failed' => esc_html__( 'Failed', idCRMActionLanguage::TEXTDOMAIN ),
		'no money' => esc_html__( 'No money', idCRMActionLanguage::TEXTDOMAIN ),
		'unallocated number' => esc_html__( 'Unallocated number', idCRMActionLanguage::TEXTDOMAIN ),
		'no limit' => esc_html__( 'No limit', idCRMActionLanguage::TEXTDOMAIN ),
		'no day limit' => esc_html__( 'No day limit', idCRMActionLanguage::TEXTDOMAIN ),
		'line limit' => esc_html__( 'Line limit', idCRMActionLanguage::TEXTDOMAIN ),
		'no money, no limit' => esc_html__( 'No money, no limit', idCRMActionLanguage::TEXTDOMAIN ),
	];

	return isset($disposition_text[$status]) ? $disposition_text[$status] : esc_html__( 'No status', idCRMActionLanguage::TEXTDOMAIN );

}

global $current_user;
wp_get_current_user();

if ( isset( $_POST['action'] ) && 'idcrm_contacts_add_contact_zadarma' == $_POST['action'] && isset( $_POST['contact_nonce_zadarma'] ) && is_user_logged_in() ) {
	if ( wp_verify_nonce( $_POST['contact_nonce_zadarma'], 'submit_contact_zadarma' ) ) {

		$contact_email = '';
		if (array_key_exists('contact_email_zadarma', $_POST)) {
			$contact_email = $_POST['contact_email_zadarma'];
		}

		$contact_surname = '';
		if (array_key_exists('contact_surname_zadarma', $_POST)) {
			$contact_surname = $_POST['contact_surname_zadarma'];
		}

		$check_contact_mail = [];
		if ( $contact_email != '' ) {
			// Mail not empty.
			// Check for mail duplicates.
			$args = array(
				'post_type'   => 'user_contact',
				'post_status' => 'publish',
				'numberposts' => -1,
				'meta_key'    => 'idcrm_contact_email',
				'meta_value'  => $contact_email,
			);
			$check_contact_mail = get_posts( $args );
		}

		$contact_item_id = 0;
		if (empty($check_contact_mail)) {

			$post_title = [];

			$contact_first_name = '';
			if (array_key_exists('contact_first_name_zadarma', $_POST)) {
				$post_title[] = sanitize_text_field( wp_unslash( $_POST['contact_first_name_zadarma'] ) );
			}

			$contact_surname = '';
			if (array_key_exists('contact_surname_zadarma', $_POST)) {
				$post_title[] = sanitize_text_field( wp_unslash( $_POST['contact_surname_zadarma'] ) );
			}

			$contact_last_name = '';
			if (array_key_exists('contact_last_name_zadarma', $_POST)) {
				$post_title[] = sanitize_text_field( wp_unslash( $_POST['contact_last_name_zadarma'] ) );
			}

			$contact_item = array(
				'post_title'  => implode(" ", $post_title),
				'post_type'   => 'user_contact',
				'post_author' => $current_user->ID,
				'post_status' => 'publish',
			);
			$contact_item_id = wp_insert_post( $contact_item );
		}

		// Metabox, taxonomy, image.
		if ( $contact_item_id > 0 ) {
			// Metabox.

			$contact_title = '';
			if (array_key_exists('contact_first_name_zadarma', $_POST)) {
				$contact_title = sanitize_text_field( wp_unslash( $_POST['contact_first_name_zadarma'] ) );
			}

			if ($contact_title != '') {
				update_post_meta( $post->ID, 'idcrm_contact_title', $contact_title );
			}


				// Contact list page.
				$contact_company = '';

				if (array_key_exists('contact_company_zadarma', $_POST)) {
					$contact_company = sanitize_text_field( wp_unslash( $_POST['contact_company_zadarma']));
				}

				if ($contact_company != '') {
					if ( ! is_admin() ) {
					    require_once( ABSPATH . 'wp-admin/includes/post.php' );
					}

					if (!is_numeric($contact_company) && !post_exists( $contact_company, '', '', 'company')) {

						$company_id = 'company_' . random_int( 100000, 999999 );

						$company_item = array(
								'post_title' => sanitize_text_field( $contact_company ),
								'post_type' => 'company',
								'post_author' => get_current_user_id(),
								'post_status' => 'publish',
								'meta_input' => [ 'idcrm_company_id' => $company_id ]
						);

						$company_item_id = wp_insert_post( $company_item );
						wp_set_object_terms( $company_item_id, esc_html__( 'Leads', idCRMActionLanguage::TEXTDOMAIN ), 'comp_status' );

						// $contact_company = $company_item_id;
					}

					update_post_meta( $contact_item_id, 'idcrm_contact_company', $contact_company );
				}


			// update_post_meta( $contact_item_id, 'idcrm_contact_user_id', get_current_user_id() );

			if ($contact_email != '') {
				update_post_meta( $contact_item_id, 'idcrm_contact_email', sanitize_text_field( wp_unslash( $contact_email ) ) );
			}

			if ($contact_surname != '') {
				update_post_meta( $contact_item_id, 'idcrm_contact_surname', sanitize_text_field( wp_unslash( $contact_surname ) ) );
			}

			$contact_phone = '';
			if (array_key_exists('contact_phone_zadarma', $_POST)) {
				$contact_phone = $_POST['contact_phone_zadarma'];
			}
            if ($contact_phone != '') {
				update_post_meta( $contact_item_id, 'idcrm_contact_phone', sanitize_text_field( wp_unslash( $contact_phone ) ) );
			}
			$contact_position = '';
			if (array_key_exists('contact_position_zadarma', $_POST)) {
				$contact_position = $_POST['contact_position_zadarma'];
			}
            if ($contact_position != '') {
				update_post_meta( $contact_item_id, 'idcrm_contact_position', sanitize_text_field( wp_unslash( $contact_position ) ) );
			}
			$contact_gender = '';
			if (array_key_exists('contact_gender_zadarma', $_POST)) {
				$contact_gender = $_POST['contact_gender_zadarma'];
			}
            if ($contact_gender != '') {
				update_post_meta( $contact_item_id, 'idcrm_contact_gender', sanitize_text_field( wp_unslash( $contact_gender ) ) );
			}
			$contact_birthday = '';
			if (array_key_exists('contact_birthday_zadarma', $_POST)) {
				$contact_birthday = $_POST['contact_birthday_zadarma'];
			}
            if ($contact_birthday != '') {
				update_post_meta( $contact_item_id, 'idcrm_contact_birthday', sanitize_text_field( wp_unslash( $contact_birthday ) ) );
			}
			// Taxonomy.
			$contact_status = '';
			if (array_key_exists('contact_status_zadarma', $_POST)) {
				$contact_status = $_POST['contact_status_zadarma'];
			}

			if ($contact_status != '') {
				wp_set_object_terms( $contact_item_id, intval( $contact_status ), 'user_status' );
				$term_object = get_term( intval( $contact_status ) );

				if ($term_object->slug !== 'user-leads') {
					update_post_meta( $contact_item_id, 'idcrm_not_a_lead', '1' );
				} else {
					update_post_meta( $contact_item_id, 'idcrm_added_as_lead', 'yes' );
				}
			}

			$contact_source = '';
			if (array_key_exists('contact_source_zadarma', $_POST)) {
				$contact_source = $_POST['contact_source_zadarma'];
			}

			if ($contact_source != '') {
				wp_set_object_terms( $contact_item_id, $contact_source, 'user_source' );
			}

			if (array_key_exists('zadarma_call_data', $_POST)) {

				$zadarma_call_data = str_replace( '&quot;', '"', trim($_POST['zadarma_call_data']));
				$zadarma_call_data = str_replace(array("\r", "\n"), '', $zadarma_call_data);

				$zadarma_call_data = json_decode($zadarma_call_data, true);

				update_post_meta($contact_item_id, 'idcrm_zadarma_call_events', serialize($zadarma_call_data));

				$event = isset($zadarma_call_data['event']) && !empty($zadarma_call_data['event']) ? sanitize_text_field($zadarma_call_data['event']) : '';
	      $duration = isset($zadarma_call_data['duration']) && !empty($zadarma_call_data['duration']) ? sanitize_text_field($zadarma_call_data['duration']) : '';
	      $disposition = isset($zadarma_call_data['disposition']) && !empty($zadarma_call_data['disposition']) ? sanitize_text_field($zadarma_call_data['disposition']) : '';
				$is_recorded = isset($zadarma_call_data['is_recorded']) && !empty($zadarma_call_data['is_recorded']) ? sanitize_text_field($zadarma_call_data['is_recorded']) : '';
	      $pbx_call_id = isset($zadarma_call_data['pbx_call_id']) && !empty($zadarma_call_data['pbx_call_id']) ? sanitize_text_field($zadarma_call_data['pbx_call_id']) : '';

				$idcrm_zadarma_call_events = [];
				$idcrm_zadarma_call_events[$pbx_call_id] = $zadarma_call_data;
				update_post_meta($contact_item_id, 'idcrm_zadarma_call_events', serialize($idcrm_zadarma_call_events));

				$post_title = 'comment-for-post-' . $contact_item_id . '-from-user-' . get_current_user_id();

				$post_body = $event == 'NOTIFY_OUT_END' ? esc_html__( 'Outgoing call', idCRMActionLanguage::TEXTDOMAIN ) : esc_html__( 'Incoming call', idCRMActionLanguage::TEXTDOMAIN );
				$post_body .= ': ' . disposition_text($disposition);

				if ($duration) {
					$post_body .= ', ' . esc_html__( 'with duration', idCRMActionLanguage::TEXTDOMAIN ) . ' ' . $duration . ' ' . esc_html__( 'seconds', idCRMActionLanguage::TEXTDOMAIN );
				}

				if ($is_recorded) {
					$post_body .= ', ' . esc_html__( 'record will be available within few minutes', idCRMActionLanguage::TEXTDOMAIN );
				}

				$new_comment = array(
					'post_title'  => $post_title,
					'post_content'  => $post_body,
					'post_type'   => 'idcrm_comments',
					'post_author' => get_current_user_id(),
					'post_status' => 'publish',
				);

				$new_comment_id = wp_insert_post( $new_comment );

				update_post_meta( $new_comment_id, 'idcrm_contact_user_id', $contact_item_id );
				update_post_meta( $new_comment_id, 'idcrm_comment_type', 'zadarma_event');
				update_post_meta( $new_comment_id, 'idcrm_post_type', 'user_contact');

				$idcrm_zadarma_unknown_calls = unserialize(get_option( 'idcrm_zadarma_unknown_calls' ) ?: 'a:0:{}');

				if ($pbx_call_id && isset($idcrm_zadarma_unknown_calls[$pbx_call_id])) {
					unset($idcrm_zadarma_unknown_calls[$pbx_call_id]);
					update_option( 'idcrm_zadarma_unknown_calls', serialize($idcrm_zadarma_unknown_calls) );
				}
			}
			// Image.
			if ( !empty( $_FILES ) ) {
				foreach ( $_FILES as $submitted_file => $file_array ) {
					if ( idcrm_zadarma_image_validation( $_FILES[ $submitted_file ]['name'] ) ) {
						$size = intval( $_FILES[ $submitted_file ]['size'] );
						if ( $size > 0 ) {
							idcrm_zadarma_insert_attachments( $submitted_file, $contact_item_id, true );
						}
					}
				}
			}

			// Add wp user.
			$user_login_id = 'user_' . random_int( 100000, 999999 );
			$password = wp_generate_password( 10, true, true );
			$user_email = !empty( $_POST['contact_email_zadarma'] ) ? sanitize_email( wp_unslash( $contact_email ) ) : '';

			$display_name = !empty( $_POST['contact_first_name_zadarma'] ) && !empty( $_POST['contact_last_name_zadarma'])
				? sanitize_text_field( wp_unslash( $_POST['contact_first_name_zadarma'] ) ) . ' ' . sanitize_text_field( wp_unslash( $_POST['contact_last_name_zadarma'] ) )
				: $user_login_id;

			$first_name = !empty( $_POST['contact_first_name_zadarma'] ) ? sanitize_text_field( wp_unslash( $_POST['contact_first_name_zadarma'] ) ) : $user_login_id;
			$last_name = !empty( $_POST['contact_last_name_zadarma'] ) ? sanitize_text_field( wp_unslash( $_POST['contact_last_name_zadarma'] ) )  : $user_login_id;

			$userdata = array(
				'user_login'   => "$user_login_id",
				'user_pass'    => "$password",
				'user_email'   => $user_email,
				'display_name' => $display_name,
				'first_name'   => $first_name,
				'last_name'    => $last_name,
				'role' => 'lead',
			);

			$user_id = wp_insert_user( $userdata );

			update_post_meta( $contact_item_id, 'idcrm_contact_user_id', sanitize_text_field( wp_unslash( $user_id ) ) );
			update_user_meta( $user_id, 'idcrm_contact_user_id', $contact_item_id );

			// Page reload.
			if ( $contact_item_id != 0 ) {
				echo "<script type='text/javascript'>window.location=document.location.href; idcrmContactManage.clearAllData();</script>";
			}
		}
        if (!empty($check_contact_mail)) {
			$message = esc_html__('This is a duplicate. Change the e-mail.', idCRMActionLanguage::TEXTDOMAIN);
			$button = esc_html__('Continue', idCRMActionLanguage::TEXTDOMAIN);
            echo '<!-- Duplicate error modal -->
				<div class="modal fade" id="al-danger-alert" tabindex="-1" aria-labelledby="vertical-center-modal" aria-hidden="true">
					<div class="modal-dialog modal-dialog-centered modal-sm">
						<div class="modal-content modal-filled bg-light-danger">
							<div class="modal-body p-4">
								<div class="text-center text-danger">
									<i data-feather="x-octagon" class="fill-white feather-lg"></i>
									<h4 class="mt-2">Error!</h4>
									<p class="mt-3">' . $message . '</p>
									<button type="button" class="btn btn-light my-2" data-bs-dismiss="modal">' . $button . '</button>
								</div>
							</div>
						</div>
						<!-- /.modal-content -->
					</div>
				</div>';
			echo "<script type='text/javascript'>
				$(window).on('load',function(){
					$('#al-danger-alert').modal('show');
				});
			</script>";
        }
	}
}


if ( isset( $_POST['action'] ) && 'idcrm_contacts_assign_contact_zadarma' == $_POST['action'] && isset( $_POST['assign_contact_nonce_zadarma'] ) && is_user_logged_in() ) {
	if ( wp_verify_nonce( $_POST['assign_contact_nonce_zadarma'], 'submit_assign_contact_zadarma' ) ) {

		$contact_id = 0;
		if (array_key_exists('assign_contact_zadarma', $_POST)) {
			$contact_id = intval($_POST['assign_contact_zadarma']);
		}

		if ($contact_id != 0) {

				if (array_key_exists('zadarma_assign_phone_call_data', $_POST)) {

					$zadarma_call_data = str_replace( '&quot;', '"', trim($_POST['zadarma_assign_phone_call_data']));
					$zadarma_call_data = str_replace(array("\r", "\n"), '', $zadarma_call_data);

					$zadarma_call_data = json_decode($zadarma_call_data, true);

					$event = isset($zadarma_call_data['event']) && !empty($zadarma_call_data['event']) ? sanitize_text_field($zadarma_call_data['event']) : '';
					$duration = isset($zadarma_call_data['duration']) && !empty($zadarma_call_data['duration']) ? sanitize_text_field($zadarma_call_data['duration']) : '';
		      $disposition = isset($zadarma_call_data['disposition']) && !empty($zadarma_call_data['disposition']) ? sanitize_text_field($zadarma_call_data['disposition']) : '';
					$is_recorded = isset($zadarma_call_data['is_recorded']) && !empty($zadarma_call_data['is_recorded']) ? sanitize_text_field($zadarma_call_data['is_recorded']) : '';
		      $pbx_call_id = isset($zadarma_call_data['pbx_call_id']) && !empty($zadarma_call_data['pbx_call_id']) ? sanitize_text_field($zadarma_call_data['pbx_call_id']) : '';

					$idcrm_zadarma_call_events = unserialize(get_post_meta($contact_id, 'idcrm_zadarma_call_events', true) ?: 'a:0:{}');
					$idcrm_zadarma_call_events[$pbx_call_id] = $zadarma_call_data;
					update_post_meta($contact_id, 'idcrm_zadarma_call_events', serialize($idcrm_zadarma_call_events));

					$post_title = 'comment-for-post-' . $contact_id . '-from-user-' . get_current_user_id();

					$post_body = $event == 'NOTIFY_OUT_END' ? esc_html__( 'Outgoing call', idCRMActionLanguage::TEXTDOMAIN ) : esc_html__( 'Incoming call', idCRMActionLanguage::TEXTDOMAIN );
					$post_body .= ': ' . disposition_text($disposition);

					if ($duration) {
						$post_body .= ', ' . esc_html__( 'with duration', idCRMActionLanguage::TEXTDOMAIN ) . ' ' . $duration . ' ' . esc_html__( 'seconds', idCRMActionLanguage::TEXTDOMAIN );
					}

					if ($is_recorded) {
						$post_body .= ', ' . esc_html__( 'record will be available within few minutes', idCRMActionLanguage::TEXTDOMAIN );
					}

					$new_comment = array(
						'post_title'  => $post_title,
						'post_content'  => $post_body,
						'post_type'   => 'idcrm_comments',
						'post_author' => get_current_user_id(),
						'post_status' => 'publish',
					);

					$new_comment_id = wp_insert_post( $new_comment );

					update_post_meta( $new_comment_id, 'idcrm_contact_user_id', $contact_id );
					update_post_meta( $new_comment_id, 'idcrm_comment_type', 'zadarma_event');
					update_post_meta( $new_comment_id, 'idcrm_post_type', 'user_contact');

					$idcrm_zadarma_unknown_calls = unserialize(get_option( 'idcrm_zadarma_unknown_calls' ) ?: 'a:0:{}');

					if ($pbx_call_id && isset($idcrm_zadarma_unknown_calls[$pbx_call_id])) {
						unset($idcrm_zadarma_unknown_calls[$pbx_call_id]);
						update_option( 'idcrm_zadarma_unknown_calls', serialize($idcrm_zadarma_unknown_calls) );
					}
				}
		}

	}
}
?>

<!-- Add Contact Popup Model -->
<div id="add-contact-zadarma" class="modal fade in" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header d-flex align-items-center">
				<h4 class="modal-title" id="myModalLabel">
					<?php esc_html_e( 'Add New Contact', idCRMActionLanguage::TEXTDOMAIN ); ?>
				</h4>
			</div>
			<div class="modal-body">
				<form class="form-horizontal form-material"	method="post" id="add_contact" enctype="multipart/form-data">
					<div class="form-group">
						<div class="col-md-12 mb-3">
							<input type="text" class="form-control mb-2" name="contact_first_name_zadarma" id="contact_first_name_zadarma" placeholder="<?php esc_html_e( 'First Name', idCRMActionLanguage::TEXTDOMAIN ); ?>" value="" required tabindex="1"/>

							<input type="text" class="form-control mb-2" name="contact_last_name_zadarma" id="contact_last_name_zadarma" placeholder="<?php esc_html_e( 'Last Name', idCRMActionLanguage::TEXTDOMAIN ); ?>" value="" tabindex="2" />

							<input type="text" class="form-control mb-2" name="contact_surname_zadarma" id="contact_surname_zadarma" placeholder="<?php esc_html_e( 'Surname', idCRMActionLanguage::TEXTDOMAIN ); ?>" value="" tabindex="3" />

							<input type="text" class="form-control mb-2" name="contact_email_zadarma" id="contact_email_zadarma" placeholder="<?php esc_html_e( 'E-mail', idCRMActionLanguage::TEXTDOMAIN ); ?>" value="" tabindex="4" />

							<input type="text" class="form-control mb-2" name="contact_phone_zadarma" id="contact_phone_zadarma" placeholder="<?php esc_html_e( 'Phone', idCRMActionLanguage::TEXTDOMAIN ); ?>" value="" tabindex="5" />

							<label><?php esc_html_e( 'Company', 'idcrm-contacts-companies' ); ?></label>
							<select class="form-control" name="contact_company_zadarma" id="contact_company_zadarma" tabindex="6">
								<option value=""></option>
								<?php

								$companies = get_posts(
									array(
										'numberposts' => -1,
										'post_type'   => 'company',
										'author' => $author,
									)
								);

								$company_post_id = is_single() ? $post->post_title : 0;

								if (!empty($companies)) { ?>
										<?php foreach($companies as $company) { ?>
											<option value="<?php echo esc_html( get_the_title($company->ID) ); ?>"><?php echo esc_html( get_the_title($company->ID) ); ?></option>
										<?php }
							 } ?>

							</select>

							<input type="text" class="form-control mb-2 mt-2" name="contact_position_zadarma" id="contact_position_zadarma" placeholder="<?php esc_html_e( 'Position', idCRMActionLanguage::TEXTDOMAIN ); ?>" value="" tabindex="7" />

							<label><?php esc_html_e( 'Gender', idCRMActionLanguage::TEXTDOMAIN ); ?></label>
							<select class="form-control mb-2" name="contact_gender_zadarma" id="contact_gender_zadarma" tabindex="8">
								<option value="male"><?php echo esc_html__( 'Male', idCRMActionLanguage::TEXTDOMAIN ); ?></option>
								<option value="female"><?php echo esc_html__( 'Female', idCRMActionLanguage::TEXTDOMAIN ); ?></option>
							</select>

							<label><?php esc_html_e( 'Birthday', idCRMActionLanguage::TEXTDOMAIN ); ?></label>
							<input type="text" class="form-control mb-2" name="contact_birthday_zadarma" id="contact_birthday_zadarma" placeholder="<?php esc_html_e( 'dd.mm.yyyy', idCRMActionLanguage::TEXTDOMAIN ); ?>" value="" tabindex="9" />

							<label><?php esc_html_e( 'Source', 'idcrm-contacts-companies' ); ?></label>
							<select class="form-control mb-2" multiple="multiple" name="contact_source_zadarma[]" id="contact_source_zadarma" tabindex="10">

							<?php $sources = get_terms(array('user_source'),array('hide_empty'=>false));

								if (!empty($sources)) { ?>
									<?php foreach($sources as $source) {
										echo '<option value="' . esc_html( $source->name ) . '">' . esc_html( $source->name ) . '</option>';
									}
								} ?>

							</select>


						</div>
						<div class="col-md-12 mb-2">
							<label><?php esc_html_e( 'Contact Status', idCRMActionLanguage::TEXTDOMAIN ); ?></label>
							<?php $default_status = esc_html__( 'Leads', idCRMActionLanguage::TEXTDOMAIN );

							$queried_object = get_queried_object();
							if (isset($queried_object->taxonomy) && $queried_object->taxonomy == 'user_status') {
								$tax_id = $queried_object->term_id;
							}

							if (is_single()) {
									$term = get_the_terms( $post->ID , 'comp_status' );
									$default_status = is_array($term) && !empty($term[0]) ? $term[0]->name : $default_status;
							} ?>

							<select class="form-control" name="contact_status_zadarma" id="contact_status_zadarma" required tabindex="11">
							<?php
							$statuses = get_terms( array( 'user_status' ), array( 'hide_empty' => false ) );

							if ( ! empty( $statuses ) ) {
								// if ( is_tax() ) {
								// 	echo '<option value="' . esc_html( $tax_title ) . '">' . esc_html( $tax_title ) . '</option>';
									// } elseif ( is_single() ) {
									// echo '<option value=' . esc_html( $comp_status->term_id ) . '>' . esc_html( $comp_status->name ) . '</option>';
								// } else {
								if ($tax_id != 0) {
									foreach ( $statuses as $user_status ) { ?>
										<option <?php selected($user_status->term_id, $tax_id); ?>  value="<?php echo esc_html( $user_status->term_id ); ?>"><?php echo esc_html( $user_status->name ); ?></option>
									<?php }
								} else {
									foreach ( $statuses as $user_status ) { ?>
										<option <?php selected($user_status->name, $default_status); ?>  value="<?php echo esc_html( $user_status->term_id ); ?>"><?php echo esc_html( $user_status->name ); ?></option>
									<?php }
								}
								// }
							}
							?>
							</select>

						</div>
						<div class="mb-3">
							<label><?php esc_html_e( 'Photo Upload', idCRMActionLanguage::TEXTDOMAIN ); ?></label>
							<div class="fileinput fileinput-new input-group" data-provides="fileinput">
								<input class="form-control upload" type="file" id="contact_image_zadarma" name="Logo">
							</div>
						</div>
					</div>
					<div class="modal-footer border-0">
						<?php wp_nonce_field( 'submit_contact_zadarma', 'contact_nonce_zadarma' ); ?>
						<input type="submit" name="submit" value="<?php esc_html_e( 'Add', idCRMActionLanguage::TEXTDOMAIN ); ?>" class="btn btn-info btn-rounded waves-effect">
						<input type="hidden" name="action" value="idcrm_contacts_add_contact_zadarma">
						<input type="hidden" id="zadarma_call_data" name="zadarma_call_data" value="">
						<button type="button" class="btn btn-danger btn-rounded waves-effect" data-bs-dismiss="modal"><?php esc_html_e( 'Cancel', idCRMActionLanguage::TEXTDOMAIN ); ?></button>
					</div>
				</form>
			</div>
		</div>
		<!-- /.modal-content -->
	</div>
	<!-- /.modal-dialog -->
</div>

<div id="assign-contact-zadarma" class="modal fade in" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header d-flex align-items-center">
				<h4 class="modal-title" id="myModalLabel">
					<?php esc_html_e( 'Assign Contact', idCRMActionLanguage::TEXTDOMAIN ); ?>
				</h4>
			</div>
			<div class="modal-body">
				<form class="form-horizontal form-material"	method="post" id="add_contact" enctype="multipart/form-data">
					<div class="form-group">
						<div class="col-md-12 mb-3">

							<input type="hidden" class="form-control mb-2" name="assign_phone_zadarma" id="assign_phone_zadarma" placeholder="<?php esc_html_e( 'Phone', idCRMActionLanguage::TEXTDOMAIN ); ?>" value="" />

							<label><?php esc_html_e( 'Contact', idCRMActionLanguage::TEXTDOMAIN ); ?></label>
							<select class="form-control" name="assign_contact_zadarma" id="assign_contact_zadarma" tabindex="1">
								<option value=""></option>
								<?php

								$contacts = get_posts(
									array(
										'numberposts' => -1,
										'post_type'   => 'user_contact',
										'author' => $author,
									)
								);

								if (!empty($contacts)) { ?>
										<?php foreach($contacts as $company) { ?>
											<option value="<?php echo esc_html( $company->ID ); ?>"><?php echo esc_html( get_the_title($company->ID) ); ?></option>
										<?php }
							 } ?>

							</select>

						</div>

					</div>
					<div class="modal-footer border-0">
						<?php wp_nonce_field( 'submit_assign_contact_zadarma', 'assign_contact_nonce_zadarma' ); ?>
						<input type="submit" name="submit" value="<?php esc_html_e( 'Assign', idCRMActionLanguage::TEXTDOMAIN ); ?>" class="btn btn-info btn-rounded waves-effect">
						<input type="hidden" name="action" value="idcrm_contacts_assign_contact_zadarma">
						<input type="hidden" id="zadarma_assign_phone_call_data" name="zadarma_assign_phone_call_data" value="">
						<button type="button" class="btn btn-danger btn-rounded waves-effect" data-bs-dismiss="modal"><?php esc_html_e( 'Cancel', idCRMActionLanguage::TEXTDOMAIN ); ?></button>
					</div>
				</form>
			</div>
		</div>
		<!-- /.modal-content -->
	</div>
	<!-- /.modal-dialog -->
</div>
