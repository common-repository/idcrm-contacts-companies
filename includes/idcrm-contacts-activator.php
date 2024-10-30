<?php

namespace idcrm\includes;

require_once('lib/idcrm-activation-pages.php');

require_once('lib/idcrm-activation.php');
require_once('lib/idcrm-activation-company.php');
require_once('lib/idcrm-activation-contact.php');

use idcrm\includes\lib\idCRMActivationPages;

class idCRMContactsActivator {
    public static function activate() {
		idCRMActivationPages::create_pages();
        lib\add_crm_roles();
        lib\create_first_company();
        lib\create_first_contact();
        flush_rewrite_rules();
    }
}

?>
