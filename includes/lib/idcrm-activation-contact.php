<?php

namespace idcrm\includes\lib;

require_once('idcrm-activation-post.php');
require_once('idcrm-activation-media.php');
require_once('idcrm-activation-taxonomy.php');

function create_first_contact() {
	$message = array('<pre>');
	array_push($message, 'create_first_contact');
	$first_contact = 0;
  	$contacts = get_page_by_path('id-crm-support', OBJECT, 'user_contact');
	array_push($message, '$contacts: ' . print_r($contacts, true));
	if (empty($contacts))  {
		$idcrm_contact_company = '';
		$companies = get_page_by_path('id-result-company', OBJECT, 'company');
		array_push($message, '$companies: ' . print_r($companies, true));
		if (!empty($companies)) {
			$idcrm_contact_company = $companies->ID;
		}
		array_push($message, '$idcrm_contact_company: ' . print_r($idcrm_contact_company, true));
		$contact_data = array(
			'post_title' => 'id:CRM Support',
			'post_content' => 'id:CRM Support',
			'post_name' => 'id-crm-support',
			'post_type' => 'user_contact',
			'post_status' => 'publish',
			'post_author' => get_current_user_id(),
			'meta_input' => array(
				'idcrm_contact_company'  => $idcrm_contact_company,
				'idcrm_contact_email'    => 'support@idresult.ru',
				// 'idcrm_contact_facebook' => 'idResult',
				// 'idcrm_contact_twitter'  => 'idResult',
				'idcrm_contact_youtube'  => 'UC-Xxdh_pi4QLeePnI2xnkgQ',
				'idcrm_contact_website'  => 'https://idresult.ru/?utm_source=idcrm',
				'idcrm_contact_lead_exclude' => 1
			),
		);
		$first_contact = wp_insert_post( $contact_data );
	} else {
		$first_contact = $contacts->ID;
	}
	array_push($message, '$first_contact: ' . print_r($first_contact, true));
	if ($first_contact != 0) {
		update_post_meta( $first_contact, 'idcrm_contact_user_id', get_current_user_id() );
		$first_term  = get_leads_id();
		array_push($message, '$first_term: ' . print_r($first_term, true));
		$has_term = has_term($first_term, 'user_status', $first_contact);
		array_push($message, '$has_term: ' . ($has_term ? 'true' : 'false'));
		if (!has_term($first_term, 'user_status', $first_contact)) {
			$result = wp_set_object_terms($first_contact, $first_term, 'user_status');
			array_push($message, '$result: ' . print_r($result, true));
		}
		$attach_id = set_attachment($first_contact, 'logo_idresult_kv');
		array_push($message, '$attach_id: ' . print_r($attach_id, true));
		if ($attach_id != 0) {
			set_post_thumbnail( $first_contact, $attach_id );
		}
	}
	array_push($message, '</pre>');
	//wp_die( implode('<br />', $message));
}

?>
