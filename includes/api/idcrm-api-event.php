<?php

namespace idcrm\includes\api;

use idcrm\idCRM;
use idcrm\includes\actions\idCRMActionLanguage;

if ( ! class_exists( '\idcrm\includes\api\idCRMApiEvent' ) ) {
    class idCRMApiEvent {
        const ACTION_STATUS = 'idcrm_ajax_status';
        const ACTION_NEW = 'idcrm_ajax_new_event';
        const ACTION_EDIT_EVENT = 'idcrm_ajax_edit_event';
        const NONCE = 'idcrm-event-ajax';

        public function register_script() {
            wp_register_script('wp_ajax_event_api', idCRM::$IDCRM_URL . 'public/js/api/ajax-event-api.js', array( 'jquery' ), IDCRM_CONTACTS_VERSION);
            wp_enqueue_script('wp_ajax_event_api');

            wp_register_script('wp_ajax_event_manage', idCRM::$IDCRM_URL . 'public/js/api/ajax-event-manage.js', array( 'jquery' ), IDCRM_CONTACTS_VERSION);
            wp_localize_script('wp_ajax_event_manage', 'wp_ajax_event_data', $this->get_ajax_data());
            wp_enqueue_script('wp_ajax_event_manage');
        }

        private function get_ajax_data() {
            return array(
                'action_status' => self::ACTION_STATUS,
                'action_new' => self::ACTION_NEW,
                'action_edit_event' => self::ACTION_EDIT_EVENT,
                'nonce' => wp_create_nonce(idCRMApiEvent::NONCE)
            );
        }

        public static function register() {
            $handler = new self();
            add_action('wp_ajax_' . self::ACTION_STATUS, array($handler, 'idCRMAjaxStatus'));
            add_action('wp_ajax_nopriv_' . self::ACTION_STATUS, array($handler, 'idCRMAjaxStatus'));

            add_action('wp_ajax_' . self::ACTION_NEW, array($handler, 'idCRMAjaxNewEvent'));
            add_action('wp_ajax_nopriv_' . self::ACTION_NEW, array($handler, 'idCRMAjaxNewEvent'));

            add_action('wp_ajax_' . self::ACTION_EDIT_EVENT, array($handler, 'idcrm_ajax_edit_event'));
            add_action('wp_ajax_nopriv_' . self::ACTION_EDIT_EVENT, array($handler, 'idcrm_ajax_edit_event'));

            add_action('wp_enqueue_scripts', array($handler, 'register_script'));
            // add_action( 'edit_post', array($handler, 'update_timstamp'), 10, 3 );
            add_action('updated_post_meta', array($handler, 'update_timstamp'), 10, 4);
        }

        public function update_timstamp($tmp, $post_ID, $meta_key, $meta_value = NULL) {
          if ( get_post_type( $post_ID ) == 'contact_event' && ($meta_key == 'idcrm_event_date' || $meta_key == 'idcrm_event_time')) {
            // clean_post_cache( $post_ID );
            $idcrm_event_date = get_post_meta($post_ID, 'idcrm_event_date', true);
            $idcrm_event_time = get_post_meta($post_ID, 'idcrm_event_time', true);

            update_post_meta( $post_ID, 'idcrm_event_timestring', strtotime($idcrm_event_date . ' ' . $idcrm_event_time) );
          }
        }

        /* Ajax Delete event on checkbox */
        public function idCRMAjaxStatus() {
            check_ajax_referer(self::NONCE);
            $message = array();
            array_push($message, 'idCRMAjaxStatus');
            $code = 0;
            $state = 'success';
            $id = 0;
            if (array_key_exists('id', $_POST)) {
                $id = $_POST['id'];
            }
            array_push($message, '$id: ' . $id);
            $status = '';
            if (array_key_exists('status', $_POST)) {
                $status = $_POST['status'];
            }
            array_push($message, '$status: ' . $status);
            $comment_post_ID = 0;
            if (array_key_exists('comment_post_ID', $_POST)) {
                $comment_post_ID = $_POST['comment_post_ID'];
            }
            $current_user_id = 0;
            if (array_key_exists('current_user_id', $_POST)) {
                $current_user_id = $_POST['current_user_id'];
            }
            array_push($message, '$current_user_id: ' . $current_user_id);
            $comment_author = false;
            if ($current_user_id != 0) {
                $comment_author = get_userdata($current_user_id);
            }
            array_push($message, '$comment_author: ' . print_r(($comment_author === false ? 'false' : 'WP_User'), true));
            $status_type_text = '';
            switch ($status) {
                    case 'active':
                            $status_type_text = esc_html__( 'Active', idCRMActionLanguage::TEXTDOMAIN );
                            break;
                    default:
                            $status_type_text = esc_html__( 'Finished', idCRMActionLanguage::TEXTDOMAIN );
            }
            array_push($message, '$status_type_text: ' . $status_type_text);
            if ($id != 0 && $status != '' && $current_user_id != 0) {
                $result = update_post_meta( $id, 'idcrm_event_status', $status );
                array_push($message, '$result: ' . ($result ? ($result === true ? 'true' : $result) : 'false'));
                if ($status == 'finished') {

                  $current_time = array_key_exists('current_time', $_POST) ? $_POST['current_time'] : current_time('mysql');

                    $my_post = [
                        'ID' => $id,
                        'post_date' => $current_time,
                        'post_author' => $current_user_id,
                    ];
                    $result = wp_update_post( wp_slash( $my_post ) );
                    array_push($message, '$result: ' . print_r($result, true));
                }
            }
            echo json_encode(array('code' => $code, 'state' => $state, 'message' => $message));
            die();
        }

        public function idcrm_ajax_edit_event() {
            check_ajax_referer(self::NONCE);

            $all_data = json_decode(stripslashes($_POST['data']));

            $result['code'] = 0;
            $result['status'] = 'success';

            $event_id = 0;
            if ( isset( $all_data->event_id ) ) {
                $event_id = intval($all_data->event_id);
            }
            $result['message'][] = '$event_id: ' . $event_id;

            $edit_event_type = 0;
            if ( isset( $all_data->edit_event_type ) ) {
                $edit_event_type = intval($all_data->edit_event_type);
            }
            $result['message'][] = '$edit_event_type: ' . $edit_event_type;

            $edit_event_timestring = 0;
            if ( isset( $all_data->edit_event_timestring ) ) {
                $edit_event_timestring = intval($all_data->edit_event_timestring);
            }
            $result['message'][] = '$edit_event_timestring: ' . $edit_event_timestring;

            $edit_event_topic = 0;
            if ( isset( $all_data->edit_event_topic ) ) {
                $edit_event_topic = $all_data->edit_event_topic;
            }
            $result['message'][] = '$edit_event_topic: ' . $edit_event_topic;

            $edit_event_date = 0;
            if ( isset( $all_data->edit_event_date ) ) {
                $edit_event_date = $all_data->edit_event_date;
            }
            $result['message'][] = '$edit_event_date: ' . $edit_event_date;

            $edit_event_time = 0;
            if ( isset( $all_data->edit_event_time ) ) {
                $edit_event_time = $all_data->edit_event_time;
            }
            $result['message'][] = '$edit_event_time: ' . $edit_event_time;

            if ($event_id !== 0 && $edit_event_timestring !== 0) {

              update_post_meta( $event_id, 'idcrm_event_timestring', $edit_event_timestring );
              update_post_meta( $event_id, 'idcrm_event_date', $edit_event_date );
              update_post_meta( $event_id, 'idcrm_event_time', $edit_event_time );

              wp_set_object_terms( $event_id, $edit_event_type, 'contact_events' );

              if ($edit_event_topic !== 0) {
                  $new_post_content = [
                      'ID' => $event_id,
                      'post_content'  => sanitize_text_field($edit_event_topic),
                  ];

                  if (wp_update_post( wp_slash( $new_post_content ) ) == 0) {
                      $result['code'] = 1;
                      $result['status'] = 'fail';
                      array_push($result['message'], 'Cant update edit_event_topic');
                  }
              }

            }

            echo json_encode($result);

            die();
        }

        /* Ajax add Event */
        public function idCRMAjaxNewEvent() {
            check_ajax_referer(self::NONCE);
            $code = 0;
            $status = 'success';
            $message = [];
            $event_date = '';
            if (array_key_exists('event_date', $_POST)) {
                $event_date = $_POST['event_date'];
            }
            $message['param']['event_date'] = $event_date;
            $event_time = '';
            if (array_key_exists('event_time', $_POST)) {
                $event_time = $_POST['event_time'];
            }
            $message['param']['event_time'] = $event_time;
            $post_content = '';
            if (array_key_exists('post_title', $_POST)) {
                $post_content = $_POST['post_title'];
            }
            $message['param']['post_content'] = $post_content;
            $event_type = '';
            if (array_key_exists('event_type', $_POST)) {
                $event_type = $_POST['event_type'];
            }
            $message['param']['event_type'] = $event_type;
            $post_author = 0;
            if (array_key_exists('post_author', $_POST)) {
                $post_author = $_POST['post_author'];
            }
            $message['param']['post_author'] = $post_author;
            $idcrm_contact_user_id = 0;
            if (array_key_exists('idcrm_contact_user_id', $_POST)) {
                $idcrm_contact_user_id = $_POST['idcrm_contact_user_id'];
            }
            $message['param']['idcrm_contact_user_id'] = $idcrm_contact_user_id;
            $idcrm_event_timestring = '';
            if (array_key_exists('idcrm_event_timestring', $_POST)) {
                $idcrm_event_timestring = $_POST['idcrm_event_timestring'];
            }
            $message['param']['idcrm_event_timestring'] = $idcrm_event_timestring;

            if (isset($event_type)
                && $post_content != ''
                && $post_author != 0
                && $idcrm_event_timestring != ''
                && $idcrm_contact_user_id != 0
            ) {
                $post_title = 'event-type-' . $event_type . '-for-user-' . $idcrm_contact_user_id;
                array_push($message, '$post_title: ' . $post_title);
                array_push($message, '$post_content: ' . ($post_content != '' ? 'set' : 'uset'));
                array_push($message, '$idcrm_contact_user_id: ' . $idcrm_contact_user_id);
                $new_event = array(
                    'post_title'  => $post_title,
                    'post_content'  => sanitize_text_field($post_content),
                    'post_type'   => 'contact_event',
                    'post_author' => $post_author,
                    'post_status' => 'publish',
                );
                $new_event_id = wp_insert_post( $new_event );
                array_push($message, '$new_event_id: ' . $new_event_id);
                $idcrm_event_iserted = true;
                if( is_wp_error($new_event_id) ){
                    $code = 1;
                    $status = 'error';
                    array_push($message, '$new_event_id->get_error_message(): ' . $new_event_id->get_error_message());
                    $idcrm_event_iserted = false;
                }
                if ($new_event_id == 0) {
                    $code = 1;
                    $status = 'error';
                    array_push($message, 'Не удалось вставить запись');
                    $idcrm_event_iserted = false;
                }
                if ($idcrm_event_iserted) {

                  $event_ids = array( $event_type );
                  $event_ids = array_map('intval', $event_ids );

                    wp_set_object_terms( $new_event_id, $event_ids, 'contact_events' );
                    add_post_meta( $new_event_id, 'idcrm_contact_user_id', $idcrm_contact_user_id );
                    add_post_meta( $new_event_id, 'idcrm_event_timestring', $idcrm_event_timestring );
                    add_post_meta( $new_event_id, 'idcrm_event_status', 'active' );
                    add_post_meta( $new_event_id, 'idcrm_event_date', $event_date );
                    add_post_meta( $new_event_id, 'idcrm_event_time', $event_time );
                    add_post_meta( $new_event_id, 'idcrm_event_user_or_company', stristr( $idcrm_contact_user_id, 'user' ) ? 'user' : 'company' );
                }
            } else {
                $code = 1;
                $status = 'fail';
                $message['error'] = 'Not all requred params recived';
            }
            echo json_encode(['code' => $code, 'status' => $status, 'message' => $message]);
            die();
        }
    }
}

?>
