<?php

namespace idcrm\includes;

require_once('gamajo-template-loader.php');

use idcrm\idCRM;

if ( ! class_exists( '\idcrm\includes\idCRMContactsTemplateLoader' ) ) {
	class idCRMContactsTemplateLoader extends gamajoTemplateLoader {
		public static $filter_prefix = 'idcrm-contacts';

		public static $theme_template_directory = 'idcrm-contacts';

		public static $plugin_template_directory = 'templates';

		public static $templates = [ 'templates/crm.php' => 'CRM Login' ];

		public static function register()
		{
			$handler = new self();
			add_filter( 'theme_page_templates', array($handler, 'plugin_page_templates' ) );
			add_filter( 'template_include', array($handler, 'load_plugin_template' ) );
			add_filter( 'template_include', array($handler, 'idcrm_contacts_templates' ) );
		}

		public function plugin_page_templates( $templates )
		{
			$templates = array_merge( $templates, apply_filters( 'idcrm_templates', self::$templates ) );
			return $templates;
		}

		public function load_plugin_template( $template )
		{
			global $post;
			if ( ! empty( $post ) ) {
				$template_name = get_post_meta(
					$post->ID,
					'_wp_page_template',
					true
				);
				if ( !empty($template_name)) {
					if (array_key_exists($template_name, self::$templates)) {
						$file = idCRM::$IDCRM_PATH . $template_name;
						if ( file_exists( $file ) ) {
							return $file;
						}
					}
				}
			}
			return $template;
		}

		public function idcrm_contacts_templates( $template )
		{
			if ( is_post_type_archive( 'company' ) ) {
				$theme_files = array( 'archive-companies.php', 'idcrm-contacts/archive-companies.php' );
				$exist       = locate_template( $theme_files, false );
				if ( $exist != '' ) {
					return $exist;
				} else {
					return idCRM::$IDCRM_PATH . 'templates/archive-companies.php';
				}
			} elseif ( is_singular( 'company' ) ) {
				$theme_files = array( 'single-company.php', 'idcrm-contacts/single-company.php' );
				$exist       = locate_template( $theme_files, false );
				if ( $exist != '' ) {
					return $exist;
				} else {
					return idCRM::$IDCRM_PATH . 'templates/single-company.php';
				}
			} elseif ( is_tax( 'comp_status' ) ) {
				$theme_files = array( 'archive-companies.php', 'idcrm-contacts/archive-companies.php' );
				$exist       = locate_template( $theme_files, false );
				if ( $exist != '' ) {
					return $exist;
				} else {
					return idCRM::$IDCRM_PATH . 'templates/archive-companies.php';
				}
			} elseif ( is_post_type_archive( 'user_contact' ) ) {
				$theme_files = array( 'archive-contacts.php', 'idcrm-contacts/archive-contacts.php' );
				$exist       = locate_template( $theme_files, false );
				if ( $exist != '' ) {
					return $exist;
				} else {
					return idCRM::$IDCRM_PATH . 'templates/archive-contacts.php';
				}
			} elseif ( is_singular( 'user_contact' ) ) {
				$theme_files = array( 'single-contact.php', 'idcrm-contacts/single-contact.php' );
				$exist       = locate_template( $theme_files, false );
				if ( $exist != '' ) {
					return $exist;
				} else {
					return idCRM::$IDCRM_PATH . 'templates/single-contact.php';
				}
			} elseif ( is_tax( 'user_status' ) ) {
				$theme_files = array( 'archive-contacts.php', 'idcrm-contacts/archive-contacts.php' );
				$exist       = locate_template( $theme_files, false );
				if ( $exist != '' ) {
					return $exist;
				} else {
					return idCRM::$IDCRM_PATH . 'templates/archive-contacts.php';
				}
			}
			return $template;
		}
	}
}

?>
