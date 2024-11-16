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

<div class="card rounded-0 mt-4">
    <div class="card-body">
        <div class="w-100 justify-content-between d-flex">
            <h4><b>Payment History</b></h4>
        </div>
        <hr class="border-warning">
        
        <?php if ($query->num_rows > 0): ?>
            <table class="table table-striped text-dark">
                <colgroup>
                    <col width="20%">
                    <col width="20%">
                    <col width="40%">
                    <col width="20%">
                </colgroup>
                <thead>
                    <tr class="bg-navy text-white">
                        <th>#</th>
                        <th>Date Paid</th>
                        <th>Amount Paid</th>
                        <th>Payment Method</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                        $i = 1;
                        while ($payment_data = $query->fetch_assoc()) { 
                    ?>
                    <tr>
                        <td><?php echo $i++; ?></td>
                        <td><?php echo date("Y-m-d", strtotime($payment_data['date_paid'])); ?></td>
                        <td><?php echo number_format($payment_data['amount_paid'], 2); ?></td>
                        <td><?php echo $payment_data['payment_method']; ?></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="text-center text-muted">No payments have been made yet.</p>
        <?php endif; ?>
    </div>
</div>

<div class="modal-footer">
    <?php if(!isset($_GET['view'])): ?>
    <button type="button" id="payButton" class="btn btn-sm btn-flat btn-primary">Pay</button>
    <?php endif; ?>
    <button type="button" class="btn btn-secondary btn-sm btn-flat" data-dismiss="modal">Close</button>
</div>

<style>
    #uni_modal>.modal-dialog>.modal-content>.modal-footer{
        display:none;
    }
    #uni_modal .modal-body{
        padding:0;
    }
</style>

<script>
     $(function() {
        // When the 'Make a Payment' button is clicked
        $('#payButton').click(function(){
            // Open the payment form modal by calling uni_modal
            uni_modal("Make a Payment", "/sogodmarket/admin/bookings/pay_form.php?id=<?php echo $id; ?>");
        });
    });

    
</script>

