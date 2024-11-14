<?php if($_settings->chk_flashdata('success')): ?>
<script>
	alert_toast("<?php echo $_settings->flashdata('success') ?>",'success')
</script>
<?php endif; ?>
<?php if($_settings->chk_flashdata('error')): ?>
<script>
	alert_toast("<?php echo $_settings->flashdata('error') ?>",'error')
</script>
<?php endif; ?>
<div class="card card-outline card-primary">
	<div class="card-header">
		<h3 class="card-title">List of Bookings</h3>
	</div>
	<div class="card-body">
		<div class="container-fluid">
			<table class="table table-bordered table-striped">
				<colgroup>
					<col width="5%">
					<col width="10%">
					<col width="15%">
					<col width="20%">
					<col width="20%"> <!-- New column for Client Address -->
					<col width="10%"> <!-- New column for Type of Space -->
					<col width="10%"> <!-- New column for Category -->
					<col width="15%"> <!-- New column for Meeting Schedule -->
					<col width="10%"> <!-- New column for Status -->
					<col width="10%"> <!-- New column for Action -->
				</colgroup>
				<thead>
					<tr class="bg-navy text-white">
						<th>#</th>
						<th>Date Booked</th>
						<th>Rent Schedule</th>
						<th>Client</th>
						<th>Client Address</th> <!-- New header for Address -->
						<th>Type of Space</th>
						<th>Category</th> <!-- New header for Category -->
						<th>Meeting Schedule</th> <!-- New header for Meeting Schedule -->
						<th>Status</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					<?php 
					$i = 1;
					// Updated query to join rent_list, space_list, and categories tables and fetch address
					$qry = $conn->query("SELECT r.*, CONCAT(c.firstname, ' ', c.lastname) as client, 
											s.space_name, cat.category, r.meeting_schedule, c.address 
											FROM `rent_list` r 
											INNER JOIN clients c ON c.id = r.client_id 
											INNER JOIN space_list s ON s.id = r.space_id 
											INNER JOIN categories cat ON cat.id = s.category_id 
											ORDER BY unix_timestamp(r.date_created) DESC");
					while($row = $qry->fetch_assoc()): 
					?>
						<tr>
							<td class="text-center"><?php echo $i++; ?></td>
							<td><?php echo date("Y-m-d H:i", strtotime($row['date_created'])) ?></td>
							<td>
								<small><span class="text-muted">Start Date:</span> <?php echo date("Y-m-d", strtotime($row['date_start'])) ?></small><br>
								<small><span class="text-muted">End Date: </span> <?php echo date("Y-m-d", strtotime($row['date_end'])) ?></small>
							</td>
							<td><?php echo $row['client'] ?></td>
							<td><?php echo $row['address']; ?></td> <!-- New cell for Client Address -->
							<td><?php echo $row['space_name']; ?></td> <!-- New cell for Type of Space -->
							<td><?php echo $row['category']; ?></td> <!-- New cell for Category -->
							<td><?php echo "Until " . date("F j, Y", strtotime($row['meeting_schedule'])); ?></td> <!-- New cell for Meeting Schedule (formatted) -->
							<td class="text-center">
                                <?php if($row['status'] == 0): ?>
                                    <span class="badge badge-light">Pending</span>
                                <?php elseif($row['status'] == 1): ?>
                                    <span class="badge badge-primary">Confirmed</span>
								<?php elseif($row['status'] == 2): ?>
                                    <span class="badge badge-danger">Cancelled</span>
								<?php elseif($row['status'] == 3): ?>
                                    <span class="badge badge-success">Done</span>
                                <?php else: ?>
                                    <span class="badge badge-danger">Cancelled</span>
                                <?php endif; ?>
                            </td>
							
							<td align="center">
								<button type="button" class="btn btn-flat btn-default btn-sm dropdown-toggle dropdown-icon" data-toggle="dropdown">
									Action
									<span class="sr-only">Toggle Dropdown</span>
								</button>
								<div class="dropdown-menu" role="menu">
									<a class="dropdown-item view_data" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>"><span class="fa fa-th-list text-dark"></span> View Details</a>
									<div class="dropdown-divider"></div>
									<a class="dropdown-item view_payments" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>"><span class="fa fa-th-list text-dark"></span> View Payments</a> <!-- New View Payments action -->
									<div class="dropdown-divider"></div>
									<a class="dropdown-item delete_data" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>"><span class="fa fa-trash text-danger"></span> Delete</a>
								</div>
							</td>
						</tr>
					<?php endwhile; ?>
				</tbody>
			</table>
		</div>
	</div>
</div>

<script>
	$(document).ready(function(){
		$('.delete_data').click(function(){
			_conf("Are you sure to delete this booking permanently?","delete_booking",[$(this).attr('data-id')])
		})
		$('.view_data').click(function(){
			uni_modal('Booking Details','bookings/view_booking.php?id='+$(this).attr('data-id'),'mid-large')
		})
		$('.view_payments').click(function(){
			// Open a modal or page to view payments
			uni_modal('View Payments', 'bookings/view_payments.php?id=' + $(this).attr('data-id'), 'mid-large');
		})
		$('.table').dataTable();
	})

	function delete_booking($id){
		start_loader();
		$.ajax({
			url:_base_url_+"classes/Master.php?f=delete_booking",
			method:"POST",
			data:{id: $id},
			dataType:"json",
			error: err => {
				console.log(err);
				alert_toast("An error occurred.", 'error');
				end_loader();
			},
			success:function(resp) {
				if(typeof resp == 'object' && resp.status == 'success') {
					location.reload();
				} else {
					alert_toast("An error occurred.", 'error');
					end_loader();
				}
			}
		})
	}
</script>
