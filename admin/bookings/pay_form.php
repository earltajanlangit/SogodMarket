<?php
require_once('../../config.php');

if (isset($_GET['id'])) {
    $booking_id = $_GET['id'];
    
    // Query to fetch booking data for the given booking_id
    $qry = $conn->query("SELECT * FROM rent_list WHERE id = '$booking_id'");
    if ($qry->num_rows > 0) {
        $rent_data = $qry->fetch_assoc();

        // Query to fetch monthly_rate from space_list table
        $space_id = $rent_data['space_id'];
        $space_qry = $conn->query("SELECT monthly_rate FROM space_list WHERE id = '$space_id'");
        if ($space_qry->num_rows > 0) {
            $space_data = $space_qry->fetch_assoc();
            $monthly_rate = $space_data['monthly_rate'];
        } else {
            $monthly_rate = 0; // Default to 0 if no match is found
        }
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
        <p><b>Monthly Rate:</b> <span id="monthly-rate"><?php echo htmlspecialchars($monthly_rate); ?></span></p>
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
        <input type="number" name="amount_paid" id="amount_paid" class="form-control form-control-sm rounded-0 text-right" min="0" step="0.01" required readonly>
    </div>

    <!-- Payment Date -->
    <div class="form-group">
        <label for="date_paid" class="control-label">Date of Payment</label>
        <input type="date" name="date_paid" id="date_paid" class="form-control form-control-sm rounded-0" required>
    </div>

    <div id="msg" class="text-danger"></div>
</form>

<script>
    $(function() {
        // Set the current date as the default value for date_paid
        const today = new Date().toISOString().split('T')[0];
        $('#date_paid').val(today);

        // Calculate amount_paid dynamically
        const monthlyRate = parseFloat($('#monthly-rate').text()) || 0;
        const calculatedAmount = (monthlyRate * 2) + 500;

        // Set the calculated value in the amount_paid input
        $('#amount_paid').val(calculatedAmount.toFixed(2));

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
