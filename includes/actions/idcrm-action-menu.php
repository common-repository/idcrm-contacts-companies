<?php

namespace idcrm\includes\actions;

use idcrm\idCRM;
use idcrm\includes\actions\idCRMActionLanguage;
include_once ABSPATH . 'wp-admin/includes/plugin.php';

if ( ! class_exists( '\idcrm\includes\actions\idCRMActionMenu' ) ) {
    class idCRMActionMenu {

		public static function register() {
      $handler = new self();
			add_action( 'admin_menu', array($handler, 'idcrm_admin_menu'), 25 );
      add_action( 'admin_menu', array($handler, 'idcrm_submenu_page'), 30 );
      add_action( 'parent_file', array($handler, 'idcrm_tax_menu_correction') );
  }

		public function idcrm_admin_menu() {

      $id_crm_title = is_plugin_active( 'idcrm-contacts-companies-pro/idcrm-contacts-companies-pro.php' ) ? 'id:CRM' : 'id:CRM Contacts';

			add_menu_page(
				$id_crm_title,
				$id_crm_title,
				'edit_user_contacts',
				'idcrm-contacts',
				//'admin_menu_callback',
				'',
				'dashicons-groups'
			);
		}

		/** Callback function admin_menu_callback */
		public function admin_menu_callback() {}

		/** Add subpage */
		public function idcrm_submenu_page() {
      global $pagenow;

      $active_company = ($pagenow === 'edit-tags.php' && isset($_GET['taxonomy']) && $_GET['taxonomy'] === 'comp_status')
      || ($pagenow === 'post-new.php' && isset($_GET['post_type']) && $_GET['post_type'] === 'company')
      ? 'active' : '';

      $active_schedule = ($pagenow === 'edit-tags.php' && isset($_GET['taxonomy']) && $_GET['taxonomy'] === 'contact_events')
      || ($pagenow === 'post-new.php' && isset($_GET['post_type']) && $_GET['post_type'] === 'contact_event')
      ? 'active' : '';

			add_submenu_page(
				'idcrm-contacts',
				'<span class="idcrm-third-level idcrm-contact-menu">' . esc_html__( 'Add contact', idCRMActionLanguage::TEXTDOMAIN ) . '</span>',
				'<span class="idcrm-third-level idcrm-contact-menu">' . esc_html__( 'Add contact', idCRMActionLanguage::TEXTDOMAIN ) . '</span>',
				'edit_user_contacts',
				'post-new.php?post_type=user_contact',
				false,
				30
			);
			add_submenu_page(
				'idcrm-contacts',
				'<span class="idcrm-third-level idcrm-contact-menu">' . esc_html__( 'Contacts Statuses', idCRMActionLanguage::TEXTDOMAIN ) . '</span>',
				'<span class="idcrm-third-level idcrm-contact-menu">' . esc_html__( 'Contacts Statuses', idCRMActionLanguage::TEXTDOMAIN ) . '</span>',
				'edit_user_contacts',
				'edit-tags.php?taxonomy=user_status',
				false,
				35
			);
			add_submenu_page(
				'idcrm-contacts',
				'<span class="idcrm-third-level idcrm-contact-menu">' . esc_html__( 'Contacts Sources', idCRMActionLanguage::TEXTDOMAIN ) . '</span>',
				'<span class="idcrm-third-level idcrm-contact-menu">' . esc_html__( 'Contacts Sources', idCRMActionLanguage::TEXTDOMAIN ) . '</span>',
				'edit_user_contacts', //'manage_options'
				'edit-tags.php?taxonomy=user_source',
				false,
				40
			);
			add_submenu_page(
				'idcrm-contacts',
				'<span class="idcrm-menu-parent ' . $active_company . '">' . esc_html__( 'Companies', idCRMActionLanguage::TEXTDOMAIN ). '</span>',
				'<span class="idcrm-menu-parent ' . $active_company . '">' . esc_html__( 'Companies', idCRMActionLanguage::TEXTDOMAIN ). '</span>',
				'edit_user_contacts', //manage_options
				'edit.php?post_type=company',
				false,
				50
			);
			add_submenu_page(
				'idcrm-contacts',
				'<span class="idcrm-third-level idcrm-company-menu">' . esc_html__( 'Add company', idCRMActionLanguage::TEXTDOMAIN ). '</span>',
				'<span class="idcrm-third-level idcrm-company-menu">' . esc_html__( 'Add company', idCRMActionLanguage::TEXTDOMAIN ). '</span>',
				'edit_user_contacts',
				'post-new.php?post_type=company',
				false,
				55
			);
			add_submenu_page(
				'idcrm-contacts',
				'<span class="idcrm-third-level idcrm-company-menu">' . esc_html__( 'Companies Statuses', idCRMActionLanguage::TEXTDOMAIN ). '</span>',
				'<span class="idcrm-third-level idcrm-company-menu">' . esc_html__( 'Companies Statuses', idCRMActionLanguage::TEXTDOMAIN ). '</span>',
				'edit_user_contacts',
				'edit-tags.php?taxonomy=comp_status',
				false,
				60
			);
			add_submenu_page(
				'idcrm-contacts',
				'<span class="idcrm-menu-parent ' . $active_schedule . '">' . esc_html__( 'Schedule', idCRMActionLanguage::TEXTDOMAIN ). '</span>',
				'<span class="idcrm-menu-parent ' . $active_schedule . '">' . esc_html__( 'Schedule', idCRMActionLanguage::TEXTDOMAIN ). '</span>',
				'edit_user_contacts', //manage_options
				'edit.php?post_type=contact_event',
				false,
				65
			);
			add_submenu_page(
				'idcrm-contacts',
				'<span class="idcrm-third-level idcrm-schedule-menu">' . esc_html__( 'Add Event', idCRMActionLanguage::TEXTDOMAIN ). '</span>',
				'<span class="idcrm-third-level idcrm-schedule-menu">' . esc_html__( 'Add Event', idCRMActionLanguage::TEXTDOMAIN ). '</span>',
				'edit_user_contacts', //manage_options
				'post-new.php?post_type=contact_event',
				false,
				75
			);
			add_submenu_page(
				'idcrm-contacts',
				'<span class="idcrm-third-level idcrm-schedule-menu">' . esc_html__( 'Event Types', idCRMActionLanguage::TEXTDOMAIN ). '</span>',
				'<span class="idcrm-third-level idcrm-schedule-menu">' . esc_html__( 'Event Types', idCRMActionLanguage::TEXTDOMAIN ). '</span>',
				'edit_user_contacts', //manage_options
				'edit-tags.php?taxonomy=contact_events',
				false,
				80
			);
		}

		/** Highlight the proper top level menu */
		public function idcrm_tax_menu_correction( $parent_file ) {
			global $current_screen;
			$taxonomy = $current_screen->taxonomy;
			if ( $taxonomy === 'contact_events' || $taxonomy === 'comp_status' || $taxonomy === 'user_source' || $taxonomy === 'user_status' ) {
				$parent_file = 'idcrm-contacts';
			}
			return $parent_file;
		}
	}
}

?>
