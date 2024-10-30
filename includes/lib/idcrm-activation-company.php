<?php

namespace idcrm\includes\lib;

require_once('idcrm-activation-post.php');
require_once('idcrm-activation-media.php');
require_once('idcrm-activation-taxonomy.php');

function create_first_company() {
	//$message = array();
	$first_company = 0;
    $companies = get_page_by_path('id-result-company', OBJECT, 'company');
	if (empty($companies)) {
        $company_data = array(
            'post_title'     => 'id:Result',
            'post_content'     => 'id:Result company',
            'post_name'     => 'id-result-company',
            'post_type'      => 'company',
            'post_status'    => 'publish',
            'meta_input'     => array(
                // 'idcrm_contact_facebook' => 'idResult',
                // 'idcrm_contact_twitter'  => 'idResult',
                'idcrm_contact_youtube'  => 'UC-Xxdh_pi4QLeePnI2xnkgQ',
                'idcrm_contact_website'  => 'https://idresult.ru/?utm_source=idcrm',
            ),
        );
		$first_company = wp_insert_post( $company_data );
	} else {
		$first_company = $companies->ID;
	}
	if ($first_company != 0) {
		update_post_meta( $first_company, 'idcrm_contact_user_id', get_current_user_id() );
		$first_term  = get_contractors_id();
		//array_push($message, '$first_term: ' . print_r($first_term, true));
		//array_push($message, '$has_term: ' . (has_term($first_term, 'comp_status', $first_company) ? 'true' : 'false'));
		if (!has_term($first_term, 'comp_status', $first_company)) {
			$result = wp_set_object_terms($first_company, $first_term, 'comp_status');
			//array_push($message, '$result: ' . print_r($result, true));
		}
		$attach_id = set_attachment($first_company, 'logo_idresult_kv');
		if ($attach_id != 0) {
			set_post_thumbnail( $first_company, $attach_id );
		}
	}
	//echo '<pre>' . implode('<br />', $message) . '</pre>';
	//die('<pre>' . implode('<br />', $message) . '</pre>');
}

?>
