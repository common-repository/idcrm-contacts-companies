<?php

use idcrm\idCRM;
use idcrm\includes\actions\idCRMActionLanguage;

?>
<!-- Add Button -->
<div class="d-flex mx-0 mx-md-0">
	<?php
	$tax_id = 0;
	/** Button Add company */
	if ( is_single() ) {
		echo '<button type="button" class="btn-sm btn-info btn-rounded mt-0 mb-4 me-2" data-bs-toggle="modal" data-bs-target="#add-company">';
		esc_html_e( 'Add company', idCRMActionLanguage::TEXTDOMAIN );
		echo '</button>';
		$current_user_id = get_current_user_id();
		$author = is_super_admin( $current_user_id ) ? "" : $current_user_id;
		$args = get_posts(
			array(
				'post_type'   => 'company',
				'post_status' => 'publish',
				'author' => $author,
				'numberposts' => -1,
			)
		);
		if ( ! empty( $args ) ) {
			echo '<a class="btn-sm btn-info btn-rounded mt-0 mb-4 assign-button" id="dropdownMenuAssign" data-bs-toggle="dropdown" aria-expanded="false">';
			esc_html_e( 'Assign company', idCRMActionLanguage::TEXTDOMAIN );
			echo '</a>';
			echo '<ul class="dropdown-menu" aria-labelledby="dropdownMenuAssign">';
			foreach ( $args as $arg ) {
				echo '<li>
					<span class="dropdown-item"><a class="assign-company" data-post-id="' . $post->ID . '" data-company-id="' . $arg->post_title . '" href="#">' . $arg->post_title . '</a></span>
				</li>';
			}
			echo '</ul>';
		}
	} else {
		echo '<button type="button" class="btn btn-info btn-rounded" data-bs-toggle="modal" data-bs-target="#add-company">';
		esc_html_e( 'Add new', idCRMActionLanguage::TEXTDOMAIN );
		echo '</button>';
	}

	?>
	</button>
</div>
<?php

function idcrm_contacts_imade_validation( $file_name ) {
	$valid_extensions = array( 'jpg', 'jpeg', 'gif', 'png', 'webp' );
	$exploded_array   = explode( '.', $file_name );
	if ( ! empty( $exploded_array ) && is_array( $exploded_array ) ) {
		$ext = array_pop( $exploded_array );
		return in_array( $ext, $valid_extensions );
	} else {
		return false;
	}
}
function idcrm_contacts_insert_attachments( $file_handler, $post_id, $setthumb = 'false' ) {
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
$action = '';
if (array_key_exists('action', $_POST)) {
	$action = $_POST['action'];
}
if ( $action != '' && is_user_logged_in() ) {
	$company_nonce = '';
	if (array_key_exists('company_nonce', $_POST)) {
		$company_nonce = $_POST['company_nonce'];
	}
	if ( wp_verify_nonce( $company_nonce, 'submit_company' ) ) {
		$company_id = 'company_' . random_int( 100000, 999999 );
		$company_title = '';
		if (array_key_exists('company_title', $_POST)) {
			$company_title = $_POST['company_title'];
		}
		$company_item_id = 0;
		if ($company_title != '') {
			$company_item = array(
				'post_title' => sanitize_text_field( $company_title ),
				'post_type' => 'company',
				'post_author' => get_current_user_id(),
				'post_status' => 'publish',
				'meta_input' => [ 'idcrm_company_id' => $company_id ]
			);
			$company_item_id = wp_insert_post( $company_item );
		}
		// Metabox, taxonomy, image.
		if ( $company_item_id != 0 ) {
			// Taxonomy.
			$company_status = '';
			if (array_key_exists('company_status', $_POST)) {
				$company_status = $_POST['company_status'];
			}
			if ( $company_status != '' ) {
				wp_set_object_terms( $company_item_id, intval( $_POST['company_status'] ), 'comp_status' );
			}
			// Image.
			if ( $_FILES ) {
				foreach ( $_FILES as $submitted_file => $file_array ) {
					if ( idcrm_contacts_imade_validation( $_FILES[ $submitted_file ]['name'] ) ) {
						$file_name = intval( $_FILES[ $submitted_file ]['name'] );
						$size = intval( $_FILES[ $submitted_file ]['size'] );
						if ( $size > 0 ) {
							idcrm_contacts_insert_attachments( $submitted_file, $company_item_id, true );
						}
					}
				}
			}
		}
		// Page reload.
		if ( $company_item_id > 0 ) {
			echo "<script type='text/javascript'>window.location=document.location.href;</script>";
		}
	}
}

?>

<!-- Add Contact Popup Model -->
<div id="add-company" class="modal fade in" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header d-flex align-items-center">
				<h4 class="modal-title" id="myModalLabel">
					<?php esc_html_e( 'Add New Company', idCRMActionLanguage::TEXTDOMAIN ); ?>
				</h4>
			</div>
			<div class="modal-body">
				<form class="form-horizontal form-material" method="post" id="add_company" enctype="multipart/form-data">
					<div class="form-group">
						<div class="col-md-12 mb-3">
							<input type="text" class="form-control" name="company_title" id="company_title" placeholder="<?php esc_html_e( 'Company Title', idCRMActionLanguage::TEXTDOMAIN ); ?>" value="" required tabindex="1" />
						</div>
						<div class="col-md-12 mb-3">
							<label>
								<?php esc_html_e( 'Company Status', idCRMActionLanguage::TEXTDOMAIN ); ?>
							</label>
							<select class="form-control" name="company_status" id="company_status" required tabindex="2">
								<?php

								$queried_object = get_queried_object();
								if (isset($queried_object->taxonomy) && $queried_object->taxonomy == 'comp_status') {
									$tax_id = $queried_object->term_id;
								}

								$statuses = get_terms( array( 'comp_status' ), array( 'hide_empty' => false ) );
								if ( ! empty( $statuses ) ) {
									// if ( is_tax() ) {
									// 	echo '<option value=' . esc_html( $tax_title ) . '>' . esc_html( $tax_title ) . '</option>';
									// } else {
										foreach ( $statuses as $comp_status ) { ?>
											<option <?php selected($comp_status->term_id, $tax_id); ?> value="<?php echo esc_html( $comp_status->term_id ); ?>"><?php echo esc_html( $comp_status->name ); ?></option>
										<?php }
									// }
								}
								?>
							</select>
						</div>
					<div class="mb-3">
							<label><?php esc_html_e( 'Logo Upload', idCRMActionLanguage::TEXTDOMAIN ); ?></label>
							<div class="fileinput fileinput-new input-group" data-provides="fileinput">
								<input class="form-control" type="file" class="upload" id="company_image" name="Logo">
							</div>
						</div>
					</div>
					<div class="modal-footer border-0">
						<?php wp_nonce_field( 'submit_company', 'company_nonce' ); ?>
						<input type="submit" name="submit" value="<?php esc_html_e( 'Add', idCRMActionLanguage::TEXTDOMAIN ); ?>" class="btn btn-info btn-rounded waves-effect">
						<input type="hidden" name="action" value="idcrm_contacts_add_company">
						<button type="button" class="btn btn-danger btn-rounded waves-effect" data-bs-dismiss="modal"><?php esc_html_e( 'Cancel', idCRMActionLanguage::TEXTDOMAIN ); ?></button>
					</div>
				</form>
			</div>

		</div>
		<!-- /.modal-content -->
	</div>
	<!-- /.modal-dialog -->
</div>
