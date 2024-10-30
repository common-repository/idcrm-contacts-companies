<?php

namespace idcrm\includes;

use idcrm\includes\actions\idCRMActionLanguage;

if ( ! class_exists( '\idcrm\includes\idCRMContactsUserCpt' ) ) {

	class idCRMContactsUserCpt {

		public static function register() {
			$handler = new self();
			add_action( 'init', array($handler, 'custom_post_type' ) );
			add_action( 'add_meta_boxes', array($handler, 'add_meta_box_contact' ) );
			add_action( 'save_post', array($handler, 'save_metabox' ), 10, 2 );
			add_action( 'wp_trash_post', array($handler, 'delete_user_trash_contact' ) );

			add_action( 'woocommerce_edit_account_form_start', array($handler, 'add_userimg_to_edit_account_form') );
			add_action( 'woocommerce_save_account_details', array($handler, 'save_userimg_account_details'), 12, 1 );
			add_action( 'woocommerce_edit_account_form_tag', array($handler, 'add_multipart_to_woocommerce_edit_account_form_tag') );
		}


		public function add_userimg_to_edit_account_form() {
		    $user = wp_get_current_user();
				$roles = $user->roles;
				if (in_array('crm_manager', $roles)) {
		    ?>
		      <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
		        <label for="favorite_color"><?php esc_html_e( 'Custom Local Avatar', idCRMActionLanguage::TEXTDOMAIN ); ?></label>

						<?php
						// Check whether we saved the custom avatar, else return the default avatar.
						$custom_avatar = get_the_author_meta( 'userimg', $user->ID );
						if ( $custom_avatar === '' ) {
								$custom_avatar = get_avatar_url( $user->ID );
						} else {
								$custom_avatar = esc_url_raw( $custom_avatar );
						}
						?>
						<img style="width: 96px; height: 96px; display: block; margin-bottom: 15px; object-fit: cover;" class="custom-avatar-preview" src="<?php echo esc_html( $custom_avatar ); ?>">
						<label for="userimg"><?php esc_html_e( 'Upload Image', 'woocommerce' ); ?></label>
						<input type="file" name="userimg" id="userimg" value="" class="woocommerce-Input" accept="image/x-png,image/gif,image/jpeg" />
						<!-- <input type='button' class="avatar-image-upload button-primary" value="<?php esc_attr_e( 'Upload Image', idCRMActionLanguage::TEXTDOMAIN ); ?>" id="uploadimage"/> -->
		    </p>
			<?php }
		}

		public function save_userimg_account_details( $user_id ) {

				if ( isset( $_FILES['userimg'] ) && !empty($_FILES['userimg']) ) {
				        require_once( ABSPATH . 'wp-admin/includes/image.php' );
				        require_once( ABSPATH . 'wp-admin/includes/file.php' );
				        require_once( ABSPATH . 'wp-admin/includes/media.php' );

				        $attachment_id = media_handle_upload( 'userimg', 0 );

				        if ( is_wp_error( $attachment_id ) ) {
				            // delete_user_meta( $user_id, 'userimg' );
				        } else {
				            update_user_meta( $user_id, 'userimg', wp_get_attachment_url($attachment_id) );
				        }
				   }
		}

		function add_multipart_to_woocommerce_edit_account_form_tag() {
		    echo 'enctype="multipart/form-data"';
		}

		public function delete_user_trash_contact( $post_id ) {
	    $post_type = get_post_type( $post_id );
	    $post_status = get_post_status( $post_id );
			$user_id = get_post_meta( $post_id, 'idcrm_contact_user_id', true );
	    if ( $post_type == 'user_contact' && in_array($post_status, array( 'publish','draft' )) && $user_id != 1 ) {
	        wp_delete_user( $user_id );
	    }
		}

		public function add_meta_box_contact() {
			add_meta_box(
				'idcrm_contacts_settings',
				esc_html__( 'Contact Info', idCRMActionLanguage::TEXTDOMAIN ),
				array( $this, 'metabox_user_html' ),
				'user_contact',
				'normal',
				'high'
			);
		}

		public function metabox_user_html( $post ) {

			wp_nonce_field( 'idcrm_contact_fields', '_idcrm_contact' );

			$contact_user_id       = get_post_meta( $post->ID, 'idcrm_contact_user_id', true );
			$contact_user_email    = get_post_meta( $post->ID, 'idcrm_contact_email', true );
			$contact_user_phone    = get_post_meta( $post->ID, 'idcrm_contact_phone', true );
			$contact_user_website  = get_post_meta( $post->ID, 'idcrm_contact_website', true );
			$contact_user_company  = get_post_meta( $post->ID, 'idcrm_contact_company', true );
			$contact_user_position = get_post_meta( $post->ID, 'idcrm_contact_position', true );
			$contact_user_facebook = get_post_meta( $post->ID, 'idcrm_contact_facebook', true );
			$contact_user_twitter  = get_post_meta( $post->ID, 'idcrm_contact_twitter', true );
			$contact_user_youtube  = get_post_meta( $post->ID, 'idcrm_contact_youtube', true );
			$contact_user_birthday = get_post_meta( $post->ID, 'idcrm_contact_birthday', true );
			$contact_user_gender   = get_post_meta( $post->ID, 'idcrm_contact_gender', true );
			$contact_user_surname = get_post_meta( $post->ID, 'idcrm_contact_surname', true );
			$checked_as_lead = get_post_meta( $post->ID, 'idcrm_contact_lead_exclude', true ) ? 'checked' : "";
			$use_surname = get_post_meta( $post->ID, 'idcrm_use_surname', true ) && get_post_meta( $post->ID, 'idcrm_use_surname', true ) == 'yes' ? 'checked' : "";

			// $company_name = '';
			// $company_title = ! empty(get_the_title($contact_user_company)) ? get_the_title($contact_user_company) : $contact_user_company;
			// if ( ! empty( $contact_user_company ) ) {
			// 	$company_name = ' - ' . esc_html( $company_title );
			// }

			$companies = get_posts(
				array(
					'numberposts' => -1,
					'post_type'   => 'company',
				)
			);

			ob_start();
			if (!empty($companies)) { ?>
					<?php foreach($companies as $company) { ?>
						<option <?php selected(get_the_title($company->ID), $contact_user_company); ?> value="<?php echo esc_html( get_the_title($company->ID) ); ?>"><?php echo esc_html( get_the_title($company->ID) ); ?></option>
					<?php }
		 	}

			$companies_html = ob_get_clean();

			echo '
                    <div class="contact__information">
                    <div class="first__block">
										<!--	<p>
															<label for="idcrm_contact_surname">' . esc_html__( 'Surname', idCRMActionLanguage::TEXTDOMAIN ) . '</label>
															<input type="text" id="idcrm_contact_surname" name="idcrm_contact_surname" value="' . esc_html( $contact_user_surname ) . '"></input>
											</p> -->

                        <p>
                                <label for="idcrm_contact_email">' . esc_html__( 'E-mail', idCRMActionLanguage::TEXTDOMAIN ) . '</label>
                                <input type="text" id="idcrm_contact_email" name="idcrm_contact_email" value="' . esc_html( $contact_user_email ) . '"></input>
                        </p>
                        <p>
                                <label for="idcrm_contact_phone">' . esc_html__( 'Phone', idCRMActionLanguage::TEXTDOMAIN ) . '</label>
                                <input type="text" id="idcrm_contact_phone" name="idcrm_contact_phone" value="' . esc_html( $contact_user_phone ) . '"></input>
                        </p>
                        <!-- <p>
                                <label for="idcrm_contact_website">' . esc_html__( 'Website with http://', idCRMActionLanguage::TEXTDOMAIN ) . '</label>
                                <input type="text" id="idcrm_contact_website" name="idcrm_contact_website" value="' . esc_html( $contact_user_website ) . '"></input>
                        </p> -->

                        <p>
                                <label for="idcrm_contact_company">' . esc_html__( 'Company', idCRMActionLanguage::TEXTDOMAIN ) . '</label>
																<select class="form-control" name="idcrm_contact_company" id="idcrm_contact_company" >
																	<option value="">' . esc_html__( 'Select Company', idCRMActionLanguage::TEXTDOMAIN ) . '</option>

																	' . $companies_html . '
																</select>
                        </p>
                        <p>
                                <label for="idcrm_contact_position">' . esc_html__( 'Position', idCRMActionLanguage::TEXTDOMAIN ) . '</label>
                                <input type="text" id="idcrm_contact_position" name="idcrm_contact_position" value="' . esc_html( $contact_user_position ) . '"></input>
                        </p>

												</div>
												<div class="second__block">

                        <p>
                                <label for="idcrm_contact_birthday">' . esc_html__( 'Birthday', idCRMActionLanguage::TEXTDOMAIN ) . '</label>
                                <input type="text" id="idcrm_contact_birthday" name="idcrm_contact_birthday" value="' . esc_html( $contact_user_birthday ) . '"></input>
                        </p>
												<p>
                                <label for="idcrm_contact_gender">' . esc_html__( 'Gender', idCRMActionLanguage::TEXTDOMAIN ) . '</label>'; ?>

																<input type="radio" name="idcrm_contact_gender" <?php checked( $contact_user_gender, "male" ); ?> value="male" /><?php echo esc_html__( 'Male', idCRMActionLanguage::TEXTDOMAIN ); ?> &nbsp;
																<input type="radio" name="idcrm_contact_gender" <?php checked( $contact_user_gender, "female" ); ?> value="female" /><?php echo esc_html__( 'Female', idCRMActionLanguage::TEXTDOMAIN ); ?>

                        <?php echo '</p>

                        <p>
                                <label for="idcrm_contact_facebook">' . esc_html__( 'Facebook', idCRMActionLanguage::TEXTDOMAIN ) . '</label>
                                <input type="text" id="idcrm_contact_facebook" name="idcrm_contact_facebook" value="' . esc_html( $contact_user_facebook ) . '"></input>
                        </p>
                        <p>
                                <label for="idcrm_contact_twitter">' . esc_html__( 'Twitter', idCRMActionLanguage::TEXTDOMAIN ) . '</label>
                                <input type="text" id="idcrm_contact_twitter" name="idcrm_contact_twitter" value="' . esc_html( $contact_user_twitter ) . '"></input>
                        </p>
                        <p>
                                <label for="idcrm_contact_youtube">' . esc_html__( 'Youtube', idCRMActionLanguage::TEXTDOMAIN ) . '</label>
                                <input type="text" id="idcrm_contact_youtube" name="idcrm_contact_youtube" value="' . esc_html( $contact_user_youtube ) . '"></input>
                        </p>

												<p>
														<label for="idcrm_contact_lead_exclude">' . esc_html__( "Not count as lead", idCRMActionLanguage::TEXTDOMAIN ) . '</label>
														<input type="checkbox" id="idcrm_contact_lead_exclude" name="idcrm_contact_lead_exclude" value="1" ' . $checked_as_lead . '  />

												</p>

												<p>
														<label for="idcrm_use_surname">' . esc_html__( "Address by first name and surname", idCRMActionLanguage::TEXTDOMAIN ) . '</label>
														<input type="checkbox" id="idcrm_use_surname" name="idcrm_use_surname" value="yes" ' . $use_surname . '  />

												</p>
					</div>
					</div>';
		}

		public function save_metabox( $post_id ) {
			if ( ! isset( $_POST['_idcrm_contact'] ) || ! wp_verify_nonce( $_POST['_idcrm_contact'], 'idcrm_contact_fields' ) ) {
				return $post_id;
			}

			if ( empty( $_POST['idcrm_contact_surname'] ) ) {
				delete_post_meta( $post_id, 'idcrm_contact_surname' );
			} else {
				update_post_meta( $post_id, 'idcrm_contact_surname', sanitize_text_field( wp_unslash( $_POST['idcrm_contact_surname'] ) ) );
			}

			if ( empty( $_POST['idcrm_contact_email'] ) ) {
				delete_post_meta( $post_id, 'idcrm_contact_email' );
			} else {
				update_post_meta( $post_id, 'idcrm_contact_email', sanitize_text_field( wp_unslash( $_POST['idcrm_contact_email'] ) ) );
			}

			if ( empty( $_POST['idcrm_contact_phone'] ) ) {
				delete_post_meta( $post_id, 'idcrm_contact_phone' );
			} else {
				update_post_meta( $post_id, 'idcrm_contact_phone', sanitize_text_field( wp_unslash( $_POST['idcrm_contact_phone'] ) ) );
			}

			if ( empty( $_POST['idcrm_contact_website'] ) ) {
				delete_post_meta( $post_id, 'idcrm_contact_website' );
			} else {
				update_post_meta( $post_id, 'idcrm_contact_website', sanitize_text_field( wp_unslash( $_POST['idcrm_contact_website'] ) ) );
			}

			if ( empty( $_POST['idcrm_contact_company'] ) ) {
				delete_post_meta( $post_id, 'idcrm_contact_company' );
			} else {
				update_post_meta( $post_id, 'idcrm_contact_company', sanitize_text_field( wp_unslash( $_POST['idcrm_contact_company'] ) ) );
			}

			if ( empty( $_POST['idcrm_contact_position'] ) ) {
				delete_post_meta( $post_id, 'idcrm_contact_position' );
			} else {
				update_post_meta( $post_id, 'idcrm_contact_position', sanitize_text_field( wp_unslash( $_POST['idcrm_contact_position'] ) ) );
			}

			if ( empty( $_POST['idcrm_contact_facebook'] ) ) {
				delete_post_meta( $post_id, 'idcrm_contact_facebook' );
			} else {
				update_post_meta( $post_id, 'idcrm_contact_facebook', sanitize_text_field( wp_unslash( $_POST['idcrm_contact_facebook'] ) ) );
			}

			if ( empty( $_POST['idcrm_contact_twitter'] ) ) {
				delete_post_meta( $post_id, 'idcrm_contact_twitter' );
			} else {
				update_post_meta( $post_id, 'idcrm_contact_twitter', sanitize_text_field( wp_unslash( $_POST['idcrm_contact_twitter'] ) ) );
			}

			if ( empty( $_POST['idcrm_contact_youtube'] ) ) {
				delete_post_meta( $post_id, 'idcrm_contact_youtube' );
			} else {
				update_post_meta( $post_id, 'idcrm_contact_youtube', sanitize_text_field( wp_unslash( $_POST['idcrm_contact_youtube'] ) ) );
			}

			if ( empty( $_POST['idcrm_contact_birthday'] ) ) {
					delete_post_meta( $post_id, 'idcrm_contact_birthday' );
			} else {
				update_post_meta( $post_id, 'idcrm_contact_birthday', sanitize_text_field( wp_unslash( $_POST['idcrm_contact_birthday'] ) ) );
			}

			if ( empty( $_POST['idcrm_contact_gender'] ) ) {
				delete_post_meta( $post_id, 'idcrm_contact_gender' );
			} else {
				update_post_meta( $post_id, 'idcrm_contact_gender', $_POST['idcrm_contact_gender'] );
			}

			if ( empty( $_POST['idcrm_contact_lead_exclude'] ) ) {
					delete_post_meta( $post_id, 'idcrm_contact_lead_exclude' );
			} else {
					update_post_meta( $post_id, 'idcrm_contact_lead_exclude',  $_POST['idcrm_contact_lead_exclude'] );
			}

			if ( empty( $_POST['idcrm_use_surname'] ) ) {
					delete_post_meta( $post_id, 'idcrm_use_surname' );
			} else {
					update_post_meta( $post_id, 'idcrm_use_surname',  $_POST['idcrm_use_surname'] );
			}

			// if(is_null($_POST['idcrm_contact_user_id'] ) ) {
			// delete_post_meta( $post_id, 'idcrm_contact_user_id');
			// } else {
			// update_post_meta( $post_id, 'idcrm_contact_user_id', sanitize_text_field( $_POST['idcrm_contact_user_id']));
			// }
			return $post_id;
		}

		public static function custom_post_type() {

				$labels = array(
					'name'              => esc_html_x( 'Statuses', 'taxonomy general name', idCRMActionLanguage::TEXTDOMAIN ),
					'singular_name'     => esc_html_x( 'Status', 'taxonomy singular name', idCRMActionLanguage::TEXTDOMAIN ),
					'search_items'      => esc_html__( 'Search Statuses', idCRMActionLanguage::TEXTDOMAIN ),
					'all_items'         => esc_html__( 'All Statuses', idCRMActionLanguage::TEXTDOMAIN ),
					'parent_item'       => esc_html__( 'Parent Status', idCRMActionLanguage::TEXTDOMAIN ),
					'parent_item_colon' => esc_html__( 'Parent Status:', idCRMActionLanguage::TEXTDOMAIN ),
					'edit_item'         => esc_html__( 'Edit Status', idCRMActionLanguage::TEXTDOMAIN ),
					'update_item'       => esc_html__( 'Update Status', idCRMActionLanguage::TEXTDOMAIN ),
					'add_new_item'      => esc_html__( 'Add New Status', idCRMActionLanguage::TEXTDOMAIN ),
					'new_item_name'     => esc_html__( 'New Status Name', idCRMActionLanguage::TEXTDOMAIN ),
					'menu_name'         => esc_html__( 'Statuses', idCRMActionLanguage::TEXTDOMAIN ),
				);

				if (!taxonomy_exists('user_status')) {
					$args = array(
						'hierarchical'      => true,
						'show_ui'           => true,
						'show_admin_column' => true,
						'query_var'         => true,
						'rewrite'           => array(
							'slug'  => 'crm-contacts/status',
							'feeds' => false,
							'feed'  => false,
						),
						'labels'            => $labels,
						'sort'              => true,
						'capabilities' => array(
							'manage_terms' => 'edit_user_status',
							'edit_terms'   => 'edit_user_status',
							'delete_terms' => 'edit_user_status',
							'assign_terms' => 'edit_user_status',
						),
						/*'default_term' => array(
                            'name' => esc_html__( 'Leads', idCRMActionLanguage::TEXTDOMAIN ),
                            'slug' => 'leads',
                            'description' => esc_html__( 'This is default term', idCRMActionLanguage::TEXTDOMAIN )
                        )*/
					);

					register_taxonomy( 'user_status', 'user_contact', $args );
				}

				/** Create contact source taxonomy */
				$labels = array(
					'name'              => esc_html_x( 'Sources', 'taxonomy general name', idCRMActionLanguage::TEXTDOMAIN ),
					'singular_name'     => esc_html_x( 'Source', 'taxonomy singular name', idCRMActionLanguage::TEXTDOMAIN ),
					'search_items'      => esc_html__( 'Search Sources', idCRMActionLanguage::TEXTDOMAIN ),
					'all_items'         => esc_html__( 'All Sources', idCRMActionLanguage::TEXTDOMAIN ),
					'parent_item'       => esc_html__( 'Parent Source', idCRMActionLanguage::TEXTDOMAIN ),
					'parent_item_colon' => esc_html__( 'Parent Source:', idCRMActionLanguage::TEXTDOMAIN ),
					'edit_item'         => esc_html__( 'Edit Source', idCRMActionLanguage::TEXTDOMAIN ),
					'update_item'       => esc_html__( 'Update Source', idCRMActionLanguage::TEXTDOMAIN ),
					'add_new_item'      => esc_html__( 'Add New Source', idCRMActionLanguage::TEXTDOMAIN ),
					'new_item_name'     => esc_html__( 'New Source Name', idCRMActionLanguage::TEXTDOMAIN ),
					'menu_name'         => esc_html__( 'Sources', idCRMActionLanguage::TEXTDOMAIN ),
				);

				if (!taxonomy_exists('user_source')) {
					$args = array(
						'hierarchical'      => false, // Use tax like tags.
						'show_ui'           => true,
						'show_admin_column' => true,
						'query_var'         => true,
						'rewrite'           => array(
							'slug'  => 'crm-contacts/source',
							'feeds' => false,
							'feed'  => false,
						),
						'labels'            => $labels,
						'sort'              => true,
						'capabilities'      => array(
							'manage_terms' => 'manage_user_source',
							'edit_terms'   => 'edit_user_source',
							'delete_terms' => 'delete_user_source',
							'assign_terms' => 'assign_user_source',
						),
					);

					register_taxonomy( 'user_source', 'user_contact', $args );
				}

				global $pagenow;

	      $active_contact = ($pagenow === 'edit-tags.php' && isset($_GET['taxonomy']) && $_GET['taxonomy'] === 'user_status')
				|| ($pagenow === 'post-new.php' && isset($_GET['post_type']) && $_GET['post_type'] === 'user_contact')
	      || ($pagenow === 'edit-tags.php' && isset($_GET['taxonomy']) && $_GET['taxonomy'] === 'user_source')
	      ? 'active' : '';

				register_post_type(
					'user_contact',
					array(
						'public'          => true,
						'has_archive'     => true,
						'rewrite'         => array(
							'slug'  => 'crm-contacts',
							'feeds' => false,
							'feed'  => false,
						),
						'label'           => esc_html__( 'Contacts', idCRMActionLanguage::TEXTDOMAIN ),
						'supports'        => array( 'title', 'editor', 'comments', 'author', 'excerpt', 'custom-fields', 'thumbnail' ),
						//'taxonomies'      => array( 'user_status', 'user_source ' ),
						'show_ui'         => true,
						'show_in_menu'    => 'idcrm-contacts',
						'capability_type' => array( 'user_contact', 'user_contacts' ),
						'map_meta_cap'    => true,
						'capabilities'     => array(
							'delete_posts'           => 'delete_user_contacts',
							'delete_published_posts' => 'delete_user_contacts',
							'delete_post'            => 'delete_user_contact',
							'delete_private_posts'   => 'delete_user_contacts',
							'delete_others_posts'    => 'delete_user_contacts',
							'edit_others_user_contacts'    => 'edit_user_contacts',
							'edit_published_user_contacts'    => 'edit_user_contacts',
							'publish_user_contacts'    => 'edit_user_contacts',
							'edit_post'   => 'edit_user_contact',
							'read_post'   => 'read_user_contact',
						),
					)
				);
		}
		/** Create first contact statuses */
		public static function create_first_contact_status() {
			wp_suspend_cache_invalidation( true );
			$cont_term_lead_check = term_exists('user-leads', 'user_status' );
			if ( empty( $cont_term_lead_check ) ) {
				wp_insert_term(
					esc_html__( 'Leads', idCRMActionLanguage::TEXTDOMAIN ),
					'user_status',
					array('slug' => 'user-leads')
				);
			}
			$cont_term_contractors_check = term_exists( 'user-contractors', 'user_status' );

			if ( empty( $cont_term_contractors_check ) ) {
				wp_insert_term(
					esc_html__( 'Contractors', idCRMActionLanguage::TEXTDOMAIN ),
					'user_status',
					array('slug' => 'user-contractors')
				);
			}
			wp_suspend_cache_invalidation( false );
		}
	}
}

?>
