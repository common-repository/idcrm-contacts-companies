<?php

namespace idcrm\includes\api;

use idcrm\idCRM;
use idcrm\includes\actions\idCRMActionLanguage;

if ( ! class_exists( '\idcrm\includes\api\idCRMApiContact' ) ) {
    class idCRMApiContact {

        const ACTION_SURNAME = 'action_use_surname';
        const ACTION_UPDATE = 'action_update_contact';
        const ACTION_POSTIMAGE = 'action_upload_postimage';
        const ACTION_UPDATE_AVATAR = 'action_update_avatar';
        const ACTION_UPDATE_TABLE = 'action_update_table';
        const ACTION_ASSIGN_CONTACT = 'action_assign_contact';
        const ACTION = 'idcrm_ajax_delete_contact';
        const ACTION_UPDATE_PROFILE = 'idcrm_update_profile';
        const NONCE = 'idcrm-contact-ajax';

        public function register_script() {
            wp_register_script('wp_ajax_contact_manage', idCRM::$IDCRM_URL . 'public/js/api/ajax-contact-manage.js', array( 'jquery' ), IDCRM_CONTACTS_VERSION);
            wp_enqueue_script('wp_ajax_contact_manage');

            wp_register_script('wp_ajax_contact_api', idCRM::$IDCRM_URL . 'public/js/api/ajax-contact-api.js', array( 'jquery' ), IDCRM_CONTACTS_VERSION);
            wp_localize_script('wp_ajax_contact_api', 'wp_ajax_contact_data', $this->get_ajax_data());
            wp_localize_script( 'wp_ajax_contact_api', 'idcrmcontact_ui_shortcuts', $this->shortcuts_data() );
            wp_enqueue_script('wp_ajax_contact_api');
        }

        private function shortcuts_data() {
            return [
              'windows' => [
                'edit' => 'Ctrl + E'
              ],
              'macos' => [
                'edit' => '⌘ + E',
              ],
              'mobile' => [
                'edit' => '',
              ],
              'other' => [
                'edit' => '',
              ],

            ];
        }

        private function get_ajax_data() {
            return array(
                'action_update_contact' => self::ACTION_UPDATE,
                'action_use_surname' => self::ACTION_SURNAME,
                'action_upload_postimage' => self::ACTION_POSTIMAGE,
                'action_update_avatar' => self::ACTION_UPDATE_AVATAR,
                'action_update_table' => self::ACTION_UPDATE_TABLE,
                'action_assign_contact' => self::ACTION_ASSIGN_CONTACT,
                'action_update_profile' => self::ACTION_UPDATE_PROFILE,
                'action' => self::ACTION,
                'nonce' => wp_create_nonce(idCRMApiContact::NONCE)
            );
        }

        public static function register() {
            $handler = new self();
            add_action('wp_ajax_' . self::ACTION, array($handler, 'idcrmAjaxDeleteContact'));
            add_action('wp_ajax_nopriv_' . self::ACTION, array($handler, 'idcrmAjaxDeleteContact'));

            add_action('wp_ajax_' . self::ACTION_SURNAME, array($handler, 'idcrm_use_surname'));
            add_action('wp_ajax_nopriv_' . self::ACTION_SURNAME, array($handler, 'idcrm_use_surname'));

            add_action('wp_ajax_' . self::ACTION_UPDATE, array($handler, 'idcrm_update_contact'));
            add_action('wp_ajax_nopriv_' . self::ACTION_UPDATE, array($handler, 'idcrm_update_contact'));

            add_action('wp_ajax_' . self::ACTION_POSTIMAGE, array($handler, 'idcrm_postimage_upload_action'));
            add_action('wp_ajax_nopriv_' . self::ACTION_POSTIMAGE, array($handler, 'idcrm_postimage_upload_action'));

            add_action('wp_ajax_' . self::ACTION_UPDATE_AVATAR, array($handler, 'idcrm_update_avatar'));
            add_action('wp_ajax_nopriv_' . self::ACTION_UPDATE_AVATAR, array($handler, 'idcrm_update_avatar'));

            add_action('wp_ajax_' . self::ACTION_UPDATE_TABLE, array($handler, 'idcrm_update_notifications_table'));
            add_action('wp_ajax_nopriv_' . self::ACTION_UPDATE_TABLE, array($handler, 'idcrm_update_notifications_table'));

            add_action('wp_ajax_' . self::ACTION_ASSIGN_CONTACT, array($handler, 'idcrm_assign_contact'));
            add_action('wp_ajax_nopriv_' . self::ACTION_ASSIGN_CONTACT, array($handler, 'idcrm_assign_contact'));

            add_action('wp_ajax_' . self::ACTION_UPDATE_PROFILE, array($handler, 'idcrm_update_profile'));
            add_action('wp_ajax_nopriv_' . self::ACTION_UPDATE_PROFILE, array($handler, 'idcrm_update_profile'));

            add_action('wp_enqueue_scripts', array($handler, 'register_script'));
            add_action('post_updated', array($handler, 'update_cridentials'), 10, 4);
            add_action('updated_post_meta', array($handler, 'update_email'), 10, 4);
            add_action( 'trashed_post', [ $handler, 'idcrm_before_delete_contact' ], 10, 2 );
            add_action( 'delete_user', [ $handler, 'idcrm_before_delete_user'], 10 );

            add_filter('wp_get_attachment_url', array($handler, 'honor_ssl_for_attachments'));
        }

        function honor_ssl_for_attachments($url) {
        	$http = site_url(FALSE, 'http');
        	$https = site_url(FALSE, 'https');
        	return ( isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ) ? str_replace($http, $https, $url) : $url;
        }

        public function idcrm_update_profile() {
            check_ajax_referer(self::NONCE);

            $current_user_id = get_current_user_id();

            $first_name = '';
            if (array_key_exists('first_name', $_POST)) {
                $first_name = $_POST['first_name'];
            }

            $last_name = '';
            if (array_key_exists('last_name', $_POST)) {
                $last_name = $_POST['last_name'];
            }

            if ($first_name && $last_name) {

              update_user_meta( $current_user_id, 'first_name', $first_name );
              update_user_meta( $current_user_id, 'last_name', $last_name );

              $idcrm_team_user_id = get_user_meta( $current_user_id, 'idcrm_team_user_id', true );

              if ($idcrm_team_user_id) {
                $team_title = $first_name . ' ' . $last_name;

                $new_title = [
                    'ID' => $idcrm_team_user_id,
                    'post_title' => $team_title
                ];

                if (wp_update_post( wp_slash( $new_title ) ) == 0) {

                }
              }
            }

            die();
        }

        public function idcrm_assign_contact() {
            check_ajax_referer(self::NONCE);
            $code = 0;
            $status = 'success';
            $message = array();
            $post_id = 0;
            if (array_key_exists('post_id', $_POST)) {
                $post_id = $_POST['post_id'];
            }
            array_push($message, '$post_id: ' . $post_id);

            $contact_id = 0;
            if (array_key_exists('contact_id', $_POST)) {
                $contact_id = intval($_POST['contact_id']);
            }
            array_push($message, '$contact_id: ' . $contact_id);

            if ($post_id != 0 && $contact_id != 0) {
                if (update_post_meta( $contact_id, 'idcrm_contact_company', $post_id ) === false) {
                    $code = 1;
                    $status = 'fail update post';
                }
            }
            echo json_encode(array('code' => $code, 'status' => $status, 'message' => $message));
            die();
        }

        public static function get_post_by_title($title = '', $type = '') {
          $post_id = 0;
          if ($title && $type && !is_numeric($title)) {
            $args = array(
                'post_type'      => $type,
                'post_status'    => 'publish',
                'posts_per_page' => 1,
                'fields'         => 'ids',
                's'              => $title,
            );

            $query = new \WP_Query($args);

            if ($query->have_posts()) {
                $post_id = $query->posts[0];
            }
          }

          return $post_id;
        }

        public function idcrm_postimage_upload_action() {
          check_ajax_referer(self::NONCE);
          $post_id = intval($_POST['post_id']);
          $wp_upload_dir = wp_upload_dir();
          $path = $wp_upload_dir['path'] . '/';

          if ( isset( $_FILES['image'] ) && !empty($_FILES['image']) ) {

            $upload_img = $_FILES['image'];

            if ($upload_img['size'] !== 0) {
            $attachment_id = '';

            $upload_overrides = array( 'test_form' => FALSE );
            $movefile = wp_handle_upload( $upload_img, $upload_overrides );

            if ( $movefile) {

                $wp_filetype = $movefile['type'];
                $filename = $movefile['file'];
                $wp_upload_dir = wp_upload_dir();
                $attachment = array(
                    'guid' => $wp_upload_dir['url'] . '/' . basename( $filename ),
                    'post_mime_type' => $wp_filetype,
                    'post_title' => preg_replace('/\.[^.]+$/', '', basename($filename)),
                    'post_content' => '',
                    'post_status' => 'inherit'
                );
                if ($upload_img['error'] === 0) {
                    $attachment_id = wp_insert_attachment( $attachment, $filename);
                    require_once(ABSPATH . 'wp-admin/includes/image.php');
                    $attach_data = wp_generate_attachment_metadata( $attachment_id, $filename );
                    $res1= wp_update_attachment_metadata( $attachment_id, $attach_data );
                }
            }

            if (!empty($attachment_id)) {
              update_post_meta($post_id, '_thumbnail_id', $attachment_id);
              echo esc_url(wp_get_attachment_url($attachment_id));
            }

            $idcrm_team_user_id = get_post_meta( $post_id, 'idcrm_team_user_id', true );

            if ($idcrm_team_user_id) {
              update_user_meta($idcrm_team_user_id, 'userimg', wp_get_attachment_url($attachment_id));
            }

          }

         }

          die();
        }

        public function idcrm_update_avatar() {
          check_ajax_referer(self::NONCE);

          $current_user_id = get_current_user_id();
          $wp_upload_dir = wp_upload_dir();
          $path = $wp_upload_dir['path'] . '/';

          if ( isset( $_FILES['image'] ) && !empty($_FILES['image']) ) {

            $upload_img = $_FILES['image'];

            if ($upload_img['size'] !== 0) {
            $attachment_id = '';

            $upload_overrides = array( 'test_form' => FALSE );
            $movefile = wp_handle_upload( $upload_img, $upload_overrides );

            if ( $movefile) {

                $wp_filetype = $movefile['type'];
                $filename = $movefile['file'];
                $wp_upload_dir = wp_upload_dir();
                $attachment = array(
                    'guid' => $wp_upload_dir['url'] . '/' . basename( $filename ),
                    'post_mime_type' => $wp_filetype,
                    'post_title' => preg_replace('/\.[^.]+$/', '', basename($filename)),
                    'post_content' => '',
                    'post_status' => 'inherit'
                );
                if ($upload_img['error'] === 0) {
                    $attachment_id = wp_insert_attachment( $attachment, $filename);
                    require_once(ABSPATH . 'wp-admin/includes/image.php');
                    $attach_data = wp_generate_attachment_metadata( $attachment_id, $filename );
                    $res1= wp_update_attachment_metadata( $attachment_id, $attach_data );
                }
            }

            if (!empty($attachment_id)) {
              update_user_meta($current_user_id, 'userimg', wp_get_attachment_url($attachment_id));

              $idcrm_team_user_id = get_user_meta( $current_user_id, 'idcrm_team_user_id', true );

              if ($idcrm_team_user_id) {
                update_post_meta( $idcrm_team_user_id, '_thumbnail_id', $attachment_id );
              }

              echo esc_url(wp_get_attachment_url($attachment_id));
            }
          }

         }

          die();
        }

        public function idcrm_update_contact() {
          check_ajax_referer(self::NONCE);
          $all_data = json_decode(stripslashes($_POST['data']));

          $contact_id = 0;
          if ( isset( $all_data->contact_id ) ) {
              $contact_id = intval($all_data->contact_id);
          }

          $post_title = [];

          $contact_first = '';
          if ( isset( $all_data->contact_first ) ) {
              // $contact_first = $all_data->contact_first;
              $post_title[] = sanitize_text_field( wp_unslash( $all_data->contact_first ) );
          }

          if ( isset( $all_data->contact_surname ) ) {
              // $contact_surname = $all_data->contact_surname;
              $post_title[] = sanitize_text_field( wp_unslash( $all_data->contact_surname ) );
          }

          $contact_last = '';
          if ( isset( $all_data->contact_last ) ) {
              // $contact_last = $all_data->contact_last;
              $post_title[] = sanitize_text_field( wp_unslash( $all_data->contact_last ) );
          }

          if (!empty($post_title) && $contact_id) {

              // $contact_title = $contact_last !== 0 ? $contact_first . ' ' . $contact_last : $contact_first;
              $new_title = [
                  'ID' => $contact_id,
                  'post_title' => implode(" ", $post_title)
              ];

              if (wp_update_post( wp_slash( $new_title ) ) == 0) {
                  // $result['code'] = 1;
                  // $result['status'] = 'fail';
                  // array_push($result['message'], 'Cant update title');
              }
          }

          // if ($contact_surname !== 0 && $contact_id !== 0) {
          //   update_post_meta( $contact_id, 'idcrm_contact_surname', sanitize_text_field( wp_unslash( $contact_surname ) ) );
          // }

          $user_status = 0;
          if ( isset( $all_data->user_status ) ) {
              $user_status = intval($all_data->user_status);
          }

          if ($user_status !== 0 && $user_status !== -1 && $contact_id !== 0) {

              $project_array = [];
              $project_array[] = $user_status;

              wp_set_post_terms( $contact_id, $project_array, 'user_status', false );

          }

          $user_source = 0;
          if ( isset( $all_data->user_source ) ) {
              $user_source = intval($all_data->user_source);
          }

          if ($user_source !== 0 && $user_source !== -1 && $contact_id !== 0) {

              $source_array = [];
              $source_array[] = $user_source;

              wp_set_post_terms( $contact_id, $source_array, 'user_source', false );

          }

          $contact_email = 0;
          if ( isset( $all_data->contact_email ) ) {
              $contact_email = $all_data->contact_email;
          }

          if ($contact_email !== 0 && $contact_id !== 0) {
            update_post_meta( $contact_id, 'idcrm_contact_email', sanitize_text_field( wp_unslash( $contact_email ) ) );
          }

          $contact_phone = 0;
          if ( isset( $all_data->contact_phone ) ) {
              $contact_phone = $all_data->contact_phone;
          }

          if ($contact_phone !== 0 && $contact_id !== 0) {
            update_post_meta( $contact_id, 'idcrm_contact_phone', sanitize_text_field( wp_unslash( $contact_phone ) ) );
          }

          $contact_website = 0;
          if ( isset( $all_data->contact_website ) ) {
              $contact_website = $all_data->contact_website;
          }

          if ($contact_website !== 0 && $contact_id !== 0) {
            update_post_meta( $contact_id, 'idcrm_contact_website', sanitize_text_field( wp_unslash( $contact_website ) ) );
          }

          $contact_position = 0;
          if ( isset( $all_data->contact_position ) ) {
              $contact_position = $all_data->contact_position;
          }

          if ($contact_position !== 0 && $contact_id !== 0) {
            update_post_meta( $contact_id, 'idcrm_contact_position', sanitize_text_field( wp_unslash( $contact_position ) ) );
          }

          $user_company = 0;
          if ( isset( $all_data->user_company ) ) {
              $user_company = $all_data->user_company;
          }

          if ($user_company !== 0 && $contact_id !== 0) {
            update_post_meta( $contact_id, 'idcrm_contact_company', sanitize_text_field( wp_unslash( $user_company ) ) );
          }

          $user_gender = 0;
          if ( isset( $all_data->user_gender ) ) {
              $user_gender = $all_data->user_gender;
          }

          if ($user_gender !== 0 && $contact_id !== 0) {
            update_post_meta( $contact_id, 'idcrm_contact_gender', sanitize_text_field( wp_unslash( $user_gender ) ) );
          }

          $user_birthday = 0;
          if ( isset( $all_data->user_birthday ) ) {
              $user_birthday = $all_data->user_birthday;
          }

          if ($user_birthday !== 0 && $contact_id !== 0) {
            update_post_meta( $contact_id, 'idcrm_contact_birthday', sanitize_text_field( wp_unslash( $user_birthday ) ) );
          }

          $user_lead_exclude = 0;
          if ( isset( $all_data->user_lead_exclude ) ) {
              $user_lead_exclude = $all_data->user_lead_exclude;
          }

          if ($user_lead_exclude === true && $contact_id !== 0) {
            update_post_meta( $contact_id, 'idcrm_contact_lead_exclude', '1' );
          }

          if ($user_lead_exclude === false && $contact_id !== 0) {
            delete_post_meta( $contact_id, 'idcrm_contact_lead_exclude');
          }

          die();
        }

        public function idcrm_use_surname() {
            check_ajax_referer(self::NONCE);

            $result['code'] = 0;
            $result['status'] = 'success';
            $contact_id = 0;

            if ( array_key_exists( 'contact_id', $_POST ) ) {
                $contact_id = intval($_POST['contact_id']);
            }

            $result['message'][] = '$contact_id: ' . $contact_id;
            $is_checked = 1;

            if ( array_key_exists( 'is_checked', $_POST ) ) {
                $is_checked = intval($_POST['is_checked']);
            }

            $result['message'][] = '$is_checked: ' . $is_checked;

            $use_surname = $is_checked === 1 ? 'yes' : '';

            if ( $contact_id != 0 ) {
              if ($is_checked !== 1) {
                delete_post_meta( $contact_id, 'idcrm_use_surname');
              } else {
                if ( update_post_meta( $contact_id, 'idcrm_use_surname', $use_surname ) === false ) {
                    $result['code'] = 1;
                    $result['status'] = 'fail update meta';
                }
              }

            }

            echo json_encode($result);
            die();
        }

        public function update_email($tmp, $post_ID, $meta_key, $meta_value = NULL) {
          if ( get_post_type( $post_ID ) == 'user_contact' && $meta_key == 'idcrm_contact_email') {

            $idcrm_contact_email = get_post_meta($post_ID, 'idcrm_contact_email', true);

            $user_id = intval(get_post_meta($post_ID, "idcrm_contact_user_id", true), 10);

            $user_update = wp_update_user( [
              'ID' => $user_id,
            	'user_email' => sanitize_email( wp_unslash( $idcrm_contact_email ) )
            ] );
          }
        }

        public function update_cridentials($post_ID, $post_after, $post_before) {
          if ( get_post_type( $post_ID ) == 'user_contact' && get_post_meta($post_ID, "idcrm_contact_user_id", true)) {

            if (get_post_meta($post_ID, 'idcrm_contact_email', true)) {

              $user_id = intval(get_post_meta($post_ID, "idcrm_contact_user_id", true), 10);
              $idcrm_contact_email = get_post_meta($post_ID, 'idcrm_contact_email', true);

              $user_update = wp_update_user( [
                'ID' => $user_id,
              	'user_email' => sanitize_email( wp_unslash( $idcrm_contact_email ) )
              ] );

            }

            if( $post_after->post_title !== $post_before->post_title ) {

            $name = explode(" ", get_the_title($post_ID));
            $first_name = is_array($name) ? $name[0] : "";

            if ( is_array($name) ) {
              unset($name[0]);
            }

            $last_name = is_array($name) ? implode(" ", $name) : "";
            $user_id = intval(get_post_meta($post_ID, "idcrm_contact_user_id", true), 10);

            $user_id = wp_update_user( [
              'ID' => $user_id,
            	'first_name' => $first_name,
            	'last_name' => $last_name,
              'display_name' => get_the_title($post_ID)
            ] );

            if ( is_wp_error( $user_id ) ) {
            	// error
              $msg = sprintf(
      					'Error! User with id: %d can\'t be changed to %s',
      					(int) $user_id,
      					esc_html( get_the_title($post_ID) )
      				);
      				// printf( '<strong>%s</strong><br/>', $msg );
            } else {
            	// ok
              $msg = sprintf(
      					'Done! User with id: %d changed to %s',
      					(int) $user_id,
      					esc_html( get_the_title($post_ID) )
      				);
      				// printf( '<strong>%s</strong><br/>', $msg );
            }
            }
          }
        }

        public function idcrmAjaxDeleteContact() {
            check_ajax_referer(self::NONCE);
            $code = 0;
            $status = 'success';
            $message = array();
            $user_id = 0;
            $post_id = 0;

            if (array_key_exists('user_id', $_POST)) {
                $user_id = intval($_POST['user_id']);
            }
            array_push($message, '$user_id: ' . $user_id);

            if (array_key_exists('post_id', $_POST)) {
                $post_id = intval($_POST['post_id']);
            }

            if ($user_id != 0 ) {
                if (wp_delete_user( $user_id ) === false) {
                    $code = 1;
                    $status = 'fail delete user';
                }
            }

            if ($post_id != 0 ) {
                if (wp_delete_post( $post_id ) === false) {
                    $code = 1;
                    $status = 'fail delete post_id';
                }
            }

            echo json_encode(array('code' => $code, 'status' => $status, 'message' => $message));
            die();
        }

        public function idcrm_before_delete_contact( $post_id, $previous_status = 'publish' ) {
            if ( get_post_type( $post_id ) == 'user_contact' && get_post_meta($post_id, "idcrm_contact_user_id", true)) {
              wp_delete_user( get_post_meta($post_id, "idcrm_contact_user_id", true));
            }
        }

        public function idcrm_before_delete_user( $user_id ) {
            if ( get_user_meta($user_id, "idcrm_contact_user_id", true)) {
              wp_delete_post( get_post_meta($user_id, "idcrm_contact_user_id", true));
            }
        }

        public static function idcrmGetTableSearch($search_query, $author) {

          $custom_fields = array(
            'idcrm_company_inn',
            'idcrm_company_kpp',
            'idcrm_company_ogrn',
            'idcrm_company_facebook',
            'idcrm_company_twitter',
            'idcrm_company_website',
            'idcrm_company_youtube',
            'idcrm_contact_email',
            'idcrm_contact_facebook',
            'idcrm_contact_phone',
            'idcrm_contact_position',
            'idcrm_contact_twitter',
            'idcrm_contact_website',
            'idcrm_contact_youtube',
          );

          $args = array(
            's' => $search_query
          );

          $search_results = new \WP_Query( $args );

          // $search_results = get_posts( $args ); error_log(json_encode($search_results));

          // if (empty($search_results)) {
          //     return json_encode([]);
          // }

          $found_posts = [];

          $exclude_types = [
            'contact_event',
            'idcrm_comments'
          ];

          if ( $search_results->have_posts() ) {
            while ( $search_results->have_posts() ) {
              $search_results->the_post();

              if ( in_array(get_post( get_the_ID() )->post_type, $exclude_types)) {
                continue;
              }

              $found_posts[] = get_the_ID();
            }
          }

          // error_log(json_encode($found_posts));

          wp_reset_postdata();

          $meta_args = [
            'fields' => 'ids',
            'post_type'   => ['user_contact', 'company'],
          	'meta_query' => [
          		'relation' => 'OR',
          		[
          			'key' => 'idcrm_company_inn',
                'value' => $search_query,
                'compare' => 'LIKE',
          		],
              [
          			'key' => 'idcrm_company_kpp',
                'value' => $search_query,
                'compare' => 'LIKE',
          		],
              [
                'key' => 'idcrm_company_ogrn',
                'value' => $search_query,
                'compare' => 'LIKE',
              ],
              [
                'key' => 'idcrm_company_facebook',
                'value' => $search_query,
                'compare' => 'LIKE',
              ],
              [
                'key' => 'idcrm_company_twitter',
                'value' => $search_query,
                'compare' => 'LIKE',
              ],
              [
                'key' => 'idcrm_company_website',
                'value' => $search_query,
                'compare' => 'LIKE',
              ],
              [
                'key' => 'idcrm_company_youtube',
                'value' => $search_query,
                'compare' => 'LIKE',
              ],
              [
                'key' => 'idcrm_contact_email',
                'value' => $search_query,
                'compare' => 'LIKE',
              ],
              [
                'key' => 'idcrm_contact_facebook',
                'value' => $search_query,
                'compare' => 'LIKE',
              ],
              [
                'key' => 'idcrm_contact_phone',
                'value' => $search_query,
                'compare' => 'LIKE',
              ],
              [
                'key' => 'idcrm_contact_position',
                'value' => $search_query,
                'compare' => 'LIKE',
              ],
              [
                'key' => 'idcrm_contact_twitter',
                'value' => $search_query,
                'compare' => 'LIKE',
              ],
              [
                'key' => 'idcrm_contact_website',
                'value' => $search_query,
                'compare' => 'LIKE',
              ],
              [
                'key' => 'idcrm_contact_youtube',
                'value' => $search_query,
                'compare' => 'LIKE',
              ],
          	]
          ];

          $meta_search_results = get_posts( $meta_args );

          if ($meta_search_results) {
            foreach ($meta_search_results as $id) {
              $found_posts[] = $id;
            }
          }

          // error_log(json_encode($meta_search_results));

          if (empty($found_posts)) {
              return json_encode([]);
          }

          // include_once ABSPATH . 'wp-admin/includes/plugin.php';

          $table_data = array_map([self::class, 'get_search_data'], array_unique($found_posts));

          return json_encode($table_data);
        }

        public static function get_search_data($post_id) {

          // $post_id = $post->ID;
          // $post_author = $post->post_author;
          $post_author = get_post_field( 'post_author', $post_id );

          $title = self::get_title($post_id);
          $type = self::get_search_type($post_id);
          $manager = self::get_manager($post_author, $post_id);
          $settings = self::get_search_settings($post_id);

          $obj = (object) [
              'id' => $post_id,
              'title' => $title,
              'date' => get_the_date( 'd.m.Y', $post_id ),
              'type' => $type,
              'manager' => $manager,
              'settings' => $settings
          ];

          return $obj;
        }

        private static function get_search_title($post_id) {

            $title = '<div class="d-flex align-items-center">';
            $title .= '<span class="ms-3 fw-normal"><a href="' . get_the_permalink($post_id) . '">' . get_the_title($post_id) . '</a></span></div>';

            return $title;
        }

        private static function get_search_type($post_id) {

          $post_types = [
            'company' => esc_html__( 'Company', idCRMActionLanguage::TEXTDOMAIN ),
            'user_contact' => esc_html__( 'Contact', idCRMActionLanguage::TEXTDOMAIN ),
            'contact_event' => esc_html__( 'Event', idCRMActionLanguage::TEXTDOMAIN ),
            'idcrm_deal' => esc_html__( 'Deal', idCRMActionLanguage::TEXTDOMAIN ),
            'idcrm_document' => esc_html__( 'Document', idCRMActionLanguage::TEXTDOMAIN ),
            'idcrm_comments' => esc_html__( 'Comments', idCRMActionLanguage::TEXTDOMAIN ),
          ];

          return IDCRM_POST_TYPES[get_post_type($post_id)];

        }

        private static function get_search_manager($post_author, $post_id) {
            $user_id = get_the_author_meta('ID', intval($post_author));
            $user_img = get_user_meta($user_id, 'userimg', true);
            $default_image = idCRM::$IDCRM_URL . 'templates/images/no-user.jpg';

            $manager = '<div class="d-flex align-items-center">';
            $manager .= '<img src="' . (empty($user_img) ? esc_html($default_image) : esc_html($user_img)) . '" width="40" height="40" class="rounded-circle object-fit-cover me-2">';
            $manager .= get_the_author_meta('display_name', get_post_field('post_author', $post_id)) . '</div>';

            return $manager;
        }

        private static function get_search_settings($post_id) {
          $settings = '<div class="dropdown dropstart"><a href="#" class="link" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-more-horizontal feather-sm"><circle cx="12" cy="12" r="1"></circle><circle cx="19" cy="12" r="1"></circle><circle cx="5" cy="12" r="1"></circle></svg></a><ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">';

          $settings .= '<li><span class="dropdown-item"><a href="' . get_the_permalink($post_id) . '?idcrm_action=edit">' . esc_html__( 'Edit', idCRMActionLanguage::TEXTDOMAIN ) . '</a></span></li><li><span class="dropdown-item"><a class="delete-contact" data-id="' . get_post_meta( $post_id, 'idcrm_system_user', true ) . '" data-url="' . get_delete_post_link($post_id) . '" href="#">' . esc_html__( 'Delete', idCRMActionLanguage::TEXTDOMAIN ) . '</a></span></li></ul></div>';

          return $settings;
        }

        public function idcrm_update_notifications_table() {
          check_ajax_referer(self::NONCE);

          $user = wp_get_current_user();
          $roles = ( array ) $user->roles;

          $current_user_id = get_current_user_id();
          $current_user_id = in_array( 'administrator', $roles ) ? "" : $current_user_id;

          $idcrm_comments = get_posts([
              'numberposts' => -1,
              'post_type' => 'idcrm_comments',
              // 'author' => $current_user_id,
              'author__not_in' => [get_current_user_id()],
              'orderby' => 'post_date',
              'order' => 'DESC',
              'fields' => 'ids',
              'meta_query' => [[
                  'key' => 'idcrm_is_email',
                  'compare' => 'NOT EXISTS'
              ],
              [
                'key' => 'idcrm_is_seen_' . get_current_user_id(),
                'compare' => 'NOT EXISTS',
              ]]
          ]);

          // error_log("idcrm_comments: " . json_encode($idcrm_comments));

          $included_comments = [];

          if ($idcrm_comments) {
            foreach ($idcrm_comments as $comment_id) {
              $idcrm_contact_user_id = get_post_meta($comment_id, 'idcrm_contact_user_id', true);

              if ($idcrm_contact_user_id && get_current_user_id() == get_post_field( 'post_author', $idcrm_contact_user_id ) || in_array( 'administrator', $roles )) {
                $included_comments[] = $comment_id;
              }
            }
          }

          // error_log("included_comments: " . json_encode($included_comments));

          $has_summary = [];

          if (post_type_exists( 'idcrm_task' )) {
            $has_task_access = get_posts( [
              'post_type'   => 'idcrm_task',
              'post_status' => 'publish',
              'numberposts' => -1,
              'fields' => 'ids',
              'meta_query' => [[
                  'key' => 'idcrm_task_access',
                  'compare' => 'REGEXP',
                  'value'   => '"' . $current_user_id . '"'
              ]]
            ] );

            // error_log("has_task_access: " . json_encode($has_task_access));

            $has_task_respondible = get_posts( [
              'post_type'   => 'idcrm_task',
              'post_status' => 'publish',
              'numberposts' => -1,
              'fields' => 'ids',
              'meta_query' => [[
                  'key' => 'idcrm_task_responsible',
                  'value'   => $current_user_id
              ]]
            ] );

            // error_log("has_task_respondible: " . json_encode($has_task_respondible));

            $has_task_creator = get_posts( [
              'post_type'   => 'idcrm_task',
              'post_status' => 'publish',
              'numberposts' => -1,
              'author' => $current_user_id,
              'fields' => 'ids',
            ] );

            // error_log("has_task_creator: " . json_encode($has_task_creator));

            $has_summary = array_unique(array_merge($has_task_access, $has_task_respondible, $has_task_creator));
          }

          $idcrm_task_comments = [];

          if (!empty($has_summary)) {

            $idcrm_task_comments = get_posts([
                'numberposts' => -1,
                'post_type' => 'idcrm_comments',
                'author__not_in' => [get_current_user_id()],
                // 'author' => $current_user_id,
                'orderby' => 'post_date',
                'order' => 'DESC',
                'meta_query' => [
                  'relation' => 'AND',
                  [
                    'key' => 'idcrm_post_type',
                    'value' => 'idcrm_task'
                  ],
                  [
                    'key' => 'idcrm_contact_user_id',
                    'value' => $has_summary,
                    'compare' => 'IN',
                  ],
                  [
                    'key' => 'idcrm_is_seen_' . get_current_user_id(),
                    'compare' => 'NOT EXISTS',
                  ]
                ],
                'fields' => 'ids',
            ]);

          }

          // error_log("idcrm_task_comments: " . json_encode($idcrm_task_comments));

          $all_comments = array_values(array_unique(array_merge($included_comments, $idcrm_task_comments)));

          // error_log("all_comments: " . json_encode($all_comments));

          if (empty($all_comments)) {
              echo json_encode([]);
          }

          $table_data = array_map([self::class, 'get_notifications_data'], $all_comments);

          echo json_encode($table_data);

          die();
        }

        public static function idcrmGetTableNotifications() {
          // $current_user_id = get_current_user_id();
          // $current_user_id = is_super_admin( $current_user_id ) ? "" : $current_user_id;
          $user = wp_get_current_user();
          $roles = ( array ) $user->roles;

          $current_user_id = in_array( 'administrator', $roles ) ? "" : get_current_user_id();

          $idcrm_comments = get_posts([
              'numberposts' => -1,
              'post_type' => 'idcrm_comments',
              // 'author' => $current_user_id,
              'author__not_in' => [get_current_user_id()],
              'orderby' => 'post_date',
              'order' => 'DESC',
              'fields' => 'ids',
              'meta_query' => [[
                  'key' => 'idcrm_is_email',
                  'compare' => 'NOT EXISTS'
              ],
              [
                'key' => 'idcrm_is_seen_' . get_current_user_id(),
                'compare' => 'NOT EXISTS',
              ]]
          ]);

          // error_log("idcrm_comments: " . json_encode($idcrm_comments));

          $included_comments = [];

          if ($idcrm_comments) {
            foreach ($idcrm_comments as $comment_id) {
              $idcrm_contact_user_id = get_post_meta($comment_id, 'idcrm_contact_user_id', true);

              if ($idcrm_contact_user_id && get_current_user_id() == get_post_field( 'post_author', $idcrm_contact_user_id ) || in_array( 'administrator', $roles)) {
                $included_comments[] = $comment_id;
              }
            }
          }

          // error_log("included_comments: " . json_encode($included_comments));

          $has_summary = [];

          if (post_type_exists( 'idcrm_task' )) {
            $has_task_access = get_posts( [
              'post_type'   => 'idcrm_task',
              'post_status' => 'publish',
              'numberposts' => -1,
              'fields' => 'ids',
              'meta_query' => [[
                  'key' => 'idcrm_task_access',
                  'compare' => 'REGEXP',
                  'value'   => '"' . $current_user_id . '"'
              ]]
            ] );

            // error_log("has_task_access: " . json_encode($has_task_access));

            $has_task_respondible = get_posts( [
              'post_type'   => 'idcrm_task',
              'post_status' => 'publish',
              'numberposts' => -1,
              'fields' => 'ids',
              'meta_query' => [[
                  'key' => 'idcrm_task_responsible',
                  'value'   => $current_user_id
              ]]
            ] );

            // error_log("has_task_respondible: " . json_encode($has_task_respondible));

            $has_task_creator = get_posts( [
              'post_type'   => 'idcrm_task',
              'post_status' => 'publish',
              'numberposts' => -1,
              'author' => $current_user_id,
              'fields' => 'ids',
            ] );

            // error_log("has_task_creator: " . json_encode($has_task_creator));

            $has_summary = array_unique(array_merge($has_task_access, $has_task_respondible, $has_task_creator));
            // error_log("has_summary: " . json_encode($has_summary));
          }

          $idcrm_task_comments = [];

          if (!empty($has_summary)) {
            $idcrm_task_comments = get_posts([
                'numberposts' => -1,
                'post_type' => 'idcrm_comments',
                'author__not_in' => [get_current_user_id()],
                // 'author' => $current_user_id,
                'orderby' => 'post_date',
                'order' => 'DESC',
                'meta_query' => [
                  'relation' => 'AND',
                  [
                    'key' => 'idcrm_post_type',
                    'value' => 'idcrm_task'
                  ],
                  [
                    'key' => 'idcrm_contact_user_id',
                    'value' => $has_summary,
                    'compare' => 'IN',
                  ],
                  [
                    'key' => 'idcrm_is_seen_' . get_current_user_id(),
                    'compare' => 'NOT EXISTS',
                  ]
                ],
                'fields' => 'ids',
            ]);
          }

          // error_log("idcrm_task_comments: " . json_encode($idcrm_task_comments));

          $all_comments = array_values(array_unique(array_merge($included_comments, $idcrm_task_comments)));

          // error_log("all_comments: " . json_encode($all_comments));

          if (empty($all_comments)) {
              return json_encode([]);
          }

          $table_data = array_map([self::class, 'get_notifications_data'], $all_comments);

          return json_encode($table_data);

        }

        public static function get_notifications_data($comment_id) {

          $text = self::get_text($comment_id);
          $date = self::get_date($comment_id);
          $source = self::get_source($comment_id);

          $obj = (object) [
              'date' => $date,
              'text' => $text,
              'source' => $source
          ];

          return $obj;
        }

        private static function get_date($comment_id) {
          $date = '<div class="d-flex align-items-center justify-content-between"><span class="d-none">' . strtotime(get_the_date( 'd.m.Y H:i:s', $comment_id )) . '</span>' . get_the_date( 'd.m.Y H:i', $comment_id );

          $user_id = get_post_field( 'post_author', $comment_id );
          $user_img = get_user_meta($user_id, 'userimg', true);
          $default_image = idCRM::$IDCRM_URL . 'templates/images/no-user.jpg';

          $date .= '<img src="' . (empty($user_img) ? esc_html($default_image) : esc_html($user_img)) . '" width="40" height="40" class="rounded-circle ms-2 object-fit-cover"></div>';

          return $date;
        }

        private static function get_text($comment_id) {
          $is_seen = get_post_meta($comment_id, 'idcrm_is_seen_' . get_current_user_id(), true);
          $idcrm_is_document = get_post_meta($comment_id, 'idcrm_is_document', true);
          $text = '';

          $p_data = get_post($comment_id);

          if ($p_data) {
            $post_content = strip_tags($p_data->post_content);
            $text = $post_content ? wp_trim_words( $post_content, 20, '...' ) : '';
          }

          $text = !$text && $idcrm_is_document ? esc_html__( 'File Attached', idCRMActionLanguage::TEXTDOMAIN ) : $text;

          $not_seen_badge = '<div class="position-relative d-flex align-items-center notification-list-seen me-1">
              <span class="mail-seen-round rounded-circle p-1 position-absolute top-50 start-0 translate-middle text-bg-info">
              </span><span class="ms-3">' . $text . '</span></div>';

              // return !$is_seen && get_post_field( 'post_author', $comment_id ) != get_current_user_id() && get_post_type(get_post_meta($comment_id, 'idcrm_contact_user_id', true)) == 'idcrm_task' ? $not_seen_badge : $text;
          return !$is_seen && get_post_field( 'post_author', $comment_id ) != get_current_user_id() ? $not_seen_badge : $text;
        }

        private static function get_source($comment_id) {

          $idcrm_contact_user_id = get_post_meta($comment_id, 'idcrm_contact_user_id', true);
          $idcrm_is_document = get_post_meta($comment_id, 'idcrm_is_document', true);
          $idcrm_document_id = get_post_meta($comment_id, 'idcrm_document_id', true);
          $idcrm_deal_id = get_post_meta($comment_id, 'idcrm_deal_id', true);

          $type = '';

          if ($idcrm_contact_user_id) {
            $post_type = get_post_type($idcrm_contact_user_id);

            switch ($post_type) {
              case 'idcrm_task':

                $projects = get_the_terms( $idcrm_contact_user_id, 'idcrm_project' );
                $type = '';

                if ( ! empty( $projects ) ) {

                  foreach ( $projects as $project ) {
                    $idcrm_project_id = get_term_meta( $project->term_id, 'idcrm_project_id', true );

                    $type .= esc_html__( 'Project', idCRMActionLanguage::TEXTDOMAIN ) . ': ';

                    if ($idcrm_project_id) {
                      $type .= '<a href="' . get_the_permalink($idcrm_project_id) . '">';
                    }

                    $type .= esc_html( $project->name );

                    if ($idcrm_project_id) {
                      $type .= '</a>';
                    }

                    $type .= ', ';

                  }
                }

                $type .= esc_html__( 'Task', idCRMActionLanguage::TEXTDOMAIN );
                break;

              case 'user_contact':
                $type = esc_html__( 'Contact', idCRMActionLanguage::TEXTDOMAIN );
                break;

              case 'company':
                $type = esc_html__( 'Company', idCRMActionLanguage::TEXTDOMAIN );
                break;

              default:
                $type = '';
            }
          }

          $url = $idcrm_contact_user_id ? get_the_permalink($idcrm_contact_user_id) : get_the_permalink($comment_id);
          $title = $idcrm_contact_user_id ? get_the_title($idcrm_contact_user_id) : get_the_title($comment_id);

          if ($idcrm_is_document && $idcrm_deal_id) {
            $url = get_the_permalink($idcrm_deal_id);
            $title = get_the_title($idcrm_deal_id);
            $type = esc_html__( 'Deal', idCRMActionLanguage::TEXTDOMAIN );
          }

          if ($idcrm_is_document && $idcrm_document_id) {
            $url = get_the_permalink($idcrm_document_id);
            $title = get_the_title($idcrm_document_id);
            $type = esc_html__( 'Document', idCRMActionLanguage::TEXTDOMAIN );
          }

          if ($idcrm_deal_id && !$idcrm_is_document) {
            $url = get_the_permalink($idcrm_deal_id);
            $title = get_the_title($idcrm_deal_id);
            $type = esc_html__( 'Deal', idCRMActionLanguage::TEXTDOMAIN );
          }

          $source = $type ? $type . ': ' : '';
          $source .= '<a href="' . $url . '">' . $title . '</a>';

            return $source;
        }

        public static function idcrmGetTableContacts($queried_obj, $author) {

          $tax_query = !empty($queried_obj) ? [['taxonomy' => 'user_status', 'terms' => $queried_obj]] : '';

          $args = array(
            'post_type'   => 'user_contact',
            'post_status' => 'publish',
            'author' => $author,
            'numberposts' => -1,
            'tax_query'   => $tax_query
          );

          $user_contacts = get_posts( $args );

          if (empty($user_contacts)) {
              return json_encode([]);
          }

          include_once ABSPATH . 'wp-admin/includes/plugin.php';

          $table_data = array_map([self::class, 'get_contact_data'], $user_contacts);

          return json_encode($table_data);
        }

        public static function get_contact_data($contact) {

          $contact_id = $contact->ID;
          $post_author = $contact->post_author;

          $title = self::get_title($contact_id);
          $event = self::get_event($contact_id);
          $status = self::get_status($contact_id);
          $company = self::get_company($contact_id);
          $position = get_post_meta($contact_id, 'idcrm_contact_position', true);
          $phone = self::get_phone($contact_id);
          $email = self::get_email($contact_id);
          $manager = self::get_manager($post_author, $contact_id);
          $settings = self::get_settings($contact_id);

          $obj = (object) [
              'id' => $contact_id,
              'date' => '<span class="d-none">' . strtotime(get_the_date( 'd.m.Y H:i:s', $contact_id )) . '</span>' . get_the_date( 'd.m.Y', $contact_id ),
              'title' => $title,
              'events' => $event,
              'status' => $status,
              'company' => $company,
              'position' => $position,
              'phone' => $phone,
              'email' => $email,
              'manager' => $manager,
              'settings' => $settings
          ];

          return $obj;
        }

        private static function get_title($contact_id) {
            $has_thumbnail = has_post_thumbnail($contact_id);
            $thumbnail_url = $has_thumbnail ? esc_html(get_the_post_thumbnail_url($contact_id, array(40, 40))) : idCRM::$IDCRM_URL . 'templates/images/no-user.jpg';

            $title = '<div class="d-flex align-items-center">';
            $title .= '<a href="' . get_permalink($contact_id) . '"><img src="' . $thumbnail_url . '" class="rounded-circle object-fit-cover" width="40" height="40"/> </a>';
            $title .= '<span class="ms-3 fw-normal"><a href="' . get_the_permalink($contact_id) . '">' . get_the_title($contact_id) . '</a></span></div>';

            return $title;
        }

        private static function get_event($contact_id) {

          $contact_events = get_posts(
            array(
              'numberposts' => 1,
              'post_type'   => 'contact_event',
              'orderby' => 'meta_value',
              'meta_key' => 'idcrm_event_timestring',
              'order' => 'ASC',
              'meta_query' => [
                [
                  'key' => 'idcrm_contact_user_id',
                  'value' => $contact_id,
                ],
                [
                  'key' => 'idcrm_event_status',
                  'value' => 'active',
                ],
              ]
            )
          );

          if ( ! empty( $contact_events ) ) {
            foreach ( $contact_events as $contact_event ) {
              $idcrm_event_timestring = get_post_meta( $contact_event->ID, 'idcrm_event_timestring', true );
            }
          } else {
            // $idcrm_event_timestring = 1121902920;
            $idcrm_event_timestring = '';
          }

          $event = $idcrm_event_timestring ? '<span style="display:none">' . $idcrm_event_timestring . '</span><span class="small">' : "";

          if ( !empty( $contact_events ) && $idcrm_event_timestring ) {
            foreach ( $contact_events as $contact_event ) {
              $event_type = get_the_terms( $contact_event->ID, 'contact_events' );
              if ($event_type !== false) {
                $term_id = $event_type[0]->term_taxonomy_id;
                $custom_icon_type = !empty(get_term_meta($term_id,'custom_icon_type', true)) ? get_term_meta($term_id,'custom_icon_type', true) : false;
                $formatted_custom_icon_type = $custom_icon_type !== false ? $custom_icon_type : 'note';
                $formatted_custom_icon_type = str_replace("icon-", "", $formatted_custom_icon_type);
                $formatted_custom_icon_type = ($formatted_custom_icon_type) ?: 'note';
                $event .= '<i class="me-1 icon-' . esc_attr($formatted_custom_icon_type) . '"></i>';
                $event_dates = get_post_meta( $contact_event->ID, 'idcrm_event_timestring', true );
                if ( ! empty( $event_dates ) ) {
                  $event .= date_i18n('d.m.Y H:i', $event_dates );
                }
                $event .= ' ' . esc_html( $contact_event->post_content );
              }
            }
          }

          return $event;
        }

        private static function get_status($contact_id) {
          $status = '';

          $statuses = get_the_terms( $contact_id, 'user_status' );
          if ( ! empty( $statuses ) ) {
            $length = count($statuses);
            foreach ( $statuses as $user_status ) {
                  $badge_class = 'bg-light-success text-success';
                  $status .= "<a href='" . get_term_link($user_status->term_id) . "'>";
                  $status .=  esc_html( $user_status->name );
                  $status .= "</a>";
                  if ($length > 1) { $status .=  ', '; }
                  $length--;
            }
          }

          return $status;
        }

        private static function get_company($contact_id) {
          $company = '';

          $idcrm_contact_company = get_post_meta( $contact_id, 'idcrm_contact_company', true );
          $company_by_title = self::get_post_by_title($idcrm_contact_company, 'company');


          $company_title = $company_by_title !== 0 ? get_the_title($company_by_title) : $idcrm_contact_company;
          if ( $company_by_title !== 0 ) {
            $company_url = get_post_permalink( $company_by_title );
            $company = '<a href="' . esc_url( $company_url ) . '">' . esc_html( $company_title ) . '</a>';
          }

          return $company;
        }

        private static function get_phone($contact_id) {
          $idcrm_contact_phone = get_post_meta($contact_id, 'idcrm_contact_phone', true);
          $phone = $idcrm_contact_phone ? '<a href="tel:' . esc_html($idcrm_contact_phone) . '">' . esc_html($idcrm_contact_phone) . '</a>' : '';

          return $phone;
        }

        private static function get_email($contact_id) {
          $idcrm_contact_email = get_post_meta($contact_id, 'idcrm_contact_email', true);
          $email = $idcrm_contact_email ? '<a href="mailto:' . esc_html($idcrm_contact_email) . '">' . esc_html($idcrm_contact_email) . '</a>' : '';

          return $email;
        }

        private static function get_manager($post_author, $contact_id) {
            $user_id = get_the_author_meta('ID', intval($post_author));
            $user_img = get_user_meta($user_id, 'userimg', true);
            $default_image = idCRM::$IDCRM_URL . 'templates/images/no-user.jpg';

            $manager = '<div class="d-flex align-items-center">';
            $manager .= '<img src="' . (empty($user_img) ? esc_html($default_image) : esc_html($user_img)) . '" width="40" height="40" class="rounded-circle object-fit-cover me-2">';
            $manager .= get_the_author_meta('display_name', get_post_field('post_author', $contact_id)) . '</div>';

            return $manager;
        }

        private static function get_settings($contact_id) {
          $settings = '<div class="dropdown dropstart"><a href="#" class="link" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-more-horizontal feather-sm"><circle cx="12" cy="12" r="1"></circle><circle cx="19" cy="12" r="1"></circle><circle cx="5" cy="12" r="1"></circle></svg></a><ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">';

          if (is_plugin_active( 'idcrm-deals-documents/idcrm-deals-documents.php' )) {
              $settings .=	'<li><span class="dropdown-item contact-add-deal" data-contact-id="' . $contact_id . '"><a href="#" data-bs-toggle="modal" data-bs-target="#add-sidebar-deal">' . esc_html__( 'Add deal', idCRMActionLanguage::TEXTDOMAIN ) . '</a></span></li>';
          }

          $settings .= '<li><span class="dropdown-item"><a href="' . get_the_permalink($contact_id) . '?idcrm_action=edit">' . esc_html__( 'Edit', idCRMActionLanguage::TEXTDOMAIN ) . '</a></span></li><li><span class="dropdown-item"><a class="delete-contact" data-post-id="' .  $contact_id . '" data-id="' . get_post_meta( $contact_id, 'idcrm_contact_user_id', true ) . '" data-url="' . home_url() . '/crm-contacts/" href="#">' . esc_html__( 'Delete', idCRMActionLanguage::TEXTDOMAIN ) . '</a></span></li></ul></div>';

          return $settings;
        }
    }
}

?>
