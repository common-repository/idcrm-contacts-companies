<?php

namespace idcrm\includes;

// use idcrmpro\idCRMPro;
use idcrm\includes\actions\idCRMActionLanguage;
include_once ABSPATH . 'wp-admin/includes/plugin.php';

class idCRMSettings {

	const IDCRM_CACHE_KEY = 'idcrm_cache_key';
	const PLUGIN_SLUG = 'idcrm-contacts-companies-pro';
	const PLUGIN_SLUG_DEALS = 'idcrm-deals-documents';
	const UPDATE_SERVER = 'https://idresult.ru/idcrm-license/update/';

	const GET_LICENSE_PRO_URL = 'https://idresult.ru/product/id-crm-contacts-companies-pro/';
	const GET_LICENSE_DEALS_URL = 'https://idresult.ru/product/id-crm-contacts-companies-pro/';

	public static function register() {
		$handler = new self();

		// add_action( 'init', [ $handler, 'licence_notice_or_enable_features'] );
		add_action( 'admin_menu', [ $handler, 'add_idcrm_settings_page' ], 90 );
		// add_action( 'admin_init', [ $handler, 'idcrm_settings_init'] );
		// add_action( 'admin_init', [ $handler, 'idcrm_mailbox_settings_init'] );

		// if ( idCRMPro::IDCRMPRO_PRO_KEY_ACTIVATED && self::get_days_left( idCRMPro::IDCRMPRO_PRO_KEY_NAME ) > 0 ) {
		// 	add_filter( 'pre_set_site_transient_update_plugins', [ $handler, 'gb_check_for_plugin_update'] );
		// 	add_filter( 'plugins_api', [ $handler, 'gb_plugin_api_call'], 10, 3 );
		// }
		//
		// if ( idCRMPro::IDCRMPRO_DEALS_KEY_ACTIVATED && self::get_days_left( idCRMPro::IDCRMPRO_DEALS_KEY_NAME ) > 0 ) {
		// 	add_filter( 'pre_set_site_transient_update_plugins', [ $handler, 'gb_check_for_plugin_update_deals'] );
		// 	add_filter( 'plugins_api', [ $handler, 'gb_plugin_api_call_deals'], 10, 3 );
		// }
	}

	private static function get_days_left($key_name) {
		$expire = get_option( $key_name . '_expire' );
		$days_left = $expire > strtotime('today') ? floor(($expire - strtotime('today'))/60/60/24) : 0;

		return $days_left;
	}

	// update C&C Pro
	public function gb_check_for_plugin_update( $checked_data ) {

	    if ( empty( $checked_data->checked ) ) {
	        return $checked_data;
	    }

	    $request_args = [
	        'slug'    => self::PLUGIN_SLUG,
	        'version' => $checked_data->checked[ self::PLUGIN_SLUG . '/' . self::PLUGIN_SLUG . '.php' ],
	    ];

	    $request_string = self::gb_prepare_request( 'basic_check', $request_args );
	    $raw_response = wp_remote_post( self::UPDATE_SERVER, $request_string );

	    if ( ! is_wp_error( $raw_response ) && ( (int) $raw_response['response']['code'] === 200 ) ) {
	        $response = unserialize( $raw_response['body'] );
	    }

	    if ( is_object( $response ) && ! empty( $response ) ) { // Feed the update data into WP updater
	        $checked_data->response[ self::PLUGIN_SLUG . '/' . self::PLUGIN_SLUG . '.php' ] = $response;
	    }

	    return $checked_data;
	}

