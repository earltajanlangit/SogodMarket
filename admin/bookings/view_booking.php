<?php 
require_once('../../config.php');
?>
<?php
require_once('../../config.php');
$id = $_GET['id'];

$stmt = $conn->prepare("SELECT p.*, b.client_id, b.space_id 
                       FROM payments p
                       INNER JOIN rent_list b ON b.id = p.booking_id
                       WHERE p.booking_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$query = $stmt->get_result();
?>
<style>
    #uni_modal .modal-content > .modal-footer, #uni_modal .modal-content > .modal-header {
        display: none;
    }

    .action-btns .btn {
        margin-right: 10px;
    }

    #update {
        margin-left: auto; /* Forces the button to align to the right */
        margin-top: 15px;
    }

    .container-fluid .row {
        margin-bottom: 20px;
    }

    .container-fluid h4 {
        background-color: #f8f9fa;
        padding: 10px;
        border-radius: 5px;
        margin-bottom: 15px;
        font-size: 1.25rem;
        font-weight: bold;
    }

    .container-fluid img {
        max-width: 100%;
        border: 1px solid #ddd;
        padding: 5px;
        border-radius: 5px;
    }

    .container-fluid .card {
        border: 1px solid #ddd;
        border-radius: 8px;
        margin-bottom: 20px;
    }

    .container-fluid .card-header {
        background-color: #f1f1f1;
        font-size: 1.1rem;
        font-weight: bold;
    }

    .container-fluid .card-body {
        padding: 20px;
    }

    .badge-custom {
        font-size: 0.9rem;
        padding: 5px 10px;
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
        justify-content: center; /* Centers the Close button */
        border-top: 1px solid #ddd;
        padding: 20px;
    }

    .modal-footer .btn {
        width: 45%; /* Adjust button width if necessary */
    }
    .container-fluid img {
    width: 100%; /* Ensure image fills the container */
    max-height: 300px; /* Set a fixed height for all images */
    object-fit: contain; /* Ensures aspect ratio is maintained while filling the container */
    border: 1px solid #ddd;
    padding: 5px;
    border-radius: 5px;
    }
</style>

<?php 
if(!isset($_GET['id'])) {
    $_settings->set_flashdata('error','No Booking ID Provided.');
    redirect('admin/?page=bookings');
}

