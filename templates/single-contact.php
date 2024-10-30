<?php

require 'inc/check-user.php';
require 'inc/header.php';
require 'inc/sidebar.php';
include_once ABSPATH . 'wp-admin/includes/plugin.php';

use idcrm\idCRM;
use idcrm\includes\api\idCRMApi;
use idcrm\includes\actions\idCRMActionLanguage;
use idcrm\includes\api\idCRMApiComment;
use idcrm\includes\api\idCRMApiContact;

// $idcrm_contact_surname = get_post_meta( $post->ID, 'idcrm_contact_surname', true ) ? ' ' . get_post_meta( $post->ID, 'idcrm_contact_surname', true ) : '';
$idcrm_contact_surname = '';
$contact_name = get_the_title($post->ID);
$name_array = explode(' ', $contact_name);

$first_name = get_the_title($post->ID);
$second_name = '';

if (is_array($name_array) && count($name_array) == 2 ) {
	$first_name = $name_array[0];
	$second_name = $name_array[1];
}

if (is_array($name_array) && count($name_array) == 3 ) {
	$first_name = $name_array[0];
	$idcrm_contact_surname = $name_array[1];
	$second_name = $name_array[2];
}

// if (is_array($name_array) && count($name_array) > 1 && $idcrm_contact_surname ) {
// 	$contact_name = $name_array[0] . $idcrm_contact_surname . ' ' . $name_array[1];
// }
?>

