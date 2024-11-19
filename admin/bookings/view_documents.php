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
            
            <div class="modal-body">
                <!-- Static Documents -->
                <div class="row">
                <?php 
                $qry = $conn->query("SELECT * FROM documents WHERE client_id = '{$client_id}'");
                $row = $qry->fetch_assoc(); // Fetching only one row
                ?>
                <div class="modal-body">
                    <div class="row">
                        <?php if ($row): ?>
                        <!-- Cedule File -->
                        <div class="row">
                            <div class="col-12 pl-4"> <!-- Added padding-left using Bootstrap class -->
                                <h5>Cedule File</h5>
                                <img src="/SogodMarket/uploads/documents/<?php echo $row['cedule_file']; ?>" alt="Cedule File" class="img-fluid" />
                            </div>
                        </div>

                        <!-- Photo ID File -->
                        <div class="row mt-4">
                            <div class="col-12 pl-4"> <!-- Added padding-left using Bootstrap class -->
                                <h5>Photo ID File</h5>
                                <img src="/SogodMarket/uploads/documents/<?php echo $row['photo_id_file']; ?>" alt="Photo ID File" class="img-fluid" />
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="col-12 mt-4 pl-4"> <!-- Added padding-left using Bootstrap class -->
                            <h5>Description</h5>
                            <p><?php echo htmlspecialchars($row['description']); ?></p>
                        </div>
                        <?php else: ?>
                        <!-- No Documents Found Message -->
                        <div class="col-12 text-center">
                            <p class="text-danger">No documents found for this client.</p>
                        </div>
                        <?php endif; ?>
                    </div>
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
