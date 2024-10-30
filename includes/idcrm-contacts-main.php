<?php

namespace idcrm\includes;

/* Include require */
require_once('idcrm-contacts-template-loader.php');
//require_once('idCRMContactsCompanyCpt.php');
//require_once('idCRMContactsUserCpt.php');
//require_once('idCRMContactsScheduleCpt.php');

/* Lib require */
require_once('lib/idcrm-activation-pages.php');
/* Action require */
require_once('actions/idcrm-action-main.php');
/* Integration require */
require_once('integrations/idcrm-integration-main.php');
/* API require */
require_once('api/idcrm-api.php');
/* UI require */
require_once('ui/idcrm-ui.php');
/* AdminUI require */
require_once('admin/idcrm-admin-ui.php');

include_once ABSPATH . 'wp-admin/includes/plugin.php';

if ( is_plugin_active( 'idcrm-contacts-companies-pro/idcrm-contacts-companies-pro.php' ) ) {
  require_once('idcrm-settings.php');
}

/* Lib use */
use idcrm\includes\lib\idCRMActivationPages;
/* Action use */
use idcrm\includes\actions\idCRMActionMain;
/* Integration use */
use idcrm\includes\integrations\idCRMIntegrationMain;
/* API use */
use idcrm\includes\api\idCRMApi;
/* UI use */
use idcrm\includes\ui\idCRMUI;
/* AdminUI use */
use \idcrm\admin\idCRMAdminUI;

if ( ! class_exists( '\idcrm\includes\idCRMContactsMain' ) ) {
    class idCRMContactsMain {
        public static function register() {
            /* Lib */
            idCRMActivationPages::register();
            /* Action */
            idCRMActionMain::register();
            /* Integration */
            IdCRMIntegrationMain::register();
            /* API */
            idCRMApi::register();
            /* UI */
            idCRMUI::register();
            /* AdminUI */
            idCRMAdminUI::register();
            /* Include */
            idCRMContactsTemplateLoader::register();
            idCRMContactsCompanyCpt::register();
            idCRMContactsScheduleCpt::register();
            idCRMContactsUserCpt::register();

            if ( is_plugin_active( 'idcrm-contacts-companies-pro/idcrm-contacts-companies-pro.php' ) ) {
              idCRMSettings::register();
            }
        }
    }
}

?>
