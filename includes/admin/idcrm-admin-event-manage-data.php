<?php

namespace idcrm\admin;

use idcrm\includes\actions\idCRMActionLanguage;

if ( ! class_exists( '\idcrm\includes\api\idCRMAdminEventManageData' ) ) {
    class idCRMAdminEventManageData {
        public static function register()
        {
            $handler = new self();
            add_filter( 'the_title', array($handler, 'scheduleEventTitleColumn') );
            add_filter( 'manage_contact_event_posts_custom_column', array($handler, 'scheduleEventDateColumnsContent'), 10, 3 );
            add_filter( 'manage_contact_event_posts_custom_column', array($handler, 'scheduleEventStatusColumnsContent'), 10, 3 );
            add_filter( 'manage_contact_event_posts_custom_column', array($handler, 'scheduleUserColumnsContent'), 10, 3 );
        }
        /**
         * Админ Список Событий
         * Колонка Дата Дела
         * Выводит в Колонку Дата Дела запланированную дату и время
         */
        function scheduleEventDateColumnsContent( $colname, $post_id ) {
            if ( $colname == 'event_date' ) {
                echo $string_date = get_post_meta( $post_id, 'idcrm_event_timestring', 1 ) ? gmdate('d.m.Y H:i', get_post_meta( $post_id, 'idcrm_event_timestring', 1 ) ) : '—';
            }
        }
        /**
         * Админ Список Событий
         * Колонка Статус
         * Выводит в ячейку Колонки Статус текущее состояние события
         */
        function scheduleEventStatusColumnsContent( $colname, $post_id ) {
            if ( $colname == 'event_status' ) {
                echo $current_status = (get_post_meta( $post_id, 'idcrm_event_status', 1 ) == 'active') ? esc_html__( 'Active', idCRMActionLanguage::TEXTDOMAIN ) : esc_html__( 'Finished', idCRMActionLanguage::TEXTDOMAIN ) ;
            }
        }
        /**
         * Админ Список Событий
         * Колонка Заголовок
         * Заменяет служебную информацию в post_title на отрывок post_content
         */
        public function scheduleEventTitleColumn($title, $id = 0) {
            if ( is_admin() ) {
                $post = get_post();
                if ( isset($post->post_type) && $post->post_type == 'contact_event' ) {
                    $suffix = '';
                    if ( mb_strlen($post->post_content) > 30 ) {
                        $suffix = '...';
                    }
                    $title = mb_substr($post->post_content, 0, 30) . $suffix;
                }
            }
            return $title;
        }
        /**
         * Админ Список Событий
         * Колонка Имя Контакта
         * Возвращает post_title постов по значению мета с префиксом по типу
         */
        private function getPostTitleByMeta( $event_id, $metaKey ) {
            $result = '';
            $contact_id = get_post_meta( $event_id, $metaKey, true );
            if ($contact_id != '') {
                $contact = get_post( $contact_id );
                if (!empty( $contact )) {
                    $prefix = esc_html__( 'Contact', idCRMActionLanguage::TEXTDOMAIN );
                    if ( $contact->post_type == 'company' ) {
                        $prefix = esc_html__( 'Company', idCRMActionLanguage::TEXTDOMAIN );
                    }
                    // $result = $prefix . ': ' . $contact->post_title;
                    $result = $contact->post_title;
                }
            }
            return $result;
        }
        /**
         * Админ Список Событий
         * Колонка Имя Контакта
         * Выводит в колонку Имя Контакта post_title контакта или компании, к которой привязано событие
         */
        public function scheduleUserColumnsContent( $colname, $event_id )
        {
            if ( $colname == 'event_username' ) {
                $contact = $this->getPostTitleByMeta( $event_id, 'idcrm_contact_user_id' );
                $result = [];
                if (!empty( $contact )) {
                    $result[] = $contact;
                }
                if (empty( $result )) {
                    $result[] = esc_html__( 'Contact or company removed', idCRMActionLanguage::TEXTDOMAIN );
                }
                echo implode('<br />' , $result);
            }
        }
    }
}

?>
