<?php
require_once('config.php');


?>  
<style>
    .badge-light {
        color: black;
    }
    .profile-card {
        margin-bottom: 20px;
    }
    .profile-image {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        object-fit: cover;
    }
    .profile-details {
        font-size: 16px;
    }
    .contract-status {
        font-weight: bold;
        color: green;
    }
    .contract-status-inactive {
        color: red;
    }
</style>

<section class="py-2">
    <div class="container">
        <!-- Top container for Manage Account button -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <!-- Empty div for spacing -->
            <div></div>
            <!-- Manage Account button -->
            <a href="./?p=edit_account" class="btn btn-dark btn-flat ml-auto">
                <div class="fa fa-user-cog"></div> Manage Account
            </a>
        </div>

        <!-- User Profile Card (2-Column Layout) -->
        <div class="card profile-card">
            <div class="card-body d-flex">
                <!-- Left Column (Profile Details) -->
                <div class="col-md-6 d-flex align-items-center">
                    <img src="uploads/blank-profile.png" alt="User Image" class="profile-image mr-3">
                    <div class="profile-details">
                        <?php
                        // Check if the user has an active contract
                        $qry = $conn->query("SELECT * FROM rent_list WHERE client_id = '{$_SESSION['id']}' AND status = 1 ORDER BY date_end DESC LIMIT 1");
                        $contract_active = false;
                        $remaining_days1 = 0;
                        if ($qry->num_rows > 0) {
                            $row = $qry->fetch_assoc();
                            $end_date = strtotime($row['date_end']);
                            $current_date = time();
                            $remaining_days1 = ceil(($end_date - $current_date) / (60 * 60 * 24)); // Calculate remaining days

                            if ($remaining_days1 >= 0) {
                                $contract_active = true;
                            }
                        }

                        if ($contract_active) {
                            echo '<div class="contract-status"><i class="fa fa-check-circle"></i> Contract is Active - ' . $remaining_days1 . ' days remaining</div>';
                        } else {
                            echo '<div class="contract-status contract-status-inactive"><i class="fa fa-times-circle"></i> No Active Contract</div>';
                        }
                        ?>

                        <h5><?php echo $_SESSION['firstname'] . ' ' . $_SESSION['lastname']; ?></h5>
                        <p class="mb-0">Address: <?php echo $_SESSION['address']; ?></p>
                    </div>
                </div>

                   <!-- Right Column (QR Code Section) -->
                <div class="col-md-6 d-flex align-items-center justify-content-center flex-column">
                        <b><p class="text-center mb-3">For Easy Login Option, Take a Picture</p></b>
                        <?php
                            $generated_code = $_SESSION['generated_code'];
                            $qr_code_url = "https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=" . urlencode($generated_code); 
                        ?>
                        <!-- QR Code Image -->
                        <img id="qrCodeImage" src="<?php echo $qr_code_url; ?>" alt="QR Code" class="img-fluid">
                        <p>
                            
                        </p>

                        <!-- Download Button -->
                        <button id="downloadBtn"  class="btn btn-info btn-flat" >Download QR Code</button>
                    </div>
            </div>
         
            <!-- View Documents Button -->
            <div class="card-body">
                <button type="button" class="btn btn-info btn-flat" data-toggle="modal" data-target="#addDocumentModal">
                    <i class="fa fa-file-alt"></i> Add Documents
                </button>
                <button type="button" class="btn btn-info btn-flat"  data-toggle="modal" data-target="#viewDocumentsModal">
                    <i class="fa fa-file-alt"></i> View Documents
                </button>
            </div>
        </div>

        <!-- View Documents Modal -->
<div class="modal fade" id="viewDocumentsModal" tabindex="-1" role="dialog" aria-labelledby="viewDocumentsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
             </div>
            <div class="modal-body">
                <!-- Static Documents -->
                <div class="row">
                <?php 
