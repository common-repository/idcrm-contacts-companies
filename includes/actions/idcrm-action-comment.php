<?php

namespace idcrm\includes\actions;

use idcrm\idCRM;

if ( ! class_exists( '\idcrm\includes\actions\idCRMActionComment' ) ) {
    class idCRMActionComment {
		public static function register() {
      $handler = new self();
			add_action( 'comment_post', array($handler, 'post_type_comment_meta_data') );
      add_filter( 'parse_comment_query', array($handler, 'hide_idcrm_custom_post_comments') );
    }

		/** Add for contacts and companies comments meta value with post type */
		function post_type_comment_meta_data( $comment_id ) {
			if ( isset( $_POST['post_type'] ) ) {
				$post_type = sanitize_text_field( $_POST['post_type'] );
				add_comment_meta( $comment_id, 'post_type', $post_type );
			}
		}

		/** Hide contacts and companies comments */
		function hide_idcrm_custom_post_comments( $comments ) {
			// Display filtered comments.
			if ( ! function_exists( 'get_current_screen' ) ) {
				require_once ABSPATH . '/wp-admin/includes/screen.php';
			}
      
			$screen = get_current_screen();
			if ( ! empty( $screen ) ) {
				if ( 'company' === $screen->id ) {
					$comments->query_vars['meta_query'] = array(
						array(
							'key'   => 'post_type',
							'value' => 'company',
						),
					);
				} elseif ( 'user_contact' === $screen->id ) {
					$comments->query_vars['meta_query'] = array(
						array(
							'key'   => 'post_type',
							'value' => 'user_contact',
						),
					);
				} else {
					$comments->query_vars['meta_query'] = array(
						array(
							'key'     => 'post_type',
							'compare' => 'NOT EXISTS',
						),
					);
				}
			} elseif ( is_singular() ) {
				$comments->query_vars['meta_query'] = array(
					array(
						'key'     => 'post_type',
						'compare' => 'NOT EXISTS',
					),
				);
			}
		}
	}
}

?>
