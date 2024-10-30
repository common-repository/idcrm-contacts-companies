<?php
/**
 * @link              https://idresult.ru
 * @since             1.0.0
 * @package           idcrm-contacts
 *
 * @wordpress-plugin
 * Plugin Name:       id:СRM Contacts & Companies
 * Description:       id:CRM module for contacts and companies.
 * Version:           2.2.3
 * Author:            id:Result
 * Author URI:        https://idresult.ru/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       idcrm-contacts-companies
 * Domain Path:       /languages
 */

namespace idcrm;

if ( ! defined( 'WPINC' ) ) {
	die;
}

require_once('includes/idcrm-contacts-company-cpt.php');
require_once('includes/idcrm-contacts-schedule-cpt.php');
require_once('includes/idcrm-contacts-user-cpt.php');
require_once('includes/idcrm-contacts-activator.php');
require_once('includes/idcrm-contacts-deactivator.php');
require_once('includes/idcrm-contacts-main.php');

use \idcrm\includes\idCRMContactsMain;

$plugin_version = '1.0.0';

if ( !function_exists('get_plugin_data') ) {
    require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
}

if ( function_exists('get_plugin_data') ) {
	$plugin_data = get_plugin_data( __FILE__ );
	$plugin_version = $plugin_data['Version'];
}

define( 'IDCRM_CONTACTS_VERSION', $plugin_version );
define( 'IDCRM_CONTACTS_PATH', plugin_dir_path( __FILE__ ) );
define( 'IDCRM_CONTACTS_URL', plugin_dir_url( __FILE__ ) );
define( 'IDCRM_CONTACTS_FILE', plugin_basename(__FILE__) );

class idCRM {
	public static $IDCRM_VERSION = \IDCRM_CONTACTS_VERSION;
	public static $IDCRM_PATH = \IDCRM_CONTACTS_PATH;
	public static $IDCRM_URL = \IDCRM_CONTACTS_URL;
	public static $IDCRM_FILE = \IDCRM_CONTACTS_FILE;
	public static function register() {
		register_activation_hook( __FILE__, array('\idcrm\includes\idCRMContactsCompanyCpt', 'custom_post_type' ) );
		register_activation_hook( __FILE__, array('\idcrm\includes\idCRMContactsCompanyCpt', 'create_first_company_status' ) );
		register_activation_hook( __FILE__, array('\idcrm\includes\idCRMContactsScheduleCpt', 'custom_post_type' ) );
		register_activation_hook( __FILE__, array('\idcrm\includes\idCRMContactsScheduleCpt', 'create_first_schedule_types' ) );
		register_activation_hook( __FILE__, array('\idcrm\includes\idCRMContactsUserCpt', 'custom_post_type' ) );
		register_activation_hook( __FILE__, array('\idcrm\includes\idCRMContactsUserCpt', 'create_first_contact_status' ) );
		register_activation_hook( __FILE__, array('\idcrm\includes\idCRMContactsActivator', 'activate') );
		register_deactivation_hook( __FILE__, array('\idcrm\includes\idCRMContactsDeactivator', 'deactivate') );
		idCRMContactsMain::register();
	}
}

idcrm::register();

?>