$qry = $conn->query("SELECT * FROM documents WHERE client_id = '{$_SESSION['id']}' LIMIT 1");
$row = $qry->fetch_assoc(); // Fetching only one row
?>
<div class="modal-body">
    <div class="row">
        <?php if ($row): ?>
        <!-- Cedule File -->
        <div class="row">
            <div class="col-12">
                <h5>Cedule File</h5>
                <img src="/SogodMarket/uploads/documents/<?php echo $row['cedule_file']; ?>" alt="Cedule File" class="img-fluid" />
            </div>
        </div>

        <!-- Photo ID File -->
        <div class="row mt-4">
            <div class="col-12">
                <h5>Photo ID File</h5>
                <img src="/SogodMarket/uploads/documents/<?php echo $row['photo_id_file']; ?>" alt="Photo ID File" class="img-fluid" />
            </div>
        </div>

        <!-- Other Document File -->
        <div class="row mt-4">
            <div class="col-12">
                <h5>Other Document File</h5>
                <img src="/SogodMarket/uploads/documents/<?php echo $row['other_document_file']; ?>" alt="Other Document File" class="img-fluid" />
            </div>
        </div>

        <!-- Description -->
        <div class="col-12 mt-4">
            <h5>Description</h5>
            <p><?php echo htmlspecialchars($row['description']); ?></p>
        </div>
        <?php else: ?>
        <!-- No Documents Found Message -->
        <div class="col-12 text-center">
            <p class="text-danger">No documents found for this client.</p>
        </div>
        <?php endif; ?>
    </div>
</div>
          <h5 class="modal-title" id="viewDocumentsModalLabel">Uploaded Documents</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
     

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>


        <!-- Add Document Modal -->
<div class="modal fade" id="addDocumentModal" tabindex="-1" role="dialog" aria-labelledby="addDocumentModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addDocumentModalLabel">Upload Document</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="" method="POST" id="addDocumentForm">
                <div class="modal-body">
                    <!-- Cedule File Input -->
                    <div class="form-group">
                        <label for="cedule">Cedule</label>
                        <input type="file" class="form-control-file" id="cedule" name="cedule" required>
                    </div>

                    <!-- Photo Copy Valid ID File Input -->
                    <div class="form-group">
                        <label for="photo_id">Photo Copy of Valid ID</label>
                        <input type="file" class="form-control-file" id="photo_id" name="photo_id" required>
                    </div>
                    <!-- Photo Copy Valid ID File Input -->
                    <div class="form-group">
                        <label for="other_document">Other Document</label>
                        <input type="file" class="form-control-file" id="other_document" name="other_document" required>
                    </div>

                    <!-- Description (Optional) -->
                    <div class="form-group">
                        <label for="document_description">Description (Optional)</label>
                        <textarea class="form-control" id="document_description" name="document_description" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Upload</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Renew Contract Modal -->
