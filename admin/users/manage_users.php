
<?php
require_once('../../config.php');
?>
<style>
    #uni_modal .modal-content > .modal-footer, #uni_modal .modal-content > .modal-header {
        display: none;
    }

    .action-btns .btn {
        margin-right: 10px;
    }

    #update {
        margin-left: auto; /* Align the update button to the right */
        margin-top: 15px;
    }

    .container-fluid .row {
        margin-bottom: 20px;
    }

    .container-fluid h4 {
        background-color: #f8f9fa;
        padding: 15px;
        border-radius: 10px;
        margin-bottom: 20px;
        font-size: 1.5rem;
        font-weight: 600;
    }

    .container-fluid img {
        width: 100%;
        max-height: 300px;
        object-fit: cover;
        border: 2px solid #ddd;
        padding: 8px;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .container-fluid .card {
        border: 1px solid #ddd;
        border-radius: 10px;
        margin-bottom: 20px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
    }

    .container-fluid .card-header {
        background-color: #f1f1f1;
        font-size: 1.2rem;
        font-weight: bold;
        border-bottom: 2px solid #ddd;
    }

    .container-fluid .card-body {
        padding: 20px;
    }

    .badge-custom {
        font-size: 0.9rem;
        padding: 6px 12px;
        border-radius: 15px;
    }

    .badge-light {
        background-color: #f8f9fa;
        color: #6c757d;
    }

    .badge-primary {
        background-color: #007bff;
        color: white;
    }

    .badge-danger {
        background-color: #dc3545;
        color: white;
    }

    .badge-success {
        background-color: #28a745;
        color: white;
    }

    .approve_application, .reject_application {
        width: 160px;
    }

    /* Modal buttons */
    .modal-footer {
        display: flex;
        justify-content: center;
        border-top: 1px solid #ddd;
        padding: 15px;
    }

    .modal-footer .btn {
        width: 45%;
        margin: 5px;
        transition: background-color 0.3s;
    }

    .modal-footer .btn:hover {
        background-color: #0069d9;
        color: white;
    }

    .row img {
        border-radius: 5px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    /* Styling for the arrow */
    .arrow {
        width: 0;
        height: 0;
        border-left: 20px solid transparent;
        border-right: 20px solid transparent;
        border-top: 25px solid #007BFF;
        margin: 0 auto;
    }

    .col-md-6 p {
        font-size: 1rem;
        line-height: 1.6;
        margin-bottom: 10px;
    }

    /* Card Header title font color */
    .container-fluid .card-header {
        color: #333;
    }

    .container-fluid .card-body {
        font-size: 1rem;
        color: #555;
    }
    
    
</style>

<?php 
$id = $_GET['id'];
$clientQry = $conn->query("SELECT * FROM clients WHERE id = {$id}");
$clientRow = $clientQry->fetch_assoc();

$rentQry = $conn->query("SELECT * FROM rent_list WHERE client_id = {$id}");
$rentRow = $rentQry->fetch_assoc();

// Check if rentRow is not empty before querying space_list
if (!empty($rentRow) && isset($rentRow['space_id'])) {
    // Fetch space information
    $spaceQry = $conn->query("SELECT * FROM space_list WHERE id = {$rentRow['space_id']}");
    $spaceRow = $spaceQry->fetch_assoc();
    $typeofspaceQry = $conn->query("SELECT * FROM space_type_list WHERE id = {$spaceRow['space_type_id']}");
    $typeofSpaceRow = $typeofspaceQry->fetch_assoc();
} 

?>
   <!-- Client Details -->
    <div class="card">
        <div class="card-header">Client Details</div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-6">
                    <p><strong>First Name:</strong> <?php echo $clientRow['firstname'] ?></p>
                    <p><strong>Last Name:</strong> <?php echo $clientRow['lastname'] ?></p>
                    <p><strong>Email:</strong> <?php echo $clientRow['email'] ?></p>
                    <p><strong>Gender:</strong> <?php echo $clientRow['gender'] ?></p>
                    <p><strong>Address:</strong> <?php echo $clientRow['address'] ?></p>
                    <p><strong>Contact:</strong> <?php echo $clientRow['contact'] ?></p>
                </div>
                <div class="col-md-6">
                <?php if (!empty($rentRow)): ?>
                    <p><strong>Space Name:</strong> 
                        <?php 
                            if (!empty($spaceRow)) {
                                echo $typeofSpaceRow['name'] . " " . $spaceRow['space_name'];
                            } else {
                                echo "No Rent information available.";
                            }
                        ?>
                    </p>
                    <p><strong>Date of Application:</strong> 
                        <?php 
                            if (!empty($rentRow['date_application'])) {
                                $dateApplication = new DateTime($rentRow['date_application']);
                                echo $dateApplication->format('F j, Y'); 
                            } else {
                                echo "No Rent information available.";
                            }
                        ?>
                    </p>
                    <p><strong>Contract Start:</strong> 
                        <?php 
                            if (!empty($rentRow['date_start'])) {
                                $dateStart = new DateTime($rentRow['date_start']);
                                echo $dateStart->format('F j, Y');
                            } else {
                                echo "No Rent information available.";
                            }
                        ?>
                    </p>
                    <p><strong>Contract End:</strong> 
                        <?php 
                            if (!empty($rentRow['date_end'])) {
                                $dateEnd = new DateTime($rentRow['date_end']);
                                echo $dateEnd->format('F j, Y');
                            } else {
                                echo "No Rent information available.";
                            }
                        ?>
                    </p>
                    <p><strong>Booking Status:</strong> 
                        <?php 
                            if (empty($rentRow['status'])) {
                                echo "No Rent information available.";
                            } else {
                                switch($rentRow['status']){
                                    case '0':
                                        echo '<span class="badge badge-light text-dark badge-custom">Pending</span>';
                                        break;
                                    case '1':
                                        echo '<span class="badge badge-primary badge-custom">Approved</span>';
                                        break;
                                    case '2':
                                        echo '<span class="badge badge-danger badge-custom">Disapproved</span>';
                                        break;
                                    case '3':
                                        echo '<span class="badge badge-success badge-custom">Done</span>';
                                        break;
                                    case '4':
                                        echo '<span class="badge badge-warning badge-custom">Ongoing</span>';
                                        break;
                                    default:
                                        echo '<span class="badge badge-danger badge-custom">Disapproved</span>';
                                        break;
                                }
                            }
                        ?>
                    </p>
                    <p><strong>Remaining Days:</strong> 
                        <?php 
                            if (!empty($rentRow['date_start']) && !empty($rentRow['date_end'])) {
                                $dateStart = new DateTime($rentRow['date_start']);
                                $dateEnd = new DateTime($rentRow['date_end']);
                                $remainingDays = $dateStart->diff($dateEnd)->days;

                                if ($dateEnd > new DateTime()) {
                                    echo $remainingDays . " days remaining";
                                } else {
                                    echo "Booking has ended.";
                                }
                            } else {
                                echo "No Rent information available.";
                            }
                        ?>
                    </p> 
                <?php else: ?>
                    <p>No Rent information available.</p>
                <?php endif; ?>
                </div>
                </div>
                
            </div>
            <div class="row mt-3">
                <div class="col-12 d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-success edit_data" data-id="<?php echo $clientRow['id']; ?>">Edit</button>
                    <button type="button" class="btn btn-danger delete_data" data-id="<?php echo $clientRow['id']; ?>">Delete</button>
                </div>
            </div>

        </div>
    </div>

    <!-- Modal Footer with Close Button -->
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" id="closeModal">Close</button>
    </div>

<script>
    $('.edit_data').click(function(){
        var clientId = $(this).data('id');
        uni_modal("<i class='fa fa-edit'></i> Edit Client's Details", 'client/manage_client.php?id=' + clientId);
    });

    $('.delete_data').click(function(){
        $('#uni_modal').modal('hide');
        var id = $(this).data('id');
        _conf("Are you sure to delete this Client permanently?", "delete_client", [id]);
    });

    $('#closeModal').click(function() {
        $('#uni_modal').modal('hide');
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
                if(resp.status === 'success'){
                    location.reload();
                } else {
                    alert_toast("An error occurred.", 'error');
                    end_loader();
                }
            }
        });
    }
</script>