<div class="page-wrapper">
	<?php require 'inc/breadcrumbs.php'; ?>

	<div class="container-fluid">

		<div class="row">
			<!-- Column -->
			<div class="col-lg-4 col-xlg-3 col-md-5">
				<div class="card editable-card" id="single-contact" data-contact-id="<?php echo esc_attr($post->ID); ?>">

					<div class="edit-control px-3 py-1 pb-2 gap-3">
						<div class="edit-contact-apply" title="<?php echo esc_html__( 'Apply changes', idCRMActionLanguage::TEXTDOMAIN ); ?>">
							<div class="icon">
	              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check-circle feather-sm"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
	            </div>
						</div>
						<div class="edit-contact-cancel" title="<?php echo esc_html__( 'Cancel changes', idCRMActionLanguage::TEXTDOMAIN ); ?>">
							<div class="icon">
	                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x-circle feather-sm"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>
	            </div>
						</div>
					</div>

					<div class="card-body">

						<?php if ( $user->ID == $post->post_author || is_super_admin( $user->ID ) ) { ?>
							<div class="edit" id="enable-edit-permission">
								<div class="dropdown dropstart">
									<a href="#" class="link" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
										<i data-feather="more-horizontal" class="feather-sm"></i>
									</a>
									<ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">

										<?php if (is_plugin_active( 'idcrm-deals-documents/idcrm-deals-documents.php' )) { ?>
											<li>
												<span class="dropdown-item">
													<?php echo '<a href="" data-bs-toggle="modal" data-bs-target="#add-sidebar-deal">' . esc_html__( 'Add deal', idCRMActionLanguage::TEXTDOMAIN ) . '</a>'; ?>
												</span>
											</li>
										<?php } ?>

										<li>
											<span class="dropdown-item">
												<?php echo '<a class="edit-contact-fields" href="' . get_edit_post_link() . '">' . esc_html__( 'Edit', idCRMActionLanguage::TEXTDOMAIN ) . ' <span class="badge bg-light text-dark fw-normal border text-muted small edit-shortcut"></span></a>'; ?>
											</span>
										</li>

										<li>
											<span class="dropdown-item">
												<?php echo '<a class="delete-contact" href="#" data-post-id="' . $post->ID . '" data-id="' . get_post_meta( $post->ID, 'idcrm_contact_user_id', true ) . '" data-url="' . home_url() . '/crm-contacts/">' . esc_html__( 'Delete', idCRMActionLanguage::TEXTDOMAIN ) . '</a>'; ?>
											</span>
										</li>

									</ul>
								</div>
							</div>
						<?php } ?>

				<div class="d-flex align-items-center">
					<input id="post-image" type="file" name="post-image" class="d-none" accept="image/x-png,image/gif,image/jpeg" />
				<?php
				if ( has_post_thumbnail() ) {
					echo get_the_post_thumbnail( $post->ID, array( 120, 120 ), array( 'class' => 'rounded-circle object-fit-cover editable' ) );
				} else {
					$default_image = idCRM::$IDCRM_URL . 'templates/images/no-user.jpg';
					echo '<img src="' . esc_html( $default_image ) . '" class="rounded-circle editable wp-post-image object-fit-cover" width="120" height="120" />';
				}
				?>

				<span class="ms-3 fw-normal">
				<h2 class="card-title mt-2 visible-container"><?php echo esc_html($contact_name); ?></h2>

				<h2 class="card-title mt-2 editable d-none hidden-edit" id="contact-first-name" data-title="<?php echo esc_html__( 'First Name', idCRMActionLanguage::TEXTDOMAIN ); ?>"><?php echo esc_html($first_name); ?></h2>
				<input type="hidden" id="hidden-first-name" data-target="contact-first-name" data-old-value="<?php echo esc_html($first_name); ?>" value="<?php echo esc_html($first_name); ?>" />


				<h2 class="card-title mt-2 editable d-none hidden-edit" id="contact-surname" data-title="<?php echo esc_html__( 'Surname', idCRMActionLanguage::TEXTDOMAIN ); ?>"><?php echo esc_html($idcrm_contact_surname); ?></h2>
				<input type="hidden" id="hidden-surname" data-target="contact-surname" data-old-value="<?php echo esc_html($idcrm_contact_surname); ?>" value="<?php echo esc_html($idcrm_contact_surname); ?>" />


				<h2 class="card-title mt-2 editable d-none hidden-edit" id="contact-second-name" data-title="<?php echo esc_html__( 'Last Name', idCRMActionLanguage::TEXTDOMAIN ); ?>"><?php echo esc_html($second_name); ?></h2>
				<input type="hidden" id="hidden-second-name" data-target="contact-second-name" data-old-value="<?php echo esc_html($second_name); ?>" value="<?php echo esc_html($second_name); ?>" />

				<?php $use_surname = get_post_meta( $post->ID, 'idcrm_use_surname', true ) && get_post_meta( $post->ID, 'idcrm_use_surname', true ) == 'yes' ? 'checked' : "";
				if ($idcrm_contact_surname) { ?>
					<div class="small d-flex">
						<div class="form-check form-switch py-0 mx-2">
							  <input data-contact-id="<?php echo esc_attr($post->ID); ?>" class="form-check-input ajax-checkbox idcrm_use_surname" type="checkbox" role="switch" id="idcrm_use_surname" <?php echo esc_attr($use_surname); ?>>
							  <label class="form-check-label" for="idcrm_use_surname"><?php esc_html_e( 'Address by first name and surname', idCRMActionLanguage::TEXTDOMAIN ); ?></label>
							</div>
					</div>
				<?php } ?>

				<h5 class="card-subtitle">
					<?php $phone = get_post_meta( $post->ID, 'idcrm_contact_phone', true ); ?>
					<a id="user-phone" href="tel:<?php echo esc_html($phone); ?>" class="editable d-block" data-title="<?php echo esc_html__( 'Phone', idCRMActionLanguage::TEXTDOMAIN ); ?>"><?php echo esc_html($phone); ?></a>
					<input type="hidden" id="hidden-user-phone" data-target="user-phone" data-old-value="<?php echo esc_html($phone); ?>" value="<?php echo esc_html($phone); ?>" />
				</h5>

				<h5 class="card-subtitle">
					<?php $email = get_post_meta( $post->ID, 'idcrm_contact_email', true ); ?>
					<a id="user-email" href="mailto:<?php echo esc_html($email); ?>" class="editable d-block" data-title="<?php echo esc_html__( 'Email', idCRMActionLanguage::TEXTDOMAIN ); ?>"><?php echo esc_html($email); ?></a>
					<input type="hidden" id="hidden-user-email" data-target="user-email" data-old-value="<?php echo esc_html($email); ?>" value="<?php echo esc_html($email); ?>" />
				</h5>

				<!-- <h5 class="card-subtitle">
					<?php
						$website = get_post_meta( $post->ID, 'idcrm_contact_website', true );
						// if ($website != '') {
						// 	$parts = wp_parse_url( $website );
						// 	echo '<a href="' . esc_html( $website ) . '">' . $parts['scheme'] . '://' . $parts['host'] . '</a>';
						// }
					?>

					<a id="user-website" href="<?php echo esc_html($website); ?>" class="editable d-block" data-title="<?php echo esc_html__( 'Website', idCRMActionLanguage::TEXTDOMAIN ); ?>"><?php echo esc_html($website); ?></a>
					<input type="hidden" id="hidden-user-website" data-target="user-website" value="<?php echo esc_html($website); ?>" />
				</h5> -->
				</span>
			</div>
			</div>

			<div class="d-flex align-items-center visible-container">
				<span class="ms-3 fw-normal">
					<?php
					$user_img      = get_user_meta( $post->post_author, 'userimg', true );
					$default_image = idCRM::$IDCRM_URL . 'templates/images/no-user.jpg';

					if ( ! empty( $user_img ) ) {
						echo '<img src="' . esc_html( $user_img ) . '" width="40" height="40" class="rounded-circle object-fit-cover">';
					} else {
						echo '<img src="' . esc_html( $default_image ) . '" width="40" height="40" class="rounded-circle object-fit-cover">';
					}
					?>
				</span>
				<span class="ms-3 fw-normal">
					<?php the_author_meta( 'display_name', $post->post_author ); ?>
				</span>
			</div>
			<hr class="visible-container" />

			<div class="card-body">
				<h5 class="card-subtitle">
					<span class="visible-container">
				<?php
				$statuses = get_the_terms( get_the_ID(), 'user_status' );
				$selected_status = '';
				$current_term_id = '';
				if ( ! empty( $statuses ) ) {
					$length = count($statuses);
					foreach ( $statuses as $user_status ) {
						$selected_status = $user_status->term_id;
						$current_term_id = $user_status->term_id;
						echo esc_html( $user_status->name );
						if ($length > 1) { echo  ', '; }
						$length--;
					}
				}
				?>
				</span>
				<?php $args = array(
								'taxonomy' => 'user_status',
								// 'hierarchical'    => true,
								'hide_empty' => false,
								'class' => 'form-control d-none hidden-edit-select',
								'name' => 'user_status',
								'id' => 'user_status',
								'selected' => $selected_status,
								'show_option_none'   => esc_html__( 'Contact Status', idCRMActionLanguage::TEXTDOMAIN ),
							);
				wp_dropdown_categories( $args ); ?>

				</h5>

				<div id="current_term_id" data-section="contacts" data-term-id="<?php echo esc_html($current_term_id); ?>" class="d-none"></div>

				<h5 class="card-subtitle">
					<span class="visible-container">
				<?php
				$sources = get_the_terms( get_the_ID(), 'user_source' );
				if ( ! empty( $sources ) ) {
					esc_html_e( 'From: ', idCRMActionLanguage::TEXTDOMAIN );
					$length = count($sources);
					foreach ( $sources as $source ) {
						echo esc_html( $source->name );
						if ($length > 1) { echo  ', '; }
						$length--;
					}
				}
				?>
				</span>
				<?php $args = array(
								'taxonomy' => 'user_source',
								'hide_empty' => false,
								'class' => 'form-control d-none hidden-edit-select',
								'name' => 'user_source',
								'id' => 'user_source',
								'selected' => $selected_status,
								'show_option_none'   => esc_html__( 'Source', idCRMActionLanguage::TEXTDOMAIN ),
							);
				wp_dropdown_categories( $args ); ?>
				</h5>

				<h5 class="card-subtitle">
					<span class="visible-container">
				<?php

				$idcrm_contact_company = get_post_meta( $post->ID, 'idcrm_contact_company', true );
				$company_by_title = idCRMApiContact::get_post_by_title($idcrm_contact_company, 'company');

				$company_title = $company_by_title !== 0 ? get_the_title($company_by_title) : $idcrm_contact_company;
				if ( $company_by_title !== 0 ) {
					$company_url = get_post_permalink( $company_by_title );
					esc_html_e( 'Works in: ', idCRMActionLanguage::TEXTDOMAIN );
					echo ' <a href="' . esc_url( $company_url ) . '">' . esc_html( $company_title ) . '</a>';
				}

				?>
				</span>
				<select class="form-control mb-2 d-none hidden-edit-select" name="task_company" id="task_company">
					<option value=""><?php echo esc_html__( 'Company', idCRMActionLanguage::TEXTDOMAIN ); ?></option>
					<?php

					$companies = get_posts(
						array(
							'numberposts' => -1,
							'post_type'   => 'company',
						)
					);

					$company_post_id = get_post_meta( $post->ID, 'idcrm_contact_company', true );

					if (!empty($companies)) { ?>
							<?php foreach($companies as $company) { ?>
								<option <?php selected(get_the_title($company->ID), $company_post_id); ?> value="<?php echo esc_html( get_the_title($company->ID) ); ?>"><?php echo esc_html( get_the_title($company->ID) ); ?></option>
							<?php }
				 } ?>

				</select>
				</h5>

				<?php $position = get_post_meta( $post->ID, 'idcrm_contact_position', true ); ?>


					<div class="d-block position-relative mb-3">
						<?php if ( $position ) { ?>
							<span class="card-subtitle visible-container"><?php esc_html_e( 'At position: ', idCRMActionLanguage::TEXTDOMAIN ); ?> </span>
						<?php } ?>
						<span class="card-subtitle editable d-block" id="position" data-title="<?php echo esc_html__( 'Position', idCRMActionLanguage::TEXTDOMAIN ); ?>">
						<?php
						if ( $position ) {

							echo esc_html( $position );
						}
						?>
						</span>
						<input type="hidden" id="hidden-position" data-target="position" data-old-value="<?php echo esc_html($position); ?>" value="<?php echo esc_html($position); ?>" />
					</div>


				<?php if (is_plugin_active( 'idcrm-deals-documents/idcrm-deals-documents.php' ) && get_post_meta( $post->ID, 'idcrm_deal_id', true )) { ?>
					<?php include WP_PLUGIN_DIR . '/idcrm-deals-documents/templates/inc/contact-deals-list.php'; ?>
				<?php } ?>

				<div class="d-block position-relative">
					<h5 class="card-subtitle editable editable-date" id="birthday" data-title="<?php echo esc_html__( 'Birthday', idCRMActionLanguage::TEXTDOMAIN ); ?>">
					<?php
					$birthday = get_post_meta( $post->ID, 'idcrm_contact_birthday', true );
					if ( ! empty( $birthday ) ) {
						// echo '<br>';
						esc_html_e( 'Birthday: ', idCRMActionLanguage::TEXTDOMAIN );
					}
						echo '<span>' . esc_html( $birthday ) . '</span>';

					?>
					</h5>
					<input type="hidden" data-old-value="<?php echo esc_html($birthday); ?>" value="<?php echo esc_html($birthday); ?>" id="hidden-birthday" class="hidden-input" data-target="birthday" />
				</div>

				<select class="form-control my-2 d-none hidden-edit-select" name="gender" id="gender">
					<option value=""><?php echo esc_html__( 'Gender', idCRMActionLanguage::TEXTDOMAIN ); ?></option>
					<?php

					$genders = array(
							'male' => esc_html__( 'Male', idCRMActionLanguage::TEXTDOMAIN ),
							'female'   => esc_html__( 'Female', idCRMActionLanguage::TEXTDOMAIN ),
					);

					if (!empty($genders)) { ?>
							<?php foreach($genders as $key => $gender) { ?>
								<option <?php selected($key, get_post_meta( $post->ID, 'idcrm_contact_gender', true )); ?> value="<?php echo esc_html( $key ); ?>"><?php echo esc_html( $gender ); ?></option>
							<?php }
				 } ?>

				</select>

				<div class="small d-none hidden-edit-select">
					<div class="form-check form-switch py-0 mx-2">
							<input data-contact-id="<?php echo esc_attr($post->ID); ?>" class="form-check-input" type="checkbox" role="switch" id="idcrm_contact_lead_exclude"<?php echo get_post_meta( $post->ID, 'idcrm_contact_lead_exclude', true ) == '1' ? 'checked' : '' ?>>
							<label class="form-check-label" for="idcrm_contact_lead_exclude"><?php esc_html_e( 'Not count as lead', idCRMActionLanguage::TEXTDOMAIN ); ?></label>
						</div>
				</div>
			</div>

			<?php
			$facebook = get_post_meta( $post->ID, 'idcrm_contact_facebook', true );
			$twitter  = get_post_meta( $post->ID, 'idcrm_contact_twitter', true );
			$youtube  = get_post_meta( $post->ID, 'idcrm_contact_youtube', true );

			if ( ! empty( $facebook || $twitter || $youtube ) ) {
				echo '<hr />
			<div class="card-body" style="margin-bottom:15px;">';

				if ( ! empty( $facebook ) ) {
					echo '
			<button class="btn btn-circle btn-secondary">
			<a target="_blank" href="https://facebook.com/' . esc_html( $facebook ) . '"><i class="icon-social-facebook"></i></a>
			</button>';
				} if ( ! empty( $twitter ) ) {
					echo '
			<button class="btn btn-circle btn-secondary">
			<a target="_blank" href="https://twitter.com/@' . esc_html( $twitter ) . '"><i class="icon-social-twitter"></i></a>
			</button>';
				} if ( ! empty( $youtube ) ) {
					echo '
			<button class="btn btn-circle btn-secondary">
			<a target="_blank" href="https://youtube.com/channel/' . esc_html( $youtube ) . '"><i class="icon-social-youtube"></i></a>
			</button>';
				}
				echo '</div>';
			} else {

			}
			?>
			</div>
			<!-- Add company to contact -->
			<?php
			//echo '$idcrm_contact_company: ' . $idcrm_contact_company . '^<br />';
			if ( empty( $idcrm_contact_company ) ) {
				include 'inc/add-company.php';
				$post_title = '';
				if (array_key_exists('company_title', $_POST)) {
					$post_title = $_POST['company_title'];
				}
				//echo '$post_title: ' . $post_title . '^<br />';
				if ($post_title != '') {
					// $company = get_page_by_title( $post_title, OBJECT, 'company' );
					$company = idCRMApiContact::get_post_by_title($post_title, 'company');
					if ($company !== 0) {
						//echo '$post->ID: ' . $post->ID . '^<br />';
						//echo '$company->ID: ' . $company->ID . '^<br />';
						update_post_meta( $post->ID, 'idcrm_contact_company', sanitize_text_field( wp_unslash( $post_title )));
					}
				}
			}
			?>
			<!-- Schedule -->

			<?php set_query_var( 'current_post_id', $post->ID );
			require idCRM::$IDCRM_PATH . 'templates/inc/content-schedule.php'; ?>

			<!-- Notes -->
			<div class="card">
				<div id="edit-note-panel" class="card-body note-panel">
					<div class="edit">
						<div class="dropdown dropstart">
							<a href="#" class="link" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false" ><i data-feather="more-horizontal" class="feather-sm"></i></a>
							<ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
							<li>
							<span class="dropdown-item">
							<?php echo '<a class="edit-note" data-id="' . $post->ID . '" href="#">' . esc_html__( 'Edit', idCRMActionLanguage::TEXTDOMAIN ) . '</a>'; ?>
							</span>
							</li>
							</ul>
						</div>
					</div>
					<h3><?php esc_html_e( 'Notes', idCRMActionLanguage::TEXTDOMAIN ); ?></h3>

					<div class="note-text"><?php
						$post_object = get_post( $post->ID );
						echo $post_object->post_content;
						// get_the_content( $post->ID );
					?></div>

					<div class="note-edit-area"></div>
				</div>
			</div>

			</div>


			<!-- Column -->
			<div class="col-lg-8 col-xlg-9 col-md-7">
				<div class="card">
					<div class="card-body">
					<!-- Nav tabs -->
					<ul class="nav nav-tabs" role="tablist">
						<li class="nav-item">
							<a class="nav-link d-flex active" data-bs-toggle="tab" href="#home2" role="tab">
							<span><i class="icon-note"></i>
							</span>
							<span class="d-none d-md-block ms-2"><?php esc_html_e( 'Comments', idCRMActionLanguage::TEXTDOMAIN ); ?></span>
							</a>
						</li>

						<?php if ( is_plugin_active( 'idcrm-contacts-companies-pro/idcrm-contacts-companies-pro.php' ) ) { ?>
							<li class="nav-item">
								<a class="nav-link d-flex" data-bs-toggle="tab" href="#profile2" role="tab">
								<span><i class="icon-envelope-letter"></i>
								</span>
								<span class="d-none d-md-block ms-2"><?php esc_html_e( 'E-mail', idCRMActionLanguage::TEXTDOMAIN ); ?></span>
								</a>
							</li>
						<?php } ?>

						<li class="nav-item">
							<a class="nav-link d-flex" data-bs-toggle="tab" href="#schedule" role="tab">
							<span><i class="icon-clock"></i>
							</span>
							<span class="d-none d-md-block ms-2"><?php esc_html_e( 'Schedule', idCRMActionLanguage::TEXTDOMAIN ); ?></span>
							</a>
						</li>

						<?php if ( is_plugin_active( 'idcrm-contacts-companies-pro/idcrm-contacts-companies-pro.php' ) ) { ?>
							<?php if (idCRMApi::is_accessable( get_current_user_id(), 'idcrm-contacts-companies-pro') && idCRMApi::is_zadarma_active()) { ?>
								<li class="nav-item">
									<a class="nav-link d-flex" data-bs-toggle="tab" href="#zadarma-calls" role="tab">
									<span><i class="icon-phone"></i>
									</span>
									<span class="d-none d-md-block ms-2"><?php esc_html_e( 'Call history', idCRMActionLanguage::TEXTDOMAIN ); ?></span>
									</a>
								</li>
							<?php } ?>
						<?php } ?>
					</ul>
					<!--  Tab panes -->
					<div class="tab-content">
						<div class="tab-pane active" id="home2" role="tabpanel">

							<?php include idCRM::$IDCRM_PATH . 'templates/inc/content-comment-form.php'; ?>

						</div>

						<?php if ( is_plugin_active( 'idcrm-contacts-companies-pro/idcrm-contacts-companies-pro.php' ) ) { ?>
							<div class="tab-pane  p-3" id="profile2" role="tabpanel">
								<?php include WP_PLUGIN_DIR . '/idcrm-contacts-companies-pro/templates/inc/content-email-form.php'; ?>
							</div>
						<?php } ?>

						<div class="tab-pane p-3" id="schedule" role="tabpanel">

							<?php include idCRM::$IDCRM_PATH . 'templates/inc/content-schedule-form.php'; ?>

						</div>

						<?php if ( is_plugin_active( 'idcrm-contacts-companies-pro/idcrm-contacts-companies-pro.php' ) ) { ?>
							<?php if (idCRMApi::is_accessable( get_current_user_id(), 'idcrm-contacts-companies-pro') && idCRMApi::is_zadarma_active()) { ?>
								<div class="tab-pane scrollable" id="zadarma-calls" role="tabpanel">
									<?php include WP_PLUGIN_DIR . '/idcrm-contacts-companies-pro/templates/inc/contact-zadarma-calls-tab.php'; ?>
								</div>
							<?php } ?>
						<?php } ?>

					</div>
				</div>
			</div>

			<div class="card">
				<div class="card-body" id="comments-container-id" data-post-id="<?php echo esc_attr($post->ID); ?>">
					<div id="comments" class="comments-area default-max-width <?php echo get_option('show_avatars') ? 'show-avatars' : ''; ?>">
						<!-- Comments -->
						<?php

						idCRMApiComment::idcrmAjaxRefreshComments($post->ID);

						?>
						</div>
						<!-- #comments -->
					</div>
				</div>
			</div>
			<!-- Column -->
		</div>
		<!-- Row -->
	</div>
</div>

<?php require 'inc/footer.php'; ?>

<?php wp_footer(); ?>

</body>
</html>
