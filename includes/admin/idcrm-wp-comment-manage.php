<?php

namespace idcrm\admin;

if ( ! class_exists( '\idcrm\includes\api\idCRMWPCommentManage' ) ) {
    class idCRMWPCommentManage {
        public static function register()
        {
            $handler = new self();
            add_action( 'comment_post', array($handler, 'addCommentMetaDataField') );
        }
        /*
        * wp comments
        * comment_post
        * WPКомментарий хук
        * Класс idCRMWPCommentManage
        */
        /* обновляет мету комментария при его добавлении, я хз зачем это, ведь комментарии используюся посты кастомного типа */
        /* wp comments */
        public function addCommentMetaDataField( $comment_id ) {
            if (array_key_exists('idcrm_comment_type', $_POST)) {
                add_comment_meta( $comment_id, 'idcrm_comment_type', sanitize_text_field( $_POST['idcrm_comment_type'] ) );
            }
            if (array_key_exists('idcrm_comment_event_id', $_POST)) {
                add_comment_meta( $comment_id, 'idcrm_comment_event_id', sanitize_text_field( $_POST['idcrm_comment_event_id'] ) );
            }
        }
    }
}

?>