	public function gb_plugin_api_call( $def, $action, $args ) {

	    if ( $action !== 'plugin_information' ) {
	        return false;
	    }

	    if ( (string) $args->slug !== (string) self::PLUGIN_SLUG ) {
	        return $def;
	    }

	    $plugin_info     = get_site_transient( 'update_plugins' );
	    $current_version = $plugin_info->checked[ self::PLUGIN_SLUG . '/' . self::PLUGIN_SLUG . '.php' ];
	    $args->version   = $current_version;

	    $request_string = self::gb_prepare_request( $action, $args );

	    $request = wp_remote_post( self::UPDATE_SERVER, $request_string );

	    if ( is_wp_error( $request ) ) {
	        $res = new WP_Error( 'plugins_api_failed', __( 'An Unexpected HTTP Error occurred during the API request.</p> <p><a href="?" onclick="document.location.reload(); return false;">Try again</a>' ), $request->get_error_message() );
	    } else {
	        $res = unserialize( $request['body'] );

	        if ( $res === false ) {
	            $res = new WP_Error( 'plugins_api_failed', __( 'An unknown error occurred' ), $request['body'] );
	        }
	    }

	    return $res;
	}

	public function gb_prepare_request( $action, $args ) {
	    global $wp_version;

	    return [
	        'body'       => [
	            'action'  => $action,
	            'request' => serialize( $args ),
	            'api-key' => md5( get_bloginfo( 'url' ) ),
	        ],
	        'user-agent' => 'WordPress/' . $wp_version . '; ' . get_bloginfo( 'url' ),
	    ];
	}

	//update D&D
	public function gb_check_for_plugin_update_deals( $checked_data ) {

	    if ( empty( $checked_data->checked ) ) {
	        return $checked_data;
	    }

	    $request_args = [
	        'slug'    => self::PLUGIN_SLUG_DEALS,
	        'version' => $checked_data->checked[ self::PLUGIN_SLUG_DEALS . '/' . self::PLUGIN_SLUG_DEALS . '.php' ],
	    ];

	    $request_string = self::gb_prepare_request_deals( 'basic_check', $request_args );
	    $raw_response = wp_remote_post( self::UPDATE_SERVER, $request_string );

	    if ( ! is_wp_error( $raw_response ) && ( (int) $raw_response['response']['code'] === 200 ) ) {
	        $response = unserialize( $raw_response['body'] );
	    }

	    if ( is_object( $response ) && ! empty( $response ) ) { // Feed the update data into WP updater
	        $checked_data->response[ self::PLUGIN_SLUG_DEALS . '/' . self::PLUGIN_SLUG_DEALS . '.php' ] = $response;
	    }

	    return $checked_data;
	}

	public function gb_plugin_api_call_deals( $def, $action, $args ) {

	    if ( $action !== 'plugin_information' ) {
	        return false;
	    }

	    if ( (string) $args->slug !== (string) self::PLUGIN_SLUG_DEALS ) {
	        return $def;
	    }

	    $plugin_info     = get_site_transient( 'update_plugins' );
	    $current_version = $plugin_info->checked[ self::PLUGIN_SLUG_DEALS . '/' . self::PLUGIN_SLUG_DEALS . '.php' ];
	    $args->version   = $current_version;

	    $request_string = self::gb_prepare_request_deals( $action, $args );

	    $request = wp_remote_post( self::UPDATE_SERVER, $request_string );

	    if ( is_wp_error( $request ) ) {
	        $res = new WP_Error( 'plugins_api_failed', __( 'An Unexpected HTTP Error occurred during the API request.</p> <p><a href="?" onclick="document.location.reload(); return false;">Try again</a>' ), $request->get_error_message() );
	    } else {
	        $res = unserialize( $request['body'] );

	        if ( $res === false ) {
	            $res = new WP_Error( 'plugins_api_failed', __( 'An unknown error occurred' ), $request['body'] );
	        }
	    }

	    return $res;
	}

	public function gb_prepare_request_deals( $action, $args ) {
	    global $wp_version;

	    return [
	        'body'       => [
	            'action'  => $action,
	            'request' => serialize( $args ),
	            'api-key' => md5( get_bloginfo( 'url' ) ),
	        ],
	        'user-agent' => 'WordPress/' . $wp_version . '; ' . get_bloginfo( 'url' ),
	    ];
	}

	public function idcrm_activate_page($links) {
		$links[] = '<a href="admin.php?page=idcrm_settings&tab=lincense">' . esc_html__('Activate License', idCRMProLanguage::TEXTDOMAIN) . '</a>';
		return $links;
	}

