<?php
require 'inc/check-user.php';
require 'inc/header.php';
require 'inc/sidebar.php';

use idcrm\idCRM;
use idcrm\includes\actions\idCRMActionLanguage;
use idcrm\includes\api\idCRMApiContact;

$columns_control = is_plugin_active( 'idcrm-contacts-companies-pro/idcrm-contacts-companies-pro.php' ) ? 'data-show-columns="true" data-cookie="true" data-cookie-id-table="notifications"' : '';
?>

<div class="page-wrapper">

	<div class="container-fluid">

		<div class="row">
			<div class="col-12">
				<div class="card">
					<div class="card-body d-flex align-items-center">

						<h1 class="card-title">
						<?php
						$tax_title  = single_term_title( '', false ); // Taxonomy title.
						$page_title = wp_title( '', false ); // Archive title.
						$current_user_id = get_current_user_id();

						$author = is_super_admin( $current_user_id ) ? "" : $current_user_id;
						?>

						<?php esc_html_e( 'Notifications', idCRMActionLanguage::TEXTDOMAIN ); ?>
						</h1>

					</div>

						<div class="table-responsive">
							<table
							id="notifications-table"
							data-pagination="true"
							data-sort-name="date"
							data-sort-order="desc"
							data-page-list="[10, 25, 50, 100, All]"
							data-locale="<?php echo str_replace('_', '-', get_locale()); ?>"
							class="table last-column-width customize-table table-hover mb-0 v-middle" <?php echo $columns_control; ?>>
								<thead class="table-light">
									<tr class="border-top-0">
										<th data-field="date" data-width="200" class="border-top-0" data-sortable="true"><?php esc_html_e( 'Date', idCRMActionLanguage::TEXTDOMAIN ); ?></th>
										<th data-field="source" class="border-top-0" data-sortable="true"><?php esc_html_e( 'Source', idCRMActionLanguage::TEXTDOMAIN ); ?></th>
										<th data-field="text" class="border-top-0"><?php esc_html_e( 'Text', idCRMActionLanguage::TEXTDOMAIN ); ?></th>

									</tr>
								</thead>
							</table>

							<script>

								var $table = $('#notifications-table');
								$(function() {
								var data = <?php echo idCRMApiContact::idcrmGetTableNotifications(); ?>;
									$table.bootstrapTable({
										data: data,
										sortStable: true
									});
								});

								function getOrder() {
										return $table.bootstrapTable('getOptions').sortOrder
												=== 'asc' ? -1 : 1;
								}

								function numberSorter(a, b) {
										if (!a) return -1 * getOrder();
										if (!b) return 1 * getOrder();
										if (a < b) return -1;
										if (a > b) return 1;
										return 0;
								}

							</script>

							<?php if ( have_posts() ) {
								while ( have_posts() ) {
									the_post();
								}
							} ?>

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
