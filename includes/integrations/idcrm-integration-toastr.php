<?php

namespace idcrm\includes\integrations;

use idcrm\idCRM;

if ( ! class_exists( '\idcrm\includes\api\IdCRMIntegrationToastr' ) ) {
    class IdCRMIntegrationToastr {
        public function register_script()
        {
			wp_register_script( 'toastr', idCRM::$IDCRM_URL . 'public/js/toastr.min.js', array( 'jquery' ), IDCRM_CONTACTS_VERSION);
            wp_enqueue_script( 'toastr' );
        }
        public static function register()
        {
            $handler = new self();

            add_action('wp_enqueue_scripts', array($handler, 'register_script'));

            // add_action('admin_enqueue_scripts', array($handler, 'register_script'));
        }
    }
}

?>
