<?php

namespace idcrm\includes\api;

use idcrm\idCRM;
use idcrm\includes\actions\idCRMActionLanguage;

if ( ! class_exists( '\idcrm\includes\api\idCRMApiCompany' ) ) {
    class idCRMApiCompany {
        const ACTION = 'idcrm_ajax_assign_company';
        const ACTION_UPDATE = 'action_update_company';
        const NONCE = 'idcrm-company-ajax';

        public function register_script() {
            wp_register_script('wp_ajax_company_manage', idCRM::$IDCRM_URL . 'public/js/api/ajax-company-manage.js', array( 'jquery' ), IDCRM_CONTACTS_VERSION);
            wp_enqueue_script('wp_ajax_company_manage');

            wp_register_script('wp_ajax_company_api', idCRM::$IDCRM_URL . 'public/js/api/ajax-company-api.js', array( 'jquery' ), IDCRM_CONTACTS_VERSION);
            wp_localize_script('wp_ajax_company_api', 'wp_ajax_company_data', $this->get_ajax_data());
            wp_enqueue_script('wp_ajax_company_api');
        }

        private function get_ajax_data() {
            return array(
                'action' => self::ACTION,
                'action_update_company' => self::ACTION_UPDATE,
                'nonce' => wp_create_nonce(idCRMApiCompany::NONCE)
            );
        }

        public static function register() {
            $handler = new self();

            add_action('wp_ajax_' . self::ACTION, array($handler, 'idcrmAjaxAssignCompany'));
            add_action('wp_ajax_nopriv_' . self::ACTION, array($handler, 'idcrmAjaxAssignCompany'));

            add_action('wp_ajax_' . self::ACTION_UPDATE, array($handler, 'idcrm_update_company'));
            add_action('wp_ajax_nopriv_' . self::ACTION_UPDATE, array($handler, 'idcrm_update_company'));

            add_action('wp_enqueue_scripts', array($handler, 'register_script'));
        }

        public function idcrm_update_company() {
          check_ajax_referer(self::NONCE);
          $all_data = json_decode(stripslashes($_POST['data']));

          $company_id = 0;
          if ( isset( $all_data->company_id ) ) {
              $company_id = intval($all_data->company_id);
          }

          $company_title = 0;
          if ( isset( $all_data->company_title ) ) {
              $company_title = $all_data->company_title;
          }

          if ($company_title !== 0 && $company_id !== 0) {

              $new_title = [
                  'ID' => $company_id,
                  'post_title' => wp_unslash(sanitize_text_field($company_title)),
              ];

              if (wp_update_post( wp_slash( $new_title ) ) == 0) {
                  // $result['code'] = 1;
                  // $result['status'] = 'fail';
                  // array_push($result['message'], 'Cant update title');
              }
          }

          $comp_status = 0;
          if ( isset( $all_data->comp_status ) ) {
              $comp_status = intval($all_data->comp_status);
          }

          if ($comp_status !== 0 && $company_id !== 0) {

              $project_array = [];
              $project_array[] = $comp_status;

              wp_set_post_terms( $company_id, $project_array, 'comp_status', false );
          }

          $company_website = 0;
          if ( isset( $all_data->company_website ) ) {
              $company_website = $all_data->company_website;
          }
          if ($company_website !== 0 && $company_id !== 0) {
            update_post_meta( $company_id, 'idcrm_company_website', sanitize_text_field( wp_unslash( $company_website ) ) );
          }

          $company_facebook = 0;
          if ( isset( $all_data->company_facebook ) ) {
              $company_facebook = $all_data->company_facebook;
          }
          if ($company_facebook !== 0 && $company_id !== 0) {
            update_post_meta( $company_id, 'idcrm_company_facebook', sanitize_text_field( wp_unslash( $company_facebook ) ) );
          }

          $company_twitter = 0;
          if ( isset( $all_data->company_twitter ) ) {
              $company_twitter = $all_data->company_twitter;
          }
          if ($company_twitter !== 0 && $company_id !== 0) {
            update_post_meta( $company_id, 'idcrm_company_twitter', sanitize_text_field( wp_unslash( $company_twitter ) ) );
          }

          $company_youtube = 0;
          if ( isset( $all_data->company_youtube ) ) {
              $company_youtube = $all_data->company_youtube;
          }
          if ($company_youtube !== 0 && $company_id !== 0) {
            update_post_meta( $company_id, 'idcrm_company_youtube', sanitize_text_field( wp_unslash( $company_youtube ) ) );
          }

          $company_inn = 0;
          if ( isset( $all_data->company_inn ) ) {
              $company_inn = $all_data->company_inn;
          }
          if ($company_inn !== 0 && $company_id !== 0) {
            update_post_meta( $company_id, 'idcrm_company_inn', sanitize_text_field( wp_unslash( $company_inn ) ) );
          }

          $company_kpp = 0;
          if ( isset( $all_data->company_kpp ) ) {
              $company_kpp = $all_data->company_kpp;
          }
          if ($company_kpp !== 0 && $company_id !== 0) {
            update_post_meta( $company_id, 'idcrm_company_kpp', sanitize_text_field( wp_unslash( $company_kpp ) ) );
          }

          $company_ogrn = 0;
          if ( isset( $all_data->company_ogrn ) ) {
              $company_ogrn = $all_data->company_ogrn;
          }
          if ($company_ogrn !== 0 && $company_id !== 0) {
            update_post_meta( $company_id, 'idcrm_company_ogrn', sanitize_text_field( wp_unslash( $company_ogrn ) ) );
          }

          die();
        }

        public function idcrmAjaxAssignCompany() {
            check_ajax_referer(self::NONCE);
            $code = 0;
            $status = 'success';
            $message = array();
            $post_id = 0;
            if (array_key_exists('post_id', $_POST)) {
                $post_id = $_POST['post_id'];
            }
            array_push($message, '$post_id: ' . $post_id);
            $company_id = 0;
            if (array_key_exists('company_id', $_POST)) {
                $company_id = $_POST['company_id'];
            }
            array_push($message, '$company_id: ' . $company_id);
            if ($post_id != 0 && $company_id != 0) {
                if (update_post_meta( $post_id, 'idcrm_contact_company', $company_id ) === false) {
                    $code = 1;
                    $status = 'fail update post';
                }
            }
            echo json_encode(array('code' => $code, 'status' => $status, 'message' => $message));
            die();
        }

        public static function idcrmGetTableCompanies($queried_obj, $author) {

          $tax_query = !empty($queried_obj) ? [['taxonomy' => 'comp_status', 'terms' => $queried_obj]] : '';

          $args = array(
            'post_type'   => 'company',
            'post_status' => 'publish',
            'author' => $author,
            'numberposts' => -1,
            'tax_query'   => $tax_query
          );

          $user_contacts = get_posts( $args );

          if (empty($user_contacts)) {
              return json_encode([]);
          }

          $table_data = array_map([self::class, 'get_company_data'], $user_contacts);

          return json_encode($table_data);
        }

        public static function get_company_data($company) {

          $company_id = $company->ID;
          $post_author = $company->post_author;

          $title = self::get_title($company_id);
          $status = self::get_status($company_id);
          $manager = self::get_manager($post_author, $company_id);
          $settings = self::get_settings($company_id);

          $obj = (object) [
              'id' => $company_id,
              'date' => '<span class="d-none">' . strtotime(get_the_date( 'd.m.Y H:i:s', $company_id )) . '</span>' . get_the_date( 'd.m.Y', $company_id ),
              'title' => $title,
              'status' => $status,
              'manager' => $manager,
              'settings' => $settings
          ];

          return $obj;
        }

        private static function get_title($company_id) {
            $has_thumbnail = has_post_thumbnail($company_id);
            $thumbnail_url = $has_thumbnail ? esc_html(get_the_post_thumbnail_url($company_id, array(40, 40))) : idCRM::$IDCRM_URL . 'templates/images/no-user.jpg';

            $title = '<div class="d-flex align-items-center">';
            $title .= '<a href="' . get_permalink($company_id) . '"><img src="' . $thumbnail_url . '" class="rounded-circle object-fit-cover" width="40" height="40"/> </a>';
            $title .= '<span class="ms-3 fw-normal"><a href="' . get_the_permalink($company_id) . '">' . get_the_title($company_id) . '</a></span></div>';

            return $title;
        }

        private static function get_status($company_id) {
          $status = '';

          $statuses = get_the_terms( $company_id, 'comp_status' );
          if ( ! empty( $statuses ) ) {
            $length = count($statuses);
            foreach ( $statuses as $comp_status ) {

                    $badge_class = 'bg-light-success text-success';
                  $status .= "<a href='" . get_term_link($comp_status->term_id) . "'>";
                  $status .=  esc_html( $comp_status->name );
                  $status .= "</a>";
                  if ($length > 1) { $status .=  ', '; }
                  $length--;
            }
          }

            return $status;
        }

        private static function get_manager($post_author, $company_id) {
            $user_id = get_the_author_meta('ID', intval($post_author));
            $user_img = get_user_meta($user_id, 'userimg', true);
            $default_image = idCRM::$IDCRM_URL . 'templates/images/no-user.jpg';

            $manager = '<div class="d-flex align-items-center">';
            $manager .= '<img src="' . (empty($user_img) ? esc_html($default_image) : esc_html($user_img)) . '" width="40" height="40" class="rounded-circle object-fit-cover me-2">';
            $manager .= get_the_author_meta('display_name', get_post_field('post_author', $company_id)) . '</div>';

            return $manager;
        }

        private static function get_settings($company_id) {
          $settings = '<div class="dropdown dropstart"><a href="#" class="link" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-more-horizontal feather-sm"><circle cx="12" cy="12" r="1"></circle><circle cx="19" cy="12" r="1"></circle><circle cx="5" cy="12" r="1"></circle></svg></a><ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">';

          $settings .= '<li><span class="dropdown-item"><a href="' . get_edit_post_link($company_id) . '">' . esc_html__( 'Edit', idCRMActionLanguage::TEXTDOMAIN ) . '</a></span></li><li><span class="dropdown-item"><a class="delete-contact" data-id="' . get_post_meta( $company_id, 'idcrm_system_user', true ) . '" data-url="' . get_delete_post_link($company_id) . '" href="#">' . esc_html__( 'Delete', idCRMActionLanguage::TEXTDOMAIN ) . '</a></span></li></ul></div>';

          return $settings;
        }
    }
}

?>
