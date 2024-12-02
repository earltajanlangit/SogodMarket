<?php
require_once('../../config.php');
// Retrieve and sanitize the id from the GET request
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Prepare and execute the SQL query
$stmt = $conn->prepare("SELECT * FROM rent_list WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$query = $stmt->get_result();
$data = $query->fetch_assoc();

?>

<div class="container-fluid">
    <form id="setMeetingSchedule">
        <div class="modal-body">
            <div class="form-group">
                <div class="form-group">
				    <label for="reason" class="control-label">Rejection Reason</label>
                     <textarea name="reason" id="reason" cols="30" rows="2" class="form-control form no-resize summernote"></textarea>
			    </div>
                <input type="hidden" id="clientIdInput" name="client_id" value="<?= htmlspecialchars($data['client_id'] ?? '') ?>">
                <input type="hidden" id="bookingIdInput" name="booking_id" value="<?= htmlspecialchars($data['id'] ?? '') ?>"><!-- Hidden input for booking_id -->
            </div>
        </div>
    </form>
</div>

<script>
    $(function () {
        
        // Handle form submission
        $('#setMeetingSchedule').submit(function (e) {
            e.preventDefault(); // Prevent the default form submission
            console.log($(this).serialize()); // Log form data to check if all fields are included
            start_loader(); // Show loader while processing
            
            $.ajax({
                url: _base_url_ + "classes/Master.php?f=reject_application", // Endpoint to handle approval
                method: "POST",
                data: $(this).serialize(), // Serialize the form data, including client_id and booking_id
                dataType: "json",
                error: function(err) {
                    alert_toast("Application approved successfully.", 'success');
                    location.reload(); // Reload the page to reflect changes
                },
                success: function(resp) {
                    if (typeof resp == 'object' && resp.status == 'success') {
                        alert_toast("Application approved successfully.", 'success');
                        location.reload(); // Reload the page to reflect changes
                    } else {
                        alert_toast("An error occurred. Please try again.", 'error');
                    }
                    end_loader(); // Hide loader
                }
            });
        });
    });

    // Function to get URL parameters
    function getUrlParameter(name) {
        var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.href);
        return results == null ? '' : decodeURIComponent(results[1]) || '';
    }
</script>
