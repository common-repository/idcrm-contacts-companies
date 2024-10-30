<?php

namespace idcrm\admin;

use idcrm\includes\actions\idCRMActionLanguage;

if ( ! class_exists( '\idcrm\includes\api\idCRMAdminEventManageQuery' ) ) {
    class idCRMAdminEventManageQuery {
        public static function register()
        {
            $handler = new self();
            add_action( 'pre_get_posts', array($handler, 'scheduleSliceOrderBy') );
            add_filter( 'parse_query', array($handler, 'filterContactByAuthor') );
            add_filter( 'parse_query', array($handler, 'filterEventByAuthor') );
            add_filter( 'parse_query', array($handler, 'filterCompanyByAuthor') );
            add_filter( 'parse_query', array($handler, 'scheduleConvertIDToTermInQuery') );
            add_filter( 'parse_query', array($handler, 'filterParseQueryCustomFieldStatus') );
            add_filter( 'parse_query', array($handler, 'filterUserParseQueryCustomFieldStatus') );
        }
        /*
        * pre_get_posts
        * Админ модификация query
        * Класс idCRMAdminEventManageQuery
        */
        /* meta_key idcrm_event_timestring */
        public function scheduleSliceOrderBy( $query ) {
            if ( is_admin() ) {
                $orderby = $query->get( 'orderby');
                if ( 'eventdate' == $orderby ) {
                    $query->set('meta_key','idcrm_event_timestring');
                    $query->set('orderby','meta_value_num');
                }
            }
        }
        /*
        * parse_query
        * Админ модификация query
        * Класс idCRMAdminEventManageQuery
        */
        /* no comments */
        /* post_type user_contact */
        public function filterContactByAuthor($query) {
            global $pagenow;
            if (isset($_GET['post_type'])
                && $pagenow === 'edit.php'
                && 'user_contact' == $_GET['post_type']
                && is_admin()
                // && wp_get_current_user()->roles[0]  !== 'administrator'
                && !current_user_can( 'manage_options' )
            ) {
                $query->query_vars['author'] = get_current_user_id();
            }
        }
        /*
        * parse_query
        * Админ модификация query
        * Класс idCRMAdminEventManageQuery
        */
        /* no comments */
        /* post_type contact_event */
        public function filterEventByAuthor($query) {
            global $pagenow;
            if (isset($_GET['post_type'])
                && $pagenow === 'edit.php'
                && 'contact_event' == $_GET['post_type']
                && is_admin()
                // && wp_get_current_user()->roles[0]  !== 'administrator'
                && !current_user_can( 'manage_options' )
            ) {
                $query->query_vars['author'] = get_current_user_id();
            }
        }
        /*
        * parse_query
        * Админ модификация query
        * Класс idCRMAdminEventManageQuery
        */
        /* no comments */
        /* post_type contact_event */
        /* taxonomy contact_events */
        function scheduleConvertIDToTermInQuery($query) {
            global $pagenow;
            $post_type = 'contact_event';
            $taxonomy  = 'contact_events';
            $q_vars    = &$query->query_vars;
            if (
                $pagenow == 'edit.php'
                && isset($q_vars['post_type'])
                && $q_vars['post_type'] == $post_type
                && isset($q_vars[$taxonomy])
                && is_numeric($q_vars[$taxonomy])
                && $q_vars[$taxonomy] != 0 ) {
                    $term = get_term_by('id', $q_vars[$taxonomy], $taxonomy);
                    $q_vars[$taxonomy] = $term->slug;
            }
        }
        /*
        * parse_query
        * Админ модификация query
        * Класс idCRMAdminEventManageQuery
        */
        /* no comments */
        /* post_type company */
        function filterCompanyByAuthor($query) {
            global $pagenow;
            if (isset($_GET['post_type'])
                && $pagenow === 'edit.php'
                && 'company' == $_GET['post_type']
                && is_admin()
                && wp_get_current_user()->roles[0]  !== 'administrator'
            ) {
                $query->query_vars['author'] = get_current_user_id();
            }
        }
        /*
        * parse_query
        * Админ модификация query
        * Класс idCRMAdminEventManageQuery
        */
        /* no comments */
        /* wp admin ui set $query settings based on custom meta_key idcrm_event_status */
        function filterParseQueryCustomFieldStatus( $query ){
            global $pagenow;
            $meta_key = 'idcrm_event_status';
            $valid_status = array_keys(
                array(
                    ''       => esc_html__( 'Status', idCRMActionLanguage::TEXTDOMAIN ),
                    'active'   => esc_html__( 'Active', idCRMActionLanguage::TEXTDOMAIN ),
                    'finished' => esc_html__( 'Finished', idCRMActionLanguage::TEXTDOMAIN ),
                )
            );
            $status = '';
            if (array_key_exists($meta_key, $_GET)) {
                $status = $_GET[$meta_key];
                if (in_array($status, $valid_status)) {
                    if ( is_admin() && 'edit.php' === $pagenow
                        && isset($_GET['post_type'])
                        && 'contact_event' == $_GET['post_type']
                        && $status )
                    {
                        $query->query_vars['meta_query']     = array(
                            array(
                                'key' => $meta_key,
                                'value' => $status,
                            ),
                        );
                    }
                }
            }
        }
        /*
        * parse_query
        * Админ модификация query
        * Класс idCRMAdminEventManageQuery
        */
        /* no comments */
        /* wp admin ui $query settings based on current page */
        function filterUserParseQueryCustomFieldStatus( $query ) {
            global $pagenow;
            $current_page = isset( $_GET['post_type'] ) ? $_GET['post_type'] : '';
            if ( is_admin() &&
                'contact_event' == $current_page &&
                'edit.php' == $pagenow &&
                isset( $_GET['idcrm_contact_user_id'] ) &&
                $_GET['idcrm_contact_user_id'] != '' )
            {
                $query->query_vars['meta_query'] = array(
                    array(
                        'key' => 'idcrm_contact_user_id',
                        'value' => $_GET['idcrm_contact_user_id'],
                    ),
                );

                // echo '<pre>$query: ' . print_r( $query, true ) . '</pre>';
            }
            if ( is_admin() &&
                'contact_event' == $current_page &&
                'edit.php' == $pagenow &&
                isset( $_GET['idcrm_event_user_or_company'] ) &&
                $_GET['idcrm_event_user_or_company'] != '' )
            {
                $query->query_vars['meta_query'] = array(
                    array(
                        'key' => 'idcrm_event_user_or_company',
                        'value' => $_GET['idcrm_event_user_or_company'],
                    ),
                );
            }
            $meta_key = 'idcrm_event_status';
            $valid_status = array_keys(
                array(
                    ''       => esc_html__( 'Status', idCRMActionLanguage::TEXTDOMAIN ),
                    'active'   => esc_html__( 'Active', idCRMActionLanguage::TEXTDOMAIN ),
                    'finished' => esc_html__( 'Finished', idCRMActionLanguage::TEXTDOMAIN ),
                )
            );
            $status = (! empty($_GET[$meta_key]) && in_array($_GET[$meta_key],$valid_status)) ? $_GET[$meta_key] : '';
            if ( is_admin()
                && 'edit.php' === $pagenow
                && isset($_GET['post_type'])
                && 'contact_event' === $_GET['post_type']
                && isset( $_GET['idcrm_contact_user_id'] ) &&
                $_GET['idcrm_contact_user_id'] != ''
                && isset( $_GET['idcrm_event_user_or_company'] )
                && $_GET['idcrm_event_user_or_company'] != '' )
            {
                $query->query_vars['meta_query']     = array(
                    array(
                        'key' => 'idcrm_contact_user_id',
                        'value' => $_GET['idcrm_contact_user_id'],
                    ),
                    array(
                        'key' => 'idcrm_event_user_or_company',
                        'value' => $_GET['idcrm_event_user_or_company'],
                    ),
                );
            }
            if ( is_admin()
                && 'edit.php' === $pagenow
                && isset($_GET['post_type'])
                && 'contact_event' === $_GET['post_type']
                && $status
                && isset( $_GET['idcrm_contact_user_id'] )
                && $_GET['idcrm_contact_user_id'] != '' )
            {
                $query->query_vars['meta_query']     = array(
                    array(
                        'key' => $meta_key,
                        'value' => $status,
                    ),
                    array(
                        'key' => 'idcrm_contact_user_id',
                        'value' => $_GET['idcrm_contact_user_id'],
                    ),
                );
            }
            if ( is_admin()
                && 'edit.php' === $pagenow
                && isset($_GET['post_type'])
                && 'contact_event' === $_GET['post_type']
                && $status
                && isset( $_GET['idcrm_event_user_or_company'] )
                && $_GET['idcrm_event_user_or_company'] != '' )
            {
                $query->query_vars['meta_query'] = array(
                    array(
                        'key' => $meta_key,
                        'value' => $status,
                    ),
                    array(
                        'key' => 'idcrm_event_user_or_company',
                        'value' => $_GET['idcrm_event_user_or_company'],
                    ),
                );
            }
            if ( is_admin()
                && 'edit.php' === $pagenow
                && isset($_GET['post_type'])
                && 'contact_event' === $_GET['post_type']
                && $status
                && isset( $_GET['idcrm_contact_user_id'] )
                && $_GET['idcrm_contact_user_id'] != ''
                && isset( $_GET['idcrm_event_user_or_company'] )
                && $_GET['idcrm_event_user_or_company'] != '' )
            {
                $query->query_vars['meta_query'] = array(
                    array(
                        'key' => $meta_key,
                        'value' => $status,
                    ),
                    array(
                        'key' => 'idcrm_contact_user_id',
                        'value' => $_GET['idcrm_contact_user_id'],
                    ),
                    array(
                        'key' => 'idcrm_event_user_or_company',
                        'value' => $_GET['idcrm_event_user_or_company'],
                    )
                );
            }
        }
    }
}

?>
