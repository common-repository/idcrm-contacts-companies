<?php

namespace idcrm\admin;

use idcrm\includes\actions\idCRMActionLanguage;

if ( ! class_exists( '\idcrm\includes\api\idCRMAdminEventManageColumns' ) ) {
    class idCRMAdminEventManageColumns {
        public static function register()
        {
            $handler = new self();
            add_filter( 'manage_contact_event_posts_columns', array($handler, 'scheduleEventDateAddColumns') );
            add_filter( 'manage_contact_event_posts_columns', array($handler, 'scheduleEventStatusAddColumns') );
            add_filter( 'manage_contact_event_posts_columns', array($handler, 'columnOrder') );
            add_filter( 'manage_contact_event_posts_columns', array($handler, 'scheduleUserAddColumns') );
            add_filter( 'manage_edit-contact_event_sortable_columns', array($handler, 'scheduleSortableColumn') );
        }
        /*
        * manage_{post_type}_posts_columns
        * Админ модификация query
        * Класс idCRMAdminEventManageColumns
        */
        /* Список событий Добавляем колонку Event Date */
        /* post_type contact_event */
        public function scheduleEventDateAddColumns( $columns ) {
            $num = 1;
            $column_name = esc_html__( 'Event Date', idCRMActionLanguage::TEXTDOMAIN );
            $new_columns = array(
                'event_date' => $column_name,
            );
            return array_slice( $columns, 0, $num ) + $new_columns + array_slice( $columns, $num );
        }
        /*
        * manage_{post_type}_posts_columns
        * Админ модификация query
        * Класс idCRMAdminEventManageColumns
        */
        /* Список событий Добавляем колонку Status */
        /* post_type contact_event */
        public function scheduleEventStatusAddColumns( $columns ) {
            $num = 4;
            $column_name = esc_html__( 'Status', idCRMActionLanguage::TEXTDOMAIN );
            $new_columns = array(
                'event_status' => $column_name,
            );
            return array_slice( $columns, 0, $num ) + $new_columns + array_slice( $columns, $num );
        }
        /*
        * manage_{post_type}_posts_columns
        * Админ модификация query
        * Класс idCRMAdminEventManageColumns
        */
        /* Список событий Передвигаем колонку тип события перез колонкой заголовка события */
        /* post_type contact_event */
        /* taxonomy contact_events */
        public function columnOrder($columns) {
            $n_columns = array();
            $move = 'taxonomy-contact_events'; // what to move
            $before = 'title'; // move before this
            foreach($columns as $key => $value) {
                if ($key==$before){
                $n_columns[$move] = $move;
                }
                $n_columns[$key] = $value;
            }
            return $n_columns;
        }
        /*
        * manage_{post_type}_posts_columns
        * Админ модификация query
        * Класс idCRMAdminEventManageColumns
        */
        /* Список событий Добавляем колонку Username */
        /* post_type contact_event */
        public function scheduleUserAddColumns( $columns ) {
            $num = 4;
            $column_name = esc_html__( 'Username', idCRMActionLanguage::TEXTDOMAIN );
            $new_columns = array(
                'event_username' => $column_name,
            );
            return array_slice( $columns, 0, $num ) + $new_columns + array_slice( $columns, $num );
        }
        /*
        * manage_{screen_id}_sortable_columns
        * Админ модификация query
        * Класс idCRMAdminEventManageColumns
        */
        /* Список событий  Заголовок колонки Дата мероприятия становится активной для сортировки /wp-admin/edit.php?post_type=contact_event*/
        /* post_type contact_event */
        public function scheduleSortableColumn( $columns )    {
            $columns['event_date'] = 'eventdate';
            return $columns;
        }
    }
}

?>