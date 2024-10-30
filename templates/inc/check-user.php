<?php
use idcrm\includes\actions\idCRMActionLanguage;

function is_user_role( $role, $user_id = null ) {
  $user = is_numeric( $user_id ) ? get_userdata( $user_id ) : wp_get_current_user();
  if ( !$user )
  return false;
  return in_array( $role, (array) $user->roles );
}

$user = wp_get_current_user();
$idcrm_team_roles = get_user_meta( get_current_user_id(), 'idcrm_team_roles', true );
$idcrm_permissions = unserialize(get_user_meta($user->ID, 'idcrm_permissions', true) ?: 'a:0:{}');
$all_roles = apply_filters( 'idcrm_get_all_roles', [] );
$current_plugin = idCRMActionLanguage::TEXTDOMAIN;

if (!is_user_logged_in()) {
  wp_safe_redirect( home_url() . '/' );
  exit;
}

if (!empty($idcrm_permissions)) {

  if (isset($idcrm_permissions[$current_plugin]) && $idcrm_permissions[$current_plugin] === 'yes' || is_user_role( 'administrator', $user->ID )) {

  } else {
    if (post_type_exists('idcrm_task') && $idcrm_team_roles == 'crm_client' || post_type_exists('idcrm_task') && $idcrm_team_roles == 'crm_team') {
      if (!isset($_GET['crm-notifications']) && !isset($_GET['crm-profile'])) {
  			wp_redirect( get_post_type_archive_link('idcrm_task'));
        exit;
      }
    } else {
      if (!isset($_GET['crm-notifications']) && !isset($_GET['crm-profile'])) {
        wp_safe_redirect( home_url() . '/' );
        exit;
      }
    }


  }

} else if ($idcrm_team_roles) {

  if (isset($all_roles[$idcrm_team_roles][$current_plugin]) && $all_roles[$idcrm_team_roles][$current_plugin] === 'yes' || is_user_role( 'administrator', $user->ID )) {

  } else {
    if (post_type_exists('idcrm_task') && $idcrm_team_roles == 'crm_client' || post_type_exists('idcrm_task') && $idcrm_team_roles == 'crm_team') {
      if (!isset($_GET['crm-notifications']) && !isset($_GET['crm-profile'])) {
  			wp_redirect( get_post_type_archive_link('idcrm_task'));
        exit;
      }
    } else {
      if (!isset($_GET['crm-notifications']) && !isset($_GET['crm-profile'])) {
        wp_safe_redirect( home_url() . '/' );
        exit;
      }
    }

  }

} else {
  if ( is_user_role( 'crm_support', $user->ID ) || is_user_role( 'crm_manager', $user->ID ) || is_user_role( 'administrator', $user->ID ) ) {

  } else {
    if (!isset($_GET['crm-notifications']) && !isset($_GET['crm-profile'])) {
      wp_safe_redirect( home_url() . '/' );
      exit;
    }
  }
}