<div class="modal fade" id="renewContractModal" tabindex="-1" role="dialog" aria-labelledby="renewContractModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="renewContractModalLabel">Renew Contract</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="renew_contract.php" method="POST" id="renewContractForm">
                <div class="modal-body">
                    <!-- Months to Renew Input -->
                    <div class="form-group">
                        <label for="months_to_extend">Months to Renew</label>
                        <input type="number" class="form-control" id="months_to_extend" name="months_to_extend" min="1" required>
                    </div>
                    
                    <!-- Hidden Date Application Input -->
                    <input type="hidden" id="date_application" name="date_application" value="<?= date('Y-m-d'); ?>">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Renew</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="card rounded-0 mb-4">
    <div class="card-body">
        <div class="w-100 justify-content-between d-flex">
            <h4><b>Application Steps and Status</b></h4>
        </div>
        <hr class="border-warning">

        <!-- Application Steps Cards with Arrows -->
        <div class="d-flex align-items-center justify-content-center flex-wrap">
            <?php
            // Define application steps
            $steps = [
                'Application Submitted',
                'Submission of Requirements',
                'Payments and Contract Signing',
                'Booking Confirmed'
            ];

            // Fetch current application step from database or session
            $current_step_index = 0; // Default to step 1
            $qry = $conn->query("SELECT application_status FROM application_tracker WHERE client_id = '{$_SESSION['id']}' LIMIT 1");
            if ($qry->num_rows > 0) {
                $application = $qry->fetch_assoc();
                $current_step_index = (int)$application['application_status']; // Assuming status is stored as a step index
            }

            // If application_status is 0, display 'No Application yet'
            if ($current_step_index == 0) {
                echo '<div class="col-12 text-center"><p>No Application yet</p></div>';
             } else {
                $total_steps = count($steps);
                foreach ($steps as $index => $step): ?>
                    <!-- Step Card -->
                    <div class="card text-center mx-2 mb-3 
                        <?php echo $index < $current_step_index ? 'bg-success text-white' : ($index == $current_step_index ? 'bg-warning text-dark' : 'bg-light text-muted'); ?>" style="min-width: 200px;">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $index + 1; ?>. <?php echo $step; ?></h5>
                            <p class="card-text">
                                <?php 
                                if ($index < $current_step_index) {
                                    echo 'Completed';
                                } elseif ($index == $current_step_index) {
                                    echo 'In Progress';
                                } else {
                                    echo 'Pending';
                                }
                                ?>
                            </p>
                        </div>
                    </div>

                    <!-- Arrow (if not the last step) -->
                    <?php if ($index < $total_steps - 1): ?>
                        <div class="mx-2">
                            <i class="fas fa-arrow-right fa-2x"></i>
                        </div>
                    <?php endif; ?>
                <?php endforeach;

                // If the application status is 4 (Booking Confirmed), calculate the duration and remaining days
                if ($current_step_index == 4) {
                    // Fetch the start and end dates from the rent_list table
                    $rent_query = $conn->query("SELECT date_start, date_end FROM rent_list WHERE client_id = '{$_SESSION['id']}' LIMIT 1");
                    if ($rent_query->num_rows > 0) {
                        $rent = $rent_query->fetch_assoc();
                        $date_start = new DateTime($rent['date_start']);
                        $date_end = new DateTime($rent['date_end']);
                        $current_date = new DateTime();
                
                        // Calculate the difference between the current date and end date
                        $interval_total = $date_start->diff($date_end);
                        $interval_remaining = $current_date->diff($date_end);
                
                        $total_days = (int)$interval_total->format('%a');
                        $remaining_days = max((int)$interval_remaining->format('%a'), 0);
                
                        // Display content based on the remaining days
                        echo '<div class="col-12 text-center mt-3">';
                        if ($remaining_days1 <= 0) {
                            // Display the Renew button
                            echo '
                            <div class="card text-white bg-warning shadow-lg" style="max-width: 300px; margin: 0 auto;">
                                <div class="card-body">
                                    <h5 class="card-title"><i class="fas fa-redo-alt"></i> Renew Contract</h5>
                                    <p class="card-text fs-4">Your contract has ended!</p>
                                    <button type="button" class="btn btn-secondary apple_new_space" >
                                        <i></i> Apply for New Space
                                    </button>
                                    <p class="mt-3">or</p>
                                    <button type="button" class="btn btn-primary"  data-toggle="modal" data-target="#renewContractModal">
                                        <i></i> Renew Current Space
                                    </button>
                                </div>
                            </div>';
                        } else {
                            // Display the duration and remaining days
                            echo '
                            <div class="card text-white bg-info shadow-lg" style="max-width: 300px; margin: 0 auto;">
                                <div class="card-body">
                                    <h5 class="card-title"><i class="fas fa-calendar-day"></i> Contract Duration</h5>
                                    <p class="card-text fs-4">' . $remaining_days1 . ' <span class="fs-6">days remaining</span></p>
                                    <p class="card-text">Duration from Start to End of your booking</p>
                                </div>
                            </div>';
                        }
                        echo '</div>';
                    }
                }
            } 
            ?>
        </div>
    </div>
