<style>
    #uni_modal .modal-content > .modal-footer, #uni_modal .modal-content > .modal-header {
        display: none;
    }
    #uni_modal1 .modal-content > .modal-footer {
        display: none; /* Hides the footer */
    }

    #uni_modal1 .modal-content > .modal-header {
        display: block; /* Ensures the header remains visible if needed */
    }
    /* General Form Styles */
.login-form {
    max-width: 400px;
    margin: auto;
    padding: 20px;
    background-color: #f8f9fa;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

/* Button Styles */
.btn-block {
    width: 100%;
}

/* Links Styling */
.text-primary {
    color: #007bff !important;
}

.text-muted {
    color: #6c757d;
}


</style>

<div class="container-fluid">
    <div class="row">
        <h3 class="float-right">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </h3>
        

            <!-- Improved Login Form -->
<!-- Improved Login Form -->
<form action="" id="login-form" class="login-form">
    <div class="text-center mb-4">
        <h2 class="text-primary">Login to Your Account</h2>
    </div>
    <div class="form-group">
        <label for="email" class="control-label">Email Address</label>
        <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" autocomplete="email" required>
    </div>
    <div class="form-group">
        <label for="password" class="control-label">Password</label>
        <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" autocomplete="current-password" required>
    </div>
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <a href="javascript:void()" id="forgot-password" class="text-primary"></a>
        </div>
        <div>
            <a href="javascript:void()" id="create_account" class="text-primary">Create an Account</a>
        </div>
    
    </div>
    <div class="form-group my-4">
        <button type="submit" class="btn btn-primary btn-block">Login</button>
    </div>
    <div class="form-group text-center my-3">
        <span class="text-muted">Or</span>
    </div>
    <div class="form-group text-center">
        <button id="qr-login-btn" class="btn btn-secondary btn-block">Login with QR</button>
    </div>
</form>
 

        <!-- OTP Verification Form (initially hidden) -->
        <form action="" method="POST" id="otp-form" style="display: none;">
            <div class="form-group">
                <label for="otp" class="control-label">Enter OTP</label>
                <input type="text" class="form-control form" id="otp" name="otp" autocomplete="one-time-code" required>
            </div>
            <div class="d-flex justify-content-between">
        <!-- QR Login Button on the left -->
        <button id="qr-login-btn" class="btn btn-primary btn-flat">Login with QR</button>
        <!-- OTP Verify Button on the right -->
        <button type="submit" class="btn btn-primary btn-flat">Verify OTP</button>
         </div>
        </form>

        </div>
    </div>
</div>

<script>
   

    // Login Form Submission
    $('#login-form').submit(function(e){
        e.preventDefault();
        start_loader();
        if ($('.err-msg').length > 0) $('.err-msg').remove();
        
        $.ajax({
            url: _base_url_ + "classes/Login.php?f=login_user",
            method: "POST",
            data: $(this).serialize(),
            dataType: "json",
            error: err => {
                console.log(err);
                alert_toast("An error occurred", 'error');
                end_loader();
            },
            success: function(resp) {
                if (typeof resp === 'object' && resp.status === 'success') {
                    $('#login-form').hide(); // Hide the login form
                    $('#otp-form').show(); // Show the OTP form
                    alert_toast("OTP sent successfully", 'success');
                    end_loader();
                } else if (resp.status === 'incorrect') {
                    var _err_el = $('<div>');
                    _err_el.addClass("alert alert-danger err-msg").text("Incorrect Credentials.");
                    $('#login-form').prepend(_err_el);
                    end_loader();
                } else {
                    console.log(resp);
                    alert_toast("An error occurred", 'error');
                    end_loader();
                }
            }
        });
    });
    $('#qr-login-btn').click(function() {
        uni_modal("", "qrlogin.php", "mid-large");
    });

    $('#create_account').click(function(){
        uni_modal("", "registration.php", "mid-large");
    });

    // OTP Form Submission
    $('#otp-form').submit(function(e){
    e.preventDefault();
    start_loader();

    // Debugging: Check the OTP being sent to the server
    console.log('Entered OTP:', $('#otp').val());

    $.ajax({
        url: _base_url_ + "classes/Login.php?f=verify_otp",
        method: "POST",
        data: $(this).serialize(),  // Ensure OTP is correctly serialized
        dataType: "json",
        timeout: 100000,  // Set timeout to 10 seconds
        error: err => {
            console.log(err);
            alert_toast("An error occurred", 'error');
            end_loader();
        },
        success: function(resp) {
            console.log(resp);  // Debugging: Check the response from the server
            if (resp.status === 'verified') {
                 alert_toast("OTP verified successfully", 'success');
                 end_loader();
                // Show the application process modal
                setTimeout(function() {
                    uni_modal("Application Process", "application_process.php", "mid-large");
                }, 500);
                // location.reload();
            } else if (resp.status === 'incorrect') {
                var _err_el = $('<div>');
                _err_el.addClass("alert alert-danger err-msg").text(resp.error_message || "Invalid OTP.");
                $('#otp-form').prepend(_err_el);
                end_loader();
            }
        }
    });
});


</script>
