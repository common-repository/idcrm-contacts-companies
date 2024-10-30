<?php

namespace idcrm\admin;

require_once('idcrm-admin-taxonomy-manage.php');
require_once('idcrm-admin-event-manage-filter.php');
require_once('idcrm-admin-event-manage-data.php');
require_once('idcrm-admin-event-manage-query.php');
require_once('idcrm-admin-event-manage-columns.php');
require_once('idcrm-admin-user-manage.php');

require_once('idcrm-wp-comment-manage.php');

if ( ! class_exists( '\idcrm\includes\api\idCRMAdminUI' ) ) {
    class idCRMAdminUI {
        public static function register()
        {
            idCRMAdminTaxonomyManage::register();
            idCRMAdminEventManageFilter::register();
            idCRMAdminEventManageData::register();
            idCRMAdminEventManageQuery::register();
            idCRMAdminEventManageColumns::register();
            idCRMAdminUserManage::register();

            /* wp comment */
            idCRMWPCommentManage::register();
        }
    }
}

?>
