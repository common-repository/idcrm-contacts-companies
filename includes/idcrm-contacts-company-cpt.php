<?php
/**
 * @package   Id_CRM_Contacts_Company_Cpt
 * @author    id:Result
 * @link      https://idresult.ru
 * @copyright Vladimir Shlykov
 * @license   GPL-2.0-or-later
 * @version   1.0.0
 */

namespace idcrm\includes;

use idcrm\includes\actions\idCRMActionLanguage;

if ( ! class_exists( '\idcrm\includes\idCRMContactsCompanyCpt' ) ) {
	class idCRMContactsCompanyCpt {
		public static function register() {
			$handler = new self();
			add_action( 'init', array($handler, 'custom_post_type' ) );
			add_action( 'add_meta_boxes', array($handler, 'add_meta_box_company' ) );
			add_action( 'save_post', array($handler, 'save_metabox' ), 10, 2 );
		}
		public function add_meta_box_company() {
			add_meta_box(
				'idcrm_contacts_settings',
				esc_html__( 'Company Info', idCRMActionLanguage::TEXTDOMAIN ),
				array( $this, 'metabox_company_html' ),
				'company',
				'normal',
				'high'
			);
		}
		public function metabox_company_html( $post ) {
			wp_nonce_field( 'idcrm_company_fields', '_idcrm_company' );
			$company_id = get_post_meta( $post->ID, 'idcrm_company_id', true );
			$facebook = get_post_meta( $post->ID, 'idcrm_company_facebook', true );
			$twitter  = get_post_meta( $post->ID, 'idcrm_company_twitter', true );
			$youtube  = get_post_meta( $post->ID, 'idcrm_company_youtube', true );
			$website  = get_post_meta( $post->ID, 'idcrm_company_website', true );
			$inn      = get_post_meta( $post->ID, 'idcrm_company_inn', true );
			$kpp      = get_post_meta( $post->ID, 'idcrm_company_kpp', true );
			$ogrn     = get_post_meta( $post->ID, 'idcrm_company_ogrn', true );
			echo '<div class="contact__information">
				<div class="first__block">
				<p>
					<label for="idcrm_company_facebook">' . esc_html__( 'Facebook', idCRMActionLanguage::TEXTDOMAIN ) . '</label>
					<input type="text" id="idcrm_company_facebook" name="idcrm_company_facebook" value="' . esc_html( $facebook ) . '"></input>
				</p>
				<p>
					<label for="idcrm_company_twitter">' . esc_html__( 'Twitter', idCRMActionLanguage::TEXTDOMAIN ) . '</label>
					<input type="text" id="idcrm_company_twitter" name="idcrm_company_twitter" value="' . esc_html( $twitter ) . '"></input>
				</p>
				<p>
					<label for="idcrm_company_youtube">' . esc_html__( 'Youtube', idCRMActionLanguage::TEXTDOMAIN ) . '</label>
					<input type="text" id="idcrm_company_youtube" name="idcrm_company_youtube" value="' . esc_html( $youtube ) . '"></input>
				</p>
				<p>
					<label for="idcrm_company_website">' . esc_html__( 'Website (with http)', idCRMActionLanguage::TEXTDOMAIN ) . '</label>
					<input type="text" id="idcrm_company_website" name="idcrm_company_website" value="' . esc_html( $website ) . '"></input>
				</p>
			</div>
			<div class="second__block">
				<p>
					<label for="idcrm_company_inn">' . esc_html__( 'TIN', idCRMActionLanguage::TEXTDOMAIN ) . '</label>
					<input type="text" id="idcrm_company_inn" name="idcrm_company_inn" value="' . esc_html( $inn ) . '"></input>
				</p>

				<p>
					<label for="idcrm_company_kpp">' . esc_html__( 'KPP', idCRMActionLanguage::TEXTDOMAIN ) . '</label>
					<input type="text" id="idcrm_company_kpp" name="idcrm_company_kpp" value="' . esc_html( $kpp ) . '"></input>
				</p>

				<p>
					<label for="idcrm_company_ogrn">' . esc_html__( 'LEI', idCRMActionLanguage::TEXTDOMAIN ) . '</label>
					<input type="text" id="idcrm_company_ogrn" name="idcrm_company_ogrn" value="' . esc_html( $ogrn ) . '"></input>
				</p>
			</div>';
		}
		public function save_metabox( $post_id ) {
			if ( ! isset( $_POST['_idcrm_company'] ) || ! wp_verify_nonce( $_POST['_idcrm_company'], 'idcrm_company_fields' ) ) {
				return $post_id;
			}
			if ( empty( $_POST['idcrm_company_id'] ) ) {
				delete_post_meta( $post_id, 'idcrm_company_id' );
			} else {
				update_post_meta( $post_id, 'idcrm_company_id', sanitize_text_field( wp_unslash( $_POST['idcrm_company_id'] ) ) );
			}
			if ( empty( $_POST['idcrm_company_facebook'] ) ) {
				delete_post_meta( $post_id, 'idcrm_company_facebook' );
			} else {
				update_post_meta( $post_id, 'idcrm_company_facebook', sanitize_text_field( wp_unslash( $_POST['idcrm_company_facebook'] ) ) );
			}
			if ( empty( $_POST['idcrm_company_twitter'] ) ) {
				delete_post_meta( $post_id, 'idcrm_company_twitter' );
			} else {
				update_post_meta( $post_id, 'idcrm_company_twitter', sanitize_text_field( wp_unslash( $_POST['idcrm_company_twitter'] ) ) );
			}
			if ( empty( $_POST['idcrm_company_youtube'] ) ) {
				delete_post_meta( $post_id, 'idcrm_company_youtube' );
			} else {
				update_post_meta( $post_id, 'idcrm_company_youtube', sanitize_text_field( wp_unslash( $_POST['idcrm_company_youtube'] ) ) );
			}
			if ( empty( $_POST['idcrm_company_website'] ) ) {
				delete_post_meta( $post_id, 'idcrm_company_website' );
			} else {
				update_post_meta( $post_id, 'idcrm_company_website', sanitize_text_field( wp_unslash( $_POST['idcrm_company_website'] ) ) );
			}
			if ( empty( $_POST['idcrm_company_inn'] ) ) {
				delete_post_meta( $post_id, 'idcrm_company_inn' );
			} else {
				update_post_meta( $post_id, 'idcrm_company_inn', sanitize_text_field( wp_unslash( $_POST['idcrm_company_inn'] ) ) );
			}
			if ( empty( $_POST['idcrm_company_kpp'] ) ) {
				delete_post_meta( $post_id, 'idcrm_company_kpp' );
			} else {
				update_post_meta( $post_id, 'idcrm_company_kpp', sanitize_text_field( wp_unslash( $_POST['idcrm_company_kpp'] ) ) );
			}
			if ( empty( $_POST['idcrm_company_ogrn'] ) ) {
				delete_post_meta( $post_id, 'idcrm_company_ogrn' );
			} else {
				update_post_meta( $post_id, 'idcrm_company_ogrn', sanitize_text_field( wp_unslash( $_POST['idcrm_company_ogrn'] ) ) );
			}
			return $post_id;
		}
		public static function custom_post_type() {
			$labels = array(
				'name'              => esc_html_x( 'Company Statuses', 'taxonomy general name', idCRMActionLanguage::TEXTDOMAIN ),
				'singular_name'     => esc_html_x( 'Company Status', 'taxonomy singular name', idCRMActionLanguage::TEXTDOMAIN ),
				'search_items'      => esc_html__( 'Search Company Statuses', idCRMActionLanguage::TEXTDOMAIN ),
				'all_items'         => esc_html__( 'All Company Statuses', idCRMActionLanguage::TEXTDOMAIN ),
				'parent_item'       => esc_html__( 'Parent Company Status', idCRMActionLanguage::TEXTDOMAIN ),
				'parent_item_colon' => esc_html__( 'Parent Company Status:', idCRMActionLanguage::TEXTDOMAIN ),
				'edit_item'         => esc_html__( 'Edit Company Status', idCRMActionLanguage::TEXTDOMAIN ),
				'update_item'       => esc_html__( 'Update Company Status', idCRMActionLanguage::TEXTDOMAIN ),
				'add_new_item'      => esc_html__( 'Add New Company Status', idCRMActionLanguage::TEXTDOMAIN ),
				'new_item_name'     => esc_html__( 'New Company Status Name', idCRMActionLanguage::TEXTDOMAIN ),
				'menu_name'         => esc_html__( 'Company Statuses', idCRMActionLanguage::TEXTDOMAIN ),
			);
			if (!taxonomy_exists('comp_status')) {
				$args = array(
					'hierarchical'      => true,
					'show_ui'           => true,
					'show_admin_column' => true,
					'query_var'         => true,
					'rewrite'           => array(
						'slug'  => 'crm-companies/status',
						'feeds' => false,
						'feed'  => false,
					),
					'labels'            => $labels,
					'sort'              => true,
					'capabilities'      => array(
						'manage_terms' => 'edit_company_status',
						'edit_terms'   => 'edit_company_status',
						'delete_terms' => 'edit_company_status',
						'assign_terms' => 'edit_company_status',
					)
				);
				$taxonomy = register_taxonomy( 'comp_status', 'company', $args );
			}
			register_post_type(
				'company',
				array(
					'public'          => true,
					'has_archive'     => true,
					'rewrite'         => array(
						'slug'  => 'crm-companies',
						'feeds' => false,
						'feed'  => false,
					),
					'label'           => esc_html__( 'Companies', idCRMActionLanguage::TEXTDOMAIN ),
					'supports'        => array( 'title', 'editor', 'comments', 'author', 'excerpt', 'custom-fields', 'thumbnail' ),
					//'taxonomies'      => array( 'comp_status' ),
					'show_ui'         => true,
					'show_in_menu'    => false,
					'capability_type' => 'post',
					'map_meta_cap'    => null,
					// 'capabilities'     => array(
					// 	'delete_posts'           => 'delete_company',
					// 	'delete_published_posts' => 'delete_company',
					// 	'delete_post'            => 'delete_company',
					// 	'delete_private_posts'   => 'delete_company',
					// 	'delete_others_posts'    => 'delete_company',
					// 	'edit_post'   => 'edit_company',
					// 	'read_post'   => 'read_company',
					// ),
				)
			);
			flush_rewrite_rules();
		}
		public static function create_first_company_status() {
			wp_suspend_cache_invalidation( true );
			$comp_term_lead_check = term_exists( 'leads-company', 'comp_status' );
			if ( empty( $comp_term_lead_check ) ) {
				wp_insert_term(
					esc_html__( 'Leads', idCRMActionLanguage::TEXTDOMAIN ),
					'comp_status',
					array('slug' => 'company-leads')
				);
			}
			$comp_term_contractors_check = term_exists( 'contractors-company', 'comp_status' );
			if (empty($comp_term_contractors_check)) {
				wp_insert_term(
					esc_html__( 'Contractors', idCRMActionLanguage::TEXTDOMAIN ),
					'comp_status',
					array('slug' => 'company-contractors')
				);
			}
			wp_suspend_cache_invalidation( false );
		}
	}
}

?>
