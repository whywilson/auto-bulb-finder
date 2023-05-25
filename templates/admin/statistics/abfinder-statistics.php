<?php

use ABFinder\Helper\ABFinder_Database;

function abfinfer_top_vehicles()
{
	$abfinderDb = new ABFinder_Database();
	$topVehicles = $abfinderDb->get_top_vehicles();
?>
	<div class="abfinder-statistics">
		<div class="abfinder-statistics-top-vehicles">
			<h2>Top Vehicles</h2>
			<table class="widefat fixed striped posts">
				<thead>
					<tr>
						<th scope="col" id="abfinder-statistics-vehicle" class="manage-column column-abfinder-statistics-vehicle column-primary">Vehicle</th>
						<th scope="col" id="abfinder-statistics-views" class="manage-column column-abfinder-statistics-views">Views</th>
						<th scope="col" id="abfinder-statistics-updated-at" class="manage-column column-abfinder-statistics-updated-at">Last Queried</th>
					</tr>
				</thead>
				<tbody id="the-list">
					<?php
					if (count($topVehicles) > 0) {
						foreach ($topVehicles as $vehicle) {
					?>
							<tr id="post-1" class="iedit author-self level-0 post-1 type-post status-publish format-standard hentry category-uncategorized">
								<td class="title column-title has-row-actions column-primary page-title" data-colname="Title">
									<strong>
										<?php
										$vehicleName = $vehicle['year'] . ' ' . $vehicle['make'] . ' ' . $vehicle['model'] . ' ' . $vehicle['submodel'] . ' ' . $vehicle['bodytype'] . ' ' . $vehicle['qualifier'];
										$vehicleName = preg_replace('/\s+/', ' ', $vehicleName);
										echo $vehicleName; ?>
									</strong>
								</td>
								<td class="counts column-counts" data-colname="Count">
									<?php echo $vehicle['count']; ?>
								</td>
								<td class="queried-at column-queried-at" data-colname="Queried At">
									<?php echo $vehicle['time']; ?>
								</td>
							</tr>
						<?php
						}
					} else {
						?>
						<tr id="post-1" class="iedit author-self level-0 post-1 type-post status
			-publish format-standard hentry category-uncategorized">
							<td class="title column-title has-row-actions column-primary page-title" data-colname="Title">
								<strong>
									No vehicles found
								</strong>
							</td>
							<td class="views column-views" data-colname="Views">
							</td>
						</tr>
					<?php
					}
					?>
				</tbody>
			</table>
		</div>
	</div>
<?php

}


function abfinder_recently_queried_vehicles()
{
	$abfinderDb = new ABFinder_Database();
	$vehicleCountByDate = $abfinderDb->get_vehicle_count_by_day();
?>

	<!-- show the line chart with vehicle query count and date -->
	<div class="abfinder-statistics">
		<div class="abfinder-statistics-recently-queried-vehicles">
			<h2>Recently Queries</h2>
			<table class="widefat fixed striped posts">
				<thead>
					<tr>
						<th scope="col" id="abfinder-statistics-date" class="manage-column column-abfinder-statistics-date column-primary">Date</th>
						<th scope="col" id="abfinder-statistics-views" class="manage-column column-abfinder-statistics-views">Views</th>
					</tr>
				</thead>
				<tbody id="the-list">
					<?php
					if (count($vehicleCountByDate) > 0) {
						foreach ($vehicleCountByDate as $vehicleCount) {
					?>
							<tr id="post-1" class="iedit author-self level-0 post-1 type-post status-publish format-standard hentry category-uncategorized">
								<td class="title column-title has-row-actions column-primary page-title" data-colname="Title">
									<strong>
										<?php echo $vehicleCount['date']; ?>
									</strong>
								</td>
								<td class="counts column-counts" data-colname="Count">
									<?php echo $vehicleCount['count']; ?>
								</td>
							</tr>
						<?php
						}
					} else {
						?>
						<tr id="post-1" class="iedit author-self level-0 post-1 type-post status
			-publish format-standard hentry category-uncategorized">
							<td class="title column-title has-row-actions column-primary page-title" data-colname="Title">
								<strong>
									No vehicles found
								</strong>
							</td>
							<td class="views column-views" data-colname="Views">
							</td>
						</tr>
					<?php
					}
					?>
				</tbody>
			</table>
		</div>
<?php
}
?>

<div class="abfinder-statistics">
	<h1 class="wp-heading-inline">Statistics</h1>
	<div class="wrap">
		<?php
		abfinfer_top_vehicles();
		abfinder_recently_queried_vehicles();
		?>
	</div>
</div>