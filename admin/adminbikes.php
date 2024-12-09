<style>
    #uni_modal .modal-content > .modal-footer, #uni_modal .modal-content > .modal-header {
        display: none;
    }
</style>
<?php

$host = 'localhost'; // Replace with your host
$user = 'root'; // Replace with your database username
$password = ''; // Replace with your database password
$dbname = 'sogod_market_db'; // Replace with your database name

// Create connection
$conn = new mysqli($host, $user, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch the category_id from the request
$category_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($category_id > 0) {
    // Updated query to fetch space details along with remaining days
    $query = $conn->prepare("
        SELECT 
            sl.id AS space_id, 
            CONCAT( stl.name,' ', sl.space_name) AS space_name, 
            sl.quantity, 
            rl.client_id, 
            CONCAT(c.firstname, ' ', c.lastname) AS client_name,
            DATEDIFF(rl.date_end, CURDATE()) AS remaining_days
        FROM 
            space_list sl
        LEFT JOIN 
            rent_list rl ON sl.id = rl.space_id
        LEFT JOIN 
            clients c ON rl.client_id = c.id
         LEFT JOIN 
            space_type_list stl ON sl.space_type_id = stl.id
        WHERE 
            sl.category_id = ? AND sl.status = 1
    ");
    $query->bind_param("i", $category_id);
    $query->execute();
    $result = $query->get_result();
} else {
    die("Invalid Category ID.");
}
?>
<!-- Main Content: Application Process -->
<div class="container-fluid">
    <div class="modal-header">
        <h5 class="modal-title" id="categorySpacesModalLabel">Spaces for Selected Category</h5>
    </div>
    <div class="modal-body">
        <?php if ($result->num_rows > 0): ?>
            <table class="table table-bordered table-striped table-hover">
                <thead class="thead-light">
                    <tr>
                        <th>#</th>
                        <th>Space Name</th>
                        <th>Status</th>
                        <th>Client Name</th>
                        <th>Remaining Days</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $i = 1;
                    while ($row = $result->fetch_assoc()):
                        $status = $row['quantity'] > 0 ? 'Available' : 'Unavailable';
                        $remaining_days = isset($row['remaining_days']) ? $row['remaining_days'] . ' days' : 'N/A';
                        ?>
                        <tr>
                            <td><?php echo $i++; ?></td>
                            <td><?php echo htmlspecialchars($row['space_name']); ?></td>
                            <td class="<?php echo $status == 'Available' ? 'text-success' : 'text-danger'; ?>">
                                <?php echo $status; ?>
                            </td>
                            <td><?php echo htmlspecialchars($row['client_name'] ?: 'No Client'); ?></td>
                            <td><?php echo htmlspecialchars($remaining_days); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>  
            <p class="text-center text-muted">No spaces available for this category.</p>
        <?php endif; ?>
    </div>
    <div class="text-right mt-4">
        <button type="button" class="btn btn-secondary btn-flat" id="close-modal-btn">Close</button>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Handle redirection when the Close button is clicked
    $('#close-modal-btn').click(function() {
        $('#uni_modal').modal('hide');
        window.location.href = "http://localhost/sogodmarket/admin/index.php";
    });
</script>