</div>

        <!-- Rest of the content remains the same -->
        <div class="card rounded-0">
            <div class="card-body">
                <div class="w-100 justify-content-between d-flex">
                    <h4><b>My Bookings</b></h4>
                </div>
                <hr class="border-warning">
                
                <!-- Booking Table -->
                <table class="table table-striped text-dark">
                    <colgroup>
                        <col width="5%">
                        <col width="15%">
                        <col width="15%">
                        <col width="10%">
                        <col width="20%">
                        <col width="10%">
                        <col width="5%">
                    </colgroup>
                    <thead>
                        <tr class="bg-navy text-white">
                            <th>#</th> 
                            <th>Date Booked</th>
                            <th>Meeting Schedule</th>
                            <th>Space Name</th>
                            <th>Client</th>
                            <th>Months to Rent</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php 
                        $i = 1;
                        $qry = $conn->query("
                            SELECT 
                                1 AS sort_key, 
                                r.id, 
                                r.date_application, 
                                r.meeting_schedule, 
                                r.status, 
                                CONCAT(c.firstname, ' ', c.lastname) AS client, 
                                s.space_name, 
                                r.months_to_rent, 
                                cat.category,  -- Category from categories table
                                stype.name AS space_type  -- Space type from space_type_list
                            FROM 
                                rent_list r 
                            INNER JOIN 
                                clients c ON c.id = r.client_id 
                            INNER JOIN 
                                space_list s ON s.id = r.space_id 
                            INNER JOIN 
                                categories cat ON cat.id = s.category_id  -- Join with categories table
                            INNER JOIN 
                                space_type_list stype ON stype.id = s.space_type_id  -- Join with space_type_list table
                            WHERE 
                                r.client_id = '{$_SESSION['id']}'
                            UNION ALL
                            SELECT 
                                2 AS sort_key, 
                                hrl.id, 
                                hrl.date_application, 
                                hrl.meeting_schedule, 
                                hrl.status, 
                                CONCAT(c.firstname, ' ', c.lastname) AS client, 
                                s.space_name, 
                                hrl.months_to_rent, 
                                cat.category,  -- Category from categories table
                                stype.name AS space_type  -- Space type from space_type_list
                            FROM 
                                history_of_rent_list hrl 
                            INNER JOIN 
                                clients c ON c.id = hrl.client_id 
                            INNER JOIN 
                                space_list s ON s.id = hrl.space_id 
                            INNER JOIN 
                                categories cat ON cat.id = s.category_id  -- Join with categories table
                            INNER JOIN 
                                space_type_list stype ON stype.id = s.space_type_id  -- Join with space_type_list table
                            WHERE 
                                hrl.client_id = '{$_SESSION['id']}'
                            ORDER BY 
                                sort_key ASC, 
                                unix_timestamp(date_application) DESC
                        ");

                        while ($row = $qry->fetch_assoc()):
                    ?>
                    <tr>
                        <td class="text-center"><?php echo $i++; ?></td>
                        <td><?php echo date("Y-m-d", strtotime($row['date_application'])) ?></td>
                        <td>
                            <?php if (empty($row['meeting_schedule'])): ?>
                                <small class="text-muted">No Meeting Schedule Yet</small>
                            <?php else: ?>
                                <small><?php echo date("l, F j, Y", strtotime($row['meeting_schedule'])); ?></small>
                            <?php endif; ?> 
                        </td>
                        <td><?php echo $row['category'] . ' - ' . $row['space_type'] . ' - ' . $row['space_name']; ?></td> <!-- Updated display -->
                        <td><?php echo $row['client'] ?></td>
                        <td><?php echo $row['months_to_rent'] ?></td>
                        <td class="text-center">
                            <?php if($row['status'] == 0): ?>
                                <span class="badge badge-light">Pending</span>
                            <?php elseif($row['status'] == 1): ?>
                                <span class="badge badge-primary">Confirmed</span>
                            <?php elseif($row['status'] == 2): ?>
                                <span class="badge badge-danger">Cancelled</span>
                            <?php elseif($row['status'] == 3): ?>
                                <span class="badge badge-success">Done</span>
                            <?php elseif($row['status'] == 4): ?>
                                <span class="badge badge-warning">Ongoing</span>
                            <?php else: ?>
                                <span class="badge badge-secondary">Unknown</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>


                    </tbody>
                </table>
            </div>
        </div>

        <!-- Payment History Section -->
        <div class="card rounded-0 mt-4">
            <div class="card-body">
                <div class="w-100 justify-content-between d-flex">
                    <h4><b>Payment History</b></h4>
                </div>
                <hr class="border-warning">
                
                <!-- Payment History Table -->
                <table class="table table-striped text-dark">
                    <colgroup>
                        <col width="5%">
                        <col width="20%">
                        <col width="40%">
                        <col width="20%">
                    </colgroup>
                    <thead>
                        <tr class="bg-navy text-white">
                            <th>#</th>
                            <th>Date Paid</th>
                            <th>Amount Paid</th>
                            <th>Payment method</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                            $qry = $conn->query("SELECT * FROM payments WHERE client_id = '{$_SESSION['id']}' ORDER BY date_paid DESC");
                            while ($row = $qry->fetch_assoc()):
                        ?>
                        <tr>
                            <td><?php echo $row['id'] ?></td>
                            <td><?php echo date("Y-m-d H:i", strtotime($row['date_paid'])) ?></td>
                            <td><?php echo number_format($row['amount_paid'], 2) ?></td>
                            <td><?php echo $row['payment_method'] ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
<script>
    document.getElementById('downloadBtn').addEventListener('click', function() {
        var qrCodeUrl = document.getElementById('qrCodeImage').src;
        var link = document.createElement('a');
        link.href = qrCodeUrl;
        link.download = 'qr_code.png';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    });

    $(document).ready(function() {
        // Handle apply for new space
        $('.apple_new_space').click(function(e) {
            if (confirm("Are you sure you want to Apply for new Space?")) {
                applyNewSpace(e); // Pass event object
            }
        });

        // Handle form submissions
        $('#addDocumentForm').submit(function(e) {
            e.preventDefault();
            var formData = new FormData(this);

            $.ajax({
                url: _base_url_ + "classes/Master.php?f=add_document",
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    try {
                        response = JSON.parse(response);
                        if (response.success) {
                            alert(response.message);
                            $('#addDocumentModal').modal('hide');
                            window.location.href = "http://localhost/sogodmarket/?p=my_account";
                        } else {
                            alert(response.message);
                        }
                    } catch (error) {
                         alert(response.message);
                            $('#addDocumentModal').modal('hide');
                            window.location.href = "http://localhost/sogodmarket/?p=my_account";
                    }
                },
                error: function(xhr, status, error) {
                    alert("An error occurred. Please try again.");
                }
            });
        });

        $('#renewContractForm').submit(function(e) {
            e.preventDefault();
            var formData = new FormData(this);

            $.ajax({
                url: _base_url_ + "classes/Master.php?f=renew_contract",
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    try {
                        response = JSON.parse(response);
                        if (response.success) {
                            alert(response.message);
                            $('#renewContractModal').modal('hide');
                            location.reload();
                        } else {
                            alert(response.message);
                        }
                    } catch (error) {
                        alert("Error parsing response.");
                    }
                },
                error: function(xhr, status, error) {
                    alert("An error occurred. Please try again.");
                }
            });
        });
    });

    function applyNewSpace(e) {
        e.preventDefault();
        var form = $('#applyForm')[0]; // Reference the form

        var formData = new FormData(form);

        $.ajax({
            url: _base_url_ + "classes/Master.php?f=apply_new_space",
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                try {
                    response = JSON.parse(response);
                    if (response.success) {
                        alert(response.message);
                        location.reload();
                    } else {
                        alert(response.message);
                    }
                } catch (error) {
                    alert("Error parsing response.");
                }
            },
            error: function(xhr, status, error) {
                alert("An error occurred. Please try again.");
            }
        });
    }
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js"></script>
