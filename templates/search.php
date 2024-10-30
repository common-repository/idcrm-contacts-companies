<?php
require 'inc/check-user.php';
require 'inc/header.php';
require 'inc/sidebar.php';

use idcrm\idCRM;
use idcrm\includes\actions\idCRMActionLanguage;
use idcrm\includes\api\idCRMApiContact;

$search_query = get_search_query();
$columns_control = is_plugin_active( 'idcrm-contacts-companies-pro/idcrm-contacts-companies-pro.php' ) ? 'data-show-columns="true" data-cookie="true" data-cookie-id-table="search"' : '';
?>

<div class="page-wrapper">
	<?php require 'inc/breadcrumbs.php'; ?>

	<div class="container-fluid">

		<div class="row">
			<div class="col-12">
				<div class="card">
					<div class="card-body">

						<h1 class="card-title">
							<?php esc_html_e( 'Search results: ', idCRMActionLanguage::TEXTDOMAIN );
							if (isset($_GET["s"]) && !empty($_GET["s"])) {
								echo stripslashes(esc_html($_GET["s"]));
							}	?>
						</h1>

						<div class="input-group col-sm-12 col-md-6 col-lg-4" style="margin-top: 25px;">

							<?php
							$current_user_id = get_current_user_id();
							$author = is_super_admin( $current_user_id ) ? "" : $current_user_id;

							// global $wp_query;
							//
							// $page_number = isset( $_POST['select_page_number'] ) ? htmlspecialchars( $_POST['select_page_number'] ) : 10;
							// $paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
							//
							// $wp_query->set( 'posts_per_page', $page_number );
							// $wp_query->set( 'paged', $paged );
							// $wp_query->set( 'author', $author );
							// $wp_query->set( 'post_type', ['user_contact', 'company', 'idcrm_deal'] );

								// query_posts( $query_string . '&paged=' . $paged . '&posts_per_page=' . $page_number . '&author=' . $author );
							?>

						</div>

					</div>

						<div class="table-responsive">
							<table
							id="search-table"
							data-pagination="true"
							data-page-list="[10, 25, 50, 100, All]"
							data-locale="<?php echo str_replace('_', '-', get_locale()); ?>"
							class="table last-column-width customize-table table-hover mb-0 v-middle" <?php echo $columns_control; ?>>
								<thead class="table-light">
									<tr class="border-top-0">
										<th data-field="id" class="border-top-0 table-50" data-sortable="true">ID</th>
										<th data-field="title" class="border-top-0" data-sortable="true"><?php esc_html_e( 'Title', idCRMActionLanguage::TEXTDOMAIN ); ?></th>
										<th data-field="date" class="border-top-0" data-sortable="true"><?php esc_html_e( 'Date', idCRMActionLanguage::TEXTDOMAIN ); ?></th>
										<th data-field="type" class="border-top-0" data-sortable="true"><?php esc_html_e( 'Type', idCRMActionLanguage::TEXTDOMAIN ); ?></th>
										<th data-field="manager" class="border-top-0"><?php esc_html_e( 'Manager', idCRMActionLanguage::TEXTDOMAIN ); ?></th>
										<th data-field="settings" class="border-top-0" data-switchable="false"><i class="icon-settings"></i></th>
									</tr>
								</thead>
							</table>

							<script>

								var $table = $('#search-table');
								$(function() {
								var data = <?php echo idCRMApiContact::idcrmGetTableSearch($search_query, $author); ?>;
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
							} else {
								// echo '<div><p>' . esc_html__( 'No results', idCRMActionLanguage::TEXTDOMAIN ) . '</p></div>';
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
