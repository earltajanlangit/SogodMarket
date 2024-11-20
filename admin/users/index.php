<?php if($_settings->chk_flashdata('success')): ?>
<script>
	alert_toast("<?php echo $_settings->flashdata('success') ?>",'success')
</script>
<?php endif;?>
<div class="card card-outline card-primary">
	<div class="card-header">
		<h3 class="card-title">List of Clients</h3>
		<div class="card-tools">
			<a href="?page=client/manage_client" class="btn btn-flat btn-primary"><span class="fas fa-plus"></span>  Create New</a>
		</div>
	</div>
	<div class="card-body">
		<div class="container-fluid">
        <div class="container-fluid">
			<table class="table table-bordered table-striped">
				<colgroup>
					<col width="5%">
					<col width="15%">
					<col width="25%">
					<col width="30%">
					<col width="10%">
					<col width="15%">
				</colgroup>
				<thead>
					<tr class="bg-navy disabled">
						<th>#</th>
						<th>Client ID</th>
						<th>Name</th>
						<th>Contact Number</th>
						<th>Gender</th>
						<th>Address</th>
						<th>Email</th>
						<th>Password</th>
						<th>QR CODE</th>
						<th>Action</th>
						
						
					</tr>
				</thead>
				<tbody>
					<?php 
					$i = 1;
						$qry = $conn->query("SELECT * from `clients` order by `id` asc ");
						while($row = $qry->fetch_assoc()):
							foreach($row as $k=> $v){
								$row[$k] = trim(stripslashes($v));
							}
                            
					?>
						<tr>
							<td class="text-center"><?php echo $i++; ?></td>
							<td><?php echo $row['id'] ?></td>
							<td><?php echo $row['firstname'] . ' ' . $row['lastname'];  ?></td>
							<td><?php echo $row['contact'] ?></td>
							<td><?php echo $row['gender'] ?></td>
							<td><?php echo $row['address'] ?></td>
							<td><?php echo $row['email'] ?></td>
							<td><?php echo $row['password'] ?></td>
							<td><?php echo $row['generated_code'] ?></td>
							
							<td align="center">
								 <button type="button" class="btn btn-flat btn-default btn-sm dropdown-toggle dropdown-icon" data-toggle="dropdown">
				                  		Action
				                    <span class="sr-only">Toggle Dropdown</span>
				                  </button>
				                  <div class="dropdown-menu" role="menu">
				                    <a class="dropdown-item" href="?page=client/manage_client&id=<?php echo $row['id'] ?>"><span class="fa fa-edit text-primary"></span> Edit</a>
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
</div>
<script>
	$(document).ready(function(){
		$('.delete_data').click(function(){
    _conf("Are you sure to delete this client permanently?","delete_users",[$(this).attr('data-id')]);
});
    $('.table td, .table th').addClass('px-2 py-1');
    $('.table').dataTable({
        columnDefs: [
            { targets: [5, 6], orderable: false }
        ],
        initComplete:function(settings, json){
            $('.table td, .table th').addClass('px-2 py-1');
        }
    });
});

function delete_users($id){
    start_loader();
    $.ajax({
        url: _base_url_ + "classes/Master.php?f=delete_client",
        method: "POST",
        data: { id: $id },
        dataType: "json",
        error: err => {
            console.log(err); // Log any errors
            alert_toast("An error occurred.", 'error');
            end_loader();
        },
        success: function(resp) {
            console.log(resp); // Log the response from the server
            if(typeof resp === 'object' && resp.status === 'success'){
                location.reload();
            } else {
                alert_toast("An error occurred.", 'error');
                end_loader();
            }
        }
    });
}

</script>