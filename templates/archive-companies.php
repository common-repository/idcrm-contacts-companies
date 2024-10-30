<?php

use idcrm\idCRM;
use idcrm\includes\actions\idCRMActionLanguage;
use idcrm\includes\api\idCRMApiCompany;

$queried_object = get_queried_object();
$tax_slug = isset($queried_object->taxonomy) ? $queried_object->slug : '';

$columns_control = is_plugin_active( 'idcrm-contacts-companies-pro/idcrm-contacts-companies-pro.php' ) ? 'data-show-columns="true" data-cookie="true" data-cookie-expire="1m" data-cookie-id-table="companies-' . $tax_slug .'"' : '';

/**
 * Template Archive fo Companies List.
 *
 * @author    id:Result
 * @link      https://isresult.ru
 * @copyright 2021 Vladimir Shlykov
 * @license   GPL-2.0-or-later
 * @version   1.0.0
 */
	require 'inc/check-user.php';
	require 'inc/header.php';
	require 'inc/sidebar.php';
?>

<div class="page-wrapper">
	<?php require 'inc/breadcrumbs.php'; ?>

	<div class="container-fluid">

	<div class="row">
		<div class="col-12">
			<div class="card">
				<div class="card-body d-flex align-items-center gap-3 justify-content-between justify-content-lg-start">
				<h1 class="card-title mb-0">
					<?php
						$tax_title  = single_term_title( '', false ); // Taxonomy title.
						$page_title = wp_title( '', false ); // Archive title.
						$current_user_id = get_current_user_id();

						$author = is_super_admin( $current_user_id ) ? "" : $current_user_id;

						echo esc_html(empty($tax_title) ? $page_title : $tax_title);
					?>
				</h1>

				<div class="ms-0">

				<?php
				if ( is_search() ) {

				} else { ?>

					<?php require 'inc/add-company.php'; ?>

				<?php } ?>
			</div>

			</div>
			<div class="table-responsive">
				<table
				id="companies-table"

				data-pagination="true"
				data-page-list="[10, 25, 50, 100, All]"
				data-locale="<?php echo str_replace('_', '-', get_locale()); ?>"
				class="table last-column-width customize-table table-hover mb-0 v-middle" <?php echo $columns_control; ?> >
					<thead class="table-light">
						<tr>
							<th data-field="id" class="border-top-0 table-50" data-sortable="true">ID</th>
							<th data-field="date" class="border-top-0" data-sortable="true"><?php esc_html_e( 'Date', idCRMActionLanguage::TEXTDOMAIN ); ?></th>
							<th data-field="title" class="border-top-0"><?php esc_html_e( 'Title', idCRMActionLanguage::TEXTDOMAIN ); ?></th>
							<th data-field="status" class="border-top-0"><?php esc_html_e( 'Status', idCRMActionLanguage::TEXTDOMAIN ); ?></th>
							<th data-field="manager" class="border-top-0"><?php esc_html_e( 'Manager', idCRMActionLanguage::TEXTDOMAIN ); ?></th>
							<th data-field="settings" class="border-top-0" data-switchable="false"><i class="icon-settings"></i></th>
						</tr>
					</thead>
				</table>

						<script>

							var $table = $('#companies-table');
							$(function() {
							var data = <?php echo idCRMApiCompany::idcrmGetTableCompanies(get_queried_object_id(), $author); ?>;
								$table.bootstrapTable({data: data});
							});

						</script>

							<?php

							if ( have_posts() ) {
								while ( have_posts() ) {
											the_post();
								}
							} else {
									echo '<div><p>' . esc_html__( 'No companies', idCRMActionLanguage::TEXTDOMAIN ) . '</p></div>';
							}  ?>


					</tbody>
				</table>
			</div>
		</div>

			</div>
		</div>

	</div>
</div>

<?php require 'inc/footer.php'; ?>

<?php wp_footer(); ?>
</body>
</html>
