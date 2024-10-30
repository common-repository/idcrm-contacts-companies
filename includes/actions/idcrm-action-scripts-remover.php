<?php

namespace idcrm\includes\actions;

use idcrm\idCRM;

if ( ! class_exists( 'idCRMActionScriptsRemover' ) ) {

class idCRMActionScriptsRemover {
    public $exclude_scripts = array();
    public $exclude_styles = array();

    private static function replace_between($str, $needle_start, $needle_end, $replacement) {
        $pos = strpos($str, $needle_start);
        $start = $pos === false ? 0 : $pos + strlen($needle_start);

        $pos = strpos($str, $needle_end, $start);
        $end = $start === false ? strlen($str) : $pos;

        return substr_replace($str,$replacement,  $start, $end - $start);
    }

    public static function remove_scripts($exclude_scripts) {

      add_action('wp_head', static function () {
        ob_start();
      }, 0);

      add_action('wp_head', static function () {
        $updatedString = ob_get_clean();

        $updatedString = self::replace_between($updatedString, '<!-- Yandex.Metrika counter -->', '<!-- /Yandex.Metrika counter -->', '');
        $updatedString = self::replace_between($updatedString, '<!-- Google Tag Manager -->', '<!-- End Google Tag Manager -->', '');

        //mail ru
        $updatedString = self::replace_between($updatedString, '<!-- Rating Mail.ru logo -->', '<!-- /Rating Mail.ru logo -->', '');
        $updatedString = self::replace_between($updatedString, '<!-- Top.Mail.Ru counter -->', '<!-- /Top.Mail.Ru counter -->', '');
        $updatedString = self::replace_between($updatedString, '<noscript><div><img src="https://top-fwz1.mail.ru/counter', 'alt="Top.Mail.Ru" /></div></noscript>', '');
        $updatedString = self::replace_between($updatedString, 'var _tmr = window._tmr || (window._tmr = []);', '})(document, window, "tmr-code");', '');

        // foreach ($search_values as $value) {
        //   $updatedString = preg_replace($value, '', $updatedString);
        // }
        // $updatedString = preg_replace("/(<!-- Yandex.Metrika counter -->)(.*?)(<!-- \/Yandex.Metrika counter -->)/", '', $updatedString);
        // $updatedString = preg_replace("/(<!-- Google Tag Manager -->)(.*?)(<!-- End Google Tag Manager -->)/", '', $updatedString);
        echo $updatedString;
      }, PHP_INT_MAX);


      add_action('wp_print_scripts', static function () use ( $exclude_scripts ) {

          if (!is_array($exclude_scripts)) {
              $exclude_scripts = array($exclude_scripts);
          }

          // $exclude_scripts = array_merge($default_exclude_scripts, $exclude_scripts);

          global $wp_scripts;
          foreach ($wp_scripts->queue as $script) {
              if (!in_array($script, $exclude_scripts)) {
                  wp_deregister_script($script);
                  wp_dequeue_script($script);
              }
          }
      }, 110);

    }

    public static function remove_styles($exclude_styles) {

      add_action('wp_print_styles', static function () use ( $exclude_styles ) {

          if (!is_array($exclude_styles)) {
              $exclude_styles = array($exclude_styles);
          }

          global $wp_styles;
          foreach ($wp_styles->queue as $style) {
              if (!in_array($style, $exclude_styles)) {
                  wp_deregister_style($style);
                  wp_dequeue_style($style);
              }
          }
      }, 100);

    }

    public static function register() {
  		$handler = new self();
  		add_action( 'idcrm_remove_scripts', array($handler, 'scripts_filter' ) );
  	}

  public static function scripts_filter($extended_scripts = array()) {

    $exclude_scripts = array(
        'jquery-core',
        'jquery',
        'jquery-migrate',
        'jquery-ui-sortable',
        'fullcalendar',
        'idcrmpro_calendar',
        'idcrmpro_ui',
        'apexcharts',
        'idcrmpro_product',
        'dropzone',
        'idcrm-mail',
        'idcrm_mail_manage',
        'select2-bootstrap',
        'toastr',
        'perfect-scrollbar',
        'sparkline',
        'icons-feather',
        'icons-custom',
        'waves',
        'wp_ajax_api',
        'wp_ajax',
        'wp_ajax_event_api',
        'wp_ajax_event_manage',
        'wp_ajax_timeline_manage',
        'wp_ajax_comment_manage',
        'wp_ajax_comment_api',
        'wp_ajax_note_manage',
        'wp_ajax_note_api',
        'wp_ajax_schedule_api',
        'wp_ajax_schedule_manage',
        'wp_ajax_company_manage',
        'wp_ajax_company_api',
        'wp_ajax_contact_manage',
        'wp_ajax_contact_api',
        'wp_ui_manage',
        'idcrmdeals_ajax_main',
        'idcrmdeals_deals_manage',
        'idcrmdeals_deals_api',
        'moment-lib',
        'moment-locale',
        'bootstrap-material-datetimepicker',
        'app-min',
        'app-init',
        'bootstrap-script',
        'bootstrap-table',
        'bootstrap-table-cookie',
        'bootstrap-table-locale',
        'sidebarmenu',
        'select2',
        'idcrmdeals_ui',
        'idcrmdeals_ui_manage',
        'idcrmdeals_statistics',
        'idcrmdeals_documents_manage',
        'idcrmdeals_documents_api',
        'wp_admin_deals_license',
        'idcrmtasks_ui',
        'idcrmtasks_ui_manage',
        'idcrmtasks_ajax_main',
        'idcrmtasks_tasks_manage',
        'idcrmtasks_tasks_api',
        'wp_admin_tasks_license',
        'wp_admin_tasks_manage',
        'wp_admin_tasks',
        'idcrmteam_ui',
        'idcrmteam_ui_manage',
        'idcrmteam_ajax_main',
        'idcrmteam_team_manage',
        'idcrmteam_team_api',
        'wp_admin_team_license',
        'wp_admin_team_manage',
        'wp_admin_team',
        'bootstrap-switch',
        'idcrmknowledge_ui',
       'idcrmknowledge_ui_manage',
       'idcrmknowledge_ajax_main',
       'idcrmknowledge_knowledge_manage',
       'idcrmknowledge_knowledge_api',
       'wp_admin_knowledge_license',
       'wp_admin_knowledge_manage',
       'wp_admin_knowledge',
       'idcrm_zadarma',
    );

    if (!is_array($extended_scripts)) {
      $extended_scripts = array($extended_scripts);
    }

    $exclude_scripts = array_merge($extended_scripts, $exclude_scripts);

		// foreach ( apply_filters( 'idcrm_remover', $exclude_scripts ) as $exclude_scripts ) {
    //   error_log($exclude_scripts);
		// 	self::remove_scripts($exclude_scripts);
		// }

    // apply_filters( 'idcrm_remover_scripts', self::remove_scripts($exclude_scripts) );
    self::remove_scripts($exclude_scripts);

	}

  public static function styles_filter() {

    $exclude_styles = array(
        'idcrm-contacts',
        'custom-style',
        'idcrm-contacts-public-pro',
        'bootstrap-material-datetimepicker',
        'bootstrap-table',
        'toastr',
        'select2',
        'select2-bootstrap',
        'fullcalendar',
        'idcrmdeals-styles',
        'monster-style',
        'apexcharts',
        'wp_admin_tasks_manage',
        'idcrmtasks-styles',
        'idcrmteam-styles',
        'bootstrap-switch',
        'idcrmknowledge-styles',
    );

    // foreach ( apply_filters( 'idcrm_remover', $exclude_scripts ) as $exclude_scripts ) {
    //   error_log($exclude_scripts);
    // 	self::remove_scripts($exclude_scripts);
    // }

    // apply_filters( 'idcrm_remover_styles', self::remove_styles($exclude_styles) );
    self::remove_styles($exclude_styles);

  }

}

}