	public function idcrm_settings_page($links) {
		$links[] = '<a href="admin.php?page=idcrm_settings">' . esc_html__('Settings', idCRMProLanguage::TEXTDOMAIN) . '</a>';
		return $links;
	}

	public function licence_notice_or_enable_features() {
		$handler = new self();

		if ( !is_plugin_active( 'idcrm-contacts-companies/idcrm-contacts.php' ) ) {
			add_action( 'admin_notices', [ $handler, 'free_plugin_notice'] );
		}

	  if ( idCRMPro::IDCRMPRO_PRO_KEY_ACTIVATED ) {
	    //require_plugin_parts();
			add_filter( 'plugin_action_links_' . idCRMPro::IDCRMPRO_FILE, array($handler, 'idcrm_settings_page') );
	  } else {
			add_filter( 'plugin_action_links_' . idCRMPro::IDCRMPRO_FILE, array($handler, 'idcrm_activate_page') );
	  }
	}

	function free_plugin_notice() {
		$class = 'notice notice-error';
		$message = esc_html__( '"id:СRM Contacts & Companies Pro" requires "id:СRM Contacts & Companies Free" plugin to be intalled and activated.', idCRMProLanguage::TEXTDOMAIN );
		$admin_url = get_admin_url() . 'plugin-install.php?s=idresult&tab=search&type=term';
		$link_message = esc_html__( 'Click here to find "id:CRM Contacts & Companies" in Wordpress repository', idCRMProLanguage::TEXTDOMAIN );

		printf( '<div class="%1$s"><p>%2$s <a href="%3$s"><strong>%4$s</strong></a></p></div>', esc_attr( $class ), esc_html( $message ), esc_url($admin_url), esc_html( $link_message ) );
	}

	public function register_idcrm_plugin_settings() {
		add_option('idcrm_settings');
		register_setting( 'idcrm_options_group', 'idcrm_settings', 'idcrm_callback' );
	}

	public function add_idcrm_settings_page() {
		$handler = new self();

		add_submenu_page(
			'idcrm-contacts',
			'<span class="dashicons dashicons-admin-generic" style="font-size: 17px"></span> ' . esc_html__( 'Settings', idCRMActionLanguage::TEXTDOMAIN ),
			'<span class="dashicons dashicons-admin-generic" style="font-size: 17px"></span> ' . esc_html__( 'Settings', idCRMActionLanguage::TEXTDOMAIN ),
			'edit_user_contacts',
			'idcrm_settings',
			[ $handler, 'do_idcrm_settings_page' ],
			100
		);
	}

	public function do_idcrm_settings_page() {
		if ( !current_user_can( 'manage_options' ) )  {
			wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', idCRMActionLanguage::TEXTDOMAIN ) );
		} else {

			$default_tab = null;
  		$tab = isset($_GET['tab']) ? $_GET['tab'] : $default_tab;

			$options = get_option( 'idcrm_settings' );
			if ( isset($options) && !empty ($options)) {
				//print_r($options);
			}

			$newsletter_plugin = is_plugin_active( 'newsletter/plugin.php' ) ? 'yes' : '';
			?>

			<div class="wrap">

		    <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

				<div class="plugins-activated" id="plugins-activated" style="display:none;" data-newsletter="<?php echo esc_attr($newsletter_plugin); ?>"></div>

				<?php settings_errors(); ?>

		    <nav class="nav-tab-wrapper" id="settings-tabs">
		      <a id="main" href="?page=idcrm_settings" class="nav-tab <?php if($tab===null):?>nav-tab-active<?php endif; ?>"><?php esc_html_e('Main Settings', idCRMActionLanguage::TEXTDOMAIN); ?></a>
		      <a id="license" href="?page=idcrm_settings&tab=lincense" class="nav-tab <?php if($tab==='lincense'):?>nav-tab-active<?php endif; ?>"><?php esc_html_e('License', idCRMActionLanguage::TEXTDOMAIN); ?></a>
		    </nav>

		    <div class="tab-content">
					<div id="custom-settings-block" class="custom-settings-block"></div>

					<p class="submit"><input type="submit" name="submit" id="submit-settings" class="button button-primary" value="<?php esc_html_e( 'Save', idCRMActionLanguage::TEXTDOMAIN ); ?>"></p>

					<script>
						//idcrm_settings = <?php echo json_encode(unserialize(get_option( 'idcrm_settings' ) ?: 'a:0:{}')); ?>;
					</script>
		    </div>
		  </div>


		<?php }
	}

