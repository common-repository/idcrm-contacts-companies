<?php

namespace idcrm\admin;

use idcrm\idCRM;
use idcrm\includes\actions\idCRMActionLanguage;

if ( ! class_exists( '\idcrm\includes\api\idCRMAdminUserManage' ) ) {
    class idCRMAdminUserManage {

      const NONCE = 'idcrm-admin-settings';
      const SAVE_SETTINGS = 'idcrm_ajax_save_settings';
      const GET_LICENSE = 'idcrm_ajax_get_license';
      const SAVE_LICENSE = 'idcrm_ajax_save_license';
      const DELETE_LICENSE = 'idcrm_ajax_delete_license';
      const GET_IMAGE = 'idcrm_get_image';
      const LICENSE_SERVER = 'https://weather-pwa.ru/idcrm-license/';

        public function register_script( $hook ) {
            wp_enqueue_style( 'wp_admin_user_manage', idCRM::$IDCRM_URL . 'admin/css/admin-user.css', array(), IDCRM_CONTACTS_VERSION);
            wp_enqueue_style( 'bootstrap-material-datetimepicker', idCRM::$IDCRM_URL . 'templates/assets/libs/bootstrap-material-datetimepicker/css/bootstrap-material-datetimepicker.css', array(), IDCRM_CONTACTS_VERSION );

            // wp_enqueue_style( 'bootstrap-style', idCRM::$IDCRM_URL . 'templates/dist/css/bootstrap.min.css' );
            // wp_enqueue_style( 'bootstrap-table', idCRM::$IDCRM_URL . 'templates/dist/css/bootstrap-table.min.css' );

            if ( $hook == 'profile.php' || $hook == 'user-edit.php' ) {
                add_thickbox();
                wp_enqueue_script( 'media-upload' );
                wp_enqueue_media();
            }

            wp_register_script('wp_admin_user', idCRM::$IDCRM_URL . 'admin/js/admin-user.js', array( 'jquery' ), IDCRM_CONTACTS_VERSION, true);
            wp_localize_script('wp_admin_user', 'idcrm_settings', $this->get_settings_data());
            wp_localize_script('wp_admin_user', 'idcrm_admin_data', $this->get_admin_data());
            wp_enqueue_script('wp_admin_user');

            wp_enqueue_media();

            wp_register_script('wp_admin_user_manage', idCRM::$IDCRM_URL . 'admin/js/admin-user-manage.js', array( 'jquery' ), IDCRM_CONTACTS_VERSION);
            wp_enqueue_script('wp_admin_user_manage');

            wp_register_script( 'moment-lib', idCRM::$IDCRM_URL . 'templates/assets/libs/moment/moment.js', array( 'jquery' ), IDCRM_CONTACTS_VERSION, true);
            wp_enqueue_script( 'moment-lib' );
            wp_register_script( 'moment-locale', idCRM::$IDCRM_URL . 'templates/assets/libs/moment/locale/' . substr(get_locale(), 0, 2) . '.js', array( 'jquery' ), IDCRM_CONTACTS_VERSION, true);
            wp_enqueue_script( 'moment-locale' );
      			wp_register_script( 'bootstrap-material-datetimepicker', idCRM::$IDCRM_URL . 'templates/assets/libs/bootstrap-material-datetimepicker/js/bootstrap-material-datetimepicker-custom.js', array( 'jquery' ), IDCRM_CONTACTS_VERSION, true);
            wp_localize_script('bootstrap-material-datetimepicker', 'wp_datetimepicker_data', $this->get_locale_data());
            wp_enqueue_script( 'bootstrap-material-datetimepicker' );

      			// wp_enqueue_script( 'bootstrap-script', idCRM::$IDCRM_URL . 'templates/dist/js/bootstrap.bundle.min.js', array( 'jquery' ), false, true );
      			// wp_enqueue_script( 'bootstrap-table', idCRM::$IDCRM_URL . 'templates/dist/js/bootstrap-table.min.js', array( 'jquery' ), false, true );
        }

        private function get_locale_data() {
            return array(
                'cancel_text' => esc_html__( 'Cancel', idCRMActionLanguage::TEXTDOMAIN ),
                'ok_text' => esc_html__( 'Ok', idCRMActionLanguage::TEXTDOMAIN ),
                'clear_text' => esc_html__( 'Clear', idCRMActionLanguage::TEXTDOMAIN ),
                'now_text' => esc_html__( 'Now', idCRMActionLanguage::TEXTDOMAIN ),
                'select_image' => esc_html__( 'Select an image', idCRMActionLanguage::TEXTDOMAIN ),
                'locale' => substr(get_locale(), 0, 2),
            );
        }

        private function get_settings_data() {
          $settings_data = [
            'markup' => [],
          ];

          $idcrm_settings = unserialize( get_option( 'idcrm_settings' ) && !is_array(get_option( 'idcrm_settings' )) ? get_option( 'idcrm_settings' ) : 'a:0:{}' );

          foreach ($idcrm_settings as $key => $setting) {
            $settings_data[$key] = $setting;
          }

          return $settings_data;
        }

        private function get_admin_data() {
          return array(
            'save_settings' => self::SAVE_SETTINGS,
            'get_license' => self::GET_LICENSE,
            'save_license' => self::SAVE_LICENSE,
            'delete_license' => self::DELETE_LICENSE,
            'get_image' => self::GET_IMAGE,
            'nonce' => wp_create_nonce(self::NONCE),
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'all_users' => self::get_all_users(),
            'all_pages' => self::get_all_pages(),
            'activate' => esc_html__( 'Activate', idCRMActionLanguage::TEXTDOMAIN ),
      			'delete' => esc_html__( 'Delete', idCRMActionLanguage::TEXTDOMAIN ),
            'license_information' => esc_html__( 'License information', idCRMActionLanguage::TEXTDOMAIN ),
      			'add_license_placeholder' => esc_html__('Add your license key here', idCRMActionLanguage::TEXTDOMAIN),
            'license_key_mismatch' => esc_html__( 'License Key has wrong format', idCRMActionLanguage::TEXTDOMAIN ),
      			'license_domain_mismatch' => esc_html__( 'Domain name is wrong', idCRMActionLanguage::TEXTDOMAIN ),
      			'license_key_notexist' => esc_html__( 'License Key is wrong or does not exist', idCRMActionLanguage::TEXTDOMAIN ),
      			'license_registered' => esc_html__( 'License Key is successfully registered', idCRMActionLanguage::TEXTDOMAIN ),
      			'license_server_error' => esc_html__( 'License server error, try later or contact support', idCRMActionLanguage::TEXTDOMAIN ),
      			'delete_license_confirm' => esc_html__( 'Are you sure you want to delete the license? You can add it again at any time.', idCRMActionLanguage::TEXTDOMAIN ),
      			'delete_license_error' => esc_html__( 'Error deleting license, please contact support', idCRMActionLanguage::TEXTDOMAIN ),
      			'license_information' => esc_html__( 'License information', idCRMActionLanguage::TEXTDOMAIN ),
            // 'add_license_placeholder' => esc_html__('Add your license key here', idCRMActionLanguage::TEXTDOMAIN),
            'default_cf7_user' => esc_html__('Contacts created with Contact Form 7 will be assigned with this user', idCRMActionLanguage::TEXTDOMAIN),
            'default_start_page' => esc_html__('Default start page', idCRMActionLanguage::TEXTDOMAIN),
      			'choose_user' => esc_html__('Choose user', idCRMActionLanguage::TEXTDOMAIN),

            'please_activate_pro' => esc_html__( 'Please activate your license key to unlock id:СRM Contacts & Companies Pro, you can get it here:', idCRMActionLanguage::TEXTDOMAIN ),
            'please_activate_deals' => esc_html__( 'Please activate your license key to unlock id:СRM Contacts & Companies Pro, you can get it here:', idCRMActionLanguage::TEXTDOMAIN ),
            'please_activate_tasks' => esc_html__( 'Please activate your license key to unlock id:СRM Contacts & Companies Pro, you can get it here:', idCRMActionLanguage::TEXTDOMAIN ),
            'get_license_text' => esc_html__('Get License', idCRMActionLanguage::TEXTDOMAIN),
            'support_pro_till' =>  esc_html__( 'Your support of id:СRM Contacts & Companies Pro is active till:', idCRMActionLanguage::TEXTDOMAIN ),
            'support_deals_till' =>  esc_html__( 'Your support of id:СRM Contacts & Companies Pro is active till:', idCRMActionLanguage::TEXTDOMAIN ),
            'support_tasks_till' =>  esc_html__( 'Your support of id:СRM Contacts & Companies Pro is active till:', idCRMActionLanguage::TEXTDOMAIN ),
            'support_expired' =>  esc_html__( 'Support expired, please update your license to get updates!', idCRMActionLanguage::TEXTDOMAIN ),
            'days_left' =>  esc_html__( 'days left', idCRMActionLanguage::TEXTDOMAIN ),
            // 'license_key_mismatch' => esc_html__( 'License Key has wrong format', idCRMProLanguage::TEXTDOMAIN ),
            // 'license_domain_mismatch' => esc_html__( 'Domain name is wrong', idCRMProLanguage::TEXTDOMAIN ),
            // 'license_key_notexist' => esc_html__( 'License Key is wrong or does not exist', idCRMProLanguage::TEXTDOMAIN ),
            // 'license_registered' => esc_html__( 'License Key is successfully registered', idCRMProLanguage::TEXTDOMAIN ),
            // 'license_server_error' => esc_html__( 'License server error, try later or contact support', idCRMProLanguage::TEXTDOMAIN ),
            // 'delete_license_confirm' => esc_html__( 'Are you sure you want to delete the license? You can add it again at any time.', idCRMProLanguage::TEXTDOMAIN ),
            // 'delete_license_error' => esc_html__( 'Error deleting license, please contact support', idCRMProLanguage::TEXTDOMAIN ),
            // 'license_information' => esc_html__( 'License information', idCRMProLanguage::TEXTDOMAIN ),
            // 'add_license_placeholder' => esc_html__('Add your license key here', idCRMProLanguage::TEXTDOMAIN),
          );
        }

        public static function register() {
            $handler = new self();
            add_action( 'show_user_profile', array($handler, 'customUserProfileFields'), 10, 1 );
            add_action( 'edit_user_profile', array($handler, 'customUserProfileFields'), 10, 1 );
            add_action( 'personal_options_update', array($handler, 'idcrmContactsSaveLocalAvatarFields') );
            add_action( 'edit_user_profile_update', array($handler, 'idcrmContactsSaveLocalAvatarFields') );
            add_filter( 'get_avatar_url', array($handler, 'idcrmContactsGetAvatarUrl'), 10, 3 );
            add_action( 'admin_enqueue_scripts', array($handler, 'register_script') );

            add_action( 'wp_ajax_' . self::SAVE_SETTINGS, [$handler, 'idcrm_ajax_save_settings'] );
            add_action( 'wp_ajax_nopriv_' . self::SAVE_SETTINGS, [$handler, 'idcrm_ajax_save_settings'] );

            add_action( 'wp_ajax_' . self::GET_LICENSE, [$handler, 'idcrm_ajax_get_license'] );
            add_action( 'wp_ajax_nopriv_' . self::GET_LICENSE, [$handler, 'idcrm_ajax_get_license'] );

            add_action( 'wp_ajax_' . self::SAVE_LICENSE, [$handler, 'idcrm_ajax_save_license'] );
            add_action( 'wp_ajax_nopriv_' . self::SAVE_LICENSE, [$handler, 'idcrm_ajax_save_license'] );

            add_action( 'wp_ajax_' . self::DELETE_LICENSE, [$handler, 'idcrm_ajax_delete_license'] );
            add_action( 'wp_ajax_nopriv_' . self::DELETE_LICENSE, [$handler, 'idcrm_ajax_delete_license'] );

            add_action( 'wp_ajax_' . self::GET_IMAGE, [$handler, 'idcrm_get_image'] );
            // add_action( 'wp_ajax_nopriv_' . self::GET_IMAGE, [$handler, 'idcrm_get_image'] );
        }

        private static function get_all_users() {
          $all_users = [];

          $users = get_users( array(
          	// 'capability'   => 'edit_user_contacts',
          ) );

          if ( ! empty( $users ) ) {
            foreach ( $users as $user ) {
              $all_users[] = [$user->ID, $user->display_name];
            }
          }

          return $all_users;
        }

        private static function get_all_pages() {
          include_once ABSPATH . 'wp-admin/includes/plugin.php';
          $all_pages = [];

          $all_pages[] = ['crm-contacts', esc_html__( 'Contacts', idCRMActionLanguage::TEXTDOMAIN )];
          $all_pages[] = ['crm-companies', esc_html__( 'Companies', idCRMActionLanguage::TEXTDOMAIN )];

          if ( is_plugin_active( 'idcrm-contacts-companies-pro/idcrm-contacts-companies-pro.php' ) ) {
            $all_pages[] = ['crm-dashboard', esc_html__( 'Dashboard', idCRMActionLanguage::TEXTDOMAIN )];
            $all_pages[] = ['mailbox', esc_html__( 'Mailbox', idCRMActionLanguage::TEXTDOMAIN )];
          }

          if ( is_plugin_active( 'idcrm-deals-documents/idcrm-deals-documents.php' ) ) {
            $all_pages[] = ['deals', esc_html__( 'Deals', idCRMActionLanguage::TEXTDOMAIN )];
            $all_pages[] = ['documents', esc_html__( 'Documents', idCRMActionLanguage::TEXTDOMAIN )];
          }

          if ( is_plugin_active( 'idcrm-projects-tasks/idcrm-projects-tasks.php' ) ) {
            $all_pages[] = ['tasks', esc_html__( 'Tasks', idCRMActionLanguage::TEXTDOMAIN )];
            $all_pages[] = ['crm-projects', esc_html__( 'Projects', idCRMActionLanguage::TEXTDOMAIN )];
          }

          if ( is_plugin_active( 'idcrm-team-motivation/idcrm-team-motivation.php' ) ) {
            $all_pages[] = ['crm-team', esc_html__( 'Team', idCRMActionLanguage::TEXTDOMAIN )];
            $all_pages[] = ['?crm-departments', esc_html__( 'Departments', idCRMActionLanguage::TEXTDOMAIN )];
            $all_pages[] = ['?crm-time', esc_html__( 'Time Reports', idCRMActionLanguage::TEXTDOMAIN )];
          }

          return $all_pages;
        }

        public static function idcrm_ajax_delete_license() {
          check_ajax_referer(self::NONCE);

          if ( array_key_exists( 'data_action', $_POST ) ) {
            $data_action = $_POST['data_action'];

            $status = [];

            $idcrm_settings = unserialize( get_option( 'idcrm_settings' ) && !is_array(get_option( 'idcrm_settings' )) ? get_option( 'idcrm_settings' ) : 'a:0:{}' );

            if (array_key_exists( $data_action . '_activated', $idcrm_settings )) {
              $idcrm_settings[$data_action] = '';
              $idcrm_settings[$data_action . '_expire'] = '';
              $idcrm_settings[$data_action . '_activated'] = '';
            }

            if (update_option( 'idcrm_settings', serialize($idcrm_settings) ) === false) {
                $status['error'] = 'fail update settings ' . $data_action;
            }

            // if (delete_option( $data_action ) === false) {
            //     $status['error'] = 'fail delete key ' . $data_action;
            // }
            //
            // if (delete_option( $data_action . '_activated' ) === false) {
            //     $status['error'] = 'fail delete activated ';
            // }
            //
            // if (delete_option( $data_action . '_expire' ) === false) {
            //     $status['error'] = 'fail delete time ';
            // }

            //error_log(json_encode($status));
          }

          die();

        }

        public static function idcrm_ajax_save_license() {
          check_ajax_referer(self::NONCE);
          $status = [];

          if ( array_key_exists( 'idcrm_license', $_POST ) && array_key_exists( 'data_action', $_POST ) ) {
            $data_action = $_POST['data_action'];
            $idcrm_license = isset($_POST['idcrm_license']) && !empty($_POST['idcrm_license']) ? trim(sanitize_text_field($_POST['idcrm_license'])) : 0;
            $idcrm_expire = isset($_POST['idcrm_expire']) && !empty($_POST['idcrm_expire']) ? strtotime($_POST['idcrm_expire']) : 0;

            $status[$data_action] = $idcrm_license;

            $idcrm_settings = unserialize( get_option( 'idcrm_settings' ) && !is_array(get_option( 'idcrm_settings' )) ? get_option( 'idcrm_settings' ) : 'a:0:{}' );

              $idcrm_settings[$data_action] = $idcrm_license;
              $idcrm_settings[$data_action . '_expire'] = $idcrm_expire;
              $idcrm_settings[$data_action . '_activated'] = 1;

            if (update_option( 'idcrm_settings', serialize($idcrm_settings) ) === false) {
                $status['error'] = 'fail update settings ' . $data_action;
            }
            //
            // if (update_option( $data_action . '_activated', '1' ) === false) {
            //     $status['error'] = 'fail update activated';
            // }
            //
            // if (update_option( $data_action . '_expire', $idcrm_expire ) === false) {
            //     $status['error'] = 'fail update time';
            // }

          }
          print_r($idcrm_settings);
          // error_log(json_encode($idcrm_settings));
          die();

        }

        public function idcrm_ajax_get_license() {
          check_ajax_referer(self::NONCE);

          $body = '';

          if ( array_key_exists( 'idrcm_license_key', $_POST ) ) {

            $idrcm_license_key = trim(sanitize_text_field($_POST['idrcm_license_key'])); //echo $idrcm_license_key;
            $idrcm_license_domain_name = array_key_exists( 'idrcm_license_domain_name', $_POST ) ? sanitize_text_field($_POST['idrcm_license_domain_name']) : ''; //echo $idrcm_license_domain_name;
            $idrcm_license_action = array_key_exists( 'idrcm_license_action', $_POST ) ? sanitize_text_field($_POST['idrcm_license_action']) : '';

            $url = self::LICENSE_SERVER . "?license={$idrcm_license_key}&action={$idrcm_license_action}&domain={$idrcm_license_domain_name}";

            $response = wp_remote_get( $url);
            $body = wp_remote_retrieve_body( $response );

            echo $body;

          }

          die();
        }

        public static function idcrm_get_image() {
          check_ajax_referer(self::NONCE);
          if (isset($_GET['idcrm_image_id']) && isset($_GET['idcrm_image_field'])) {
              $image = wp_get_attachment_image( filter_input( INPUT_GET, 'idcrm_image_id', FILTER_VALIDATE_INT ), 'medium', false, array( 'id' => 'image-' . $_GET['idcrm_image_field'], 'class' => 'settings-image' ) );
              $url = wp_get_attachment_url($_GET['idcrm_image_id']);

              $data = array(
                  'image' => $image,
                  'url' => $url
              );
              wp_send_json_success( $data );
          } else {
              wp_send_json_error();
          }

          die();
        }

        public static function idcrm_ajax_save_settings() {
          check_ajax_referer(self::NONCE);
          $all_data = json_decode(stripslashes($_POST['idcrm_settings']));
          $settings = [];
          $status = [];

          foreach ( $all_data as $key => $value ) {

            if ($key === 'markup' || $key === 'settings') {
              continue;
            }

            $settings[$key] = $value;

            // $idcrm_settings = $_POST['idcrm_settings'];

            // $idcrm_license = isset($_POST['idcrm_license']) && !empty($_POST['idcrm_license']) ? trim(sanitize_text_field($_POST['idcrm_license'])) : 0;
            // $idcrm_expire = isset($_POST['idcrm_expire']) && !empty($_POST['idcrm_expire']) ? strtotime($_POST['idcrm_expire']) : 0;
            //
            // $status[$data_action] = $idcrm_license;
            //
            // if (update_option( $data_action, $idcrm_license ) === false) {
            //     $status['error'] = 'fail update key ' . $data_action;
            // }
            //
            // if (update_option( $data_action . '_activated', '1' ) === false) {
            //     $status['error'] = 'fail update activated';
            // }
            //
            // if (update_option( $data_action . '_expire', $idcrm_expire ) === false) {
            //     $status['error'] = 'fail update time';
            // }


          }

          if (is_array($settings) && !empty($settings)) {
            if (update_option( 'idcrm_settings', serialize($settings) ) === false) {
                // $status['error'] = 'fail update key ' . $data_action;
            }
          }


          print_r($settings);
          //error_log(json_encode($status));
          die();

        }

        public function customUserProfileFields( $profileuser ) {
            ?>
            <h3><?php esc_html_e( 'Custom Local Avatar', idCRMActionLanguage::TEXTDOMAIN ); ?></h3>
            <table class="form-table idcrm_contacts-avatar-upload-options">
                <tr>
                    <th>
                        <label for="image"><?php esc_html_e( 'Custom Local Avatar', idCRMActionLanguage::TEXTDOMAIN ); ?></label>
                    </th>
                    <td>
                        <?php
                        // Check whether we saved the custom avatar, else return the default avatar.
                        $custom_avatar = get_the_author_meta( 'userimg', $profileuser->ID );
                        if ( $custom_avatar === '' ) {
                            $custom_avatar = get_avatar_url( $profileuser->ID );
                        } else {
                            $custom_avatar = esc_url_raw( $custom_avatar );
                        }
                        ?>
                        <img style="width: 96px; height: 96px; display: block; margin-bottom: 15px; object-fit: cover;" class="custom-avatar-preview" src="<?php echo esc_html( $custom_avatar ); ?>">
                        <input type="text" name="userimg" id="userimg" value="<?php echo esc_attr( esc_url_raw( get_the_author_meta( 'userimg', $profileuser->ID ) ) ); ?>" class="regular-text" />
                        <input type='button' class="avatar-image-upload button-primary" value="<?php esc_attr_e( 'Upload Image', idCRMActionLanguage::TEXTDOMAIN ); ?>" id="uploadimage"/><br />
                        <span class="description">
                            <?php esc_html_e( 'Please upload a custom avatar for your profile, to remove the avatar simple delete the URL and click update.', idCRMActionLanguage::TEXTDOMAIN ); ?>
                        </span>
                    </td>
                </tr>
            </table>
            <?php
        }

        function idcrmContactsSaveLocalAvatarFields( $user_id ) {
            if ( current_user_can( 'edit_user', $user_id ) ) {
                $userimg = '';
                if (array_key_exists('userimg', $_POST)) {
                    $userimg = $_POST['userimg'];
                }
                if ( $userimg != '' ) {
                    $avatar = esc_url_raw( wp_unslash( $userimg ) );
                    update_user_meta( $user_id, 'userimg', $avatar );
                }
            }
        }

        function idcrmContactsGetAvatarUrl( $url, $id_or_email, $args ) {
            $id = '';
            if ( is_numeric( $id_or_email ) ) {
                $id = (int) $id_or_email;
            } elseif ( is_object( $id_or_email ) ) {
                if ( ! empty( $id_or_email->user_id ) ) {
                    $id = (int) $id_or_email->user_id;
                }
            } else {
                $user = get_user_by( 'email', $id_or_email );
                $id   = ! empty( $user ) ? $user->data->ID : '';
            }
            $custom_url = $id ? get_user_meta( $id, 'userimg', true ) : '';
            if ( $custom_url === '' || ! empty( $args['force_default'] ) ) {
                return idCRM::$IDCRM_URL . 'templates/images/no-user.jpg';
            } else {
                return esc_url_raw( $custom_url );
            }
        }
    }
}

?>
