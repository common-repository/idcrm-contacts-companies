<?php

namespace idcrm\includes\api;

require_once('idcrm-api-event.php');
require_once('idcrm-api-timeline.php');
require_once('idcrm-api-comment.php');
require_once('idcrm-api-note.php');
require_once('idcrm-api-schedule.php');
require_once('idcrm-api-company.php');
require_once('idcrm-api-contact.php');

use idcrm\idCRM;
use idcrm\includes\actions\idCRMActionLanguage;

if ( ! class_exists( '\idcrm\includes\api\idCRMApi' ) ) {

    class idCRMApi {
        public function register_script() {
            wp_register_script( 'wp_ajax_api', idCRM::$IDCRM_URL . 'public/js/api/ajax-api.js', [ 'jquery' ], IDCRM_CONTACTS_VERSION );
            wp_enqueue_script( 'wp_ajax_api');

			      wp_register_script( 'wp_ajax', idCRM::$IDCRM_URL . 'public/js/api/ajax.js', [ 'jquery', 'toastr' ], IDCRM_CONTACTS_VERSION );
            wp_localize_script( 'wp_ajax', 'wp_ajax_data', $this->get_ajax_data() );
            wp_localize_script( 'wp_ajax', 'idcrm_in_progress', array() );
            wp_localize_script( 'wp_ajax', 'idcrm_notifications', array() );
            wp_localize_script( 'wp_ajax', 'wp_ajax_toastr', $this->get_ajax_toastr_data() );
            wp_enqueue_script( 'wp_ajax' );

        }

        private function get_ajax_data() {
            return array(
                'ajax_url' => admin_url( 'admin-ajax.php' )
            );
        }

        public static function register() {
            $handler = new self();

            add_action('wp_enqueue_scripts', [$handler, 'register_script'] );
            // add_action('admin_enqueue_scripts', [$handler, 'register_script'] );

            idCRMApiEvent::register();
            idCRMApiTimeline::register();
            idCRMApiComment::register();
            idCRMApiNote::register();
            idCRMApiSchedule::register();
            idCRMApiCompany::register();
            idCRMApiContact::register();
        }

        private function get_ajax_toastr_data() {
            return array(
                'strings' => array(
                    'idcrmActivated' => esc_html__( 'activated', idCRMActionLanguage::TEXTDOMAIN ),
                    'idcrmFinished' => esc_html__( 'finished', idCRMActionLanguage::TEXTDOMAIN ),
                    'idcrmEvent' => esc_html__( 'Event', idCRMActionLanguage::TEXTDOMAIN ),
                    'idcrmEventAdded' => esc_html__( 'Event added', idCRMActionLanguage::TEXTDOMAIN ),
                    'idcrmSave' => esc_html__( 'Save', idCRMActionLanguage::TEXTDOMAIN ),
                    'idcrmCancel' => esc_html__( 'Cancel', idCRMActionLanguage::TEXTDOMAIN ),
                    'idcrmDeleted' => esc_html__( 'Deleted', idCRMActionLanguage::TEXTDOMAIN ),
                    'adminUrl' => get_admin_url(),
                    'idcrmSendError' => esc_html__( 'Error. All fields are required', idCRMActionLanguage::TEXTDOMAIN ),
                    'idcrmCommentSent' => esc_html__( 'Comment sent', idCRMActionLanguage::TEXTDOMAIN ),
                    'idcrmEmailSent' => esc_html__( 'Email sent', idCRMActionLanguage::TEXTDOMAIN ),
                    'idcrmError' => esc_html__( 'Error', idCRMActionLanguage::TEXTDOMAIN ),
                    'idcrmErrorTestConfirm' => esc_html__( 'You have to fill email options and send a test email in settings', idCRMActionLanguage::TEXTDOMAIN ),
                    'idcrmMailMarkStarred' => esc_html__( 'Mark as Starred', idCRMActionLanguage::TEXTDOMAIN ),
                    'idcrmMailMarkSpam' => esc_html__( 'Mark as Spam', idCRMActionLanguage::TEXTDOMAIN ),
                    'idcrmMailMarkNotSpam' => esc_html__( 'Not a Spam', idCRMActionLanguage::TEXTDOMAIN ),
                    'idcrmMailMoveTo' => esc_html__( 'Move to', idCRMActionLanguage::TEXTDOMAIN ),
                    'idcrmMailMovedSuccess' => esc_html__( 'Successfully moved', idCRMActionLanguage::TEXTDOMAIN ),
                    'idcrmMailMoveToTrash' => esc_html__( 'Move to Trash', idCRMActionLanguage::TEXTDOMAIN ),
                    'idcrmMailDelete' => esc_html__( 'Delete', idCRMActionLanguage::TEXTDOMAIN ),
                    'idcrmNothingChecked' => esc_html__( 'Please check at least one message', idCRMActionLanguage::TEXTDOMAIN ),
                    'idcrmMailMoveFromTrash' => esc_html__( 'Recover from Trash', idCRMActionLanguage::TEXTDOMAIN ),
                    'idcrmMailComposeMessage' => esc_html__( 'Message', idCRMActionLanguage::TEXTDOMAIN ),
                    'idcrmMailnothingTosave' => esc_html__( 'Nothing to save', idCRMActionLanguage::TEXTDOMAIN ),
                ),
                'security' => array(
                    'security' => wp_create_nonce( "idcrm_ajax_infinite" ),
                    'security_newevent' => wp_create_nonce( "idcrm_ajax_new_event" ),
                    'security_refresh_schedule' => wp_create_nonce( "idcrm_ajax_refresh_schedule" ),
                    'security_refresh_comments' => wp_create_nonce( "idcrm_ajax_refresh_comments" ),
                    'security_send_email' => wp_create_nonce( "idcrm_ajax_send_email" ),
                    'security_send_comment' => wp_create_nonce( "idcrm_ajax_send_comment" ),
                    'security_editcomment' => wp_create_nonce( "idcrm_ajax_edit_comment" ),
                    'security_get_folder' => wp_create_nonce( "idcrm_ajax_get_folder" ),
                    'security_change_attr' => wp_create_nonce( "idcrm_ajax_change_attr" ),
                    'security_save_draft' => wp_create_nonce( "idcrm_ajax_save_draft" ),
                    'security_load_draft' => wp_create_nonce( "idcrm_ajax_load_draft" ),
                    'security_mail_delete' => wp_create_nonce( "idcrm_ajax_mail_delete" ),
                    'security_mail_star' => wp_create_nonce( "idcrm_ajax_mail_star" ),
                    'security_mail_spam' => wp_create_nonce( "idcrm_ajax_mail_spam" ),
                    'security_mail_spamrecover' => wp_create_nonce( "idcrm_ajax_mail_spamrecover" ),
                    'security_load_reply' => wp_create_nonce( "idcrm_ajax_load_reply" ),
                    'security_send_message' => wp_create_nonce( "idcrm_ajax_send_message" ),
                    'security_move_message' => wp_create_nonce( "idcrm_ajax_move_message" ),
                    'security_seen_message' => wp_create_nonce( "idcrm_ajax_seen_message" ),
                )
            );
        }

        public static function is_user_role( $role, $user_id = null ) {
          $user = is_numeric( $user_id ) ? get_userdata( $user_id ) : wp_get_current_user();
          if ( !$user )
          return false;
          return in_array( $role, (array) $user->roles );
        }

        public static function is_zadarma_active() {
          $idcrm_settings = unserialize( get_option( 'idcrm_settings' ) && !is_array(get_option( 'idcrm_settings' )) ? get_option( 'idcrm_settings' ) : 'a:0:{}' );
          $idcrm_zadarma_public = array_key_exists( 'idcrm_zadarma_public', $idcrm_settings ) && !empty($idcrm_settings['idcrm_zadarma_public']) ? $idcrm_settings['idcrm_zadarma_public'] : '';
          $idcrm_zadarma_secret = array_key_exists( 'idcrm_zadarma_secret', $idcrm_settings ) && !empty($idcrm_settings['idcrm_zadarma_secret']) ? $idcrm_settings['idcrm_zadarma_secret'] : '';

          if ($idcrm_zadarma_public && $idcrm_zadarma_secret) {
            return true;
          } else {
            return false;
          }
        }

        public static function is_accessable( $user_id, $current_plugin) {

        	$idcrm_team_roles = get_user_meta( $user_id, 'idcrm_team_roles', true );
        	$idcrm_permissions = unserialize(get_user_meta($user_id, 'idcrm_permissions', true) ?: 'a:0:{}');
        	$all_roles = apply_filters( 'idcrm_get_all_roles', [] );

        	if (!empty($idcrm_permissions)) {

        	  if (isset($idcrm_permissions[$current_plugin]) && $idcrm_permissions[$current_plugin] === 'yes' || self::is_user_role( 'administrator', $user_id )) {
        			return true;
        	  } else {
        	    return false;
        	  }

        	} else if ($idcrm_team_roles) {

        	  if (isset($all_roles[$idcrm_team_roles][$current_plugin]) && $all_roles[$idcrm_team_roles][$current_plugin] === 'yes' || self::is_user_role( 'administrator', $user_id )) {
        			return true;
        	  } else {
        	    return false;
        	  }

        	} else {
        	  if ( self::is_user_role( 'crm_support', $user_id ) || self::is_user_role( 'crm_manager', $user_id ) || self::is_user_role( 'administrator', $user_id ) ) {
        			return true;
        	  } else {
        	    return false;
        	  }
        	}
        }
    }
}

?>
