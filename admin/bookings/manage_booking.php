<?php
require_once('../../config.php');

if(isset($_GET['id']) && $_GET['id'] > 0){
    $qry = $conn->query("SELECT * from `rent_list` where id = '{$_GET['id']}' ");
    if($qry->num_rows > 0){
        foreach($qry->fetch_assoc() as $k => $v){
            $$k = stripslashes($v);
        }
    }
}

if(isset($space_id)){
    $bike = $conn->query("SELECT b.*, c.category, bb.name as brand from `space_list` b 
                          inner join categories c on b.category_id = c.id 
                          inner join space_type_list bb on b.space_type_id = bb.id 
                          where b.id = '{$space_id}' ");
    if($bike->num_rows > 0){
        foreach($bike->fetch_assoc() as $k => $v){
            $bike_meta[$k] = stripslashes($v);
        }
    }
}
?>

<div class="container-fluid">
    <form action="" id="book-form">
        <p><b>Category:</b> <?php echo $bike_meta['category'] ?></p>
        <p><b>Type of Space:</b> <?php echo $bike_meta['brand'] ?></p>
        <p><b>Space:</b> <?php echo $bike_meta['space_name'] ?></p>
        <input type="hidden" name="id" value="<?php echo $_GET['id'] ?>">
        <input type="hidden" name="space_id" value="<?php echo isset($space_id) ? $space_id : ''  ?>">
        <input type="hidden" name="client_id" value="<?php echo isset($client_id) ? $client_id : ''  ?>">

        <div class="form-group">
            <label for="date_start" class="control-label">Rent Start Date</label>
            <input type="date" name="date_start" id="date_start" class="form-control form-control-sm rounded-0" value="<?php echo isset($date_start) ? $date_start : ''  ?>" required>
        </div>
        <div class="form-group">
            <label for="months_to_rent" class="control-label">Months to Rent</label>
            <input type="number" name="months_to_rent" id="months_to_rent" class="form-control form-control-sm rounded-0 text-right" value="<?php echo isset($months_to_rent) ? $months_to_rent : 1 ?>" required>
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
            <input type="number" name="amount" id="amount" class="form-control form-control-sm rounded-0 text-right" value="<?php echo isset($amount) ? $amount : 0  ?>" required readonly>
        </div>

        <div class="form-group">
            <label for="" class="control-label">Status</label>
            <select name="status" class="custom-select custom-select-sm">
                <option value="0" <?php echo $status == 0 ? "selected" : '' ?>>Pending</option>
                <option value="1" <?php echo $status == 1 ? "selected" : '' ?>>Confirmed</option>
                <option value="2" <?php echo $status == 2 ? "selected" : '' ?>>Cancelled</option>
                <option value="3" <?php echo $status == 3 ? "selected" : '' ?>>Done</option>
            </select>
        </div>
    </form>
</div>

<script>
    function calc_amount(){
        var monthly_rate = "<?php echo isset($bike_meta['monthly_rate']) ? $bike_meta['monthly_rate'] : 0; ?>";
        var months = $('#months_to_rent').val();
        var amount = monthly_rate * months;
        $('#amount').val(amount);
    }

    $(function(){
        $('#months_to_rent').change(function(){
            calc_amount();
        });

        $('#book-form').submit(function(e){
            e.preventDefault();
            var _this = $(this);

            // Validate form
            if(_this.find('.border-danger').length > 0){
                alert_toast('Can\'t proceed submission due to invalid inputs in some fields.','warning');
                return false;
            }

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
                error: err => {
                    console.log(err);
                    alert_toast("An error occurred", 'error');
                    end_loader();
                },
                success: function(resp){
                    if(typeof resp == 'object' && resp.status == 'success'){
                        location.reload();
                    } else if(resp.status == 'failed' && !!resp.msg){
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
