<?php

namespace idcrm\includes\actions;

use idcrm\idCRM;
use idcrm\includes\actions\idCRMActionLanguage;

if ( ! class_exists( '\idcrm\includes\actions\idCRMActionPro' ) ) {
    class idCRMActionPro {
		public static function register()
        {
            $handler = new self();
			add_filter( 'plugin_action_links_' . idCRM::$IDCRM_FILE, array($handler, 'idcrm_pro_page') );
        }
		function idcrm_pro_page($links) {
			$links[] = '<a href="https://idresult.ru/product/id-crm-contacts-companies-pro/">' . esc_html('Get PRO', idCRMActionLanguage::TEXTDOMAIN) . '</a>';
			return $links;
		}
	}
}

?>