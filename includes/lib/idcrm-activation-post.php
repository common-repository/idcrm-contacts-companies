<?php

namespace idcrm\includes\lib;

/*function get_post_by($post_name, $post_type) {
	$args = array(
		'name' => $post_name,
		'post_type' => $post_type,
		'post_status' => 'publish'
	);
  	$contacts = get_posts($args);
}*/

function wp_get_attachment_by_post_name( $post_name ) {
	$result = null;
	$args           = array(
		'posts_per_page' => 1,
		'post_type'      => 'attachment',
		'name'           => trim( $post_name ),
	);
	//$get_attachment = new \WP_Query( $args );
	$get_attachment = get_posts( $args );
	if ( !empty($get_attachment) ) {
		$result = $get_attachment[0];
	}
	return $result;
}

?>