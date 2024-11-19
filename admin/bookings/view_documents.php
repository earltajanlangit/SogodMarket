<?php
// Include the configuration file for the database connection
require_once('../../config.php');

// Sanitize client_id to prevent SQL injection
$client_id = isset($_GET['client_id']) ? intval($_GET['client_id']) : 0;
?>

<!-- Modal structure -->
<div class="modal fade" id="uni_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h5 class="modal-title">Uploaded Documents</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>



            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm btn-flat" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Activation Script -->
<script>
    // To open the modal
    $('#uni_modal').modal('show');
</script>
