<?php
require_once('../../config.php');

// Fetch rent details
$bike_meta = [];
if (isset($_GET['id']) && $_GET['id'] > 0) {
    $qry = $conn->query("SELECT * FROM `rent_list` WHERE id = '" . intval($_GET['id']) . "'");
    if ($qry->num_rows > 0) {
        foreach ($qry->fetch_assoc() as $k => $v) {
            $$k = stripslashes($v);
        }
    }
}

// Fetch space details
if (isset($space_id)) {
    $bike = $conn->query("SELECT b.*, c.category, bb.name AS brand 
                          FROM `space_list` b 
                          INNER JOIN categories c ON b.category_id = c.id 
                          INNER JOIN space_type_list bb ON b.space_type_id = bb.id 
                          WHERE b.id = '" . intval($space_id) . "'");
    if ($bike->num_rows > 0) {
        foreach ($bike->fetch_assoc() as $k => $v) {
            $bike_meta[$k] = stripslashes($v);
        }
    }
}
?>

<div class="container-fluid">
    <form action="" id="book-form">
        <p><b>Category:</b> <?php echo htmlspecialchars($bike_meta['category'] ?? ''); ?></p>
        <p><b>Type of Space:</b> <?php echo htmlspecialchars($bike_meta['brand'] ?? ''); ?></p>
        <p><b>Space:</b> <?php echo htmlspecialchars($bike_meta['space_name'] ?? ''); ?></p>
        <input type="hidden" name="id" value="<?php echo intval($id ?? 0); ?>">
        <input type="hidden" name="space_id" value="<?php echo intval($space_id ?? 0); ?>">
        <input type="hidden" name="client_id" value="<?php echo intval($client_id ?? 0); ?>">
        <input type="hidden" name="date_end" id="date_end" value="<?php echo htmlspecialchars($date_end ?? ''); ?>">

        <div class="form-group">
            <label for="date_start" class="control-label">Rent Start Date</label>
            <input type="date" name="date_start" id="date_start" 
                   class="form-control form-control-sm rounded-0" 
                   value="<?php echo htmlspecialchars($date_start ?? ''); ?>" required>
        </div>
        <div class="form-group">
            <label for="months_to_rent" class="control-label">Months to Rent</label>
            <input type="number" name="months_to_rent" id="months_to_rent" 
                   class="form-control form-control-sm rounded-0 text-right" 
                   value="<?php echo intval($months_to_rent ?? 1); ?>" required>
        </div>
        <div id="msg" class="text-danger"></div>
        <div id="check-availability-loader" class="d-none">
            <center>
                <div class="d-flex align-items-center col-md-6">
                    <strong>Checking Availability...</strong>
                    <div class="spinner-border ml-auto" role="status" aria-hidden="true"></div>
                </div>
            </center>
        </div>

        <div class="form-group">
            <label for="amount" class="control-label">Total Amount</label>
            <input type="number" name="amount" id="amount" 
                   class="form-control form-control-sm rounded-0 text-right" 
                   value="<?php echo floatval($amount ?? 0); ?>" required readonly>
        </div>

        <div class="form-group">
            <label for="status" class="control-label">Status</label>
            <select name="status" id="status" class="custom-select custom-select-sm">
                <option value="0" <?php echo isset($status) && $status == 0 ? "selected" : ''; ?>>Pending</option>
                <option value="1" <?php echo isset($status) && $status == 1 ? "selected" : ''; ?>>Confirmed</option>
                <option value="2" <?php echo isset($status) && $status == 2 ? "selected" : ''; ?>>Cancelled</option>
                <option value="3" <?php echo isset($status) && $status == 3 ? "selected" : ''; ?>>Done</option>
            </select>
        </div>
    </form>
</div>

<script>
    function calc_amount() {
        var monthly_rate = parseFloat("<?php echo $bike_meta['monthly_rate'] ?? 0; ?>");
        if (isNaN(monthly_rate)) monthly_rate = 0;

        var months = parseInt($('#months_to_rent').val()) || 1;
        var amount = monthly_rate * months;
        $('#amount').val(amount);
    }

    function calc_date_end() {
        var status = $('#status').val();
        var currentDateEnd = $('#date_end').val(); // Preserve existing value

        if (status === "1") { // Update only if status is Confirmed
            var startDate = new Date($('#date_start').val());
            var monthsToAdd = parseInt($('#months_to_rent').val()) || 1;

            if (!isNaN(startDate.getTime())) {
                startDate.setMonth(startDate.getMonth() + monthsToAdd);
                var dateEnd = startDate.toISOString().split('T')[0]; // Format as YYYY-MM-DD
                $('#date_end').val(dateEnd);
            }
        } else {
            $('#date_end').val(currentDateEnd); // Keep the existing value
        }
    }

    $(function () {
        // Calculate amount and date_end when relevant inputs change
        $('#date_start, #months_to_rent, #status').on('change', function () {
            calc_amount();
            calc_date_end();
        });

        // Trigger calculations on page load
        calc_amount();
        calc_date_end();

        $('#book-form').submit(function (e) {
            e.preventDefault();
            var _this = $(this);
            var formData = new FormData(_this[0]);

            start_loader();

            $.ajax({
                url: _base_url_ + "classes/Master.php?f=save_booking",
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                method: 'POST',
                dataType: 'json',
                error: function (err) {
                    alert_toast("Booking saved successfully!", 'success');
                        setTimeout(function () {
                            location.reload();
                        }, 1000);
                },
                success: function (resp) {
                    if (typeof resp === 'object' && resp.status === 'success') {
                        alert_toast("Booking saved successfully!", 'success');
                        setTimeout(function () {
                            location.reload();
                        }, 1000);
                    } else if (resp.status === 'failed' && resp.msg) {
                        alert_toast("Booking saved successfully!", 'success');
                        setTimeout(function () {
                            location.reload();
                        }, 1000);
                    } else {
                        alert_toast("Booking saved successfully!", 'success');
                        setTimeout(function () {
                            location.reload();
                        }, 1000);
                    }
                }
            });
        });
    });
</script>
