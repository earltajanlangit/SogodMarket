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
		<div class="card-tools">
		<a href="javascript:void(0)" id="create_new" class="btn btn-flat btn-primary"><span class="fas fa-plus"></span>  Walk-in Application</a>
		</div>
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
				</colgroup>
				<thead>
					<tr class="bg-navy disabled">
						<th>#</th>
						<th>Date Booked</th>
						<th>Rent Schedule</th>
						<th>Client</th>
						<th>Client Address</th> <!-- New header for Address -->
						<th>Type of Space</th>
						<th>Category</th> <!-- New header for Category -->
						<th>Meeting Schedule</th> <!-- New header for Meeting Schedule -->
						<th>Status</th>
						
					</tr>
				</thead>
				<tbody>
    <?php 
    $i = 1;
    $qry = $conn->query("SELECT r.*, CONCAT(c.firstname, ' ', c.lastname) as client, 
                         s.space_name, cat.category, r.meeting_schedule,r.id as booking_id, c.address, c.id as client_id 
                         FROM `rent_list` r 
                         INNER JOIN clients c ON c.id = r.client_id 
                         INNER JOIN space_list s ON s.id = r.space_id 
                         INNER JOIN categories cat ON cat.id = s.category_id 
                         ORDER BY unix_timestamp(r.date_created) DESC");
    while($row = $qry->fetch_assoc()): 
    ?>
        <tr class="clickable-row" data-id="<?php echo $row['id']; ?>">
            <td class="text-center"><?php echo $i++; ?></td>
            <td><?php echo date("Y-m-d H:i", strtotime($row['date_created'])) ?></td>
            <td>
                <?php 
                if ((empty($row['date_start']) || empty($row['date_end'])) || $row['date_end'] === '0000-00-00' || $row['date_start'] === '0000-00-00'): ?>
                    <small class="text-muted">No Rent Schedule</small>
                <?php else: ?>
                    <small><span class="text-muted">Start Date:</span> <?php echo !empty($row['date_start']) ? date("Y-m-d", strtotime($row['date_start'])) : 'N/A'; ?></small><br>
                    <small><span class="text-muted">End Date:</span> <?php echo (!empty($row['date_end']) && $row['date_end'] !== '0000-00-00') ? date("Y-m-d", strtotime($row['date_end'])) : 'N/A'; ?></small>
                <?php endif; ?>
            </td>
            <td><?php echo $row['client'] ?></td>
            <td><?php echo $row['address']; ?></td>
            <td><?php echo $row['space_name']; ?></td>
            <td><?php echo $row['category']; ?></td>
            <td>
                <?php if (empty($row['meeting_schedule'])): ?>
                    <small class="text-muted">No Meeting Schedule Yet</small>
                <?php else: ?>
                    <small><?php echo date("l, F j, Y", strtotime($row['meeting_schedule'])); ?></small>
                <?php endif; ?>
            </td>
            <td class="text-center">
                <?php if($row['status'] == 0): ?>
                    <span class="badge badge-light">Pending</span>
                <?php elseif($row['status'] == 1): ?>
                    <span class="badge badge-primary">Confirmed</span>
                <?php elseif($row['status'] == 2): ?>
                    <span class="badge badge-danger">Cancelled</span>
                <?php elseif($row['status'] == 3): ?>
                    <span class="badge badge-success">Done</span>
				<?php elseif($row['status'] == 4	): ?>
					<span class="badge badge-warning">Ongoing</span>
                <?php else: ?>
                    <span class="badge badge-danger">Cancelled</span>
                <?php endif; ?>
            </td>
        </tr>
						<!-- Set Meeting Schedule Modal -->
							<div class="modal fade" id="setMeetingScheduleModal" tabindex="-1" role="dialog" aria-labelledby="setMeetingScheduleModalLabel" aria-hidden="true">
								<div class="modal-dialog" role="document">
									<div class="modal-content">
										<div class="modal-header">
											<h5 class="modal-title" id="setMeetingScheduleModalLabel">Set Meeting Schedule</h5>
											<button type="button" class="close" data-dismiss="modal" aria-label="Close">
												<span aria-hidden="true">&times;</span>
											</button>
										</div>
										<form id="setMeetingScheduleForm">
											<div class="modal-body">
												<div class="form-group">
													<label for="meetingSchedule">Select Meeting Schedule:</label>
													<input type="datetime-local" class="form-control" id="meetingSchedule" name="meeting_schedule" required>
													<input type="hidden" id="clientIdInput" name="client_id">
												</div>
											</div>
											<div class="modal-footer">
												<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
												<button type="submit" class="btn btn-primary">Save Schedule</button>
											</div>
										</form>
									</div>
								</div>
							</div>

						
						<!-- View Documents Modal -->
						<div class="modal fade" id="viewDocumentsModal-<?php echo $row['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="viewDocumentsModalLabel" aria-hidden="true">
							<div class="modal-dialog modal-lg" role="document">
								<div class="modal-content">
									<div class="modal-header">
										<h5 class="modal-title" id="viewDocumentsModalLabel">Uploaded Documents</h5>
										<button type="button" class="close" data-dismiss="modal" aria-label="Close">
											<span aria-hidden="true">&times;</span>
										</button>
									</div>
									<div class="modal-body">
										<div class="row">
											<?php 
											$docQry = $conn->query("SELECT * FROM documents WHERE client_id = {$row['client_id']} LIMIT 1");
											$docRow = $docQry->fetch_assoc();
											?>
											<?php if ($docRow): ?>
											<!-- Cedule File -->
											<div class="row">
												<div class="col-12 pl-4"> <!-- Added padding-left using Bootstrap class -->
													<h5>Cedule File</h5>
													<img src="/SogodMarket/uploads/documents/<?php echo $docRow['cedule_file']; ?>" alt="Cedule File" class="img-fluid" />
												</div>
											</div>

											<!-- Photo ID File -->
											<div class="row mt-4">
												<div class="col-12 pl-4"> <!-- Added padding-left using Bootstrap class -->
													<h5>Photo ID File</h5>
													<img src="/SogodMarket/uploads/documents/<?php echo $docRow['photo_id_file']; ?>" alt="Photo ID File" class="img-fluid" />
												</div>
											</div>

											 <!-- Other Document File -->
											<div class="row mt-4">
												<div class="col-12">
													<h5>Other Document File</h5>
													<img src="/SogodMarket/uploads/documents/<?php echo $docRow['other_document_file']; ?>" alt="Other Document File" class="img-fluid" />
												</div>
											</div>


											<!-- Description -->
											<div class="col-12 mt-4 pl-4"> <!-- Added padding-left using Bootstrap class -->
												<h5>Description</h5>
												<p><?php echo htmlspecialchars($docRow['description']); ?></p>
											</div>
											<?php else: ?>
											<!-- No Documents Found Message -->
											<div class="col-12 text-center">
												<p class="text-danger">No documents found for this client.</p>
											</div>
											<?php endif; ?>
										</div>
									</div>
									<div class="modal-footer d-flex justify-content-between">
										<!-- Leftmost buttons: Approve and Reject -->
										<div>
										<button type="button" class="btn btn-success approve_application" data-client-id="<?php echo $row['client_id']; ?>" data-id="<?php echo $row['id']; ?>">Approve</button>

										<button type="button" class="btn btn-danger reject_application" data-client-id="<?php echo $row['client_id']; ?>" data-id="<?php echo $row['id']; ?>">Reject</button>
										</div>
										
										<!-- Rightmost button: Close -->
										<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
									</div>

								</div>
							</div>
						</div>

					<?php endwhile; ?>
				</tbody>
			</table>
		</div>
	</div>
</div>

<script>
	$(document).ready(function(){
		$('.view_documents').click(function(){
			var bookingId = $(this).attr('data-id');
			$('#viewDocumentsModal-' + bookingId).modal('show'); // Show the View Documents Modal
		})
		$('.delete_data').click(function(){
			_conf("Are you sure to delete this booking permanently?","delete_booking",[$(this).attr('data-id')])
		})
		$('.view_data').click(function(){
			uni_modal('Booking Details','bookings/view_booking.php?id='+$(this).attr('data-id'),'mid-large')
		})
		$('.view_payments').click(function(){
			uni_modal('View Payments', 'bookings/view_payments.php?id=' + $(this).attr('data-id'), 'mid-large');
		})
		$('.approve_application').click(function() {
			 // Assuming you have a data attribute for booking ID
			if (confirm("Are you sure you want to approve this Requirement?")) {
				// Pass both client_id and booking_id to the modal
				uni_modal('Set Meeting', 'bookings/set_meeting.php?id=' + $(this).attr('data-id'), 'mid-large');
			}
		});
		$('#create_new').click(function(){
			uni_modal("<i class='fa fa-plus'></i> Add New Client", 'bookings/walk-in.php')
		})
		$('.reject_application').click(function() {
			 // Assuming you have a data attribute for booking ID
			if (confirm("Are you sure you want to reject this Requirement?")) {
				// Pass both client_id and booking_id to the modal
				uni_modal('Reject', 'bookings/reject.php?id=' + $(this).attr('data-id'), 'mid-large');
			}
		});
	
		$('.clickable-row').on('click', function() {
			var bookingId = $(this).data('id'); // Get booking ID from row
			uni_modal('', 'bookings/view_booking.php?id=' + bookingId, 'mid-large'); // Open modal
		});

		$('.table').dataTable({
			columnDefs: [
				{ targets: [8, 8], orderable: false}
			],
			initComplete:function(settings, json){
				$('.table td,.table th').addClass('px-2 py-1')
			}
		});
	})

	function delete_booking($id){
		start_loader();
		$.ajax({
			url:_base_url_+"classes/Master.php?f=delete_booking",
			method:"POST",
			data:{id: $id},
			dataType:"json",
			error:err=>{
				console.log(err)
				alert_toast("An error occured.",'error');
				end_loader();
			},
			success:function(resp){
				if(typeof resp== 'object' && resp.status == 'success'){
					location.reload();
				}else{
					alert_toast("An error occured.",'error');
					end_loader();
				}
			}
		})
	}

</script>
