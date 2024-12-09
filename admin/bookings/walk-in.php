<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/SogodMarket/config.php');
if(isset($_GET['id']) && $_GET['id'] > 0){
    $qry = $conn->query("SELECT * FROM `clients` WHERE id = '{$_GET['id']}'");
    if($qry->num_rows > 0){
        foreach($qry->fetch_assoc() as $k => $v){
            $$k = $v;
        }
    }
}
?>
<div class="card card-outline card-primary">
    <div class="card-body">
        <div class="container-fluid">
            <div id="msg"></div>
            <form action="" id="manage-client">    
                <div class="form-group">
                    <label for="firstname">First Name</label>
                    <input type="text" name="firstname" id="firstname" class="form-control" value="<?php echo isset($firstname) ? htmlspecialchars($firstname) : '' ?>" required>
                </div>
                <div class="form-group">
                    <label for="lastname">Last Name</label>
                    <input type="text" name="lastname" id="lastname" class="form-control" value="<?php echo isset($lastname) ? htmlspecialchars($lastname) : '' ?>" required>
                </div>
                <div class="form-group">
                    <label for="contact">Contact Number</label>
                    <input type="text" name="contact" id="contact" class="form-control" value="<?php echo isset($contact) ? htmlspecialchars($contact) : '' ?>" required>
                </div>
                <div class="form-group">
                    <label for="gender">Gender</label>
                    <select name="gender" id="gender" class="custom-select select" required>
                        <option <?php echo isset($gender) && $gender == "Male" ? "selected" : '' ?>>Male</option>
                        <option <?php echo isset($gender) && $gender == "Female" ? "selected" : '' ?>>Female</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="address">Address</label>
                    <input type="text" name="address" id="address" class="form-control" value="<?php echo isset($address) ? htmlspecialchars($address) : '' ?>" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" class="form-control" value="<?php echo isset($email) ? htmlspecialchars($email) : '' ?>" required autocomplete="off">
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" class="form-control" placeholder="Enter new password" autocomplete="off">
                </div>
                <div class="form-group">
                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" name="confirm_password" id="confirm_password" class="form-control" placeholder="Confirm your password" autocomplete="off">
                </div>
                <input type="hidden" name="id" value="<?php echo isset($id) ? htmlspecialchars($id) : '' ?>">
            </form>
        </div>
    </div>

    <script>
        $(document).ready(function(){
            $('#manage-client').submit(function(e){
                e.preventDefault();
                var _this = $(this);
                $('.err-msg').remove();

                // Check if passwords match
                var password = $('#password').val();
                var confirmPassword = $('#confirm_password').val();
                
                if (password !== confirmPassword) {
                    var el = $('<div>').addClass("alert alert-danger err-msg").text("Passwords do not match.");
                    _this.prepend(el);
                    el.show('slow');
                    $("html, body").animate({ scrollTop: _this.closest('.card').offset().top }, "fast");
                    return false; // Stop form submission if passwords don't match
                }

                start_loader();
                $.ajax({
                    url: _base_url_ + "classes/Clients.php?f=save",
                    data: new FormData($(this)[0]),
                    cache: false,
                    contentType: false,
                    processData: false,
                    method: 'POST',
                    dataType: 'json',
                    error: function(err){
                        console.log(err);
                        alert_toast("An error occurred", 'error');
                        end_loader();
                    },
                    success: function(resp){
                        if(typeof resp == 'object' && resp.status == 'success'){
                            window.location.href = "http://localhost/sogodmarket/index.php";
                        } else if(resp.status == 'failed' && !!resp.msg){
                            var el = $('<div>').addClass("alert alert-danger err-msg").text(resp.msg);
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
</div>
