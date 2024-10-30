<?php

namespace idcrm\includes\api;

use idcrm\idCRM;

if ( ! class_exists( '\idcrm\includes\api\idCRMApiNote' ) ) {
    class idCRMApiNote {
        const ACTION = 'idcrm_ajax_edit_note';
        const NONCE = 'idcrm-note-ajax';

        public function register_script() {
            wp_register_script('wp_ajax_note_manage', idCRM::$IDCRM_URL . 'public/js/api/ajax-note-manage.js', array( 'jquery' ), IDCRM_CONTACTS_VERSION);
            wp_enqueue_script('wp_ajax_note_manage');

            wp_register_script('wp_ajax_note_api', idCRM::$IDCRM_URL . 'public/js/api/ajax-note-api.js', array( 'jquery' ), IDCRM_CONTACTS_VERSION);
            wp_localize_script('wp_ajax_note_api', 'wp_ajax_note_data', $this->get_ajax_data());
            wp_enqueue_script('wp_ajax_note_api');
        }

        private function get_ajax_data() {
            return array(
                'action' => self::ACTION,
                'nonce' => wp_create_nonce(idCRMApiNote::NONCE)
            );
        }

        public static function register() {
            $handler = new self();
            add_action('wp_ajax_' . self::ACTION, array($handler, 'idcrmAjaxEditNote'));
            add_action('wp_ajax_nopriv_' . self::ACTION, array($handler, 'idcrmAjaxEditNote'));
            add_action('wp_enqueue_scripts', array($handler, 'register_script'));
        }

        public function idcrmAjaxEditNote() {
            check_ajax_referer(self::NONCE);
            $message = array();
            array_push($message, 'idcrmAjaxEditNote');
            $code = 0;
            $state = 'success';
            $post_id = 0;
            if (array_key_exists('post_id', $_POST)) {
                $post_id = $_POST['post_id'];
            }
            array_push($message, '$post_id: ' . $post_id);
            $note_text = '';
            if (array_key_exists('note_text', $_POST)) {
                $note_text = $_POST['note_text'];
            }
            array_push($message, '$note_text: ' . ($note_text != '' ? 'set' : 'unset'));
            if ($post_id != 0) {
                if (isset($note_text)) {
                    $newPost = [
                        'ID' => $post_id,
                        'post_content' => $note_text,
                    ];
                    $result = wp_update_post($newPost);
                    if ( $result == 0) {
                        $code = 1;
                        $state = 'update post error';
                    }
                }
            }
            echo json_encode(array('code' => $code, 'state' => $state, 'message' => $message));
            die();
        }
    }
}

?>
