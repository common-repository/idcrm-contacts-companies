<?php

namespace idcrm\includes;

require_once('lib/idcrm-deactivation.php');

if ( ! class_exists( '\idcrm\includes\idCRMContactsDeactivator' ) ) {
    class idCRMContactsDeactivator {
        public static function deactivate() {
            lib\delete_crm_roles();
            flush_rewrite_rules();
        }

    }
}

?>