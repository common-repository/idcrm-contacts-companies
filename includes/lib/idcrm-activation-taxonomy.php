<?php

namespace idcrm\includes\lib;

use idcrm\includes\actions\idCRMActionLanguage;

function get_leads_id() {
	//$leads_array = get_term_by('name',esc_html__( 'Leads', idCRMActionLanguage::TEXTDOMAIN ),'comp_status');
	$leads_array = get_term_by('slug', 'user-contractors', 'user_status');
	return isset($leads_array->term_id) ? $leads_array->term_id : "";
}

function get_contractors_id() {
	//$leads_array = get_term_by('name',esc_html__( 'Contractors', idCRMActionLanguage::TEXTDOMAIN ),'user_status');
    //echo '<pre>taxonomy_exists: ' . (taxonomy_exists('comp_status') ? 'true' : 'false') . '</pre>';
    //echo '<pre>get_terms: ' . print_r(get_terms(['taxonomy' => 'comp_status']), true) . '</pre>';
	$leads_array = get_term_by('slug', 'company-contractors', 'comp_status');
    //echo '<pre>$leads_array: ' . ($leads_array === false  ? 'false' : print_r($leads_array, true)) . '</pre>';
    //die();
	return isset($leads_array->term_id) ? $leads_array->term_id : "";
}

?>