	public function idcrm_settings_init() {
		$handler = new self();
    add_settings_section(
        'idcrm_setting_section',
        esc_html__( 'License information', idCRMActionLanguage::TEXTDOMAIN ),
        [ $handler, 'my_setting_section_callback_function' ],
        'idcrm_settings'
    );

		add_settings_field(
		   idCRMPro::IDCRMPRO_PRO_KEY_NAME,
		   esc_html__( 'id:СRM Contacts & Companies Pro', idCRMActionLanguage::TEXTDOMAIN ),
		   [ $handler, 'idcrm_pro_markup' ],
		   'idcrm_settings',
		   'idcrm_setting_section',
			 array(
				'id' => idCRMPro::IDCRMPRO_PRO_KEY_NAME,
				'option_name' => 'my_option'
			)
		);

		if ( is_plugin_active( 'idcrm-deals-documents/idcrm-deals-documents.php' ) ) {
			add_settings_field(
			   idCRMPro::IDCRMPRO_DEALS_KEY_NAME,
			   esc_html__( 'id:CRM Deals & Documents', idCRMActionLanguage::TEXTDOMAIN ),
			   [ $handler, 'idcrm_deals_markup' ],
			   'idcrm_settings',
			   'idcrm_setting_section',
				 array(
					'id' => idCRMPro::IDCRMPRO_DEALS_KEY_NAME,
					'option_name' => 'my_option'
				)
			);
		}

		register_setting( 'idcrm_settings', idCRMPro::IDCRMPRO_PRO_KEY_NAME, [ $handler, 'key_options_validate' ] );
		register_setting( 'idcrm_settings', idCRMPro::IDCRMPRO_DEALS_KEY_NAME, [ $handler, 'key_options_validate' ] );
	}

	public function idcrm_mailbox_settings_init() {
		$handler = new self();

		add_settings_section(
        'idcrm_mailbox_setting_section',
        esc_html__( 'Mailbox settings', idCRMActionLanguage::TEXTDOMAIN ),
        [ $handler, 'my_setting_section_callback_function' ],
        'idcrm_mailbox_settings'
    );

		add_settings_field(
		   'idcrm_mailbox_field',
		   esc_html__( 'Mailbox messages count per page', idCRMActionLanguage::TEXTDOMAIN ),
		   [ $handler, 'idcrm_mailbox_settings_markup' ],
		   'idcrm_mailbox_settings',
		   'idcrm_mailbox_setting_section'
		);

		// add_option('idcrm_mailbox');
		register_setting( 'idcrm_mailbox_settings', 'idcrm_mailbox', [ $handler, 'key_options_validate' ] );
	}

	public function key_options_validate( $input ) {
		$output = trim(sanitize_text_field($input));
    return $output;
	}

	public function idcrm_mailbox_settings_markup() {
		$count = [10, 20, 30, 50, 100];
		$idcrm_mailbox_settings = get_option( 'idcrm_mailbox' ); ?>
		<select name="idcrm_mailbox">
			<?php foreach ($count as $counter) { ?>
				<option value="<?php echo $counter ?>" <?php selected($counter, $idcrm_mailbox_settings); ?>><?php echo $counter ?></option>
			<?php } ?>
		</select>
	<?php }

