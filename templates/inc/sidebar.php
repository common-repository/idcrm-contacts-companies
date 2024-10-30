<?php

use idcrm\includes\actions\idCRMActionLanguage;
use idcrm\includes\api\idCRMApi;
include_once ABSPATH . 'wp-admin/includes/plugin.php';

$in_class_contacts = is_single() && get_post_type( get_the_ID() ) == 'user_contact' ? 'in' : '';
$active_class_contacts = is_single() && get_post_type( get_the_ID() ) == 'user_contact' ? 'active' : '';
$in_class_companies = is_single() && get_post_type( get_the_ID() ) == 'company' ? 'in' : '';
$active_class_companies = is_single() && get_post_type( get_the_ID() ) == 'company' ? 'active' : '';

$user = wp_get_current_user();
?>

<aside class="left-sidebar">
	<!-- Sidebar scroll-->
	<div class="scroll-sidebar">
		<!-- Sidebar navigation-->
		<nav class="sidebar-nav">
			<ul id="sidebarnav" class="pb-2">
			<li class="nav-small-cap">
					<!-- <i class="mdi mdi-dots-horizontal"></i> -->
					<span class="hide-menu"></span>
				</li>
				<?php if (idCRMApi::is_accessable( $user->ID, 'idcrm-contacts-companies-pro')) { ?>
					<?php if ( is_plugin_active( 'idcrm-contacts-companies-pro/idcrm-contacts-companies-pro.php' ) ) { ?>
						<li class="sidebar-item">
							<a class="sidebar-link waves-effect waves-dark" href="/crm-dashboard/" aria-expanded="false" >
								<i data-feather="grid" class="feather-icon"></i>
								<span class="hide-menu"><?php esc_html_e( 'CRM Dashboard', idCRMActionLanguage::TEXTDOMAIN ); ?></span></a>
						</li>
					<?php } ?>
				<?php } ?>


				<?php if (is_user_logged_in()) { ?>
					<li class="sidebar-item">
						<a class="sidebar-link waves-effect waves-dark" href="<?php echo get_home_url() . '/?crm-notifications' ; ?>" aria-expanded="false" >
							<i data-feather="message-square" class="feather-icon"></i>
							<span class="hide-menu"><?php esc_html_e( 'Notifications', idCRMActionLanguage::TEXTDOMAIN ); ?></span></a>
					</li>
				<?php } ?>

<?php if (idCRMApi::is_accessable( $user->ID, 'idcrm-contacts-companies')) { ?>

				<li class="nav-small-cap">
					<i class="icon-options"></i>
					<span class="hide-menu"><?php esc_html_e( 'Database', idCRMActionLanguage::TEXTDOMAIN ); ?></span>
				</li>
				<li class="sidebar-item sidebar-contacts">
					<a class="sidebar-link has-arrow waves-effect waves-dark <?php echo $active_class_contacts; ?>" href="javascript:void(0)" aria-expanded="false">
						<i data-feather="sidebar" class="feather-icon"></i>
						<span class="hide-menu"><?php esc_html_e( 'Contacts', idCRMActionLanguage::TEXTDOMAIN ); ?></span>
					</a>
					<ul aria-expanded="false" class="collapse first-level <?php echo $in_class_contacts; ?>">
						<ul class="sidebar-item-ul">
							<li class="cat-item"><a href="<?php echo get_home_url() . '/crm-contacts/' ; ?>"><?php esc_html_e( 'All', idCRMActionLanguage::TEXTDOMAIN ); ?></a></li>
							<?php
								$args = array(
									'taxonomy'        => 'user_status',
									'show_option_none' => '',
									// 'show_option_all' => esc_html__( 'All', idCRMActionLanguage::TEXTDOMAIN ),
									'style'           => 'list',
									'title_li'        => '',
									'depth'           => '2',
									// 'show_count'      => '1',
								);

								wp_list_categories( $args );
								?>
						</ul>
						</ul>
				</li>
				<li class="sidebar-item sidebar-companies">
					<a class="sidebar-link has-arrow waves-effect waves-dark <?php echo $active_class_companies; ?>" href="javascript:void(0)" aria-expanded="false">
						<i data-feather="home" class="feather-icon"></i>
						<span class="hide-menu"><?php esc_html_e( 'Companies', idCRMActionLanguage::TEXTDOMAIN ); ?></span>
					</a>
					<ul aria-expanded="false" class="collapse first-level <?php echo $in_class_companies; ?>">
						<ul class="sidebar-item-ul">
							<li class="cat-item"><a href="<?php echo get_home_url() . '/crm-companies/' ; ?>"><?php esc_html_e( 'All', idCRMActionLanguage::TEXTDOMAIN ); ?></a></li>
							<?php
								$args = array(
									'taxonomy'        => 'comp_status',
									'show_option_none' => '',
									// 'show_option_all' => esc_html__( 'All', idCRMActionLanguage::TEXTDOMAIN ),
									'style'           => 'list',
									'title_li'        => '',
									'depth'           => '2',
									// 'show_count'      => '1',
								);

								wp_list_categories( $args );
								?>
							</ul>
						</ul>
				</li>
<?php } ?>

				<?php if (idCRMApi::is_accessable( $user->ID, 'idcrm-deals-documents')) { ?>
					<?php if ( is_plugin_active( 'idcrm-deals-documents/idcrm-deals-documents.php' ) ) { ?>
						<?php include WP_PLUGIN_DIR . '/idcrm-deals-documents/templates/inc/sidebar-part.php'; ?>
					<?php } ?>
				<?php } ?>

				<?php if (idCRMApi::is_accessable( $user->ID, 'idcrm-projects-tasks')) { ?>
					<?php if ( is_plugin_active( 'idcrm-projects-tasks/idcrm-projects-tasks.php' ) ) { ?>
						<?php include WP_PLUGIN_DIR . '/idcrm-projects-tasks/templates/inc/sidebar-part.php'; ?>
					<?php } ?>
				<?php } ?>

				<?php if (idCRMApi::is_accessable( $user->ID, 'idcrm-team-motivation')) { ?>
					<?php if ( is_plugin_active( 'idcrm-team-motivation/idcrm-team-motivation.php' ) ) { ?>
						<?php include WP_PLUGIN_DIR . '/idcrm-team-motivation/templates/inc/sidebar-part.php'; ?>
					<?php } ?>
				<?php } ?>

				<?php if (idCRMApi::is_accessable( get_current_user_id(), 'idcrm-knowledge-experience')) { ?>
					<?php if ( is_plugin_active( 'idcrm-knowledge-experience/idcrm-knowledge-experience.php' ) ) { ?>
						<?php include WP_PLUGIN_DIR . '/idcrm-knowledge-experience/templates/inc/sidebar-part.php'; ?>
					<?php } ?>
				<?php } ?>

				<?php if (idCRMApi::is_accessable( $user->ID, 'idcrm-contacts-companies-pro')) { ?>
					<?php if ( is_plugin_active( 'idcrm-contacts-companies-pro/idcrm-contacts-companies-pro.php' ) ) { ?>
						<?php include WP_PLUGIN_DIR . '/idcrm-contacts-companies-pro/templates/inc/sidebar-part.php'; ?>
					<?php } ?>
				<?php } ?>

			</ul>
		</nav>
		<!-- End Sidebar navigation -->
	</div>
	<!-- End Sidebar scroll-->
	<!-- Bottom points-->
	<div class="sidebar-footer">
		<!-- item-->
		<!--a href="javascript:void(0)" class="service-panel-toggle"
				><i class="icon-settings"></i
			></a-->


	</div>
	<!-- End Bottom points-->
</aside>
