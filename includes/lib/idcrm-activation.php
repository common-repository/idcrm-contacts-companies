<?php

namespace idcrm\includes\lib;

function add_crm_roles() {

	if ( get_role('lead') ) {
		remove_role('lead');
	}

	add_role(
		'lead',
		'CRM Lead',
		array(
			'read'    => true,
			'level_0' => true,
		)
	);

	// Update admin capabilities.
	$role_admin = get_role( 'administrator' );

	$capabilities = array(
    'read_user_contacts',
    'read_private_user_contacts',
    'create_user_contacts',
    'edit_user_contacts',
    'edit_others_user_contacts',
    'edit_published_user_contacts',
    'publish_user_contacts',
    'delete_user_contacts',
    'create_companies',
		'read_companies',
		'read_private_companies',
		'edit_companies',
		'edit_others_companies',
		'publish_companies',
		'delete_companies',
		'edit_user_source',
		'manage_user_source',
		'delete_user_source',
		'assign_user_source',
		'edit_user_status',
		'edit_company_status',
		'delete_company',
		'edit_company',
		'read_company'

	);

	foreach ( $capabilities as $cap ) {
    $role_admin->add_cap( $cap );
	}

}

?>
