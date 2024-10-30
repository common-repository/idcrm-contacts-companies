<?php

use idcrm\idCRM;
use idcrm\includes\api\idCRMApi;
use idcrm\includes\actions\idCRMActionLanguage;

include_once ABSPATH . 'wp-admin/includes/plugin.php';
require_once __DIR__ . '/../../includes/actions/idcrm-action-scripts-remover.php';
use idcrm\includes\actions\idCRMActionScriptsRemover;

$user = wp_get_current_user();
$is_fired = get_user_meta(get_current_user_id(), 'idcrm_is_fired', true);
// if ( isset( $user->caps ) && !empty($user->caps) && array_key_exists('crm_support', $user->caps) || $is_fired == 'yes' ) {
if ( $is_fired == 'yes' ) {
	wp_safe_redirect( home_url() );
	exit;
}

$logo_locale = substr(get_locale(), 0, 2) == 'ru' ? '-' . substr(get_locale(), 0, 2) : '';
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>

	<meta http-equiv="Content-Type" content="<?php bloginfo( 'html_type' ); ?>; charset=<?php bloginfo( 'charset' ); ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<meta name='robots' content='noindex, nofollow' />

	<?php
		// add_action( 'wp_print_styles', 'remove_all_theme_styles', 100 );
		// add_action( 'wp_print_scripts', 'remove_all_theme_scripts', 110 );

		// use idcrm\includes\actions\idCRMActionScriptsRemover;
		// idCRMActionScriptsRemover::remove();

		// $exclude_scripts = array('new_script_1', 'new_script_2');
		// $theme_scripts_remover = new idCRMActionScriptsRemover();
		// // $theme_scripts_remover->register();
		// $theme_scripts_remover->scripts_filter();
		// $theme_scripts_remover->styles_filter();

		idCRMActionScriptsRemover::scripts_filter();
		idCRMActionScriptsRemover::styles_filter();

		wp_enqueue_script( 'app-min', idCRM::$IDCRM_URL . 'templates/dist/js/app.js', array( 'jquery' ), IDCRM_CONTACTS_VERSION, true );
		wp_enqueue_script( 'app-init', idCRM::$IDCRM_URL . 'templates/dist/js/app.init.js', array( 'jquery' ), IDCRM_CONTACTS_VERSION, true );
		wp_enqueue_script( 'app-style-switcher', idCRM::$IDCRM_URL . 'templates/dist/js/app-style-switcher.js', array( 'jquery' ), IDCRM_CONTACTS_VERSION, true );
		wp_enqueue_script( 'sidebarmenu', idCRM::$IDCRM_URL . 'templates/dist/js/sidebarmenu.js', array( 'jquery' ), IDCRM_CONTACTS_VERSION, true );

		wp_enqueue_script( 'bootstrap-table', idCRM::$IDCRM_URL . 'templates/dist/js/bootstrap-table.min.js', array( 'jquery' ), IDCRM_CONTACTS_VERSION, true );
		wp_enqueue_script( 'bootstrap-table-cookie', idCRM::$IDCRM_URL . 'templates/dist/js/bootstrap-table-cookie.js', array( 'jquery' ), IDCRM_CONTACTS_VERSION, true );
		wp_enqueue_script( 'bootstrap-table-locale', idCRM::$IDCRM_URL . 'templates/assets/libs/bootstrap-table/locale/bootstrap-table-' . str_replace('_', '-', get_locale()) . '.js', array( 'jquery' ), IDCRM_CONTACTS_VERSION, true );
		wp_enqueue_script( 'bootstrap-switch', idCRM::$IDCRM_URL . 'templates/dist/libs/bootstrap-switch/js/bootstrap-switch.min.js', array( 'jquery' ), IDCRM_CONTACTS_VERSION, true );

		wp_enqueue_style( 'monster-style', idCRM::$IDCRM_URL . 'templates/dist/css/style.min.css', array(), IDCRM_CONTACTS_VERSION );
		wp_enqueue_style( 'idcrm-contacts', idCRM::$IDCRM_URL . 'public/css/idcrm-contacts-public.min.css', array(), IDCRM_CONTACTS_VERSION );
		wp_enqueue_style( 'toastr', idCRM::$IDCRM_URL . 'public/css/toastr.min.css', array(), IDCRM_CONTACTS_VERSION );
		// wp_enqueue_style( 'bootstrap-style', idCRM::$IDCRM_URL . 'templates/dist/css/bootstrap.min.css' );
		wp_enqueue_style( 'bootstrap-table', idCRM::$IDCRM_URL . 'templates/dist/css/bootstrap-table.min.css', array(), IDCRM_CONTACTS_VERSION );
		wp_enqueue_style( 'bootstrap-material-datetimepicker', idCRM::$IDCRM_URL . 'templates/assets/libs/bootstrap-material-datetimepicker/css/bootstrap-material-datetimepicker.css', array(), IDCRM_CONTACTS_VERSION );
		wp_enqueue_style( 'fullcalendar', idCRM::$IDCRM_URL . 'public/css/fullcalendar.min.css', array(), IDCRM_CONTACTS_VERSION );
		wp_enqueue_style( 'apexcharts', idCRM::$IDCRM_URL . 'public/css/apexcharts.css', array(), IDCRM_CONTACTS_VERSION );
		wp_enqueue_style( 'select2', idCRM::$IDCRM_URL . 'public/css/select2.min.css', array(), IDCRM_CONTACTS_VERSION );
		wp_enqueue_style( 'select2-bootstrap', idCRM::$IDCRM_URL . 'public/css/select2-bootstrap-5-theme.min.css', array(), IDCRM_CONTACTS_VERSION );
		wp_enqueue_style( 'bootstrap-switch', idCRM::$IDCRM_URL . 'templates/dist/libs/bootstrap-switch/css/bootstrap-switch.min.css', array(), IDCRM_CONTACTS_VERSION );

		add_filter( 'show_admin_bar', '__return_false' ); // Disable admin bar.
		remove_action( 'wp_head', '_admin_bar_bump_cb' ); // Disable admin bar styles.
		remove_action( 'wp_head', 'wp_resource_hints', 2 );
		remove_action( 'wp_head', 'feed_links', 2 );
		remove_action( 'wp_head', 'feed_links_extra', 3 );
		remove_action( 'wp_head', 'rsd_link' );
		remove_action( 'wp_head', 'wlwmanifest_link' );
		remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0 );
		remove_action( 'wp_head', 'wp_generator' );
		remove_action( 'wp_head', 'rel_canonical' );
		remove_action( 'wp_head', 'wp_shortlink_wp_head', 10, 0 );
		remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
		 remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
		 remove_action( 'wp_print_styles', 'print_emoji_styles' );
		 remove_action( 'admin_print_styles', 'print_emoji_styles' );
		 remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
		 remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
		 remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
		 add_filter( 'tiny_mce_plugins', 'disable_emojis_tinymce' );
		 add_filter( 'wp_resource_hints', 'disable_emojis_remove_dns_prefetch', 10, 2 );
		wp_head();
	?>
	<script>var $ = jQuery.noConflict();</script>