	public function idcrm_pro_markup( $val ) {

		$id = $val['id'];
		$readonly = idCRMPro::IDCRMPRO_PRO_KEY_ACTIVATED ? "readonly" : "";
		$disabled = idCRMPro::IDCRMPRO_PRO_KEY_ACTIVATED ? "" : "disabled";
		$expire = get_option( $id . '_expire' );
		$expire_date = isset($expire) && $expire !== 0 ? date("d.m.Y", $expire) : date("d.m.Y", 1924945358);
    ?>
		<div class="license-settings">
			<input <?php echo esc_attr($readonly); ?> placeholder="<?php esc_html_e('Add your license key here', idCRMProLanguage::TEXTDOMAIN); ?>" type="text" name="<?php echo esc_attr($id); ?>" id="<?php echo esc_attr($id); ?>" value="<?php echo get_option( $id ); ?>" class="regular-text" autocomplete="off">

			<span class="license-spinner <?php echo esc_attr($id); ?>" role="status">
					<svg class="license-spinner-inner" viewBox="0 0 40 40">
							<circle class="path" cx="20" cy="20" r="15" fill="none" stroke-width="5"></circle>
					</svg>
			</span>

			<button type="button" data-server="<?php echo esc_attr(idCRMPro::IDCRMPRO_AUTH_METHOD); ?>" data-action="<?php echo esc_attr($id); ?>" id="idcrm_pro_activate_plugin" name="idcrm_pro_activate_plugin" class="button" disabled><?php esc_html_e('Activate', idCRMProLanguage::TEXTDOMAIN); ?></button>
			<button type="button" id="idcrm_pro_delete_license" name="idcrm_pro_delete_license" data-action="<?php echo esc_attr($id); ?>" class="button idcrm_delete_license" <?php echo esc_attr($disabled); ?>><?php esc_html_e('Delete', idCRMProLanguage::TEXTDOMAIN); ?></button>

			<?php if ( idCRMPro::IDCRMPRO_PRO_KEY_ACTIVATED ) { ?>
				<div class="activation-notice <?php echo esc_attr($id); ?>"><?php esc_html_e( 'Your support of id:СRM Contacts & Companies Pro is active till:', idCRMProLanguage::TEXTDOMAIN ); ?> <strong><?php echo esc_html($expire_date); ?>.</strong>
					<?php if (self::get_days_left( idCRMPro::IDCRMPRO_PRO_KEY_NAME ) > 0) { ?>
						<p>
							<strong><?php echo esc_html(self::get_days_left( idCRMPro::IDCRMPRO_PRO_KEY_NAME )); ?></strong>
							<?php esc_html_e( 'days left', idCRMProLanguage::TEXTDOMAIN ); ?>
						</p>
					<?php } else { ?>
						<p style="color:red;">
							<?php esc_html_e( 'Support expired, please update your license to get updates!', idCRMProLanguage::TEXTDOMAIN ); ?>
							<a href="<?php echo esc_url(self::GET_LICENSE_PRO_URL); ?>" target="_blank"><?php esc_html_e('Get License', idCRMProLanguage::TEXTDOMAIN); ?></a>
						</p>
					<?php } ?>
				</div>
			<?php } else { ?>
				<div class="activation-notice <?php echo esc_attr($id); ?> activation-error"><?php esc_html_e( 'Please activate your license key to unlock id:СRM Contacts & Companies Pro, you can get it here:', idCRMProLanguage::TEXTDOMAIN );?> <a href="<?php echo esc_url(self::GET_LICENSE_PRO_URL); ?>" target="_blank"><?php esc_html_e('Get License', idCRMProLanguage::TEXTDOMAIN); ?></a></div>
			<?php } ?>
		</div>

		<div id="license-responce-<?php echo esc_attr($id); ?>"></div>
    <?php
	}

