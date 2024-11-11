<style>
    #uni_modal .modal-content>.modal-footer,#uni_modal .modal-content>.modal-header{
        display:none;
    }
</style>
<div class="container-fluid">
    <form action="" id="registration">
        <div class="row">
            <h3 class="text-center">Create New Account
                <span class="float-right">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </span>
            </h3>
            <hr>
        </div>
        <div class="row align-items-center h-100">
            <div class="col-lg-5 border-right">
                <div class="form-group">
                    <label for="" class="control-label">Firstname</label>
                    <input type="text" class="form-control form-control-sm form" name="firstname" required>
                </div>
                <div class="form-group">
                    <label for="" class="control-label">Lastname</label>
                    <input type="text" class="form-control form-control-sm form" name="lastname" required>
                </div>
                <div class="form-group">
                    <label for="" class="control-label">Phone Number</label>
                    <input type="text" class="form-control form-control-sm form" name="contact" required>
                    <button type="button" id="send_code" class="btn btn-secondary btn-sm mt-2">Send Verification Code</button>
                </div>
                <div class="form-group">
                    <label for="" class="control-label">Verification Code</label>
                    <input type="text" class="form-control form-control-sm form" name="verification_code" required>
                </div>
                <div class="form-group">
                    <label for="" class="control-label">Gender</label>
                    <select name="gender" id="" class="custom-select select" required>
                        <option>Male</option>
                        <option>Female</option>
                    </select>
                </div>  
            </div>
            <div class="col-lg-7">
                <div class="form-group">
                    <label for="" class="control-label">Address</label>
                    <textarea class="form-control form" rows='3' name="address" required></textarea>
                </div>
                <div class="form-group">
                    <label for="" class="control-label">Email</label>
                    <input type="text" class="form-control form-control-sm form" name="email" required>
                </div>
                <div class="form-group">
                    <label for="" class="control-label">Password</label>
                    <input type="password" class="form-control form-control-sm form" name="password" required>
                </div>
                <div class="form-group d-flex justify-content-between">
                    <a href="javascript:void()" id="login-show">Already have an Account</a>
                    <button class="btn btn-primary btn-flat">Register</button>
                </div>
                <div class="form-group">
                    <input type="hidden" id="generated_code" name="generated_code" value="">
                </div>
            </div>
        </div>
    </form>
</div>

<script>
 $(function() {
    function generateRandomCode() {
        return Math.floor(100000 + Math.random() * 900000); // Generates a 6-digit number
    }
    const generatedCode = generateRandomCode();
    $('#login-show').click(function() {  // Removed hyphen from the selector
    uni_modal("", "login.php", "mid-large");
        });
    $('#generated_code').val(generatedCode);

    $('#send_code').click(function() {
        const phoneNumber = $('[name="contact"]').val();
        $.ajax({
            url: 'send-verification.php', // Update with the actual path
            method: 'POST',
            data: {
                contact: phoneNumber,
                generated_code: generatedCode
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    alert('Verification code sent successfully');
                } else {
                    alert('Failed to send verification code: ' + response.msg);
                }
            },
            error: function(xhr, status, error) {
                console.error(error);
                alert('An error occurred while sending the verification code');
            }
        });
    });

    $('#registration').submit(function(e) {
        e.preventDefault();
        start_loader();
        if ($('.err-msg').length > 0)
            $('.err-msg').remove();

        // Check the verification code
        const enteredCode = $('[name="verification_code"]').val();
        const correctCode = $('#generated_code').val();

        if (enteredCode !== correctCode) {
            var _err_el = $('<div>');
            _err_el.addClass("alert alert-danger err-msg").text("The verification code is incorrect.");
            $('[name="verification_code"]').after(_err_el);
            end_loader();
            return;
        }

        $.ajax({
            url: _base_url_ + "classes/Master.php?f=register",
            method: "POST",
            data: $(this).serialize(),
            dataType: "json",
            error: err => {
                console.log(err);
                alert_toast("an error occurred", 'error');
                end_loader();
            },
            success: function(resp) {
                if (typeof resp === 'object' && resp.status === 'success') {
                    alert_toast("Account successfully registered", 'success');
                    setTimeout(function() {
                        location.reload();
                    }, 2000);
                } else if (resp.status === 'failed' && !!resp.msg) {
                    var _err_el = $('<div>');
                    _err_el.addClass("alert alert-danger err-msg").text(resp.msg);
                    $('[name="password"]').after(_err_el);
                    end_loader();
                } else {
                    console.log(resp);
                    alert_toast("an error occurred", 'error');
                    end_loader();
                }
            }
        });
    });
});

</script>