<style>
    #uni_modal .modal-content > .modal-footer, #uni_modal .modal-content > .modal-header {
        display: none;
    }
</style>

<!-- Main Content: Application Process -->
<div class="container-fluid">
    <h3 class="text-center">Application Process</h3>
    <p class="text-center">Welcome! Here's an overview of the application process.</p>
    <ul>
        <li>Step 1: Make Application</li>
        <li>Step 2: Submit required documents</li>
        <li>Step 3: Make a Payment</li>
        <li>Step 4: Confirmation</li>
    </ul>
    <div class="text-right mt-4">
        <!-- Added a unique ID for the Close button -->
        <button type="button" class="btn btn-secondary btn-flat" id="close-modal-btn">Close</button>
    </div>
</div>

<script>
    // Handle redirection when the Close button is clicked
    $('#close-modal-btn').click(function() {
        // Close the modal first
        $('#uni_modal').modal('hide');

        // Redirect to the desired URL
        window.location.href = "http://localhost/sogodmarket/index.php";
    });
</script>
