<?php

use idcrm\includes\actions\idCRMActionLanguage;

?>
<!-- Add Button -->
<div class="d-flex mx-3 mx-md-0">
	<?php
	/** Button size */

	if ( is_single() ) {
		echo '<button type="button" class="btn btn-sm btn-rounded m-t-10 mb-3" data-bs-toggle="modal" data-bs-target="#add-contact" style="margin: -15px 20px 0px 0px">';
	} else {
		echo '<button type="button" class="btn btn-info btn-rounded m-t-10 mb-2" data-bs-toggle="modal" data-bs-target="#add-contact" style="margin: -1px 20px 0px 0px">';
	}
	?>
	<?php esc_html_e( 'Add new', idCRMActionLanguage::TEXTDOMAIN ); ?>
	</button>
</div>

<?php
function idcrm_contacts_image_validation( $file_name ) {
	$valid_extensions = array( 'jpg', 'jpeg', 'gif', 'png' );
	$exploded_array   = explode( '.', $file_name );
	if ( ! empty( $exploded_array ) && is_array( $exploded_array ) ) {
		$ext = array_pop( $exploded_array );
		return in_array( $ext, $valid_extensions );
	} else {
		return false;
	}
}

function idcrm_contacts_insert_attachments( $file_handler, $post_id, $setthumb = false ) {

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

global $current_user;
wp_get_current_user();

if ( isset( $_POST['action'] ) && is_user_logged_in() ) {

	if ( wp_verify_nonce( $_POST['contact_nonce'], 'submit_contact' ) ) {

		if ( empty( $_POST['contact_email'] ) ) {
			$contact_item = array(
				'post_title'  => sanitize_text_field( wp_unslash( $_POST['contact_first_name'] ) ) . ' ' . sanitize_text_field( wp_unslash( $_POST['contact_last_name'] ) ),
				'post_type'   => 'user_contact',
				'post_author' => $current_user->ID,
				'post_status' => 'publish',
			);

			$contact_item_id = wp_insert_post( $contact_item );

			// Metabox, taxonomy, image.

			if ( $contact_item_id > 0 ) {

				// Metabox.
				$contact_title = sanitize_text_field( wp_unslash( $_POST['title'] ) );
				update_post_meta( $post->ID, 'title', $contact_title );

				if ( is_single() ) { // Single contact page.
					update_post_meta( $contact_item_id, 'idcrm_contact_company', $post->ID );
				} else {
					if ( isset( $_POST['contact_company'] ) && $_POST['contact_company'] !== '' ) { // Contact list page.
						update_post_meta( $contact_item_id, 'idcrm_contact_company', sanitize_text_field( wp_unslash( $_POST['contact_company'] ) ) );
					}
				}
				if ( isset( $_POST['contact_email'] ) && $_POST['contact_email'] !== '' ) {
					update_post_meta( $contact_item_id, 'idcrm_contact_email', sanitize_text_field( wp_unslash( $_POST['contact_email'] ) ) );
				}
				if ( isset( $_POST['contact_phone'] ) && $_POST['contact_phone'] !== '' ) {
					update_post_meta( $contact_item_id, 'idcrm_contact_phone', sanitize_text_field( wp_unslash( $_POST['contact_phone'] ) ) );
				}
				if ( isset( $_POST['contact_position'] ) && $_POST['contact_position'] !== '' ) {
					update_post_meta( $contact_item_id, 'idcrm_contact_position', sanitize_text_field( wp_unslash( $_POST['contact_position'] ) ) );
				}
				if ( isset( $_POST['contact_gender'] ) && $_POST['contact_gender'] !== '' ) {
					update_post_meta( $contact_item_id, 'idcrm_contact_gender', sanitize_text_field( wp_unslash( $_POST['contact_gender'] ) ) );
				}
				if ( isset( $_POST['contact_birthday'] ) && $_POST['contact_birthday'] !== '' ) {
					update_post_meta( $contact_item_id, 'idcrm_contact_birthday', sanitize_text_field( wp_unslash( $_POST['contact_birthday'] ) ) );
				}

				// Taxonomy.
				if ( isset( $_POST['contact_status'] ) ) {
					wp_set_object_terms( $contact_item_id, intval( $_POST['contact_status'] ), 'user_status' );
				}
				if ( isset( $_POST['contact_source'] ) ) {
					wp_set_object_terms( $contact_item_id, intval( $_POST['contact_source'] ), 'user_source' );
				}

					// Image.
				if ( ! empty( $_FILES ) ) {
					foreach ( $_FILES as $submitted_file => $file_array ) {
						if ( idcrm_contacts_image_validation( $_FILES[ $submitted_file ]['name'] ) ) {
								$size = intval( $_FILES[ $submitted_file ]['size'] );

							if ( $size > 0 ) {
								idcrm_contacts_insert_attachments( $submitted_file, $contact_item_id, true );
							}
						}
					}
				}
			}

			// Page reload.
			if ( $contact_item_id > 0 ) {
				echo "<script type='text/javascript'>window.location=document.location.href;</script>";
			}
		} else { // Mail not empty.

			// Check for mail duplicates.
			$args = array(
				'post_type'   => 'user_contact',
				'post_status' => 'publish',
				'numberposts' => -1,
				'meta_key'    => 'idcrm_contact_email',
				'meta_value'  => $_POST['contact_email'],
			);

			$check_contact_mail = get_posts( $args );

			if ( empty( $check_contact_mail ) ) {

				$contact_item = array(
					'post_title'  => sanitize_text_field( wp_unslash( $_POST['contact_first_name'] ) ) . ' ' . sanitize_text_field( wp_unslash( $_POST['contact_last_name'] ) ),
					'post_type'   => 'user_contact',
					'post_author' => $current_user->ID,
					'post_status' => 'publish',
				);

				$contact_item_id = wp_insert_post( $contact_item );

				// Metabox, taxonomy, image.

				if ( $contact_item_id > 0 ) {

					// Metabox.
					$contact_title = sanitize_text_field( wp_unslash( $_POST['title'] ) );
					update_post_meta( $post->ID, 'title', $contact_title );

					if ( is_single() ) { // Single contact page.
						update_post_meta( $contact_item_id, 'idcrm_contact_company', $post->ID );
					} else {
						if ( isset( $_POST['contact_company'] ) && $_POST['contact_company'] !== '' ) { // Contact list page.
							update_post_meta( $contact_item_id, 'idcrm_contact_company', sanitize_text_field( wp_unslash( $_POST['contact_company'] ) ) );
						}
					}
					if ( isset( $_POST['contact_email'] ) && $_POST['contact_email'] !== '' ) {
						update_post_meta( $contact_item_id, 'idcrm_contact_email', sanitize_text_field( wp_unslash( $_POST['contact_email'] ) ) );
					}
					if ( isset( $_POST['contact_phone'] ) && $_POST['contact_phone'] !== '' ) {
						update_post_meta( $contact_item_id, 'idcrm_contact_phone', sanitize_text_field( wp_unslash( $_POST['contact_phone'] ) ) );
					}
					if ( isset( $_POST['contact_position'] ) && $_POST['contact_position'] !== '' ) {
						update_post_meta( $contact_item_id, 'idcrm_contact_position', sanitize_text_field( wp_unslash( $_POST['contact_position'] ) ) );
					}
					if ( isset( $_POST['contact_gender'] ) && $_POST['contact_gender'] !== '' ) {
						update_post_meta( $contact_item_id, 'idcrm_contact_gender', sanitize_text_field( wp_unslash( $_POST['contact_gender'] ) ) );
					}
					if ( isset( $_POST['contact_birthday'] ) && $_POST['contact_birthday'] !== '' ) {
						update_post_meta( $contact_item_id, 'idcrm_contact_birthday', sanitize_text_field( wp_unslash( $_POST['contact_birthday'] ) ) );
					}

					// Taxonomy.
					if ( isset( $_POST['contact_status'] ) ) {
						wp_set_object_terms( $contact_item_id, intval( $_POST['contact_status'] ), 'user_status' );
					}
					if ( isset( $_POST['contact_source'] ) ) {
						wp_set_object_terms( $contact_item_id, intval( $_POST['contact_source'] ), 'user_source' );
					}

						// Image.
					if ( ! empty( $_FILES ) ) {
						foreach ( $_FILES as $submitted_file => $file_array ) {
							if ( idcrm_contacts_image_validation( $_FILES[ $submitted_file ]['name'] ) ) {
									$size = intval( $_FILES[ $submitted_file ]['size'] );

								if ( $size > 0 ) {
									idcrm_contacts_insert_attachments( $submitted_file, $contact_item_id, true );
								}
							}
						}
					}
				}

				// Page reload.
				if ( $contact_item_id > 0 ) {
					echo "<script type='text/javascript'>window.location=document.location.href;</script>";
				}
			} else {
				echo "
				<script type='text/javascript'>
					$(window).on('load',function(){
						$('#al-danger-alert').modal('show');
					});
				</script>
				";
				echo '
				<!-- Duplicate error modal -->
				<div class="modal fade" id="al-danger-alert" tabindex="-1" aria-labelledby="vertical-center-modal" aria-hidden="true">
					<div class="modal-dialog modal-dialog-centered modal-sm">
						<div class="modal-content modal-filled bg-light-danger">
							<div class="modal-body p-4">
								<div class="text-center text-danger">
									<i
										data-feather="x-octagon"
										class="fill-white feather-lg"
									></i>
									<h4 class="mt-2">Error!</h4>
									<p class="mt-3">' .
										esc_html_e( 'This is a duplicate. Change the e-mail.', idCRMActionLanguage::TEXTDOMAIN ) .
									'</p>
									<button
										type="button"
										class="btn btn-light my-2"
										data-bs-dismiss="modal"
									>' .
										esc_html_e( 'Continue', idCRMActionLanguage::TEXTDOMAIN ) .
									'</button>
								</div>
							</div>
						</div>
						<!-- /.modal-content -->
					</div>
				</div>
				';
			}
		}
	}
}
?>

<!-- Add Contact Popup Model -->
<div
	id="add-contact"
	class="modal fade in"
	tabindex="-1"
	role="dialog"
	aria-labelledby="myModalLabel"
	aria-hidden="true"
>
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
							<input
							type="text"
							class="form-control"
							name="contact_first_name"
							id="contact_first_name"
							placeholder="<?php esc_html_e( 'First Name', idCRMActionLanguage::TEXTDOMAIN ); ?>"
							value=""
							required
							tabindex="1"
							/>

							<input
							type="text"
							class="form-control"
							name="contact_last_name"
							id="contact_last_name"
							placeholder="<?php esc_html_e( 'Last Name', idCRMActionLanguage::TEXTDOMAIN ); ?>"
							value=""
							required
							tabindex="2"
							/>

							<input
							type="text"
							class="form-control"
							name="contact_email"
							id="contact_email"
							placeholder="E-mail"
							value=""
							tabindex="3"
							/>

							<input
							type="text"
							class="form-control"
							name="contact_phone"
							id="contact_phone"
							placeholder="<?php esc_html_e( 'Phone', idCRMActionLanguage::TEXTDOMAIN ); ?>"
							value=""
							tabindex="4"
							/>

							<input
							type="text"
							class="form-control"
							name="contact_company"
							id="contact_company"
							placeholder="<?php esc_html_e( 'Company', idCRMActionLanguage::TEXTDOMAIN ); ?>"
							<?php
							if ( is_single() ) {
								echo 'value="' . esc_html( get_the_title() ) . '"';
							} else {
								echo 'value=""';
							}
							?>
							tabindex="5"
							/>

							<input
							type="text"
							class="form-control"
							name="contact_position"
							id="contact_position"
							placeholder="<?php esc_html_e( 'Position', idCRMActionLanguage::TEXTDOMAIN ); ?>"
							value=""
							tabindex="6"
							/>

							<label><?php esc_html_e( 'Gender', idCRMActionLanguage::TEXTDOMAIN ); ?></label>
							<select
							class="form-control"
							name="contact_gender"
							id="contact_gender"
							tabindex="7"
							>

							<option value="male"><?php echo esc_html__( 'Male', idCRMActionLanguage::TEXTDOMAIN ); ?></option>
							<option value="female"><?php echo esc_html__( 'Female', idCRMActionLanguage::TEXTDOMAIN ); ?></option>

							</select>

							<input
							type="text"
							class="form-control"
							name="contact_birthday"
							id="contact_birthday"
							placeholder="<?php esc_html_e( 'Birthday', idCRMActionLanguage::TEXTDOMAIN ); ?>"
							value=""
							tabindex="8"
							/>

							<input
							type="text"
							class="form-control"
							name="contact_source"
							id="contact_source"
							placeholder="<?php esc_html_e( 'Source', idCRMActionLanguage::TEXTDOMAIN ); ?>"
							value=""
							tabindex="9"
							/>
							<!--?php
								$sources = get_terms(array('user_source'),array('hide_empty'=>false));

								if(!empty($sources)){
								foreach($sources as $source){
									echo '<label value='.$source->term_id.'>'.$source->name.'</label>';
								}
								}
							?-->
						</div>
						<div class="col-md-12 mb-3">
							<label><?php esc_html_e( 'Contact Status', idCRMActionLanguage::TEXTDOMAIN ); ?></label>
							<select
							class="form-control"
							name="contact_status"
							id="contact_status"
							required
							tabindex="10"
							>
							<?php
							$statuses = get_terms( array( 'user_status' ), array( 'hide_empty' => false ) );

							if ( ! empty( $statuses ) ) {
								if ( is_tax() ) {
									echo '<option value=' . esc_html( $tax_title ) . '>' . esc_html( $tax_title ) . '</option>';
									// } elseif ( is_single() ) {
									// echo '<option value=' . esc_html( $comp_status->term_id ) . '>' . esc_html( $comp_status->name ) . '</option>';
								} else {
									foreach ( $statuses as $user_status ) {
										echo '<option ' . selected($user_status->name, esc_html__( 'Leads', idCRMActionLanguage::TEXTDOMAIN )) . ' value=' . esc_html( $user_status->term_id ) . '>' . esc_html( $user_status->name ) . '</option>';
									}
								}
							}
							?>
							</select>
						</div>
						<div class="mb-3">
							<label><?php esc_html_e( 'Photo Upload', idCRMActionLanguage::TEXTDOMAIN ); ?></label>
							<div class="fileinput fileinput-new input-group" data-provides="fileinput">
								<input class="form-control upload" type="file" id="contact_image" name="Logo">
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<?php wp_nonce_field( 'submit_contact', 'contact_nonce' ); ?>
						<input type="submit" name="submit" value="<?php esc_html_e( 'Add Contact', idCRMActionLanguage::TEXTDOMAIN ); ?>" class="btn btn-info btn-rounded waves-effect">
						<input type="hidden" name="action" value="idcrm_contacts_add_contact">
						<button type="button" class="btn btn-danger btn-rounded waves-effect" data-bs-dismiss="modal"><?php esc_html_e( 'Cancel', idCRMActionLanguage::TEXTDOMAIN ); ?></button>
					</div>
				</form>
			</div>
		</div>
		<!-- /.modal-content -->
	</div>
	<!-- /.modal-dialog -->
</div>
