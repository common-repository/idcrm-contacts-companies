<?php

require 'inc/check-user.php';
require 'inc/header.php';
require 'inc/sidebar.php';
include_once ABSPATH . 'wp-admin/includes/plugin.php';

use idcrm\idCRM;
use idcrm\includes\actions\idCRMActionLanguage;
use idcrm\includes\api\idCRMApiComment;

$active_tabs = [
	'',
	'active'
]; ?>

	<div class="page-wrapper">

		<?php require 'inc/breadcrumbs.php'; ?>

		<div class="container-fluid">

		<div class="row">
			<!-- Column -->
			<div class="col-lg-4 col-xlg-3 col-md-5">
				<div class="card editable-card" id="single-company" data-company-id="<?php echo esc_attr($post->ID); ?>">

					<div class="edit-control px-3 py-1 pb-2 gap-3">
						<div class="edit-company-apply" title="<?php echo esc_html__( 'Apply changes', idCRMActionLanguage::TEXTDOMAIN ); ?>">
							<div class="icon">
	              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check-circle feather-sm"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
	            </div>
						</div>
						<div class="edit-company-cancel" title="<?php echo esc_html__( 'Cancel changes', idCRMActionLanguage::TEXTDOMAIN ); ?>">
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
												<?php echo '<a class="edit-company-fields" href="' . get_edit_post_link() . '">' . esc_html__( 'Edit', idCRMActionLanguage::TEXTDOMAIN ) . ' <span class="badge bg-light text-dark fw-normal border text-muted small edit-shortcut"></span></a>'; ?>
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
								echo '<img src="' . esc_html( $default_image ) . '" class="rounded-circle editable object-fit-cover wp-post-image" width="120" height="120" />';
							}
							?>
							<span class="ms-3 fw-normal">
								<h2 class="card-title mt-2 editable" id="company-title" data-title="<?php esc_html_e( 'Company Title', idCRMActionLanguage::TEXTDOMAIN ); ?>"><?php the_title(); ?></h2>
								<input type="hidden" id="hidden-company-title" data-target="company-title" data-old-value="<?php the_title(); ?>" value="<?php the_title(); ?>" />

								<h5 class="card-subtitle">
									<span class="visible-container">
								<?php
								$statuses = get_the_terms( get_the_ID(), 'comp_status' );
								$selected_status = '';
								if ( ! empty( $statuses ) ) {
									$length = count($statuses);
									foreach ( $statuses as $comp_status ) {
										$selected_status = $comp_status->term_id;
										echo esc_html( $comp_status->name );
										if ($length > 1) { echo  ', '; }
										$length--;
									}
								}
								?>
									</span>

									<?php $args = array(
													'taxonomy' => 'comp_status',
													// 'hierarchical'    => true,
													'hide_empty' => false,
													'class' => 'form-control d-none hidden-edit-select',
													'name' => 'comp_status',
													'id' => 'comp_status',
													'selected' => $selected_status,
													'show_option_none'   => esc_html__( 'Company Status', idCRMActionLanguage::TEXTDOMAIN ),
												);
									wp_dropdown_categories( $args ); ?>

								</h5>

								<div id="current_term_id" data-section="companies" data-term-id="<?php echo esc_html($selected_status); ?>" class="d-none"></div>

								<h5 class="card-subtitle">
									<?php $website = get_post_meta( $post->ID, 'idcrm_company_website', true ); ?>

									<a target="_blank" id="company-website" href="<?php echo esc_html($website); ?>" class="editable d-block" data-title="<?php echo esc_html__( 'Website', idCRMActionLanguage::TEXTDOMAIN ); ?>"><?php echo esc_html($website); ?></a>
									<input type="hidden" id="hidden-company-website" data-target="company-website" data-old-value="<?php echo esc_html($website); ?>" value="<?php echo esc_html($website); ?>" />
								</h5>

							</span>
						</div>
					</div>

					<div class="d-flex align-items-center" style="margin-bottom: 15px;">
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
						<span class="ms-3 fw-normal" >
								<?php the_author_meta( 'display_name', $post->post_author ); ?>
						</span>
					</div>


					<?php
							$facebook = get_post_meta( $post->ID, 'idcrm_company_facebook', true );
							$twitter  = get_post_meta( $post->ID, 'idcrm_company_twitter', true );
							$youtube  = get_post_meta( $post->ID, 'idcrm_company_youtube', true ); ?>

						<div class="px-3">

						<?php if ( ! empty( $facebook ) ) { ?>
									<button class="btn btn-circle btn-secondary visible-container">
										<a target="_blank" href="https://facebook.com/<?php echo esc_html( $facebook ); ?>"><i class="icon-social-facebook"></i></a>
									</button>
						<?php } ?>
								<div id="company-facebook" class="editable d-none hidden-edit-select mb-3" data-title="<?php echo esc_html__( 'Facebook', idCRMActionLanguage::TEXTDOMAIN ); ?>"><?php echo esc_html($facebook); ?></div>
								<input type="hidden" id="hidden-company-facebook" data-target="company-facebook" data-old-value="<?php echo esc_html($facebook); ?>" value="<?php echo esc_html($facebook); ?>" />

						<?php if ( ! empty( $twitter ) ) { ?>
								<button class="btn btn-circle btn-secondary visible-container">
									<a target="_blank" href="https://twitter.com/@<?php echo esc_html( $twitter ); ?>"><i class="icon-social-twitter"></i></a>
								</button>
						<?php } ?>
							<div id="company-twitter" class="editable d-none hidden-edit-select mb-3" data-title="<?php echo esc_html__( 'Twitter', idCRMActionLanguage::TEXTDOMAIN ); ?>"><?php echo esc_html($twitter); ?></div>
							<input type="hidden" id="hidden-company-twitter" data-target="company-twitter" data-old-value="<?php echo esc_html($twitter); ?>" value="<?php echo esc_html($twitter); ?>" />

						<?php if ( ! empty( $youtube ) ) { ?>
								<button class="btn btn-circle btn-secondary visible-container">
									<a target="_blank" href="https://youtube.com/channel/<?php echo esc_html( $youtube ); ?>"><i class="icon-social-youtube"></i></a>
								</button>
						<?php } ?>
							<div id="company-youtube" class="editable d-none hidden-edit-select mb-3" data-title="<?php echo esc_html__( 'YouTube', idCRMActionLanguage::TEXTDOMAIN ); ?>"><?php echo esc_html($youtube); ?></div>
							<input type="hidden" id="hidden-company-youtube" data-target="company-youtube" data-old-value="<?php echo esc_html($youtube); ?>" value="<?php echo esc_html($youtube); ?>" />

						</div>

						<!-- Company Settings  -->

						<?php
							$inn  = get_post_meta( $post->ID, 'idcrm_company_inn', true );
							$kpp  = get_post_meta( $post->ID, 'idcrm_company_kpp', true );
							$ogrn = get_post_meta( $post->ID, 'idcrm_company_ogrn', true );

						if ( ! empty( $inn || $kpp || $ogrn ) ) {
							echo '<hr class="my-2">';
						} ?>
								<div class="px-3">

							<?php if ( ! empty( $inn ) ) { ?>
								<div class="visible-container mb-2"><?php echo esc_html__( 'TIN:', idCRMActionLanguage::TEXTDOMAIN ) . ' ' . esc_html( $inn ); ?></div>
							<?php } ?>
								<div id="company-inn" class="editable d-none hidden-edit-select mb-3" data-title="<?php echo esc_html__( 'TIN:', idCRMActionLanguage::TEXTDOMAIN ); ?>"><?php echo esc_html($inn); ?></div>
								<input type="hidden" id="hidden-company-inn" data-target="company-inn" data-old-value="<?php echo esc_html($inn); ?>" value="<?php echo esc_html($inn); ?>" />

							<?php if ( ! empty( $kpp ) ) { ?>
								<div class="visible-container mb-2"><?php echo esc_html__( 'KPP:', idCRMActionLanguage::TEXTDOMAIN ) . ' ' . esc_html( $kpp ); ?></div>
							<?php } ?>
								<div id="company-kpp" class="editable d-none hidden-edit-select mb-3" data-title="<?php echo esc_html__( 'KPP:', idCRMActionLanguage::TEXTDOMAIN ); ?>"><?php echo esc_html($kpp); ?></div>
								<input type="hidden" id="hidden-company-kpp" data-target="company-kpp" data-old-value="<?php echo esc_html($kpp); ?>" value="<?php echo esc_html($kpp); ?>" />

							<?php if ( ! empty( $ogrn ) ) { ?>
								<div class="visible-container mb-2"><?php echo esc_html__( 'LEI:', idCRMActionLanguage::TEXTDOMAIN ) . ' ' . esc_html( $ogrn ); ?></div>
							<?php } ?>
								<div id="company-ogrn" class="editable d-none hidden-edit-select mb-3" data-title="<?php echo esc_html__( 'LEI:', idCRMActionLanguage::TEXTDOMAIN ); ?>"><?php echo esc_html($ogrn); ?></div>
								<input type="hidden" id="hidden-company-ogrn" data-target="company-ogrn" data-old-value="<?php echo esc_html($ogrn); ?>" value="<?php echo esc_html($ogrn); ?>" />

							</div>

					</div>

					<div class="card">
						<div class="table-responsive scrollable"  style="font-size:14px;">
							<table class="table customize-table table-hover mb-0 v-middle">
								<thead class="table-light">
									<tr>
										<th class="border-top-0 min-width-100"><?php esc_html_e( 'Name', idCRMActionLanguage::TEXTDOMAIN ); ?></th>
										<th class="border-top-0"><?php esc_html_e( 'Position', idCRMActionLanguage::TEXTDOMAIN ); ?></th>
										<th class="border-top-0"><?php esc_html_e( 'Phone', idCRMActionLanguage::TEXTDOMAIN ); ?></th>
										<th class="border-top-0 text-nowrap"><?php esc_html_e( 'Email', idCRMActionLanguage::TEXTDOMAIN ); ?></th>
										<th class="border-top-0">&nbsp;</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<?php
											// User object.
											$user_query = get_posts(
												array(
													'post_type'   => 'user_contact',
													'post_status' => 'publish',
													'numberposts' => -1,
													// 'meta_key'    => 'idcrm_contact_company',
													// 'meta_value'  => $post->ID,
													'meta_query' => [
															[
															'key' => 'idcrm_contact_company',
															'value' => $post->post_title,
															],
													],
												)
											);

											if ( ! empty( $user_query ) ) {
												foreach ( $user_query as $user ) {
													?>
										<td class="min-width-100">
											<a href="<?php echo esc_url( get_permalink( $user->ID ) ); ?>"><?php echo get_the_title( $user->ID ); ?></a>
										</td>
										<td>
													<?php
													$position = get_post_meta( $user->ID, 'idcrm_contact_position', true );
													echo esc_html( $position );
													?>
										</td>
										<td>
													<?php
													$phone = get_post_meta( $user->ID, 'idcrm_contact_phone', true );
													echo '<a href="tel:' . esc_html( $phone ) . '">' . esc_html( $phone ) . '</a>';
													?>
										</td>
										<td>
													<?php
													$email = get_post_meta( $user->ID, 'idcrm_contact_email', true );
													echo '<a href="mailto:' . esc_html( $email ) . '">' . esc_html( $email ) . '</a>';
													?>
										</td>
										<td>
											<div class="dropdown dropstart">
												<a href="#" class="link" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
													<i data-feather="more-horizontal" class="feather-sm"></i>
												</a>
												<ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
													<li>
													<span class="dropdown-item"><?php echo '<a href="' . get_the_permalink($user->ID) . '?idcrm_action=edit">' . esc_html__( 'Edit', idCRMActionLanguage::TEXTDOMAIN ) . '</a>'; ?></span>
													</li>


												</ul>
											</div>
										</td>
									</tr>
													<?php
												}
											}
											?>
								</tbody>
							</table>
						</div>
					</div>
					<!-- Add contact form -->


					<?php require 'inc/add-user.php'; ?>

					<?php
						// set_query_var( 'current_post_id', $post->ID );
						// require idCRM::$IDCRM_PATH . 'templates/inc/content-schedule.php';
					?>

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

							<div class="note-text"><?php $post_object = get_post( $post->ID );
							echo $post_object->post_content; ?></div>

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

							<?php if ( is_plugin_active( 'idcrm-deals-documents/idcrm-deals-documents.php' ) ) {
								$active_tabs = [
									'active',
									''
								]; ?>
								<li class="nav-item">
									<a class="nav-link d-flex <?php echo $active_tabs[0]; ?>" data-bs-toggle="tab" href="#deals" role="tab">
									<span><i class="icon-briefcase"></i>
									</span>
									<span class="d-none d-md-block ms-2"><?php esc_html_e( 'Deals', idCRMActionLanguage::TEXTDOMAIN ); ?></span>
									</a>
								</li>
							<?php } ?>

							<li class="nav-item">
								<a class="nav-link d-flex <?php echo $active_tabs[1]; ?>" data-bs-toggle="tab" href="#home2" role="tab">
								<span><i class="icon-note"></i>
								</span>
								<span class="d-none d-md-block ms-2"><?php esc_html_e( 'Comments', idCRMActionLanguage::TEXTDOMAIN ); ?></span>
								</a>
							</li>

							<!--<li class="nav-item">
								<a class="nav-link d-flex" data-bs-toggle="tab" href="#schedule" role="tab">
								<span><i class="icon-clock"></i>
								</span>
								<span class="d-none d-md-block ms-2"><?php esc_html_e( 'Schedule', idCRMActionLanguage::TEXTDOMAIN ); ?></span>
								</a>
							</li>-->
						</ul>

						<div class="tab-content">

							<?php if ( is_plugin_active( 'idcrm-deals-documents/idcrm-deals-documents.php' ) ) { ?>
								<div class="tab-pane <?php echo $active_tabs[0]; ?>" id="deals" role="tabpanel">

									<?php include WP_PLUGIN_DIR . '/idcrm-deals-documents/templates/inc/content-deals-tab.php'; ?>

								</div>
							<?php } ?>

							<div class="tab-pane <?php echo $active_tabs[1]; ?>" id="home2" role="tabpanel">

								<?php include idCRM::$IDCRM_PATH . 'templates/inc/content-comment-form.php'; ?>

							</div>

						<!--<div class="tab-pane p-3" id="schedule" role="tabpanel">

							<?php
								// include idCRM::$IDCRM_PATH . 'templates/inc/content-schedule-form.php';
							?>

						</div>-->

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

		</div>
</div>
	<?php require 'inc/footer.php'; ?>

	<?php wp_footer(); ?>
	</body>
</html>
