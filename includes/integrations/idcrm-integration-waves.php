<?php

namespace idcrm\includes\integrations;

use idcrm\idCRM;

if ( ! class_exists( '\idcrm\includes\api\IdCRMIntegrationWaves' ) ) {
    class IdCRMIntegrationWaves {
        public function register_script()
        {
            wp_register_script( 'waves', idCRM::$IDCRM_URL . 'templates/dist/js/waves.js', array( 'jquery' ), IDCRM_CONTACTS_VERSION );
            wp_enqueue_script( 'waves' );
        }
        public static function register()
        {
            $handler = new self();
            add_action('wp_enqueue_scripts', array($handler, 'register_script'));
        }
    }
}

?>
