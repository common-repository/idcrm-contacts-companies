<?php

namespace idcrm\includes\actions;

use idcrm\idCRM;
use idcrm\includes\actions\idCRMActionLanguage;

define( 'IDCRM_POST_TYPES', [
	'company' => esc_html__( 'Company', idCRMActionLanguage::TEXTDOMAIN ),
	'user_contact' => esc_html__( 'Contact', idCRMActionLanguage::TEXTDOMAIN ),
	'contact_event' => esc_html__( 'Event', idCRMActionLanguage::TEXTDOMAIN ),
	'idcrm_deal' => esc_html__( 'Deal', idCRMActionLanguage::TEXTDOMAIN ),
	'idcrm_document' => esc_html__( 'Document', idCRMActionLanguage::TEXTDOMAIN ),
	'idcrm_comments' => esc_html__( 'Comments', idCRMActionLanguage::TEXTDOMAIN ),
] );

if ( ! class_exists( '\idcrm\includes\actions\idCRMActionSearch' ) ) {
    class idCRMActionSearch {

		public static function register() {
      $handler = new self();

			if (!is_admin()) {
				add_action( 'pre_get_posts', array($handler, 'filter_main_search_query')  , 21 );
				add_filter( 'posts_search', array($handler, 'filter_posts_search') , 21, 2 );
				add_filter( 'posts_where',  array($handler, 'filter_posts_where') , 21, 2 );
			}

    }

    public function filter_main_search_query( $query ) {

        if ( !$query->is_main_query() || !strlen( $query->get( 's' ) )) {
            return;
        }

        $meta_query = (array) $query->get( 'meta_query' );

        $custom_fields = array(
          'idcrm_company_inn',
          'idcrm_company_kpp',
          'idcrm_company_ogrn',
          'idcrm_company_facebook',
          'idcrm_company_twitter',
          'idcrm_company_website',
          'idcrm_company_youtube',
          'idcrm_contact_email',
          'idcrm_contact_facebook',
          'idcrm_contact_phone',
          'idcrm_contact_position',
          'idcrm_contact_twitter',
          'idcrm_contact_website',
          'idcrm_contact_youtube',
        );

        $meta_query2   = array( 'relation' => 'OR' );

				$s = $query->get( 's' );
        $search_from_plugin = isset($_GET['search_type']) ? $_GET['search_type'] : '';

				if ('idcrm' == $search_from_plugin) {

					foreach ( $custom_fields as $meta_key ) {
							$meta_query2[] = array(
									'key'     => $meta_key,
									'value'   => $s,
									'compare' => 'LIKE',
							);
					}

					$meta_query[] = $meta_query2;
					$query->set( 'meta_query', $meta_query );
					$query->set( 'post_type', ['user_contact', 'company', 'idcrm_deal'] );
					$query->set( '_search_OR', true );

				} else {

					$exclude_post_types = [
						'idcrm_comments',
						'user_contact',
						'company',
						'contact_event',
						'idcrm_deal',
						'idcrm_document',
						'idcrm_task',
						'idcrm_project',
					];
					$query->set('post_type', array_diff(get_post_types(), $exclude_post_types));

				}


    }

    public function filter_posts_search( $search, $query ) {
        if ( $query->get( '_search_OR' ) ) {
            $query->set( '_search_SQL', $search );
            $search = '';
        }

        return $search;
    }

    public function filter_posts_where( $where, $query ) {
        if ( $query->get( '_search_OR' ) &&
            $search = $query->get( '_search_SQL' )
        ) {
            global $wpdb;

            $clauses = $query->meta_query->get_sql( 'post', $wpdb->posts, 'ID', $query );

            if ( ! empty( $clauses['where'] ) ) {
                $where2 = "( 1 $search ) OR ( 1 {$clauses['where']} )";
                $where  = str_replace( $clauses['where'], " AND ( $where2 )", $where );

                $query->set( '_search_SQL', false );
                $query->set( '_search_OR', false );
            }
        }

        return $where;
    }

	}
}

?>