$booking = $conn->query("SELECT r.*, 
                        CONCAT(c.firstname, ' ', c.lastname) AS client, 
                        c.email, 
                        c.contact, 
                        r.months_to_rent
                        FROM `rent_list` r 
                        INNER JOIN clients c ON c.id = r.client_id 
                        WHERE r.id = '{$_GET['id']}' ");

if($booking->num_rows > 0){
    foreach($booking->fetch_assoc() as $k => $v){
        $$k = $v;
    }
}else{
    $_settings->set_flashdata('error','Booking ID provided is Unknown');
    redirect('admin/?page=bookings');
}

if(isset($space_id)){
    $bike = $conn->query("SELECT b.*, c.category, bb.name as brand, b.monthly_rate 
                          FROM `space_list` b 
                          INNER JOIN categories c ON b.category_id = c.id 
                          INNER JOIN space_type_list bb ON b.space_type_id = bb.id 
                          WHERE b.id = '{$space_id}' ");
    if($bike->num_rows > 0){
        foreach($bike->fetch_assoc() as $k => $v){
            $bike_meta[$k]=stripslashes($v);
        }
    }
}

$docQry = $conn->query("SELECT * FROM documents WHERE client_id = {$client_id} LIMIT 1");
$docRow = $docQry->fetch_assoc();
?>

<div class="container-fluid px-3 py-2">
     <!-- View Documents Section -->
     <div class="card mt-4">
        <div class="card-header">Uploaded Documents</div>
        <div class="card-body">
            <?php if ($docRow): ?>
            <div class="row">
                <div class="col-12">
                    <h5>Cedule File</h5>
                    <img src="/SogodMarket/uploads/documents/<?php echo $docRow['cedule_file']; ?>" alt="Cedule File" class="img-fluid" />
                </div>
                <div class="col-12 mt-4">
                    <h5>Photo ID File</h5>
                    <img src="/SogodMarket/uploads/documents/<?php echo $docRow['photo_id_file']; ?>" alt="Photo ID File" class="img-fluid" />
                </div>
                <div class="col-12 mt-4">
                    <h5>Other Document File</h5>
                    <img src="/SogodMarket/uploads/documents/<?php echo $docRow['other_document_file']; ?>" alt="Other Document File" class="img-fluid" />
                </div>
                <input type="hidden" id="status" name="status" value="4">
                <div class="col-12 mt-4">
                    <h5>Description</h5>
                    <p><?php echo htmlspecialchars($docRow['description']); ?></p>
                </div>
                <!-- Approve and Reject Buttons inside Uploaded Documents -->
                <div class="col-12 text-center mt-4">
                    <button type="button" class="btn btn-success approve_application" data-id="<?php echo $_GET['id']; ?>">Approve</button>
                    <button type="button" class="btn btn-danger reject_application"  data-id="<?php echo $_GET['id']; ?>">Reject</button>
                </div>
            </div>
            <?php else: ?>
            <div class="col-12 text-center">
                <p class="text-danger">No documents found for this client.</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
   <!-- Payments -->
<div class="card rounded-0 mt-4">
    <div class="card-header">
        <h5>Payments History</h5> <!-- Add h5 for consistency -->
    </div>
    <div class="card-body">
        <div class="w-100 justify-content-between d-flex">
            <hr class="border-warning">

            <?php if ($query->num_rows > 0): ?>
                <table class="table table-striped text-dark">
                    <colgroup>
                        <col width="10%">
                        <col width="20%">
                        <col width="15%">
                        <col width="20%">
                        <col width="35%">
                    </colgroup>
                    <thead>
                        <tr class="bg-navy text-white">
                            <th>#</th>
                            <th>Receipt Number</th>
                            <th>Date Paid</th>
                            <th>Amount Paid</th>
                            <th>Purpose</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                            $i = 1;
                            while ($payment_data = $query->fetch_assoc()) { 
                        ?>
                        <tr>
                            <td><?php echo $i++; ?></td>
                            <td><?php echo $payment_data['receipt_number']; ?></td>
                            <td><?php echo date("Y-m-d", strtotime($payment_data['date_paid'])); ?></td>
                            <td><?php echo number_format($payment_data['amount_paid'], 2); ?></td>
                            <td><?php echo $payment_data['purpose']; ?></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
                <?php else: ?>
                    <div class="col-12 text-center">
                        <p class="text-danger">No payments have been made yet.</p>
                    </div>
                <?php endif; ?>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" id="payButton" class="btn btn-sm btn-flat btn-primary" 
        <?php echo ($status != 4 && $status != 1) ? 'disabled' : ''; ?>>Pay</button>
    </div>
</div>

   <!-- Booking Details -->
    <div class="card">
        <div class="card-header">Booking Details</div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-6">
                    <p><strong>Client Name:</strong> <?php echo $client ?></p>
                    <p><strong>Client Email:</strong> <?php echo $email ?></p>
                    <p><strong>Client Contact:</strong> <?php echo $contact ?></p>
                    <p><strong>Rent Start Date:</strong> <?php echo date("M d,Y" ,strtotime($date_start)) ?></p>
                    <p><strong>Rent End Date:</strong> <?php echo date("M d,Y" ,strtotime($date_end)) ?></p>
                    <div class="row mt-4">
                        <div class="col-3"><strong>Booking Status:</strong></div>
                        <div class="col-auto">
                            <?php 
                                switch($status){
                                    case '0':
                                        echo '<span class="badge badge-light text-dark badge-custom">Pending</span>';
                                    break;
                                    case '1':
                                        echo '<span class="badge badge-primary badge-custom">Confirmed</span>';
                                    break;
                                    case '2':
                                        echo '<span class="badge badge-danger badge-custom">Cancelled</span>';
                                    break;
                                    case '3':
                                        echo '<span class="badge badge-success badge-custom">Done</span>';
                                    break;
                                    case '4':
                                        echo '<span class="badge badge-warning badge-custom">Ongoing</span>';
                                    break;
                                    default:
                                        echo '<span class="badge badge-danger badge-custom">Cancelled</span>';
                                    break;
                                }
                            ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <p><strong>Category:</strong> <?php echo $bike_meta['category'] ?></p>
                    <p><strong>Type of Space:</strong> <?php echo $bike_meta['brand'] ?></p>
                    <p><strong>Monthly Rate:</strong> <?php echo number_format($bike_meta['monthly_rate'], 2) ?></p>
                    <p><strong>Months to Rent:</strong> <?php echo $months_to_rent ?></p>
                    <p><strong>Client Payable Amount:</strong> <?php echo number_format($bike_meta['monthly_rate'] * $months_to_rent, 2) ?></p>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-12 d-flex justify-content-end">
                    <button type="button" class="btn btn-primary" id="update">Edit</button>
                </div>
            </div>
        </div>
    </div>
  



    <!-- Modal Footer with Close Button -->
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" id="closeModal">Close</button>
    </div>

</div>

<script>
    $('#payButton').click(function(){
            // Open the payment form modal by calling uni_modal
            uni_modal("Make a Payment", "/sogodmarket/admin/bookings/pay_form.php?id=<?php echo $id; ?>");
    });
    $('#update').click(function() {
            uni_modal("Edit Booking Details", "./bookings/manage_booking.php?id=<?php echo $id ?>")
    });

    $('.approve_application').click(function() {
			 // Assuming you have a data attribute for booking ID
			if (confirm("Are you sure you want to approve this Requirement?")) {
				// Pass both client_id and booking_id to the modal
				uni_modal('Set Meeting', 'bookings/set_meeting.php?id=' + $(this).attr('data-id'), 'mid-large');
			}
	});
    $('.reject_application').click(function() {
			 // Assuming you have a data attribute for booking ID
			if (confirm("Are you sure you want to reject this Requirement?")) {
				// Pass both client_id and booking_id to the modal
				uni_modal('Reject', 'bookings/reject.php?id=' + $(this).attr('data-id'), 'mid-large');
			}
	});
    $('#closeModal').click(function() {
        // Logic to close the modal
        $('#uni_modal').modal('hide');
    });
</script>
