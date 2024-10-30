<?php

namespace idcrm\includes\integrations;

require_once('idcrm-integration-cf7.php');
require_once('idcrm-integration-datepicker.php');
require_once('idcrm-integration-toastr.php');
require_once('idcrm-integration-scrollbar.php');
require_once('idcrm-integration-icons.php');
require_once('idcrm-integration-waves.php');

use idcrm\idCRM;

if ( ! class_exists( '\idcrm\includes\integrations\idCRMIntegrationMain' ) ) {
    class idCRMIntegrationMain {
        public static function register()
        {
            IdCRMIntegrationCF7::register();
            IdCRMIntegrationDatepicker::register();
            IdCRMIntegrationToastr::register();
            IdCRMIntegrationScrollbar::register();
            IdCRMIntegrationIcons::register();
            IdCRMIntegrationWaves::register();
        }
    }
}

?>
