<?php

namespace idcrm\includes\api;

use idcrm\idCRM;

if ( ! class_exists( '\idcrm\includes\api\idCRMApiComment' ) ) {
    class idCRMApiComment {
        const ACTION_REFRESH = 'idcrm_ajax_refresh_comments';
        const ACTION_NEW = 'idcrm_ajax_send_comment';
        const ACTION_EDIT = 'idcrm_ajax_edit_comment';
        const ACTION_DELETE = 'idcrm_ajax_delete_comment';
        const ACTION_SET_SEEN = 'idcrm_set_comment_seen';
        const ACTION_ADD_LIKE = 'idcrm_add_like';
        const NONCE = 'idcrm-comment-ajax';

        public function register_script() {
            wp_register_script('wp_ajax_comment_manage', idCRM::$IDCRM_URL . 'public/js/api/ajax-comment-manage.js', array( 'jquery' ), IDCRM_CONTACTS_VERSION);
            wp_enqueue_script('wp_ajax_comment_manage');

            wp_register_script('wp_ajax_comment_api', idCRM::$IDCRM_URL . 'public/js/api/ajax-comment-api.js', array( 'jquery' ), IDCRM_CONTACTS_VERSION);
            wp_localize_script('wp_ajax_comment_api', 'wp_ajax_comment_data', $this->get_ajax_data());
            wp_enqueue_script('wp_ajax_comment_api');
        }

        private function get_ajax_data() {
            return array(
                'unread_comments' => self::get_uread_comments(),
                'action_refresh' => self::ACTION_REFRESH,
                'action_new' => self::ACTION_NEW,
                'action_edit' => self::ACTION_EDIT,
                'action_delete' => self::ACTION_DELETE,
                'action_set_comments_seen' => self::ACTION_SET_SEEN,
                'action_add_like' => self::ACTION_ADD_LIKE,
                'nonce' => wp_create_nonce(idCRMApiComment::NONCE)
            );
        }

        public static function register() {
            $handler = new self();
            add_action('wp_ajax_' . self::ACTION_NEW, array($handler, 'idcrmAjaxSendComment'));
            add_action('wp_ajax_nopriv_' . self::ACTION_NEW, array($handler, 'idcrmAjaxSendComment'));

            add_action('wp_ajax_' . self::ACTION_EDIT, array($handler, 'idcrmAjaxEditComment'));
            add_action('wp_ajax_nopriv_' . self::ACTION_EDIT, array($handler, 'idcrmAjaxEditComment'));

            add_action('wp_ajax_' . self::ACTION_DELETE, array($handler, 'idcrmAjaxDeleteComment'));
            add_action('wp_ajax_nopriv_' . self::ACTION_DELETE, array($handler, 'idcrmAjaxDeleteComment'));

            add_action('wp_ajax_' . self::ACTION_REFRESH, array($handler, 'idcrmAjaxRefreshComments'));
            add_action('wp_ajax_nopriv_' . self::ACTION_REFRESH, array($handler, 'idcrmAjaxRefreshComments'));

            add_action('wp_ajax_' . self::ACTION_SET_SEEN, array($handler, 'idcrm_set_comment_seen'));
            add_action('wp_ajax_nopriv_' . self::ACTION_SET_SEEN, array($handler, 'idcrm_set_comment_seen'));

            add_action('wp_ajax_' . self::ACTION_ADD_LIKE, array($handler, 'idcrm_add_like'));
            add_action('wp_ajax_nopriv_' . self::ACTION_ADD_LIKE, array($handler, 'idcrm_add_like'));

            add_action('wp_enqueue_scripts', array($handler, 'register_script'));
        }

        private static function get_uread_comments() {
          // $current_user_id = get_current_user_id();
          // $current_user_id = is_super_admin( $current_user_id ) ? "" : $current_user_id;

          $user = wp_get_current_user();
          $roles = ( array ) $user->roles;

          $current_user_id = in_array( 'administrator', $roles ) ? "" : get_current_user_id();

          $seen_key = 'idcrm_is_seen_' . get_current_user_id();

          // $idcrm_comments = get_posts([
          //     'numberposts' => -1,
          //     'post_type' => 'idcrm_comments',
          //     'author' => $current_user_id,
          //     'orderby' => 'post_date',
          //     'order' => 'DESC',
          //     'fields' => 'ids',
          //     'meta_query' => [[
          //       'key' => $seen_key,
          //       'compare' => 'NOT EXISTS'
          //     ]]
          // ]);

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

            $has_task_creator = get_posts( [
              'post_type'   => 'idcrm_task',
              'post_status' => 'publish',
              'numberposts' => -1,
              'author' => $current_user_id,
              'fields' => 'ids',
            ] );

            $has_summary = array_values(array_unique(array_merge($has_task_access, $has_task_respondible, $has_task_creator)));
          }

          $idcrm_task_comments = [];

          if (!empty($has_summary)) {

            $idcrm_task_comments = get_posts([
                'numberposts' => -1,
                'post_type' => 'idcrm_comments',
                // 'author' => $current_user_id,
                'author__not_in' => [get_current_user_id()],
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
                    'key' => $seen_key,
                    'compare' => 'NOT EXISTS'
                  ],
                ],
                'fields' => 'ids',
            ]);

          }

          // $all_comments = array_values(array_unique(array_merge($idcrm_comments, $idcrm_task_comments)));

          if (empty($idcrm_task_comments)) {
              return 0;
          }

          return count($idcrm_task_comments);
        }

        public function idcrm_add_like() {
          check_ajax_referer(self::NONCE);

          $comment_id = 0;
          if (array_key_exists('comment_id', $_POST)) {
              $comment_id = intval($_POST['comment_id']);
          }

          if ($comment_id != 0) {

            $current_user_id = get_current_user_id();

            $idcrm_likes = unserialize(get_post_meta($comment_id, 'idcrm_likes', true) ?: 'a:0:{}');

            if (!empty($idcrm_likes) && ($key = array_search($current_user_id, $idcrm_likes)) !== false) {
                unset($idcrm_likes[$key]);
            } else {
                $idcrm_likes[] = $current_user_id;
            }

    				update_post_meta( $comment_id, 'idcrm_likes', serialize($idcrm_likes) );

          }

          die();
        }

        public function idcrm_set_comment_seen() {
          check_ajax_referer(self::NONCE);
          $all_data = json_decode(stripslashes($_POST['data']));
          $current_user_id = get_current_user_id();

          if (!empty($all_data)) {
            foreach ($all_data as $comment_id) {
              update_post_meta($comment_id, 'idcrm_is_seen_' . $current_user_id, 'yes');
            }
          }

          echo self::get_uread_comments();

          die();
        }

        public static function idcrmAjaxRefreshComments( $post_id = 0, $post_type = '' ) {
            $message = [];
            $mode = 'direct';

            if (array_key_exists('post_type', $_POST)) {
                $post_type = $_POST['post_type'];
            }

            if (array_key_exists('post_id', $_POST)) {
                $post_id = $_POST['post_id'];
                $mode = 'ajax';
            }
            if ($mode == 'ajax') {
                check_ajax_referer(self::NONCE);
            }
            array_push($message, '$post_id: ' . $post_id);
            if ($post_id != 0) {
                set_query_var( 'current_post_id', $post_id );
                set_query_var( 'post_type', $post_type );
                include idCRM::$IDCRM_PATH . 'templates/inc/comments-loop.php';
                set_query_var( 'current_loop_id', $post_id );
                set_query_var( 'post_type', $post_type );
                include idCRM::$IDCRM_PATH . 'templates/inc/events-loop.php';
            }
            if ($mode == 'ajax') {
                die();
            }
        }

        public function idcrmAjaxSendComment() {
            check_ajax_referer(self::NONCE);
            $code = 0;
            $status = 'success';
            $message = array();
            $post_id = 0;
            if (array_key_exists('post_id', $_POST)) {
                $post_id = $_POST['post_id'];
            }
            array_push($message, '$post_id: ' . $post_id);
            $comment = '';
            if (array_key_exists('comment', $_POST)) {
                $comment = $_POST['comment'];
            }
            array_push($message, '$comment: ' . ($comment !='' ? 'set' : 'uset'));
            $post_type = '';
            if (array_key_exists('post_type', $_POST)) {
                $post_type = $_POST['post_type'];
            }
            array_push($message, '$post_type: ' . $post_type);
            $user_id = 0;
            if (array_key_exists('user_id', $_POST)) {
                $user_id = $_POST['user_id'];
            }
            array_push($message, '$user_id: ' . $user_id);
            if ($post_id != 0 && $comment != '' && $user_id != 0) {
                $manager = get_userdata($user_id);
                $post_title = 'comment-for-post-' . $post_id . '-from-user-' . $user_id;
                $new_comment = array(
                    'post_title'  => $post_title,
                    'post_content'  => $comment,
                    'post_type'   => 'idcrm_comments',
                    'post_author' => get_current_user_id(),
                    'post_status' => 'publish',
                );
                $new_event_id = wp_insert_post( $new_comment );
                $idcrm_comments_iserted = true;
                if( is_wp_error($new_event_id) ){
                    array_push($message, $post_id->get_error_message());
                    $idcrm_comments_iserted = false;
                }
                if ($new_event_id == 0) {
                    $code = 1;
                    $status = 'error';
                    array_push($message, 'Не удалось вставить запись');
                    $idcrm_comments_iserted = false;
                }
                if ($idcrm_comments_iserted) {
                    array_push($message, '$new_event_id: ' . $new_event_id);
                    /* $idcrm_company_id = get_post_meta( $post_id, 'idcrm_company_id', true );
                    $idcrm_contact_user_id = get_post_meta( $post_id, 'idcrm_contact_user_id', true );
                    $key = $idcrm_company_id ? 'idcrm_company_id' : 'idcrm_contact_user_id'; */
                    add_post_meta( $new_event_id, 'idcrm_contact_user_id', $post_id);
                    update_post_meta( $new_event_id, 'idcrm_comment_type', 'comment');
                    add_post_meta( $new_event_id, 'idcrm_event_timestring', current_time('timestamp')*1000 );

                    if ($post_type) {
                      add_post_meta( $new_event_id, 'idcrm_post_type', $post_type);
                    }
                }
            }
            if (empty($message)) {
                array_push($message, 'success');
            }
            echo json_encode(array('code' => $code, 'status' => $status, 'message' => $message));
            die();
        }

        public function idcrmAjaxEditComment() {
            check_ajax_referer(self::NONCE);
            $code = 0;
            $status = 'success';
            $message = array();
            /*$post_id = 0;
            if (array_key_exists('post_id', $_POST)) {
                $post_id = $_POST['event_id'];
            }
            array_push($message, '$post_id: ' . $post_id);*/
            $event_id = 0;
            $is_comment = false;

            if (array_key_exists('is_comment', $_POST)) {
                $is_comment = $_POST['is_comment'];
            }

            if (array_key_exists('event_id', $_POST)) {
                $event_id = $_POST['event_id'];
            }

            array_push($message, '$event_id: ' . $event_id);
            $comment_text = '';

            if (array_key_exists('comment_text', $_POST)) {
                $comment_text = $_POST['comment_text'];
            }

            array_push($message, '$comment_text: ' . $comment_text);

            if ($event_id != 0 && $comment_text != '') {
                $new_post = [
                    'ID' => $event_id,
                    'post_content' => $comment_text,
                ];

                if (wp_update_post( wp_slash( $new_post ) ) == 0) {
                    $code = 1;
                    $status = 'fail';
                }
            }

            echo json_encode(array('code' => $code, 'status' => $status, 'message' => $message));

            die();
        }

        public function idcrmAjaxDeleteComment() {
            check_ajax_referer(self::NONCE);
            $code = 0;
            $status = 'success';
            $message = array();

            $id = 0;
            if (array_key_exists('id', $_POST)) {
                $id = $_POST['id'];
            }

            array_push($message, '$id: ' . $id);

            if ($id != 0 ) {

                if (wp_delete_post( $id, true ) === false) {
                    $code = 1;
                    $status = 'fail';
                }
            }
            echo json_encode(array('code' => $code, 'status' => $status, 'message' => $message));
            die();
        }

    }
}

?>
