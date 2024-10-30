<?php

namespace idcrm\includes\integrations;

use idcrm\idCRM;
use idcrm\includes\actions\idCRMActionLanguage;

if ( ! class_exists( '\idcrm\includes\api\IdCRMIntegrationDatepicker' ) ) {
    class IdCRMIntegrationDatepicker {
		public function enqueue_styles() {
			// wp_enqueue_style( 'bootstrap-material-datetimepicker', idCRM::$IDCRM_URL . 'templates/assets/libs/bootstrap-material-datetimepicker/css/bootstrap-material-datetimepicker.css' );
			// wp_enqueue_style( 'fullcalendar', idCRM::$IDCRM_URL . 'public/css/fullcalendar.min.css' );
		}
        public function register_script() {
            wp_register_script( 'moment-lib', idCRM::$IDCRM_URL . 'templates/assets/libs/moment/moment.js', array( 'jquery' ), IDCRM_CONTACTS_VERSION, true);
            wp_enqueue_script( 'moment-lib' );
            wp_register_script( 'moment-locale', idCRM::$IDCRM_URL . 'templates/assets/libs/moment/locale/' . substr(get_locale(), 0, 2) . '.js', array( 'jquery' ), IDCRM_CONTACTS_VERSION, true);
            wp_enqueue_script( 'moment-locale' );
			      wp_register_script( 'bootstrap-material-datetimepicker', idCRM::$IDCRM_URL . 'templates/assets/libs/bootstrap-material-datetimepicker/js/bootstrap-material-datetimepicker-custom.js', array( 'jquery' ), IDCRM_CONTACTS_VERSION, true);
            wp_localize_script('bootstrap-material-datetimepicker', 'wp_datetimepicker_data', $this->get_locale_data());
            wp_enqueue_script( 'bootstrap-material-datetimepicker' );
        }

        private function get_locale_data() {
            return array(
                'cancel_text' => esc_html__( 'Cancel', idCRMActionLanguage::TEXTDOMAIN ),
                'ok_text' => esc_html__( 'Ok', idCRMActionLanguage::TEXTDOMAIN ),
                'clear_text' => esc_html__( 'Clear', idCRMActionLanguage::TEXTDOMAIN ),
                'now_text' => esc_html__( 'Now', idCRMActionLanguage::TEXTDOMAIN ),
                'locale' => substr(get_locale(), 0, 2),
                'source' => esc_html__( 'Source', idCRMActionLanguage::TEXTDOMAIN ),
                'company' => esc_html__( 'Company', idCRMActionLanguage::TEXTDOMAIN ),
                'contact' => esc_html__( 'Contact', idCRMActionLanguage::TEXTDOMAIN ),
                'noresults' => esc_html__( 'No results', idCRMActionLanguage::TEXTDOMAIN ),
            );
        }

        public static function register()
        {
            $handler = new self();
            add_action('wp_enqueue_scripts', array($handler, 'register_script'));
            add_action('wp_enqueue_scripts', array($handler, 'enqueue_styles'));
        }
    }
}

?>
