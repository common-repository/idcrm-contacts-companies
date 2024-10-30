<?php

use idcrm\includes\actions\idCRMActionLanguage;
use idcrm\includes\api\idCRMApi;
include_once ABSPATH . 'wp-admin/includes/plugin.php';

if ( is_plugin_active( 'idcrm-deals-documents/idcrm-deals-documents.php' ) ) {
	include WP_PLUGIN_DIR . '/idcrm-deals-documents/templates/inc/add-sidebar-deal.php';
} ?>


<?php if (idCRMApi::is_accessable( get_current_user_id(), 'idcrm-contacts-companies-pro')) { ?>
	<?php if ( is_plugin_active( 'idcrm-contacts-companies-pro/idcrm-contacts-companies-pro.php' ) ) { ?>
		<?php require 'add-user-zadarma.php'; ?>
	<?php } ?>
<?php } ?>

<footer class="footer small"><?php esc_html_e( 'Copyright', idCRMActionLanguage::TEXTDOMAIN ); ?> &copy;<?php echo date('Y'); ?>. <?php esc_html_e( 'id:CRM by', idCRMActionLanguage::TEXTDOMAIN ); ?> <a href="https://idresult.ru">id:Result</a>.</footer>

	</div>

		</div>

			<aside class="customizer">

				<div class="customizer-body">

					<div class="tab-content" id="pills-tabContent">
						<!-- Tab 1 -->
						<div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">
							<div class="p-3 border-bottom">
								<!-- Sidebar -->
								<h5 class="font-weight-medium mb-2 mt-2"><?php esc_html_e( 'Layout Settings', idCRMActionLanguage::TEXTDOMAIN ); ?></h5>
								<div class="form-check mt-3">
									<input type="checkbox" name="theme-view" class="form-check-input" id="theme-view" />
									<label class="form-check-label" for="theme-view">
										<span><?php esc_html_e( 'Dark Theme', idCRMActionLanguage::TEXTDOMAIN ); ?></span>
									</label>
								</div>
								<div class="form-check mt-2">
									<input type="checkbox" class="sidebartoggler form-check-input" name="collapssidebar" id="collapssidebar" />
									<label class="form-check-label" for="collapssidebar">
										<span><?php esc_html_e( 'Collapse Sidebar', idCRMActionLanguage::TEXTDOMAIN ); ?></span>
									</label>
								</div>
								<div class="form-check mt-2">
									<input type="checkbox" name="sidebar-position" class="form-check-input" id="sidebar-position" />
									<label class="form-check-label" for="sidebar-position">
										<span><?php esc_html_e( 'Fixed Sidebar', idCRMActionLanguage::TEXTDOMAIN ); ?></span>
									</label>
								</div>
								<div class="form-check mt-2">
									<input type="checkbox" name="header-position" class="form-check-input" id="header-position" />
									<label class="form-check-label" for="header-position">
										<span><?php esc_html_e( 'Fixed Header', idCRMActionLanguage::TEXTDOMAIN ); ?></span>
									</label>
								</div>
								<div class="form-check mt-2">
									<input type="checkbox" name="boxed-layout" class="form-check-input" id="boxed-layout" />
									<label class="form-check-label" for="boxed-layout">
										<span><?php esc_html_e( 'Boxed Layout', idCRMActionLanguage::TEXTDOMAIN ); ?></span>
									</label>
								</div>
							</div>
							<div class="p-3 border-bottom">
								<!-- Logo BG -->
								<h5 class="font-weight-medium mb-2 mt-2"><?php esc_html_e( 'Logo Backgrounds', idCRMActionLanguage::TEXTDOMAIN ); ?></h5>
								<ul class="theme-color m-0 p-0">
									<li class="theme-item list-inline-item me-1">
										<a href="javascript:void(0)" class="theme-link rounded-circle d-block" data-logobg="skin1"></a>
									</li>
									<li class="theme-item list-inline-item me-1">
										<a href="javascript:void(0)" class="theme-link rounded-circle d-block" data-logobg="skin2"></a>
									</li>
									<li class="theme-item list-inline-item me-1">
										<a href="javascript:void(0)" class="theme-link rounded-circle d-block" data-logobg="skin3"></a>
									</li>
									<li class="theme-item list-inline-item me-1">
										<a href="javascript:void(0)" class="theme-link rounded-circle d-block" data-logobg="skin4"></a>
									</li>
									<li class="theme-item list-inline-item me-1">
										<a href="javascript:void(0)" class="theme-link rounded-circle d-block" data-logobg="skin5"></a>
									</li>
									<li class="theme-item list-inline-item me-1">
										<a href="javascript:void(0)" class="theme-link rounded-circle d-block" data-logobg="skin6"></a>
									</li>
								</ul>
								<!-- Logo BG -->
							</div>
							<div class="p-3 border-bottom">
								<!-- Navbar BG -->
								<h5 class="font-weight-medium mb-2 mt-2"><?php esc_html_e( 'Navbar Backgrounds', idCRMActionLanguage::TEXTDOMAIN ); ?></h5>
								<ul class="theme-color m-0 p-0">
									<li class="theme-item list-inline-item me-1">
										<a href="javascript:void(0)" class="theme-link rounded-circle d-block" data-navbarbg="skin1"></a>
									</li>
									<li class="theme-item list-inline-item me-1">
										<a href="javascript:void(0)" class="theme-link rounded-circle d-block" data-navbarbg="skin2"></a>
									</li>
									<li class="theme-item list-inline-item me-1">
										<a href="javascript:void(0)" class="theme-link rounded-circle d-block" data-navbarbg="skin3"></a>
									</li>
									<li class="theme-item list-inline-item me-1">
										<a href="javascript:void(0)" class="theme-link rounded-circle d-block" data-navbarbg="skin4"></a>
									</li>
									<li class="theme-item list-inline-item me-1">
										<a href="javascript:void(0)" class="theme-link rounded-circle d-block" data-navbarbg="skin5"></a>
									</li>
									<li class="theme-item list-inline-item me-1">
										<a href="javascript:void(0)" class="theme-link rounded-circle d-block" data-navbarbg="skin6"></a>
									</li>
								</ul>
								<!-- Navbar BG -->
							</div>
							<div class="p-3 border-bottom">
								<!-- Logo BG -->
								<h5 class="font-weight-medium mb-2 mt-2"><?php esc_html_e( 'Sidebar Backgrounds', idCRMActionLanguage::TEXTDOMAIN ); ?></h5>
								<ul class="theme-color m-0 p-0">
									<li class="theme-item list-inline-item me-1">
										<a href="javascript:void(0)" class="theme-link rounded-circle d-block" data-sidebarbg="skin1"></a>
									</li>
									<li class="theme-item list-inline-item me-1">
										<a href="javascript:void(0)" class="theme-link rounded-circle d-block" data-sidebarbg="skin2"></a>
									</li>
									<li class="theme-item list-inline-item me-1">
										<a href="javascript:void(0)" class="theme-link rounded-circle d-block" data-sidebarbg="skin3"></a>
									</li>
									<li class="theme-item list-inline-item me-1">
										<a href="javascript:void(0)" class="theme-link rounded-circle d-block" data-sidebarbg="skin4"></a>
									</li>
									<li class="theme-item list-inline-item me-1">
										<a href="javascript:void(0)" class="theme-link rounded-circle d-block" data-sidebarbg="skin5"></a>
									</li>
									<li class="theme-item list-inline-item me-1">
										<a href="javascript:void(0)" class="theme-link rounded-circle d-block" data-sidebarbg="skin6"></a>
									</li>
								</ul>

							</div>
						</div>

					</div>
				</div>
			</aside>

			<!-- All scripts loads in /public/class-idcrm-contacts-public.php -->
