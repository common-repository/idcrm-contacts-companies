<?php

/**
 * Template Name: idCRM Contacts
 * @author    id:Result
 * @link      https://isresult.ru
 * @copyright 2021 Vladimir Shlykov
 * @license   GPL-2.0-or-later
 * @version   1.0.0
 */

require 'inc/check-user.php';
require 'inc/header.php';
require 'inc/sidebar.php';

use idcrm\idCRM;
use idcrm\includes\actions\idCRMActionLanguage;
use idcrm\includes\api\idCRMApi;

$current_user_id = get_current_user_id();
$user_meta = get_user_meta($current_user_id);
$idcrm_team_user_id = get_user_meta( $current_user_id, 'idcrm_team_user_id', true );

$first_name = $user_meta['first_name'][0];
$last_name = $user_meta['last_name'][0];

$user_full_name = $first_name && $last_name ? $first_name . ' ' . $last_name : $first_name;
// $user_full_name = $idcrm_team_user_id ? get_the_title($idcrm_team_user_id) : $user_full_name;

$userimg = get_user_meta( $current_user_id, 'userimg', true );
$default_image = idCRM::$IDCRM_URL . 'templates/images/no-user.jpg';

$user_avatar = $userimg ? '<img src="' . esc_html( $userimg ) . '" class="rounded-circle editable wp-post-image object-fit-cover profile-avatar" width="120" height="120" />' : '';
$user_avatar = $idcrm_team_user_id && has_post_thumbnail($idcrm_team_user_id) ? get_the_post_thumbnail( $idcrm_team_user_id, array( 120, 120 ), array( 'class' => 'rounded-circle object-fit-cover editable' ) ) : $user_avatar;
$user_avatar = $user_avatar ?: '<img src="' . esc_html( $default_image ) . '" class="rounded-circle editable wp-post-image object-fit-cover profile-avatar" width="120" height="120" />';

$email = isset($user_meta['email'][0]) ? $user_meta['email'][0] : '';
$email = $idcrm_team_user_id && get_post_meta( $idcrm_team_user_id, 'idcrm_team_email', true ) ? get_post_meta( $idcrm_team_user_id, 'idcrm_team_email', true ) : $email;

?>

<div class="page-wrapper" id="user-profile">

	<div class="container-fluid">

<?php
//if (idCRMTeam::IDCRM_TASKS_KEY_ACTIVATED) {

// if ( is_super_admin( get_current_user_id() ) || $user->ID == $post->post_author ) {
	?>

	<div class="row">

		<div class="col-lg-12 col-xlg-12 col-md-12">
			<div class="card">
					<div class="card-body d-flex align-items-center flex-row flex-lg-row">

						<h2 class="card-title mb-0"><?php esc_html_e( 'My Profile', idCRMActionLanguage::TEXTDOMAIN ); ?></h2>

					</div>
			</div>

		</div>

		<div class="col-lg-6 col-xlg-6 col-md-6">
			<div class="card">
					<div class="card-body d-flex align-items-center flex-row flex-lg-row">

						<div class="avatar-image">
							<div class="avatar-image-placeholder">
								<i data-feather="camera" class="feather-icon"></i>
							</div>
							<?php echo $user_avatar; ?>
						</div>

						<span class="ms-3 fw-normal">
							<h2 class="card-title mt-2"><?php echo esc_html($user_full_name); ?></h2>

							<h5 class="card-subtitle">
								<?php echo esc_html($email); ?>
							</h5>
						</span>

					</div>


					<div class="card-body d-flex align-items-center flex-row flex-lg-row gap-4">
						<input type="text" class="form-control" name="first_name" id="first_name" maxlength="30" placeholder="<?php esc_html_e( 'First Name', idCRMActionLanguage::TEXTDOMAIN ); ?>" value="<?php echo $first_name; ?>" required tabindex="1">
						<input type="text" class="form-control" name="last_name" id="last_name" maxlength="30" placeholder="<?php esc_html_e( 'Last Name', idCRMActionLanguage::TEXTDOMAIN ); ?>" value="<?php echo $last_name; ?>" required tabindex="2">
					</div>

					<input type="button" name="update_profile" value="<?php esc_html_e( 'Update', idCRMActionLanguage::TEXTDOMAIN ); ?>" class="update_profile btn btn-primary btn-rounded mt-0 ms-auto me-3 mb-3">

			</div>

		</div>

		<?php if (idCRMApi::is_accessable( $user->ID, 'idcrm-contacts-companies-pro')) { ?>
			<?php if ( is_plugin_active( 'idcrm-contacts-companies-pro/idcrm-contacts-companies-pro.php' ) ) { ?>
				<?php include WP_PLUGIN_DIR . '/idcrm-contacts-companies-pro/templates/inc/profile-mail.php'; ?>
			<?php } ?>
		<?php } ?>

</div>
</div>

	<?php
// } else {
//
// 	echo esc_html__( 'You do not have sufficient permissions to access this page.', idCRMActionLanguage::TEXTDOMAIN );
//
// }
// } else {
// 		idCRMTeamSettings::activate_notice();
// 	}
	?>

	</div>
</div>
		<?php require 'inc/footer.php'; ?>
		<?php wp_footer(); ?>
	</body>
</html>
