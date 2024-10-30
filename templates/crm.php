<?php

// require 'inc/header.php';

use idcrm\idCRM;
use idcrm\includes\actions\idCRMActionLanguage;

$idcrm_settings = unserialize( get_option( 'idcrm_settings' ) && !is_array(get_option( 'idcrm_settings' )) ? get_option( 'idcrm_settings' ) : 'a:0:{}' );

$idcrm_start_page = array_key_exists( 'idcrm_start_page', $idcrm_settings ) ? $idcrm_settings['idcrm_start_page'] : '';

if ( is_user_logged_in() && post_type_exists('company') ) {

	$user = wp_get_current_user();
	$current_user_id = get_current_user_id();
	$idcrm_team_user_id = $current_user_id ? get_user_meta($current_user_id, 'idcrm_team_user_id', true) : '';
	$idcrm_team_roles = $idcrm_team_user_id ? get_post_meta($idcrm_team_user_id, 'idcrm_team_roles', true) : '';

	if ( isset( $user->caps ) && !empty($user->caps)
		&& !array_key_exists('crm_client', $user->caps)
		&& $idcrm_team_roles != 'crm_client'
		&& !array_key_exists('crm_team', $user->caps)
		&& $idcrm_team_roles != 'crm_team'
		&& !array_key_exists('crm_support', $user->caps)
		&& $idcrm_team_roles != 'crm_support') {
	//echo '<pre>get_post_type_archive_link: ' . (get_post_type_archive_link('company') === false ? 'false' : get_post_type_archive_link('company')) . '</pre>';
	//echo '<pre>post_type_exists: ' . (post_type_exists('company') ? 'true' : 'false') . '</pre>';

		if ($idcrm_start_page) {
			wp_redirect(home_url('/') . $idcrm_start_page);
			exit;
		} else {
			wp_redirect( get_post_type_archive_link('company'));
			exit;
		}

	} else {
		if (post_type_exists('idcrm_task') && $idcrm_team_roles == 'crm_client'
			|| post_type_exists('idcrm_task') && $idcrm_team_roles == 'crm_team'
			|| post_type_exists('idcrm_task') && $idcrm_team_roles == 'crm_support') {
			wp_redirect( get_post_type_archive_link('idcrm_task'));
			exit;
		} else {
			wp_redirect(home_url());
			exit;
		}
	}

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
	function remove_all_theme_styles() {
		global $wp_styles;
		foreach ( $wp_styles->queue as $style ) :
			if ( 'idcrm-contacts' !== $style && 'monster-style' !== $style ) {
				wp_deregister_style( $style );
				wp_dequeue_style( $style );
			}
			endforeach;
	}

		add_action( 'wp_print_styles', 'remove_all_theme_styles', 100 );

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

		wp_enqueue_style( 'monster-style', idCRM::$IDCRM_URL . 'templates/dist/css/style.css', array() );
		wp_enqueue_style( 'idcrm-contacts', idCRM::$IDCRM_URL . 'public/css/idcrm-contacts-public.min.css' );
		wp_enqueue_style( 'toastr', idCRM::$IDCRM_URL . 'public/css/toastr.min.css', array() );
		wp_enqueue_style( 'bootstrap-style', idCRM::$IDCRM_URL . 'templates/dist/css/bootstrap.min.css' );

		wp_head();
	?>
	<script>var $ = jQuery.noConflict();</script>
</head>

	<body>
		<div class="main-wrapper">

			<div class="preloader">
				<svg class="tea lds-ripple" width="37" height="48" viewbox="0 0 37 48" fill="none" xmlns="http://www.w3.org/2000/svg">
				  <path d="M27.0819 17H3.02508C1.91076 17 1.01376 17.9059 1.0485 19.0197C1.15761 22.5177 1.49703 29.7374 2.5 34C4.07125 40.6778 7.18553 44.8868 8.44856 46.3845C8.79051 46.79 9.29799 47 9.82843 47H20.0218C20.639 47 21.2193 46.7159 21.5659 46.2052C22.6765 44.5687 25.2312 40.4282 27.5 34C28.9757 29.8188 29.084 22.4043 29.0441 18.9156C29.0319 17.8436 28.1539 17 27.0819 17Z" stroke="#2962FF" stroke-width="2"></path>
				  <path d="M29 23.5C29 23.5 34.5 20.5 35.5 25.4999C36.0986 28.4926 34.2033 31.5383 32 32.8713C29.4555 34.4108 28 34 28 34" stroke="#2962FF" stroke-width="2"></path>
				  <path id="teabag" fill="#2962FF" fill-rule="evenodd" clip-rule="evenodd" d="M16 25V17H14V25H12C10.3431 25 9 26.3431 9 28V34C9 35.6569 10.3431 37 12 37H18C19.6569 37 21 35.6569 21 34V28C21 26.3431 19.6569 25 18 25H16ZM11 28C11 27.4477 11.4477 27 12 27H18C18.5523 27 19 27.4477 19 28V34C19 34.5523 18.5523 35 18 35H12C11.4477 35 11 34.5523 11 34V28Z"></path>
				  <path id="steamL" d="M17 1C17 1 17 4.5 14 6.5C11 8.5 11 12 11 12" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" stroke="#2962FF"></path>
				  <path id="steamR" d="M21 6C21 6 21 8.22727 19 9.5C17 10.7727 17 13 17 13" stroke="#2962FF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
				</svg>
			</div>

			<div class="row auth-wrapper gx-0">
				<div class="col-lg-4 col-xl-3 bg-info auth-box-2 on-sidebar">
					<div class="h-100 d-flex align-items-center justify-content-center">
						<div class="row justify-content-center text-center">
							<div class="col-md-7 col-lg-12 col-xl-12">
								<div>
									<span class="db">
											<?php echo '<img src="' . plugins_url( 'images/logo-light-icon.svg', __FILE__ ) . '" alt="logo" class="light-logo" width="45" height="40" />'; ?>
									</span>
									<span class="db">
										<?php if ( is_plugin_active( 'idcrm-contacts-companies-pro/idcrm-contacts-companies-pro.php' ) || is_plugin_active( 'idcrm-deals-documents/idcrm-deals-documents.php' ) ) { ?>
											<?php echo '<img src="' . plugins_url( '/images/logo-light-text-crm.svg', __FILE__ ) . '" alt="logo" width="105" height="70" />'; ?>
										<?php } else { ?>
											<?php echo '<img src="' . plugins_url( '/images/logo-light-text' . $logo_locale . '.svg', __FILE__ ) . '" alt="logo" width="148" height="70" />'; ?>
										<?php } ?>
									</span>
								</div>
								<!--h2 class="text-white mt-4 fw-light">
									<span class="font-weight-medium">id:CRM</span>
									<br>WordPress CRM Plugin
								</h2-->
								<!-- Left info block
								<p class="op-5 text-white fs-4 mt-4">
									Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed
									do eiusmod tempor incididunt.
								</p>
								-->
							</div>
						</div>
					</div>
				</div>
				<div class="col-lg-8 col-xl-9 d-flex align-items-center justify-content-center">
				<div class="auth-box p-4 bg-white rounded">
					<div id="loginform">
						<div class="logo">
							<h3 class="box-title mb-3"><?php esc_html_e( 'Sign In', idCRMActionLanguage::TEXTDOMAIN ); ?></h3>
						</div>
						<!-- Form -->
						<div class="row">
							<div class="col-12">
								<form class="form-horizontal mt-3 form-material" id="loginform" action="<?php bloginfo( 'url' ); ?>/wp-login.php" method="post">
									<div class="form-group mb-3">
										<div class="">
											<input class="form-control" type="text" required placeholder="<?php esc_html_e( 'Login', idCRMActionLanguage::TEXTDOMAIN ); ?>" id="user_login" name="log" />
										</div>
									</div>
									<div class="form-group mb-4">
										<div class="">
											<input class="form-control" type="password" required placeholder="<?php esc_html_e( 'Password', idCRMActionLanguage::TEXTDOMAIN ); ?>" id="user_pass" name="pwd" />
										</div>
									</div>
									<div class="form-group">
										<div class="d-flex">
											<div class="checkbox checkbox-info pt-0">
												<input id="checkbox-signup" type="checkbox" class="material-inputs chk-col-indigo" name="rememberme" value="forever" />
												<label for="checkbox-signup"><?php esc_html_e( 'Remember me', idCRMActionLanguage::TEXTDOMAIN ); ?></label>
											</div>
											<div class="ms-auto">
												<a href="<?php echo esc_url( wp_lostpassword_url() ); ?>" id="to-recover" class="link font-weight-medium">
													<i class="fa fa-lock me-1"></i><?php esc_html_e( 'Forgot pwd?', idCRMActionLanguage::TEXTDOMAIN ); ?>
												</a>
											</div>
										</div>
									</div>
									<div class="form-group text-center mt-4 mb-3">
										<div class="col-xs-12">
											<button class="btn btn-info d-block w-100 waves-effect waves-light" type="submit" name="wp-submit" id="wp-submit">
												<?php esc_html_e( 'Log In', idCRMActionLanguage::TEXTDOMAIN ); ?>
											</button>

											<input type="hidden" name="redirect_to" value="<?php bloginfo( 'url' ); ?>/crm/" />

											<input type="hidden" name="testcookie" value="1" />
										</div>
									</div>

									</div>
								</form>
							</div>
						</div>
					</div>

									<!-- Go back to custom login if errors -->

							</div>
						</div>
					</div>
				</div>
			</div>

		</div>

<?php
wp_footer();
?>
