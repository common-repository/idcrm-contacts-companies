<?php

namespace idcrm\admin;

// require_once('idCRMAdminUserManage.php');
use idcrm\includes\actions\idCRMActionLanguage;
use idcrm\includes\api\idCRMAdminUserManage;

if ( ! class_exists( '\idcrm\includes\api\idCRMAdminEventManageFilter' ) ) {
    class idCRMAdminEventManageFilter {
        public static function register()
        {
            $handler = new self();
            add_action( 'restrict_manage_posts', [ $handler, 'scheduleFilterPostTypeByTaxonomy' ], 10, 2 );
            add_action( 'restrict_manage_posts', [ $handler, 'scheduleFilterPostByCustomFieldStatus' ] , 10, 2);
            add_action( 'restrict_manage_posts', [ $handler, 'scheduleFilterUserByCustomFieldStatus' ] , 10, 2);
        }
        /**
         * Админ Список Событий
         * Поле перед выводом кнопки Фильтр
         * Выводит список опций Тип дела
         */
        public function scheduleFilterPostTypeByTaxonomy($post_type) {
            if ($post_type == 'contact_event') {
                $taxonomy = 'contact_events';
                $selected = '';
                if (array_key_exists($taxonomy, $_GET)) {
                    $selected = $_GET[$taxonomy];
                }
                $info_taxonomy = get_taxonomy($taxonomy);
                wp_dropdown_categories(array(
                    'show_option_all' => sprintf( __( '%s', idCRMActionLanguage::TEXTDOMAIN ), $info_taxonomy->label ),
                    'taxonomy'        => $taxonomy,
                    'name'            => $taxonomy,
                    'orderby'         => 'name',
                    'selected'        => $selected,
                    'hide_empty'      => true,
                ));
            }
        }
        /**
         * Админ Список Событий
         * Поле перед выводом кнопки Фильтр
         * Выводит список опций Статус
         */
        public function scheduleFilterPostByCustomFieldStatus( $post_type, $which ) {
            if ( $post_type == 'contact_event' ) {
                $meta_key = 'idcrm_event_status';
                $meta_value = '';
                if (array_key_exists($meta_key, $_GET)) {
                    $meta_value = $_GET[$meta_key];
                }
                $options = array(
                    ''       => esc_html__( 'Status', idCRMActionLanguage::TEXTDOMAIN ),
                    'active'   => esc_html__( 'Active', idCRMActionLanguage::TEXTDOMAIN ),
                    'finished' => esc_html__( 'Finished', idCRMActionLanguage::TEXTDOMAIN ),
                );
                echo '<select name="' . $meta_key . '" id="' . $meta_key . '" class="postform">';
                foreach ( $options as $value => $name ) {
                    $selected = '';
                    if ($meta_value == $value) {
                        $selected = ' selected="selected"';
                    }
                    echo '<option value="' . esc_attr($value) . '" ' . $selected . '>' . esc_html($name) . '</option>';
                }
                echo '</select>';
            }
        }
        /**
         * Админ Список Событий
         * Поле перед выводом кнопки Фильтр
         * Выводит список Контактов и Компаний
         * Выводит список Типов контактов
        */
        private function userCustomFieldStatusOptions()
        {
            // global $wp_query;

            // $wp_query->query_vars['meta_query'] = [];

            // $wp_query->meta_query = new \WP_Meta_Query( [] );

            // echo '<pre>' . print_r( $wp_query, true ) . '</pre>';

            $all_users = [
                '' => esc_html__( 'All', idCRMActionLanguage::TEXTDOMAIN ) . ' ' . esc_html__( 'Contacts', idCRMActionLanguage::TEXTDOMAIN )
            ];

            // $args = [
            //     'numberposts' => -1,
            //
            //     'post_type' => 'user_contact',
            //
            //     'orderby' => 'type',
            // ];

            // $wp_query = new \WP_Query( $args );
            //
            // $wp_query->query_vars['meta_query'] = [];
            //
            // $wp_query->meta_query = new \WP_Meta_Query( [] );

            // echo '<pre>$wp_query->request: ' . print_r( $wp_query->request, true ) . '</pre>';

            // echo '<pre>$wp_query: ' . print_r( $wp_query, true ) . '</pre>';

            // $contacts = get_posts( $args );

            // $contacts = $wp_query->query($args);
            // $user_contacts = get_posts(

            global $wpdb;
            $user_contacts = $wpdb->get_col( "SELECT ID FROM $wpdb->posts where post_type='user_contact' and post_status='publish'" );

            // echo '<pre>$user_contacts: ' . print_r( $user_contacts, true ) . '</pre>';
            // echo '<pre>$contacts: ' . print_r( $contacts, true ) . '</pre>';

            foreach ( $user_contacts as $contact ) {
              $each_post = get_post($contact);
                $all_users[$contact] = $each_post->post_title;
            }

            return $all_users;
        }
        /*
        * restrict_manage_posts
        * Админ Список Событий
        * Класс idCRMAdminEventManageFilter
        */
        /* На списке Событий contact_event добавляет список Контактов и компаний и список Типов контактов и компаний перед выводом кнопки Фильтр */
        /* post_type contact_event */
        /* admin ui post_type contact_event post list new filter options */
        public function scheduleFilterUserByCustomFieldStatus( $post_type, $which )
        {
            if ( $post_type == 'contact_event' ) {
                $meta_key = 'idcrm_contact_user_id';

                $options = self::userCustomFieldStatusOptions();

                echo '<select name="' . $meta_key . '" id="' . $meta_key .'" class="postform">';

                foreach ( $options as $value => $name ) {
                    $selected = '';

                    // if (array_key_exists($meta_key, $_GET)) {
                    //     $selected = $_GET[$meta_key];
                    // }

                    printf(
                      '<option value="%1$s" %2$s>%3$s</option>',
                      esc_attr($value),
                      ( ( isset( $_GET[$meta_key] ) && ( $_GET[$meta_key] == $value ) ) ? ' selected="selected"' : '' ),
                      esc_html($name)
                    );

                    // echo '<option value="' . $value . '" ' . $selected . '>' . $name . '</option>';
                }

                echo '</select>';

                $contact_key = 'idcrm_event_user_or_company';

                $contact = [
                    '' => esc_html__( 'All', idCRMActionLanguage::TEXTDOMAIN ),

                    'user' => esc_html__( 'Contact', idCRMActionLanguage::TEXTDOMAIN ),
                ];

                // echo '<select name="' . $contact_key . '" id="' . $contact_key . '" class="postform">';
                //
                // foreach ( $contact as $value => $name ) {
                //
                //     $selected = '';
                //
                //     if (array_key_exists($contact_key, $_GET)) {
                //         $selected = $_GET[$contact_key];
                //     }
                //
                //     echo '<option value="' . $value . '" ' . $selected . '>' . $name . '</option>';
                // }
                //
                // echo '</select>';
            }
        }
    }
}

function userCustomFieldStatusOptions() {

    $all_users = [
        '' => esc_html__( 'All', idCRMActionLanguage::TEXTDOMAIN ) . ' ' . esc_html__( 'Contacts', idCRMActionLanguage::TEXTDOMAIN )
    ];

    $user_contacts = get_posts(
      array(
        'numberposts' => -1,
        'post_type'   => 'user_contact',
      )
    );

    foreach ( $user_contacts as $contact ) {
        $all_users[$contact->ID] = $contact->post_title;
    }

    return $all_users;
}

?>
