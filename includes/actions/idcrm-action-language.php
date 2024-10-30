<?php

namespace idcrm\includes\actions;

use idcrm\idCRM;

if ( ! class_exists( '\idcrm\includes\actions\idCRMActionLanguage' ) ) {
    class idCRMActionLanguage {
		const TEXTDOMAIN = 'idcrm-contacts-companies';

		public static function register() {
        $handler = new self();
        add_action( 'init', array($handler, 'idcrmContactsLoadTextdomain') );
    }

		public static function idcrmContactsLoadTextdomain() {
			foreach (apply_filters( 'idcrm_textdomain', [self::TEXTDOMAIN => idCRM::$IDCRM_PATH] ) as $domain => $path) {
				$locale = apply_filters('plugin_locale', get_locale(), $domain);

				load_textdomain( $domain, $path . '/languages/' . $domain . '-' . $locale . '.mo' );
				load_plugin_textdomain( $domain, false, $path . '/languages/');
			}
		}

	}
}

?>
