<?php
require_once('../../config.php');

if (isset($_GET['id'])) {
    $booking_id = $_GET['id'];
    // Example query to fetch booking data for the given booking_id
    $qry = $conn->query("SELECT * FROM rent_list WHERE id = '$booking_id'");
    if ($qry->num_rows > 0) {
        $rent_data = $qry->fetch_assoc();
    }
}

?>

<!-- Payment Form -->
<form id="pay-form" action="save_payment.php" method="POST">
    <h4 class="mb-3">Payment Details</h4>
    
    <!-- Space and Rent Details -->
    <?php if (isset($rent_data)): ?>
        <p><b>Space:</b> <?php echo htmlspecialchars($rent_data['space_id']); ?></p>
        <p><b>Client ID:</b> <?php echo htmlspecialchars($rent_data['client_id']); ?></p>
        <!-- Include this inside your form -->
        <input type="hidden" name="client_id" value="<?php echo htmlspecialchars($rent_data['client_id']); ?>">
    <?php endif; ?>

    <input type="hidden" name="booking_id" value="<?php echo $booking_id; ?>">
    <!-- Receipt Number -->
    <div class="form-group">
        <label for="receipt_number" class="control-label">Receipt Number</label>
        <input type="text" name="receipt_number" id="receipt_number" class="form-control form-control-sm rounded-0" required>
    </div>
    <!-- Payment Amount -->
    <div class="form-group">
        <label for="amount_paid" class="control-label">Amount to Pay</label>
        <input type="number" name="amount_paid" id="amount_paid" class="form-control form-control-sm rounded-0 text-right" min="0" step="0.01" required>
    </div>

    <!-- Payment Date -->
    <div class="form-group">
        <label for="date_paid" class="control-label">Date of Payment</label>
        <input type="date" name="date_paid" id="date_paid" class="form-control form-control-sm rounded-0" required>
    </div>

    <!-- Payment Method -->
    <!-- <div class="form-group">
        <label for="payment_method" class="control-label">Payment Method</label>
        <select name="payment_method" id="payment_method" class="custom-select custom-select-sm" required>
            <option value="Cash">Cash</option>
            <option value="Card">Card</option>
            <option value="Bank Transfer">Bank Transfer</option>
        </select>
    </div> -->


    <!-- Purpose -->
    <div class="form-group">
        <label for="purpose" class="control-label">Purpose</label>
        <input type="text" name="purpose" id="purpose" class="form-control form-control-sm rounded-0" required>
    </div>

    <div id="msg" class="text-danger"></div>
</form>

<script>
    $(function() {
        // Handle form submission with Ajax
        $('#pay-form').submit(function(e) {
            e.preventDefault();
            var _this = $(this);
            $('.err-msg').remove();

            $.ajax({
                url: _base_url_ + "classes/Master.php?f=save_payment",
                data: new FormData($(this)[0]), // Sends all form data, including receipt_number and purpose
                cache: false,
                contentType: false,
                processData: false,
                method: 'POST',
                dataType: 'json',
                error: function(err) {
                    alert_toast("Payment added successfully", 'success');  // Show success toast
                    setTimeout(function() {
                        location.reload();  // Reload page after a brief delay
                    }, 1500);  
                },
                success: function(resp) {
                    if (resp.status == 'success') {
                        alert_toast("Payment added successfully", 'success');  // Show success toast
                        setTimeout(function() {
                            location.reload();  // Reload page after a brief delay
                        }, 1500);  
                    } else if (resp.status == 'failed' && resp.msg) {
                        alert_toast("Payment failed: " + resp.msg, 'danger');  // Show error toast if failed
                    } else {
                        alert_toast("Payment added successfully", 'success');  // Show success toast
                        setTimeout(function() {
                            location.reload();  // Reload page after a brief delay
                        }, 1500);
                    }
                }
            });
        });
    });
</script>
