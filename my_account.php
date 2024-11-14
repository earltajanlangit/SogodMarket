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
                        $remaining_days = 0;
                        if ($qry->num_rows > 0) {
                            $row = $qry->fetch_assoc();
                            $end_date = strtotime($row['date_end']);
                            $current_date = time();
                            $remaining_days = ceil(($end_date - $current_date) / (60 * 60 * 24)); // Calculate remaining days

                            if ($remaining_days >= 0) {
                                $contract_active = true;
                            }
                        }

                        if ($contract_active) {
                            echo '<div class="contract-status"><i class="fa fa-check-circle"></i> Contract is Active - ' . $remaining_days . ' days remaining</div>';
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
                    <b> <p class="text-center mb-3">For Easy Login Option, Take a Picture</p></b>
                    <?php
                        $generated_code = $_SESSION['generated_code'];
                        $qr_code_url = "https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=" . urlencode($generated_code); 
                    ?>
                    <img src="<?php echo $qr_code_url; ?>" alt="QR Code" class="img-fluid">
                </div>
            </div>

            <!-- View Documents Button -->
            <div class="card-body">
                <button type="button" class="btn btn-info btn-flat" id="view-documents-btn">
                    <i class="fa fa-file-alt"></i> Add Documents
                </button>
                <button type="button" class="btn btn-info btn-flat" id="view-documents-btn">
                    <i class="fa fa-file-alt"></i> View Documents
                </button>
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
                        <col width="25%">
                        <col width="20%">
                        <col width="10%">
                        <col width="15%">
                    </colgroup>
                    <thead>
                        <tr class="bg-navy text-white">
                            <th>#</th>
                            <th>Date Booked</th>
                            <th>Rent Schedule</th>
                            <th>Client</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                            $i = 1;
                            $qry = $conn->query("SELECT r.*, CONCAT(c.firstname, ' ', c.lastname) AS client FROM rent_list r INNER JOIN clients c ON c.id = r.client_id WHERE client_id = '{$_SESSION['id']}' ORDER BY unix_timestamp(r.date_created) DESC ");
                            while ($row = $qry->fetch_assoc()):
                        ?>
                        <tr>
                            <td class="text-center"><?php echo $i++; ?></td>
                            <td><?php echo date("Y-m-d H:i", strtotime($row['date_created'])) ?></td>
                            <td>
                                <small><span class="text-muted">Pick up:</span> <?php echo date("Y-m-d", strtotime($row['date_start'])) ?></small><br>
                                <small><span class="text-muted">Return: </span> <?php echo date("Y-m-d", strtotime($row['date_end'])) ?></small>
                            </td>
                            <td><?php echo $row['client'] ?></td>
                            <td class="text-center">
                                <?php if($row['status'] == 0): ?>
                                    <span class="badge badge-light">Pending</span>
                                <?php elseif($row['status'] == 1): ?>
                                    <span class="badge badge-primary">Confirmed</span>
                                <?php elseif($row['status'] == 2): ?>
                                    <span class="badge badge-danger">Cancelled</span>
                                <?php elseif($row['status'] == 3): ?>
                                    <span class="badge badge-warning">Done</span>
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
                                    <a class="dropdown-item view_data" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>">
                                        <span class="fa fa-th-list text-dark"></span> View Details
                                    </a>
                                </div>
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
                    <th>#</th> <!-- Add this column header for number -->
                    <th>Date Paid</th>
                    <th>Amount Paid</th>
                    <th>Payment method</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                    $qry = $conn->query("SELECT * FROM payments WHERE client_id = '{$_SESSION['id']}' ORDER BY date_paid DESC");
                    $i = 1; // Initialize the counter variable
                    while ($row = $qry->fetch_assoc()):
                ?>
                <tr>
                    <td><?php echo $i++; ?></td> <!-- Display the row number -->
                    <td><?php echo date("Y-m-d", strtotime($row['date_paid'])) ?></td>
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



