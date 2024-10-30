<?php

namespace idcrm\includes\integrations;

use idcrm\idCRM;

if ( ! class_exists( '\idcrm\includes\integrations\IdCRMIntegrationScrollbar' ) ) {
    class IdCRMIntegrationScrollbar {

        public function register_script() {
    			wp_register_script( 'perfect-scrollbar', idCRM::$IDCRM_URL . 'templates/assets/libs/perfect-scrollbar/dist/perfect-scrollbar.min.js', array( 'jquery' ), IDCRM_CONTACTS_VERSION );
          wp_enqueue_script( 'perfect-scrollbar' );
    			wp_register_script( 'sparkline', idCRM::$IDCRM_URL . 'templates/assets/libs/jquery-sparkline/jquery.sparkline.min.js', array( 'jquery' ), IDCRM_CONTACTS_VERSION );
          wp_enqueue_script( 'sparkline' );
        }

        public static function register() {
            $handler = new self();
            add_action('wp_enqueue_scripts', array($handler, 'register_script'));
        }
    }
}

?>
