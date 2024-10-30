<?php

namespace idcrm\includes\actions;

require_once('idcrm-action-language.php');
require_once('idcrm-action-menu.php');
require_once('idcrm-action-search.php');
require_once('idcrm-action-comment.php');
require_once('idcrm-action-pro.php');
require_once('idcrm-action-redirects.php');
// require_once('idcrm-action-scripts-remover.php');

use idcrm\idCRM;

if ( ! class_exists( '\idcrm\includes\integrations\idCRMActionMain' ) ) {
    class idCRMActionMain {
        public static function register() {
            idCRMActionLanguage::register();
            idCRMActionSearch::register();
            idCRMActionMenu::register();
            idCRMActionComment::register();
            idCRMActionPro::register();
            idCRMActionRedirects::register();
            // idCRMActionScriptsRemover::register();
        }
    }
}

?>
