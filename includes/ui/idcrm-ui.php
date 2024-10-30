<?php

namespace idcrm\includes\ui;

use idcrm\idCRM;

if ( ! class_exists( '\idcrm\includes\api\idCRMUI' ) ) {
  class idCRMUI {
		public function enqueue_styles() {
			// wp_enqueue_style( 'monster-style', idCRM::$IDCRM_URL . 'templates/dist/css/style.css', array() );
			// wp_enqueue_style( 'idcrm-contacts', idCRM::$IDCRM_URL . 'public/css/idcrm-contacts-public.css' );
			// wp_enqueue_style( 'toastr', idCRM::$IDCRM_URL . 'public/css/toastr.min.css', array() );
			// wp_enqueue_style( 'bootstrap-style', idCRM::$IDCRM_URL . 'templates/dist/css/bootstrap.min.css' );
			// wp_enqueue_style( 'bootstrap-table', idCRM::$IDCRM_URL . 'templates/dist/css/bootstrap-table.min.css' );
			// wp_enqueue_style( 'apexcharts', idCRM::$IDCRM_URL . 'public/css/apexcharts.css', array() );
		}

    public function register_script() {
			// wp_enqueue_script( 'app-min', idCRM::$IDCRM_URL . 'templates/dist/js/app.min.js', array( 'jquery' ), IDCRM_CONTACTS_VERSION, true );
			// wp_enqueue_script( 'app-init', idCRM::$IDCRM_URL . 'templates/dist/js/app.init.js', array( 'jquery' ), IDCRM_CONTACTS_VERSION, true );
			// wp_enqueue_script( 'app-style-switcher', idCRM::$IDCRM_URL . 'templates/dist/js/app-style-switcher.js', array( 'jquery' ), IDCRM_CONTACTS_VERSION, true );
			wp_enqueue_script( 'bootstrap-script', idCRM::$IDCRM_URL . 'templates/dist/js/bootstrap.bundle.min.js', array( 'jquery' ), IDCRM_CONTACTS_VERSION, true );
      // wp_enqueue_script( 'bootstrap-table', idCRM::$IDCRM_URL . 'templates/dist/js/bootstrap-table.min.js', array( 'jquery' ), IDCRM_CONTACTS_VERSION, true );
      //
      // wp_enqueue_script( 'bootstrap-table-cookie', idCRM::$IDCRM_URL . 'templates/dist/js/bootstrap-table-cookie.js', array( 'jquery' ), IDCRM_CONTACTS_VERSION, true );
			// wp_enqueue_script( 'bootstrap-table-locale', idCRM::$IDCRM_URL . 'templates/assets/libs/bootstrap-table/locale/bootstrap-table-' . str_replace('_', '-', get_locale()) . '.js', array( 'jquery' ), IDCRM_CONTACTS_VERSION, true );

      // wp_enqueue_script( 'sidebarmenu', idCRM::$IDCRM_URL . 'templates/dist/js/sidebarmenu.js', array( 'jquery' ), IDCRM_CONTACTS_VERSION, true );
      wp_enqueue_script( 'select2', idCRM::$IDCRM_URL . 'public/js/select2.full.min.js', array( 'jquery' ), IDCRM_CONTACTS_VERSION, true );

      wp_register_script('wp_ui_manage', idCRM::$IDCRM_URL . 'public/js/ui/ui-manage.js', array( 'jquery' ), IDCRM_CONTACTS_VERSION);
      wp_enqueue_script('wp_ui_manage');
    }

    public static function register() {
      $handler = new self();
      add_action('wp_enqueue_scripts', array($handler, 'register_script'));
      add_action('wp_enqueue_scripts', array($handler, 'enqueue_styles'));
    }
  }
}

?>
