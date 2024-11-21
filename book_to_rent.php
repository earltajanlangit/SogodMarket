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
   <style>
        .underline {
            border: none;
            border-bottom: 1px solid black;
            outline: none;
            width: 100%;
        }
        .container {
            width: 80%;
            margin: 0 auto;
        }
        h2 {
            text-align: center; 
        }
    </style>

<!-- Booking Form -->
<div class="container-fluid">
    <form action="" id="book-form">
        <input type="hidden" name="space_id" value="<?php echo $_GET['id'] ?>">
        
        <!-- Start Date Field -->
        <div class="form-group">
            <label for="date_application" class="control-label">Application Date</label>
            <input type="date" name="date_application" id="date_application" class="form-control form-control-sm rounded-0" required>
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
        

        <!-- Terms and Conditions Checkbox -->
        <div class="form-group">
            <label class="control-label">
                <input type="checkbox" id="agree_terms" required>
                I have read and agree to the <a href="terms.html" target="_blank">Terms and Conditions</a> and <a href="policy.html" target="_blank">Privacy Policy</a>.
            </label>
        </div>

        <!-- Message/Error Display -->
        <div id="msg" class="text-danger"></div>
    </form>
</div>

<!-- Contract Modal -->
<div class="modal fade" id="contract_modal" tabindex="-1" role="dialog" aria-labelledby="contract_modal_label" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="contract_modal_label">Confirm Contract</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container">
                    <h2>APPLICATION TO LEASE MARKET STALL</h2>

                    <p>The Chairman, Market Committee<br>Municipality of Sogod</p>

                    <p>Sir:</p>

                    <p>I hereby apply under the following contract for the lease of Market Stall No. <span class="underline">__________</span> of the Municipal Market of Sogod. I am <span class="underline">__________</span> years of age, <span class="underline">__________</span> citizen, married to <span class="underline">__________</span>, and a resident of <span class="underline">__________</span>.</p>

                    <p>Should the above-mentioned stall be leased to me in accordance with the market rules and regulations, I promise to hold the same under the following conditions:</p>

                    <ol>
                        <li>That while I am occupying or leasing this stall (or these stalls), I shall, at all times, have my picture and that of my helper (or those of my helpers) conveniently framed and displayed conspicuously in the stall.</li>
                        <li>That I shall keep the stall (or stalls) in good sanitary condition at all times and comply strictly with all sanitary and market rules and regulations now existing or which may hereafter be promulgated.</li>
                        <li>That I shall pay the corresponding occupancy fee, two (2) months advance payment of the monthly rental for the booth (or booths), or stall (or stalls) in the manner prescribed by this ordinance.</li>
                        <li>The business to be conducted in the stall (or stalls) is owned exclusively by me.</li>
                        <li>That I will allow the Market Administrator and other authorized agency to inspect all equipment or paraphernalia used in my business during business hours to ensure that they are in accordance with the regulation set forth by the Bureau of Standard and other agencies.</li>
                        <li>That I shall not sublease or sell this privilege of the stall (stalls or booths), or otherwise permit another person to conduct a business therein.</li>
                        <li>Any violation on my part or on the part of my helpers of the foregoing conditions shall be sufficient cause for market authorities to cancel this contract after due process has been instituted.</li>
                    </ol>

                    <p>Very Respectfully,</p>

                    <p><span class="underline">________________________</span></p>

                    <p>I, <span class="underline">________________________</span>, do hereby state that I am the person who signed the foregoing statement/application, that I have read the same, and that the contents hereof are true to the best of my knowledge and belief.</p>

                    <p>Applicant</p>

                    <p>SUBSCRIBED AND SWORN to before me, in the Municipality of Sogod, Philippines, this <span class="underline">__________</span> day of <span class="underline">________________</span>, <span class="underline">__________</span>. Applicant/Affiant exhibited his/her Community Tax Receipt No. <span class="underline">__________</span> issued on <span class="underline">____________________</span>, at <span class="underline">____________________</span>, Philippines.</p>

                    <p><span class="underline">________________________</span> (Applicant's Signature)</p>
                    <p>
                        <a href="uploads/contracts/contractOfLease.pdf" class="btn btn-info" target="_blank" download>
                            <i class="fas fa-download"></i> Download Contract of Lease
                        </a>
                    </p>
                    <p>
                        <a href="uploads/contracts/applicationToLease.pdf" class="btn btn-info" target="_blank" download>
                            <i class="fas fa-download"></i> Download Application to Lease
                        </a>
                    </p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirm_contract">Confirm</button>
            </div>
        </div>
    </div>
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

            // Check if the checkbox is checked
            if (!$('#agree_terms').is(':checked')) {
                $('#msg').text('You must agree to the terms and conditions before submitting.');
                return; // Prevent form submission
            }

            // Show contract modal for confirmation
            const monthlyRate = parseFloat("<?php echo isset($monthly_rate) ? $monthly_rate : 0 ?>");
            const months = parseInt($('#months_to_rent').val());
            const amount = monthlyRate * months;

            // Set values for the contract modal
            $('#contract_monthly_rate').text(monthlyRate.toFixed(2));
            $('#contract_total_amount').text(amount.toFixed(2));

            // Show the contract modal
            $('#contract_modal').modal('show');

            // Handle contract confirmation
            $('#confirm_contract').click(function() {
                // Hide the contract modal
                $('#contract_modal').modal('hide');

                // Proceed with form submission
                const _this = $('#book-form');
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
    });
</script>
