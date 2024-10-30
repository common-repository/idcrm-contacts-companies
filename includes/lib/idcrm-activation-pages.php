<?php

namespace idcrm\includes\lib;

use idcrm\includes\actions\idCRMActionLanguage;

class idCRMActivationPages {

	public static function register() {
		$handler = new self();
		add_action( 'idcrmpro_pages', array($handler, 'create_pages' ) );
	}

	protected static function create($slug, $title, $textdomain) {
		$page = get_page_by_path($slug, OBJECT);
		$id = 0;
		if ( empty( $page ) ) {
			$mailbox_page = [
				'post_type'   => 'page',
				'post_title'  => esc_html__( $title, $textdomain ),
				'post_name'   => $slug,
				'post_status' => 'publish',
			];

			$id = wp_insert_post( $mailbox_page );
		} else {
			$id = $page->ID;
		}

		if ( !is_wp_error($id) && $id != 0 ) {
			update_post_meta( $id, '_wp_page_template', str_replace( ':template', $slug, 'templates/:template.php' ) );
		}
	}

	public static function create_pages() {
		foreach ( apply_filters( 'idcrm_pages', [ 'crm' => [ 'name' => 'CRM Login', 'textdomain' => idCRMActionLanguage::TEXTDOMAIN ] ] ) as $slug => $name ) {
			self::create($slug, $name['name'], $name['textdomain']);
		}
	}
}


?>
