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

		public static function register() {
			$handler = new self();
			add_filter( 'theme_page_templates', array($handler, 'plugin_page_templates' ) );

			add_filter( 'template_include', array($handler, 'load_plugin_template' ) );
			add_filter( 'template_include', array($handler, 'idcrm_contacts_search' ) );
			add_filter( 'template_include', array($handler, 'idcrm_contacts_templates' ) );
			add_filter( 'template_include', array($handler, 'unauthorized_redirect' ) );

		}

		public function plugin_page_templates( $templates ) {
			$templates = array_merge( $templates, apply_filters( 'idcrm_templates', self::$templates ) );
			return $templates;
		}

		public function load_plugin_template( $template ) {
			global $post;
			if ( ! empty( $post ) ) {
				$template_file = get_post_meta(
					$post->ID,
					'_wp_page_template',
					true
				);
				if ( !empty( $template_file ) ) {
					$templates = apply_filters( 'idcrm_templates', self::$templates );
					if ( array_key_exists( $template_file, $templates ) ) {
						$paths = apply_filters( 'idcrm_templates_path', [idCRM::$IDCRM_PATH => self::$templates] );
						$current_path = '';
						foreach ($paths as $path => $files) {
							if ( array_key_exists($template_file, $files) ) {
								$current_path = $path;
							}
						}
						if ( $current_path != '' ) {
							$file = $current_path . $template_file;
							if ( file_exists( $file ) ) {
								return $file;
							}
						}
						// $file = idCRM::$IDCRM_PATH . $template_file;
						// print_r($templates[$template_file]);
						// $file = $templates[$template_file] . $template_file;
						// if ( file_exists( $file ) ) {
						// 	return $file;
						// }
					}
				}
			}
			return $template;
		}

		public function idcrm_contacts_search($template) {
				global $wp_query;

			if (!$wp_query->is_search || !isset($_GET["search_type"]) && empty($_GET["search_type"])) {
					return $template;
				}

			return idCRM::$IDCRM_PATH . 'templates/search.php';
		}

		public function idcrm_contacts_templates( $template ) {
			if ( is_post_type_archive( 'company' ) ) {
				$theme_files = array( 'archive-companies.php', 'idcrm-contacts/archive-companies.php' );
				$exist = locate_template( $theme_files, false );
				if ( $exist != '' ) {
					return $exist;
				} else {
					return idCRM::$IDCRM_PATH . 'templates/archive-companies.php';
				}
			} elseif ( is_singular( 'company' ) ) {
				$theme_files = array( 'single-company.php', 'idcrm-contacts/single-company.php' );
				$exist = locate_template( $theme_files, false );
				if ( $exist != '' ) {
					return $exist;
				} else {
					return idCRM::$IDCRM_PATH . 'templates/single-company.php';
				}
			} elseif ( is_tax( 'comp_status' ) ) {
				$theme_files = array( 'archive-companies.php', 'idcrm-contacts/archive-companies.php' );
				$exist = locate_template( $theme_files, false );
				if ( $exist != '' ) {
					return $exist;
				} else {
					return idCRM::$IDCRM_PATH . 'templates/archive-companies.php';
				}
			} elseif ( is_post_type_archive( 'user_contact' ) ) {
				$theme_files = array( 'archive-contacts.php', 'idcrm-contacts/archive-contacts.php' );
				$exist = locate_template( $theme_files, false );
				if ( $exist != '' ) {
					return $exist;
				} else {
					return idCRM::$IDCRM_PATH . 'templates/archive-contacts.php';
				}
			} elseif ( is_singular( 'user_contact' ) ) {
				$theme_files = array( 'single-contact.php', 'idcrm-contacts/single-contact.php' );
				$exist  = locate_template( $theme_files, false );
				if ( $exist != '' ) {
					return $exist;
				} else {
					return idCRM::$IDCRM_PATH . 'templates/single-contact.php';
				}
			} elseif ( is_tax( 'user_status' ) ) {
				$theme_files = array( 'archive-contacts.php', 'idcrm-contacts/archive-contacts.php' );
				$exist = locate_template( $theme_files, false );
				if ( $exist != '' ) {
					return $exist;
				} else {
					return idCRM::$IDCRM_PATH . 'templates/archive-contacts.php';
				}
			} elseif ( isset($_GET['crm-profile']) ) {
				$theme_files = array( 'crm-profile.php', 'idcrm-contacts/crm-profile.php' );
				$exist = locate_template( $theme_files, false );
				if ( $exist != '' ) {
					return $exist;
				} else {
					return idCRM::$IDCRM_PATH . 'templates/crm-profile.php';
				}
			} elseif ( isset($_GET['crm-notifications']) ) {
				$theme_files = array( 'crm-notifications.php', 'idcrm-contacts/crm-notifications.php' );
				$exist = locate_template( $theme_files, false );
				if ( $exist != '' ) {
					return $exist;
				} else {
					return idCRM::$IDCRM_PATH . 'templates/crm-notifications.php';
				}
			}
			// elseif ( isset($_GET['zadarma']) ) {
			// 	$theme_files = array( 'zadarma.php', 'idcrm-contacts/zadarma.php' );
			// 	$exist = locate_template( $theme_files, false );
			// 	if ( $exist != '' ) {
			// 		return $exist;
			// 	} else {
			// 		return idCRM::$IDCRM_PATH . 'templates/zadarma.php';
			// 	}
			// }

			return $template;
		}

		public function kill_taxonomy_archive($taxonomy) {

	    add_action('pre_get_posts', function($qry) {
	            if (is_admin()) return;
	            if (is_tax($taxonomy)){
	                $qry->set_404();
	            }
	        }
	    );
		}

		public function unauthorized_redirect($template) {

		// 	$queried_object = get_queried_object();
	 // print_r( $queried_object );

			if ( is_singular( 'contact_event' )) {
				wp_safe_redirect( home_url() . '/crm/' );
				exit;
			}

			if ( is_singular( 'idcrm_comments' )) {
				wp_safe_redirect( home_url() . '/crm/' );
				exit;
			}

			if ( is_post_type_archive( 'contact_event' )) {
				wp_safe_redirect( home_url() . '/crm/' );
				exit;
			}

			if ( is_post_type_archive( 'contact_events' )) {
				wp_safe_redirect( home_url() . '/crm/' );
				exit;
			}

			if ( is_post_type_archive( 'idcrm_comments' )) {
				wp_safe_redirect( home_url() . '/crm/' );
				exit;
			}

			if ( is_tax( 'contact_events' )) {
				wp_safe_redirect( home_url() . '/crm/' );
				exit;
			}

			add_action('pre_get_posts', function($query) {

            if (is_admin()) {
							return;
						}

            if (is_tax('contact_events')){
                $query->set_404();
            }

						if ( $query->is_feed() ) {
							$taxonomy = 'contact_events';

					    $terms = get_terms([
					        'taxonomy' => $taxonomy,
					        'fields' => 'ids',
					    ]);

							$tax_query = array([
					        'taxonomy' => $taxonomy,
					        'field' => 'term_id',
					        'terms' => (array) $terms,
					        'operator' => 'NOT IN',
					    ]);

							$query->set( 'tax_query', $tax_query );
						}

        }

    );

			$user = wp_get_current_user();

			if ( isset( $user->caps ) && !empty($user->caps) && !in_array(['crm_manager', 'administrator'], $user->caps) ) {

				if ( is_singular( 'contact_event' )) {
					wp_safe_redirect( home_url() . '/crm/' );
					exit;
				}

				if ( is_singular( 'idcrm_comments' )) {
					wp_safe_redirect( home_url() . '/crm/' );
					exit;
				}

				if ( is_post_type_archive( 'contact_event' )) {
					wp_safe_redirect( home_url() . '/crm/' );
					exit;
				}

				if ( is_post_type_archive( 'idcrm_comments' )) {
					wp_safe_redirect( home_url() . '/crm/' );
					exit;
				}

			}

			return $template;
		}
	}
}

?>
