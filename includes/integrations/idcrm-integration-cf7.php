<?php

namespace idcrm\includes\integrations;

/** Contacts Form 7 integration */
if ( ! class_exists( '\idcrm\includes\IdCRMIntegrationCF7' ) ) {
    class IdCRMIntegrationCF7 {
        public static function register() {
            $handler = new self();
            add_action( 'wpcf7_before_send_mail', array($handler, 'getContactLead'), 10, 3 );
        }

        private static function checkPush(array &$queryPart, string $value, string $key) {
            if (!empty($value)) {
                array_push($queryPart, ['key' => $key, 'value' => $value]);
            }
        }

        private static function checkUpdateMeta(int $postID, string $metaKey, string $metaValue) {
            if (!empty($metaValue)) {
                $meta_value = get_post_meta( $postID, $metaKey, true );
                if ($meta_value == '') {
                    update_post_meta( $postID, $metaKey, sanitize_text_field( wp_unslash( $metaValue ) ) );
                }
            }
        }

        public static function getContactLead( \WPCF7_ContactForm $contact_form, bool $abort, \WPCF7_Submission $submission ) {
            $message = array();
            array_push($message, 'getContactLead');
            $yourName = $submission->get_posted_data( self::$formFieldTitle );
            if (!empty($yourName)) {
                array_push($message, '$yourName: ' . $yourName);
                $yourEmail = $submission->get_posted_data( self::$formFieldEmail );
                $yourPhone = $submission->get_posted_data( self::$formFieldPhone );
                $yourMessage = $submission->get_posted_data( self::$formFieldContent );
                $args = array(
                    'post_type' => 'user_contact',
                    'post_status' => 'publish',
                    'numberposts' => -1,
                );
                $meta_query = array();

                if ($yourEmail) {
                  self::checkPush($meta_query, $yourEmail, self::$metaEmail);
                }

                if ($yourPhone) {
                  self::checkPush($meta_query, $yourPhone, self::$metaPhone);
                }

                if (!empty($meta_query)) {
                    $meta_query['relation'] = 'OR';
                    $args['meta_query'] = $meta_query;
                }

                $check_contact_mail = get_posts( $args );
                // array_push($message, '$check_contact_mail: ' . print_r($check_contact_mail, true));
                $contact_item_id = 0;

                if ( empty( $check_contact_mail ) ) {

                    $idcrm_settings = unserialize(get_option( 'idcrm_settings' ) ?: 'a:0:{}');
                    $post_author = isset($idcrm_settings['idcrm_cf7_default_user']) && !empty($idcrm_settings['idcrm_cf7_default_user'])
                        ? intval($idcrm_settings['idcrm_cf7_default_user'])
                        : '';

                    $contact_data = array(
                        'post_type' => 'user_contact',
                        'post_title' => $yourName,
                        'post_status' => 'publish',
                        'post_author' => $post_author,
                    );

                    if (!empty($yourMessage)) {
                        $contact_data['post_content'] = $yourMessage;
                    }
                    $contact_item_id = wp_insert_post( $contact_data );

                    $user_login_id = 'user_' . random_int( 100000, 999999 );
              			$password = wp_generate_password( 10, true, true );
              			$user_email = !empty( $yourEmail ) ? sanitize_email( wp_unslash( $yourEmail ) ) : '';
              			$display_name = !empty( $yourName ) ? sanitize_text_field( wp_unslash( $yourName ) ) : $user_login_id;

                    $name_array = explode(' ', $yourName);
              			$first_name = is_array($name_array) && isset($name_array[0]) ? sanitize_text_field( wp_unslash( $name_array[0] ) ) : $display_name;
              			$last_name = is_array($name_array) && isset($name_array[1]) ? sanitize_text_field( wp_unslash( $name_array[1] ) )  : $display_name;

              			$userdata = array(
              				'user_login'   => "$user_login_id",
              				'user_pass'    => "$password",
              				'user_email'   => $user_email,
              				'display_name' => $display_name,
              				'first_name'   => $first_name,
              				'last_name'    => $last_name,
              				'role' => 'lead',
              			);

              			$user_id = wp_insert_user( $userdata );

                    update_post_meta( $contact_item_id, 'idcrm_contact_user_id', sanitize_text_field( wp_unslash( $user_id ) ) );
              			update_post_meta( $contact_item_id, 'idcrm_contact_source', 'cf7' );
                    update_post_meta( $contact_item_id, 'idcrm_added_as_lead', 'yes' );

                } else {
                    if (count($check_contact_mail) == 1) {
                        $contact_item_id = $check_contact_mail[0]->ID;
                    }
                }

                if ( $contact_item_id > 0 ) {
                  if ($yourEmail) {
                    self::checkUpdateMeta($contact_item_id, self::$metaEmail, $yourEmail);
                  }

                  if ($yourPhone) {
                    self::checkUpdateMeta($contact_item_id, self::$metaPhone, $yourPhone);
                  }
                    $terms = wp_set_object_terms( $contact_item_id, 'user-leads', 'user_status' );
                    // array_push($message, '$terms: ' . print_r($terms, true));
                }
            }

            ob_start();
            //echo '<pre>' . implode('<br />', $message) . '</pre>';
            error_log(ob_get_clean());
            //die();
        }

        public static $metaEmail = 'idcrm_contact_email';
        public static $metaPhone = 'idcrm_contact_phone';
        public static $formFieldTitle = 'your-name';
        public static $formFieldContent = 'your-message';
        public static $formFieldEmail = 'your-email';
        public static $formFieldPhone = 'your-phone';
    }
}

?>
