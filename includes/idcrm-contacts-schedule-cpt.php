<?php

namespace idcrm\includes;

use idcrm\includes\actions\idCRMActionLanguage;

if ( ! class_exists( '\idcrm\includes\idCRMContactsScheduleCpt' ) ) {
	class idCRMContactsScheduleCpt {
		public static function register() {
			$handler = new self();
			add_action( 'init', array($handler, 'custom_post_type' ) );
			add_action( 'add_meta_boxes', array($handler, 'add_meta_box_event' ) );
			add_action( 'save_post', array($handler, 'save_metabox' ), 10, 2 );
		}
		public function add_meta_box_event() {
			add_meta_box(
				'idcrm_event_settings',
				esc_html__( 'Event Info', idCRMActionLanguage::TEXTDOMAIN ),
				array( $this, 'metabox_event_html' ),
				'contact_event',
				'normal',
				'high'
			);
		}
		public function metabox_event_html( $post ) {
			wp_nonce_field( 'idcrm_event_fields', '_idcrm_schedule' );
			$contact_user_id    = get_post_meta( $post->ID, 'idcrm_contact_user_id', true );
			$contact_event_date = get_post_meta( $post->ID, 'idcrm_event_date', true );
			$contact_event_time = get_post_meta( $post->ID, 'idcrm_event_time', true );
			$contact_event_status = get_post_meta( $post->ID, 'idcrm_event_status', true );
			echo '<p>
				<label for="idcrm_event_date">' . esc_html__( 'Date', idCRMActionLanguage::TEXTDOMAIN ) . '</label>
				<input type="text" id="idcrm_event_date" name="idcrm_event_date" value="' . esc_html( $contact_event_date ) . '"></input>
			</p>
			<p>
				<label for="idcrm_event_time">' . esc_html__( 'Time', idCRMActionLanguage::TEXTDOMAIN ) . '</label>
				<input type="text" id="idcrm_event_time" name="idcrm_event_time" value="' . esc_html( $contact_event_time ) . '"></input>
			</p>';
		}
		public function save_metabox( $post_id ) {
			if ( ! isset( $_POST['_idcrm_schedule'] ) || ! wp_verify_nonce( $_POST['_idcrm_schedule'], 'idcrm_event_fields' ) ) {
					return $post_id;
			}
			if ( empty( $_POST['idcrm_event_time'] ) ) {
					delete_post_meta( $post_id, 'idcrm_event_time' );
			} else {
					update_post_meta( $post_id, 'idcrm_event_time', sanitize_text_field( wp_unslash( $_POST['idcrm_event_time'] ) ) );
			}
			if ( empty( $_POST['idcrm_event_date'] ) ) {
					delete_post_meta( $post_id, 'idcrm_event_date' );
			} else {
					update_post_meta( $post_id, 'idcrm_event_date', sanitize_text_field( wp_unslash( $_POST['idcrm_event_date'] ) ) );
			}
			return $post_id;
		}
		public static function custom_post_type() {
			$labels = array(
				'name'              => esc_html_x( 'Event Type', 'taxonomy general name', idCRMActionLanguage::TEXTDOMAIN ),
				'singular_name'     => esc_html_x( 'Event', 'taxonomy singular name', idCRMActionLanguage::TEXTDOMAIN ),
				'search_items'      => esc_html__( 'Search Events', idCRMActionLanguage::TEXTDOMAIN ),
				'all_items'         => esc_html__( 'All Events', idCRMActionLanguage::TEXTDOMAIN ),
				'parent_item'       => esc_html__( 'Parent Event', idCRMActionLanguage::TEXTDOMAIN ),
				'parent_item_colon' => esc_html__( 'Parent Event:', idCRMActionLanguage::TEXTDOMAIN ),
				'edit_item'         => esc_html__( 'Edit Event', idCRMActionLanguage::TEXTDOMAIN ),
				'update_item'       => esc_html__( 'Update Event', idCRMActionLanguage::TEXTDOMAIN ),
				'add_new_item'      => esc_html__( 'Add New Event', idCRMActionLanguage::TEXTDOMAIN ),
				'new_item_name'     => esc_html__( 'New Event Name', idCRMActionLanguage::TEXTDOMAIN ),
				'menu_name'         => esc_html__( 'Schedules', idCRMActionLanguage::TEXTDOMAIN ),
			);
			if (!taxonomy_exists('contact_events')) {
				$args = array(
					'hierarchical'      => true,
					'show_ui'           => true,
					'show_admin_column' => true,
					'query_var'         => true,
					'rewrite'           => array(
						'slug'  => 'schedule/events',
						'feeds' => false,
						'feed'  => false,
					),
					'labels'            => $labels,
					'sort'              => true,
					'capabilities     ' => array(
						'manage_terms' => 'edit_user_status',
						'edit_terms'   => 'edit_user_status',
						'delete_terms' => 'edit_user_status',
						'assign_terms' => 'edit_user_status',
					),
				);
				register_taxonomy( 'contact_events', 'contact_event', $args );
			}
			register_post_type(
				'contact_event',
				array(
					'public'          => true,
					'has_archive'     => true,
					'rewrite'         => array(
						'slug'  => 'schedule',
						'feeds' => false,
						'feed'  => false,
					),
					'label'           => esc_html__( 'Schedule', idCRMActionLanguage::TEXTDOMAIN ),
					'supports'        => array( 'title', 'editor', 'author', 'excerpt', 'custom-fields', 'thumbnail' ),
					//'taxonomies'      => array( 'contact_events' ),
					'show_ui'         => true,
					'show_in_menu'    => false,
					'capability_type' => array( 'user_contact', 'user_contacts' ),
					'map_meta_cap'    => true,
					'capabilities'     => array(
						'delete_posts'           => 'delete_user_contacts',
						'delete_published_posts' => 'delete_user_contacts',
						'delete_post'            => 'delete_user_contact',
						'delete_private_posts'   => 'delete_user_contacts',
						'delete_others_posts'    => 'delete_user_contacts',
						'create_posts' => 'edit_user_contacts',
						'edit_others_posts' => 'edit_others_user_contacts',
						'publish_posts' => 'publish_user_contacts',
						'edit_post'   => 'edit_user_contact',
						'read_post'   => 'edit_user_contact'
					),
				)
			);
			register_post_type(
				'idcrm_comments',
				array(
					'public'          => true,
					'has_archive'     => true,
					'rewrite'         => array(
						'slug'  => 'idcrm_comments',
						'feeds' => false,
						'feed'  => false,
					),
					'label'           => esc_html__( 'Comments', idCRMActionLanguage::TEXTDOMAIN ),
					'supports'        => array( 'title', 'editor', 'author', 'excerpt', 'custom-fields', 'thumbnail' ),
					'show_ui'         => true,
					'show_in_menu'    => false,
					'capability_type' => array( 'user_contact', 'user_contacts' ),
					'map_meta_cap'    => true,
					'capabilities'     => array(
						'delete_posts'           => 'delete_user_contacts',
						'delete_published_posts' => 'delete_user_contacts',
						'delete_post'            => 'delete_user_contact',
						'delete_private_posts'   => 'delete_user_contacts',
						'delete_others_posts'    => 'delete_user_contacts',
						'create_posts' => 'edit_user_contacts',
						'edit_others_posts' => 'edit_others_user_contacts',
						'publish_posts' => 'publish_user_contacts',
						'edit_post'   => 'edit_user_contact',
						'read_post'   => 'edit_user_contact'
					),
				)
			);
		}
		public static function create_first_schedule_types() {
			$cont_term_check = term_exists( 'call', 'contact_events' );
			if ( empty( $cont_term_check ) ) {
				wp_insert_term(
					esc_html__( 'Call', idCRMActionLanguage::TEXTDOMAIN ),
					'contact_events',
					array('slug' => 'event-call')
				);
			}
			$cont_term_check = term_exists( 'meeting', 'contact_events' );
			if ( empty( $cont_term_check ) ) {
				wp_insert_term(
					esc_html__( 'Meeting', idCRMActionLanguage::TEXTDOMAIN ),
					'contact_events',
					array('slug' => 'event-meeting')
				);
			}
			$cont_term_check = term_exists( 'mail', 'contact_events' );
			if ( empty( $cont_term_check ) ) {
				wp_insert_term(
					esc_html__( 'Mail', idCRMActionLanguage::TEXTDOMAIN ),
					'contact_events',
					array('slug' => 'event-mail')
				);
			}
			$cont_term_check = term_exists( 'bill', 'contact_events' );
			if ( empty( $cont_term_check ) ) {
				wp_insert_term(
					esc_html__( 'Bill', idCRMActionLanguage::TEXTDOMAIN ),
					'contact_events',
					array('slug' => 'idcrm-event-bill')
				);
			}
			$cont_term_check = term_exists( 'other', 'contact_events' );
			if ( empty( $cont_term_check ) ) {
				wp_insert_term(
					esc_html__( 'Other', idCRMActionLanguage::TEXTDOMAIN ),
					'contact_events',
					array('slug' => 'idcrm-event-other')
				);
			}
			$event_types = get_terms( array( 'contact_events' ), array( 'hide_empty' => false ) );
			if ( ! empty( $event_types ) ) {
				foreach ( $event_types as $event_type ) {
					if (empty(get_term_meta($event_type->term_id,'custom_icon_type', true))) {
                        switch ($event_type->slug) {
                                case 'event-call' :
                                    update_term_meta($event_type->term_id,'custom_icon_type', 'phone');
                                    break;
                                case 'event-meeting' :
                                    update_term_meta($event_type->term_id,'custom_icon_type', 'people');
                                    break;
                                case 'event-mail' :
                                    update_term_meta($event_type->term_id,'custom_icon_type', 'envelope-letter');
                                    break;
                                case 'event-bill' :
                                    update_term_meta($event_type->term_id,'custom_icon_type', 'doc');
                                    break;
                                default:
                                    update_term_meta($event_type->term_id,'custom_icon_type', 'clock');
                        }
                    }
				}
			}
		}
        public function get_user_contacts() {
            $user_contacts = get_posts(
                array(
                    'numberposts' => -1,
                    'meta_key'    => 'idcrm_contact_user_id',
                    'post_type'   => 'user_contact',
                )
            );
            return $user_contacts;
        }
    }
}

?>
