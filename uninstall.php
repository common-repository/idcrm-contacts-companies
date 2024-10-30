<?php
/**
 *
 * @since      1.0.0
 * @package    idcrm-contacts
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// For delelting id:CRM data uncomment those strings.

// // Delete companies then plugin is uninstall.
// $companies = get_posts(
// 	array(
// 		'post_type'   => 'company',
// 		'numberposts' => -1,
// 	)
// );
// foreach ( $companies as $company ) {
// 	wp_delete_post( $company->ID, true );
// }

// // Delete comp_status taxonomy
// function comp_status_uninstall() {
//     unregister_taxonomy_for_object_type('comp_status', 'company');
//     unregister_taxonomy('comp_status');
//     flush_rewrite_rules();
// }
// comp_status_uninstall();

// // Delete contacts then plugin is uninstall.
// $contacts = get_posts(
// 	array(
// 		'post_type'   => 'user_contact',
// 		'numberposts' => -1,
// 	)
// );
// foreach ( $contacts as $contact ) {
// 	wp_delete_post( $contact->ID, true );
// }

// // Delete user_status taxonomy
// function user_status_uninstall() {
//     unregister_taxonomy_for_object_type('user_status', 'user_contact');
//     unregister_taxonomy('user_status');
//     flush_rewrite_rules();
// }
// user_status_uninstall();

// // Delete events then plugin is uninstall.
// $contacts = get_posts(
// 	array(
// 		'post_type'   => 'contact_event',
// 		'numberposts' => -1,
// 	)
// );
// foreach ( $contacts as $contact ) {
// 	wp_delete_post( $contact->ID, true );
// }

// // Delete contact_events taxonomy
// function contact_events_uninstall() {
//     unregister_taxonomy_for_object_type('contact_events', 'contact_event');
//     unregister_taxonomy('contact_events');
//     flush_rewrite_rules();
// }
// user_status_uninstall();
