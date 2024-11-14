<?php
require_once('../../config.php');

if(isset($_GET['id']) && $_GET['id'] > 0){
    $qry = $conn->query("SELECT * FROM `rent_list` WHERE id = '{$_GET['id']}' ");
    if($qry->num_rows > 0){
        $rent_data = $qry->fetch_assoc();
    }
}
?>

<div class="container-fluid">
    <form action="" id="pay-form">
        <h4 class="mb-3">Make a Payment</h4>

        <!-- Display space and rent details -->
        <?php if(isset($rent_data)): ?>
            <p><b>Space:</b> <?php echo htmlspecialchars($rent_data['space_id']); ?></p>
            <p><b>Client ID:</b> <?php echo htmlspecialchars($rent_data['client_id']); ?></p>
        <?php endif; ?>

        <input type="hidden" name="booking_id" value="<?php echo isset($_GET['id']) ? $_GET['id'] : ''; ?>">

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
        <div class="form-group">
            <label for="payment_method" class="control-label">Payment Method</label>
            <select name="payment_method" id="payment_method" class="custom-select custom-select-sm" required>
                <option value="Cash">Cash</option>
                <option value="Card">Card</option>
                <option value="Bank Transfer">Bank Transfer</option>
            </select>
        </div>

        <div id="msg" class="text-danger"></div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-primary btn-flat btn-sm">Submit Payment</button>
            <button type="button" class="btn btn-secondary btn-sm btn-flat" data-dismiss="modal">Cancel</button>
        </div>
    </form>
</div>

<script>
    $(function() {
        $('#pay-form').submit(function(e) {
            e.preventDefault();
            var _this = $(this);
            $('.err-msg').remove();
            start_loader();

            $.ajax({
                url: _base_url_ + "classes/Master.php?f=save_booking",
                data: new FormData($(this)[0]),
                cache: false,
                contentType: false,
                processData: false,
                method: 'POST',
                type: 'POST',
                dataType: 'json',
                error: function(err) {
                    console.log(err);
                    alert_toast("An error occurred", 'error');
                    end_loader();
                },
                success: function(resp) {
                    if (typeof resp == 'object' && resp.status == 'success') {
                        location.reload();
                    } else if (resp.status == 'failed' && !!resp.msg) {
                        var el = $('<div>');
                        el.addClass("alert alert-danger err-msg").text(resp.msg);
                        _this.prepend(el);
                        el.show('slow');
                        $("html, body").animate({ scrollTop: _this.closest('.card').offset().top }, "fast");
                        end_loader();
                    } else {
                        alert_toast("An error occurred", 'error');
                        end_loader();
                        console.log(resp);
                    }
                }
            });
        });
    });
</script>