</head>
<body <?php body_class(idCRMActionLanguage::TEXTDOMAIN . ' idcrm-body'); ?>>

		<div class="preloader">
			<svg class="tea lds-ripple" width="37" height="48" viewbox="0 0 37 48" fill="none" xmlns="http://www.w3.org/2000/svg">
				<path d="M27.0819 17H3.02508C1.91076 17 1.01376 17.9059 1.0485 19.0197C1.15761 22.5177 1.49703 29.7374 2.5 34C4.07125 40.6778 7.18553 44.8868 8.44856 46.3845C8.79051 46.79 9.29799 47 9.82843 47H20.0218C20.639 47 21.2193 46.7159 21.5659 46.2052C22.6765 44.5687 25.2312 40.4282 27.5 34C28.9757 29.8188 29.084 22.4043 29.0441 18.9156C29.0319 17.8436 28.1539 17 27.0819 17Z" stroke="#009efb" stroke-width="2"></path>
				<path d="M29 23.5C29 23.5 34.5 20.5 35.5 25.4999C36.0986 28.4926 34.2033 31.5383 32 32.8713C29.4555 34.4108 28 34 28 34" stroke="#009efb" stroke-width="2" ></path>
				<path id="teabag" fill="#009efb" fill-rule="evenodd" clip-rule="evenodd" d="M16 25V17H14V25H12C10.3431 25 9 26.3431 9 28V34C9 35.6569 10.3431 37 12 37H18C19.6569 37 21 35.6569 21 34V28C21 26.3431 19.6569 25 18 25H16ZM11 28C11 27.4477 11.4477 27 12 27H18C18.5523 27 19 27.4477 19 28V34C19 34.5523 18.5523 35 18 35H12C11.4477 35 11 34.5523 11 34V28Z"></path>
				<path id="steamL" d="M17 1C17 1 17 4.5 14 6.5C11 8.5 11 12 11 12" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" stroke="#009efb"></path>
				<path id="steamR" d="M21 6C21 6 21 8.22727 19 9.5C17 10.7727 17 13 17 13" stroke="#009efb" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
			</svg>
		</div>

		<div id="main-wrapper">

			<header class="topbar">
				<nav class="navbar top-navbar navbar-expand-md navbar-dark">
					<div class="navbar-header">
						<!-- This is for the sidebar toggle which is visible on mobile only -->
						<a class="nav-toggler waves-effect waves-light d-block d-md-none" href="javascript:void(0)"><i class="icon-menu"></i></a>

						<a class="navbar-brand" href="#">
							<!-- Logo icon -->
							<b class="logo-icon">
								<!--You can put here icon as well // <i class="wi wi-sunset"></i> //-->
								<!-- Dark Logo icon -->
								<?php echo '<img src="' . plugins_url( '../images/logo-icon.svg', __FILE__ ) . '" alt="homepage" class="dark-logo" />'; ?>
								<!-- Light Logo icon -->
								<?php echo '<img src="' . plugins_url( '../images/logo-light-icon.svg', __FILE__ ) . '" alt="homepage" class="light-logo" />'; ?>
							</b>
							<!--End Logo icon -->
							<!-- Logo text -->
							<span class="logo-text">
								<!-- dark Logo text -->
								<?php if ( is_plugin_active( 'idcrm-contacts-companies-pro/idcrm-contacts-companies-pro.php' ) ) { ?>
									<?php echo '<img src="' . plugins_url( '../images/logo-text-crm.svg', __FILE__ ) . '" alt="homepage" class="dark-logo" />'; ?>
								<?php } else { ?>
									<?php echo '<img src="' . plugins_url( '../images/logo-text' . $logo_locale . '.svg', __FILE__ ) . '" alt="homepage" class="dark-logo height-60" />'; ?>
								<?php } ?>
								<!-- Light Logo text -->
								<?php echo '<img src="' . plugins_url( '../images/logo-light-text' . $logo_locale . '.svg', __FILE__ ) . '" class="light-logo" alt="homepage" />'; ?>
							</span>
						</a>

						<a class="topbartoggler d-flex d-md-none align-items-center waves-effect waves-light" href="javascript:void(0)" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><i class="icon-options"></i></a>
					</div>

					<div class="navbar-collapse collapse" id="navbarSupportedContent">

						<ul class="navbar-nav me-3">
							<li class="nav-item d-none d-md-block">
								<a class="nav-link sidebartoggler waves-effect waves-light d-flex align-items-center" href="javascript:void(0)" data-sidebartype="mini-sidebar"><i class="icon-arrow-left-circle"></i></a>
							</li>

							<?php if (is_user_logged_in()) { ?>

								<li class="nav-item d-flex align-items-center px-2">
									<a class="position-relative d-flex mt-1 align-items-center text-white" href="<?php echo get_home_url() . '/?crm-notifications' ; ?>">
		                  <i data-feather="message-square" class="feather-icon"></i>
											<span id="unread-message-counter" class="position-absolute top-0 start-100 translate-middle badge fw-light small rounded-pill bg-danger"></span>
		                </a>

										<audio id="message-audio" class="d-none">
										  <source src="<?php echo idCRM::$IDCRM_URL; ?>public/audio/message-notification.mp3" type="audio/mpeg" />
										</audio>
										<button class="play-message-button d-none">Play</button>
								</li>

							<?php } ?>

							<?php $new_cf7_leads = 0;
							$current_user_id = get_current_user_id();
							$author = is_super_admin( $current_user_id ) ? "" : $current_user_id;
							$today = getdate();

							$leads_posts = get_posts([
		              'numberposts' => -1,
		              'post_type' => 'user_contact',
		              'author' => $author,
									'year'     => $today['year'],
									'monthnum' => $today['mon'],
									'day'      => $today['mday'],
		              'fields' => 'ids',
									'meta_query' => [[
		                  'key' => 'idcrm_contact_source',
		                  'value'   => 'cf7'
		              ]]
		          ]);

							if ($leads_posts) {
								$new_cf7_leads = count($leads_posts);
							}

							if ($new_cf7_leads > 0) { ?>

								<li class="nav-item d-flex align-items-center px-2">
									<a class="position-relative d-flex align-items-center text-white" href="<?php echo get_home_url() . '/crm-contacts/'; ?>">
		                  <i data-feather="user" class="feather-icon"></i>
											<span id="new-cf7-leads" class="position-absolute top-0 start-100 translate-middle badge fw-light small rounded-pill bg-danger"><?php echo $new_cf7_leads; ?></span>
		                </a>
								</li>

							<?php } ?>


							<?php if (idCRMApi::is_accessable( $user->ID, 'idcrm-contacts-companies-pro')) { ?>
								<?php if ( is_plugin_active( 'idcrm-contacts-companies-pro/idcrm-contacts-companies-pro.php' ) ) { ?>

									<li class="nav-item d-flex align-items-center px-2">
										<a class="position-relative d-flex align-items-center text-white" href="<?php echo get_home_url() . '/mailbox/' ; ?>">
			                  <i data-feather="mail" class="feather-icon"></i>
												<span id="unread-mail-counter" class="position-absolute top-0 start-100 translate-middle badge fw-light small rounded-pill bg-danger"></span>
			                </a>
									</li>

							<?php } ?>
						<?php } ?>

						</ul>

						<div class="navbar-nav me-auto navbar-info-container text-light d-flex gap-3"></div>

						<ul class="navbar-nav">

							<li class="nav-item search-box d-none d-md-block">

								<?php if (is_user_logged_in()) { ?>

									<?php if ( get_page_template_slug() == 'templates/mailbox.php' ) { ?>

										<div class="app-search mt-3-1 me-2">
											<input placeholder="<?php esc_html_e( 'Search Mail', idCRMActionLanguage::TEXTDOMAIN ); ?>" id="search-mail" type="text" class="form-control rounded-pill border-0" />
											<button type="button" class="search-mail-button srh-btn border border-0 bg-transparent">
												<i data-feather="search" class="feather-sm mt-n1-ird"></i>
											</button>
										</div>

									<?php } else { ?>

									<form class="app-search mt-3-1 me-2" role="search" method="get" id="searchform" action="<?php echo home_url( '/' ); ?>" >
										<!--label class="screen-reader-text" for="s">Поиск: </label-->
										<input class="form-control rounded-pill border-0" type="text" placeholder="<?php echo esc_attr_e( 'Search …', idCRMActionLanguage::TEXTDOMAIN ); ?>" value="<?php echo get_search_query(); ?>" name="s" id="s" />
										<input type="hidden" value="idcrm" name="search_type" />
										<!--input type="submit" class="srh-btn" value="<?php echo esc_attr_x( 'Search', 'submit button' ); ?>" /-->
										<!--a class="srh-btn"><i data-feather="search" class="feather-sm fill-white mt-n1"></i></a-->
										<!--input type="submit" class="srh-btn" style="border: 0px;" id="searchsubmit" value="" /-->
										<button type="submit" class="srh-btn border border-0 bg-transparent">
											<i data-feather="search" class="feather-sm mt-n1-ird"></i>
										</button>
									</form>

									<?php } ?>

								<?php } ?>
							</li>

							<?php if (is_user_logged_in()) { ?>
								<?php
									$current_user = wp_get_current_user();
									$user_profile_img      = get_user_meta( $current_user->ID, 'userimg', true );
									$default_profile_image = idCRM::$IDCRM_URL . 'templates/images/no-user.jpg';
								?>
								<li class="nav-item dropdown">
									<a class="nav-link dropdown-toggle waves-effect waves-dark" href="#" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
										<?php
										if ( ! empty( $user_profile_img ) ) {
											echo '<img src="' . esc_html( $user_profile_img ) . '" width="30" height="30" class="profile-pic rounded-circle">';
										} else {
											echo '<img src="' . esc_html( $default_profile_image ) . '" width="30" height="30" class="profile-pic rounded-circle">';
										}
										?>
									</a>
									<div class="dropdown-menu dropdown-menu-end user-dd animated flipInY">
										<div class="d-flex no-block align-items-center p-3 bg-info text-white mb-2">
											<div>
												<?php
												if ( ! empty( $user_profile_img ) ) {
													echo '<img src="' . esc_html( $user_profile_img ) . '" width="60" height="60" class="profile-pic rounded-circle">';
												} else {
													echo '<img src="' . esc_html( $default_profile_image ) . '" width="60" height="60" class="profile-pic rounded-circle">';
												}
												?>
											</div>
											<div class="ms-2">
												<h4 class="mb-0 text-white">
												<?php
													echo esc_html( "$current_user->user_firstname" );
													echo '<br>' . esc_html( "$current_user->user_lastname" );
												?>
												</h4>
											</div>

										</div>
										<?php $edit_user_link = get_edit_user_link( $current_user->ID ); ?>
										<a class="dropdown-item" href="<?php echo get_home_url() . '/?crm-profile'; ?>">
											<i data-feather="user" class="feather-sm text-info me-1 ms-1"></i>
											<?php esc_html_e( 'My Profile', idCRMActionLanguage::TEXTDOMAIN ); ?>
										</a>

										<div class="dropdown-divider"></div>
										<a class="dropdown-item" href="<?php echo wp_logout_url( home_url() . '/crm/' ); ?>">
											<i data-feather="log-out" class="feather-sm text-danger me-1 ms-1"></i>
											<?php esc_html_e( 'Logout', idCRMActionLanguage::TEXTDOMAIN ); ?>
										</a>

									</div>
								</li>

							<?php } ?>

						</ul>
					</div>
				</nav>
			</header>
