<?php if($_settings->chk_flashdata('success')): ?>
<script>
    alert_toast("<?php echo $_settings->flashdata('success') ?>",'success')
</script>
<?php endif; ?>
<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title">List of Clients</h3>
        <div class="card-tools">
            <a href="javascript:void(0)" id="create_new" class="btn btn-flat btn-primary"><span class="fas fa-plus"></span> Create New</a>
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
                            <th>QR CODE</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $i = 1;
                        $qry = $conn->query("SELECT * from `clients` order by `id` asc ");
                        while($row = $qry->fetch_assoc()):
                            foreach($row as $k => $v){
                                $row[$k] = trim(stripslashes($v));
                            }
                        ?>
                        <tr class="clickable-row" data-id="<?php echo $row['id']; ?>">
                            <td class="text-center"><?php echo $i++; ?></td>
                            <td><?php echo $row['id'] ?></td>
                            <td><?php echo $row['firstname'] . ' ' . $row['lastname']; ?></td>
                            <td><?php echo $row['contact'] ?></td>
                            <td><?php echo $row['gender'] ?></td>
                            <td><?php echo $row['address'] ?></td>
                            <td><?php echo $row['email'] ?></td>
                            <td><?php echo $row['generated_code'] ?></td>
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
        $('#create_new').click(function(){
            uni_modal("<i class='fa fa-plus'></i> Add New Client", 'client/manage_client.php')
        });

        $('.edit_data').click(function(){
            uni_modal("<i class='fa fa-edit'></i> Edit Client's Details", 'client/manage_client.php?id=' + $(this).attr('data-id'))
        });

        $('.delete_data').click(function(){
            _conf("Are you sure to delete this Client permanently?", "delete_client", [$(this).attr('data-id')])
        });

        // Make rows clickable
        $('.clickable-row').on('click', function() {
            var clientId = $(this).data('id'); // Get client ID from row
            uni_modal('Client Details', 'users/manage_users.php?id=' + clientId, 'mid-large'); // Open modal
        });

        $('.table td,.table th').addClass('px-2 py-1');
        $('.table').dataTable({
            columnDefs: [
                { targets: [3, 4], orderable: false }
            ],
            initComplete: function(settings, json){
                $('.table td,.table th').addClass('px-2 py-1');
            }
        });
    });

    function delete_client($id){
        start_loader();
        $.ajax({
            url: _base_url_ + "classes/Clients.php?f=delete",
            method: "POST",
            data: { id: $id },
            dataType: "json",
            error: function(err){
                console.log(err);
                alert_toast("An error occurred.", 'error');
                end_loader();
            },
            success: function(resp){
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
