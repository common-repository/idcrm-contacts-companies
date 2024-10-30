<?php

namespace idcrm\includes\api;

use idcrm\idCRM;

if ( ! class_exists( '\idcrm\includes\api\idCRMApiSchedule' ) ) {
    class idCRMApiSchedule {
        const ACTION = 'idcrm_ajax_refresh_schedule';
        const NONCE = 'idcrm-schedule-ajax';

        public function register_script() {
            wp_register_script('wp_ajax_schedule_api', idCRM::$IDCRM_URL . 'public/js/api/ajax-schedule-api.js', array( 'jquery' ), IDCRM_CONTACTS_VERSION);
            wp_enqueue_script('wp_ajax_schedule_api');
            wp_register_script('wp_ajax_schedule_manage', idCRM::$IDCRM_URL . 'public/js/api/ajax-schedule-manage.js', array( 'jquery' ), IDCRM_CONTACTS_VERSION);
            wp_localize_script('wp_ajax_schedule_manage', 'wp_ajax_schedule_data', $this->get_ajax_data());
            wp_enqueue_script('wp_ajax_schedule_manage');
        }

        private function get_ajax_data() {
            return array(
                'action' => self::ACTION,
                'nonce' => wp_create_nonce(idCRMApiSchedule::NONCE)
            );
        }

        public static function register() {
            $handler = new self();
            add_action('wp_ajax_' . self::ACTION, array($handler, 'idcrmAjaxRefreshSchedule'));
            add_action('wp_ajax_nopriv_' . self::ACTION, array($handler, 'idcrmAjaxRefreshSchedule'));
            add_action('wp_enqueue_scripts', array($handler, 'register_script'));
        }

        public static function idcrmAjaxRefreshSchedule($post_id = 0) {
            $mode = 'direct';
            if (array_key_exists('post_id', $_POST)) {
                $post_id = $_POST['post_id'];
                $mode = 'ajax';
            }
            if ($mode == 'ajax') {
                check_ajax_referer(self::NONCE);
            }
            if ($post_id != 0) {
                $events = get_posts(
                    [
                        'numberposts' => -1,
                        'post_type'   => 'contact_event',
                        'meta_query' => [
                            'relation' => 'AND',
                            [
                                'key' => 'idcrm_event_status',
                                'value' => 'active',
                            ],
                            [
                                'key' => 'idcrm_contact_user_id',
                                'value' => $post_id,
                            ],
                        ]
                    ]
                );
                if ( !empty( $events ) ) {
                    foreach ( $events as $event ) {
                        set_query_var( 'current_post_id', $event->ID );
                        set_query_var( 'post_id', $post_id );
                        include idCRM::$IDCRM_PATH . 'templates/inc/schedule-loop.php';
                    }
                }
            } else {
                echo '';
            }
            if ($mode == 'ajax') {
                die();
            }
        }
    }
}

?>
