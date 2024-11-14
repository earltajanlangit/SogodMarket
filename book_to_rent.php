<?php
require_once('config.php');
$monthly_rate = 0.00;  // Set default value

if (isset($_GET['id']) && $_GET['id'] > 0) {
    // Fetch monthly_rate from the database based on the provided space ID
    $qry = $conn->query("SELECT monthly_rate FROM `space_list` WHERE id = '{$_GET['id']}'");
    if ($qry->num_rows > 0) {
        $space = $qry->fetch_assoc();
        $monthly_rate = $space['monthly_rate'];  // Get monthly_rate from the result
    }
}
?>  

<div class="container-fluid">
    <form action="" id="book-form">
        <input type="hidden" name="space_id" value="<?php echo $_GET['id'] ?>">
        
        <!-- Start Date Field -->
        <div class="form-group">
            <label for="date_start" class="control-label">Start Date</label>
            <input type="date" name="date_start" id="date_start" class="form-control form-control-sm rounded-0" required>
        </div>
        
        <!-- Months to Rent Field -->
        <div class="form-group">
            <label for="months_to_rent" class="control-label">Months to Rent</label>
            <input type="number" name="months_to_rent" id="months_to_rent" class="form-control form-control-sm rounded-0 text-right" value="1" required>
        </div>
        
        <!-- Monthly Rate Field (readonly) -->
        <div class="form-group">
            <label for="monthly_rate" class="control-label">Monthly Rate</label>
            <input type="text" id="monthly_rate" class="form-control form-control-sm rounded-0 text-right" value="<?php echo number_format($monthly_rate, 2) ?>" readonly>
        </div>
        
        <!-- Total Amount Field (readonly) -->
        <div class="form-group">
            <label for="amount" class="control-label">Total Amount</label>
            <input type="number" name="amount" id="amount" class="form-control form-control-sm rounded-0 text-right" value="0" readonly>
        </div>
        
        <!-- Hidden End Date Field -->
        <input type="hidden" name="date_end" id="date_end">

        <!-- Message/Error Display -->
        <div id="msg" class="text-danger"></div>
    </form>
</div>

<script>
    // Function to calculate the end date based on start date and months to rent
    function calc_end_date() {
        const startDate = new Date($('#date_start').val());
        const months = parseInt($('#months_to_rent').val());

        if (isNaN(startDate.getTime()) || isNaN(months)) return;

        // Add the specified months to the start date
        startDate.setMonth(startDate.getMonth() + months);
        
        // Format the date as YYYY-MM-DD and update the hidden date_end field
        const dateEnd = startDate.toISOString().split('T')[0];
        $('#date_end').val(dateEnd);
    }

    // Function to calculate total amount based on monthly rate and months
    function calc_amount() {
        const monthlyRate = parseFloat("<?php echo isset($monthly_rate) ? $monthly_rate : 0 ?>");
        const months = parseInt($('#months_to_rent').val());
        
        // Calculate the total amount
        const amount = monthlyRate * months;
        
        // Update the input fields with the calculated values
        $('#monthly_rate').val(monthlyRate.toFixed(2));
        $('#amount').val(amount.toFixed(2));
    }

    $(function() {
        // Trigger calculations when start date or months to rent field changes
        $('#date_start, #months_to_rent').change(function() {
            $('#msg').text('');
            calc_amount();
            calc_end_date();  // Calculate end date
        });

        // Handle form submission via AJAX
        $('#book-form').submit(function(e) {
            e.preventDefault();
            const _this = $(this);
            $('.err-msg').remove();
            start_loader();

            $.ajax({
                url: _base_url_ + "classes/Master.php?f=save_bookingspart2",
                data: new FormData(_this[0]),
                cache: false,
                contentType: false,
                processData: false,
                method: 'POST',
                dataType: 'json',
                error: function(err) {
                    console.log(err);
                    alert_toast("An error occurred", 'error');
                    end_loader();
                },
                success: function(resp) {
                    if (typeof resp == 'object' && resp.status == 'success') {
                        end_loader();
                        $('#uni_modal').modal('hide');
                        setTimeout(() => {
                            uni_modal('', 'success_booking.php');
                        }, 500);
                    } else if (resp.status == 'failed' && !!resp.msg) {
                        const el = $('<div>').addClass("alert alert-danger err-msg").text(resp.msg);
                        _this.prepend(el);
                        el.show('slow');
                        $("html, body").animate({
                            scrollTop: _this.closest('.card').offset().top
                        }, "fast");
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