	public function idcrm_deals_markup( $val ) {
		$id = $val['id'];
		$readonly = idCRMPro::IDCRMPRO_DEALS_KEY_ACTIVATED ? "readonly" : "";
		$disabled = idCRMPro::IDCRMPRO_DEALS_KEY_ACTIVATED ? "" : "disabled";
		$expire = get_option( $id . '_expire' );
		$expire_date = isset($expire) && $expire !== 0 ? date("d.m.Y", $expire) : date("d.m.Y", 1924945358);
		?>
		<div class="license-settings">
			<input <?php echo esc_attr($readonly); ?> placeholder="<?php esc_html_e('Add your license key here', idCRMProLanguage::TEXTDOMAIN); ?>" type="text" name="<?php echo esc_attr($id); ?>" id="<?php echo esc_attr($id); ?>" value="<?php echo get_option( $id ); ?>" class="regular-text" autocomplete="off">

			<span class="license-spinner <?php echo esc_attr($id); ?>" role="status">
					<svg class="license-spinner-inner" viewBox="0 0 40 40">
							<circle class="path" cx="20" cy="20" r="15" fill="none" stroke-width="5"></circle>
					</svg>
			</span>

			<button type="button" data-server="<?php echo esc_attr(idCRMPro::IDCRMPRO_AUTH_METHOD); ?>" data-action="<?php echo esc_attr($id); ?>" id="idcrm_deals_activate_plugin" name="idcrm_deals_activate_plugin" class="button" disabled><?php esc_html_e('Activate', idCRMProLanguage::TEXTDOMAIN); ?></button>
			<button type="button" id="idcrm_deals_delete_license" name="idcrm_deals_delete_license" data-action="<?php echo esc_attr($id); ?>" class="button idcrm_delete_license" <?php echo esc_attr($disabled); ?>><?php esc_html_e('Delete', idCRMProLanguage::TEXTDOMAIN); ?></button>

			<?php if ( idCRMPro::IDCRMPRO_DEALS_KEY_ACTIVATED ) { ?>
				<div class="activation-notice <?php echo esc_attr($id); ?>"><?php esc_html_e( 'Your support of id:СRM Deals & Documents is active till:', idCRMProLanguage::TEXTDOMAIN ); ?> <strong><?php echo esc_html($expire_date); ?>.</strong>
					<?php if (self::get_days_left( idCRMPro::IDCRMPRO_DEALS_KEY_NAME ) > 0) { ?>
						<p>
							<strong><?php echo esc_html(self::get_days_left( idCRMPro::IDCRMPRO_DEALS_KEY_NAME )); ?></strong>
							<?php esc_html_e( 'days left', idCRMProLanguage::TEXTDOMAIN ); ?>
						</p>
					<?php } else { ?>
						<p style="color:red;">
							<?php esc_html_e( 'Support expired, please update your license to get updates!', idCRMProLanguage::TEXTDOMAIN ); ?>
							<a href="<?php echo esc_url(self::GET_LICENSE_PRO_URL); ?>" target="_blank"><?php esc_html_e('Get License', idCRMProLanguage::TEXTDOMAIN); ?></a>
						</p>
					<?php } ?>
				</div>
			<?php } else { ?>
				<div class="activation-notice <?php echo esc_attr($id); ?> activation-error"><?php esc_html_e( 'Please activate your license key to unlock id:СRM Deals & Documents, you can get it here:', idCRMActionLanguage::TEXTDOMAIN ); ?> <a href="<?php echo esc_url(self::GET_LICENSE_DEALS_URL); ?>" target="_blank"><?php esc_html_e('Get License', idCRMActionLanguage::TEXTDOMAIN); ?></a></div>
			<?php } ?>
		</div>

		<div id="license-responce-<?php echo esc_attr($id); ?>"></div>
		<?php
	}


	public function my_setting_section_callback_function() {

	}

	public function main_settings() { ?>
		<div class="custom-email-block">
			<form method="post" action="options.php">
					<?php settings_fields( 'idcrm_mailbox_settings' ); ?>
					<?php do_settings_sections( 'idcrm_mailbox_settings' ); ?>
					<?php submit_button(); ?>
			</form>
			<span><?php esc_html_e( 'To add an e-mail address please open User Profile', idCRMActionLanguage::TEXTDOMAIN ); ?> - <a href="<?php echo get_admin_url() . "profile.php#add_email"?>"><?php esc_html_e( 'Add mailbox', idCRMActionLanguage::TEXTDOMAIN ); ?></a></span>
		</div>
	<?php }

	public function lincense_settings() { ?>
		<div class="custom-email-block">
			<form method="post" action="options.php">
					<?php settings_fields( 'idcrm_settings' ); ?>
					<?php do_settings_sections( 'idcrm_settings' ); ?>
					<?php submit_button(); ?>
				</form>
		</div>
	<?php }
}

?>
