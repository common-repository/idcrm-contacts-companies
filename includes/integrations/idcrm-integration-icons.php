<?php

namespace idcrm\includes\integrations;

use idcrm\idCRM;

if ( ! class_exists( '\idcrm\includes\api\IdCRMIntegrationIcons' ) ) {
    class IdCRMIntegrationIcons {
        public function register_script()
        {
            wp_register_script( 'icons-feather', idCRM::$IDCRM_URL . 'templates/dist/js/feather.min.js', array( 'jquery' ), IDCRM_CONTACTS_VERSION );
            wp_enqueue_script( 'icons-feather' );
			wp_register_script( 'icons-custom', idCRM::$IDCRM_URL . 'templates/dist/js/custom.js', array( 'jquery' ), IDCRM_CONTACTS_VERSION);
            wp_enqueue_script( 'icons-custom' );
        }
        public static function register()
        {
            $handler = new self();
            add_action('wp_enqueue_scripts', array($handler, 'register_script'));
        }
    }
}

?>
