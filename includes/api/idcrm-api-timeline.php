<?php

namespace idcrm\includes\api;

use idcrm\idCRM;

if ( ! class_exists( '\idcrm\includes\api\idCRMApiTimeline' ) ) {
    class idCRMApiTimeline {

        public function register_script() {
            wp_register_script('wp_ajax_timeline_manage', idCRM::$IDCRM_URL . 'public/js/api/ajax-timeline-manage.js', array( 'jquery' ), IDCRM_CONTACTS_VERSION);
            wp_enqueue_script('wp_ajax_timeline_manage');
        }

        public static function register() {
            $handler = new self();
            add_action('wp_enqueue_scripts', array($handler, 'register_script'));
        }
    }
}

?>
