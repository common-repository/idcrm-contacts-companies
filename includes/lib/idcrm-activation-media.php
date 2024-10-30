<?php

namespace idcrm\includes\lib;

use idcrm\idCRM;

function set_attachment($post_id, $media_name = 'logo_idresult_kv') {
    $image = wp_get_attachment_by_post_name( $media_name );
    $url = idCRM::$IDCRM_URL . 'templates/images/' . $media_name . '.jpg';
    $attach_id = 0;
    if (empty($image)) {
        $tmp = download_url( $url );
        $file_array = array(
            'name'     => basename( $url ),
            'tmp_name' => $tmp,
            'error'    => 0,
            'size'     => filesize( $tmp ),
        );
        if ( get_post_status( $post_id ) !== false ) {
            $attach_id = media_handle_sideload( $file_array, $post_id, '', ['post_title' => 'logo_idresult_kv'] );
        }
        @unlink( $tmp );
    } else {
        $attach_id = $image->ID;
    }
    return $attach_id;
}

?>
