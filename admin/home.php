<style>
    #uni_modal  > .modal-footer, #uni_modal  > .modal-header {
        display: none;
    }
</style>
    <h1>Welcome to Sogod Market Vendor's Leasing and Renewal Management System</h1>
    <hr>
    <div class="row">
        <!-- Total Categories -->
        <div class="col-12 col-sm-6 col-md-3">
            <a href="http://localhost/sogodmarket/admin/?page=maintenance/category" class="text-decoration-none text-dark">
                <div class="info-box">
                    <span class="info-box-icon bg-light elevation-1"><i class="fas fa-th-list"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Categories</span>
                        <span class="info-box-number">
                            <?php
                                $category = $conn->query("SELECT count(id) as total FROM categories WHERE status = '1'")->fetch_assoc()['total'];
                                echo number_format($category);
                            ?>
                        </span>
                    </div>
                </div>
            </a>
        </div>
        
        <!-- Total Type of Spaces -->
        <div class="col-12 col-sm-6 col-md-3">
            <a href="http://localhost/sogodmarket/admin/?page=maintenance/brands" class="text-decoration-none text-dark">
                <div class="info-box mb-3">
                    <span class="info-box-icon bg-info elevation-1"><i class="fas fa-copyright"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Type of Spaces</span>
                        <span class="info-box-number">
                            <?php
                                $brands = $conn->query("SELECT count(id) as total FROM space_type_list WHERE status = '1'")->fetch_assoc()['total'];
                                echo number_format($brands);
                            ?>
                        </span>
                    </div>
                </div>
            </a>
        </div>

        <!-- Total Spaces Listed -->
        <div class="col-12 col-sm-6 col-md-3">
            <a href="http://localhost/sogodmarket/admin/?page=bike" class="text-decoration-none text-dark">
                <div class="info-box mb-3">
                    <span class="info-box-icon bg-success elevation-1"><i class="nav-icon fas fa-store"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Spaces Listed</span>
                        <span class="info-box-number">
                            <?php
                                $bike = $conn->query("SELECT count(id) as total FROM space_list WHERE status = '1'")->fetch_assoc()['total'];
                                echo number_format($bike);
                            ?>
                        </span>
                    </div>
                </div>
            </a>
        </div>

        <!-- Clients -->
        <div class="col-12 col-sm-6 col-md-3">
            <a href="http://localhost/sogodmarket/admin/?page=users" class="text-decoration-none text-dark">
                <div class="info-box mb-3">
                    <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-users"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Clients</span>
                        <span class="info-box-number">
                            <?php
                                $clients = $conn->query("SELECT count(id) as total FROM clients")->fetch_assoc()['total'];
                                echo number_format($clients);
                            ?>
                        </span>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Category Listings -->
    <div class="container px-4 px-lg-5 mt-5">
    <div class="row gx-4 gx-lg-5 row-cols-md-3 row-cols-xl-4 row-cols-sm-1 justify-content-center">
    <?php
$bikes = $conn->query("SELECT
        c.*,
        COALESCE(COUNT(sl.id), 0) AS total_rows,
        COALESCE(COUNT(CASE WHEN sl.quantity <= 0 THEN 1 END), 0) AS total_quantity_leq_zero,
        COALESCE(COUNT(CASE WHEN sl.quantity > 0 THEN 1 END), 0) AS total_quantity_gt_zero,
        c.category_image,
        GROUP_CONCAT(CONCAT(cl.firstname, ' ', cl.lastname) SEPARATOR ', ') AS clients_rented
    FROM
        categories c
    LEFT JOIN
        space_list sl
        ON c.id = sl.category_id AND sl.status = 1
    LEFT JOIN
        rent_list rl
        ON rl.space_id = sl.id
    LEFT JOIN
        clients cl
        ON rl.client_id = cl.id
    WHERE
        c.status = 1
    GROUP BY
        c.id
    ORDER BY
        c.category ASC
");

if ($bikes->num_rows > 0):
    while ($row = $bikes->fetch_assoc()):
        $upload_path = base_app . '/uploads/' . $row['category_image'];
        $img = "/uploads/categories/" . $row['category_image'];

        // Fetching all the space names related to the current category
        $spaces = $conn->query("SELECT space_name, quantity FROM space_list WHERE category_id = {$row['id']} AND status = 1");
        $space_details = [];
        while ($space = $spaces->fetch_assoc()) {
            $status = $space['quantity'] > 0 ? 'Available' : 'Occupied';
            $space_details[] = htmlspecialchars($space['space_name']) . " - <span class='text-" . ($status == 'Available' ? 'success' : 'danger') . "'>$status</span>";
        }
        $space_details_str = implode("<br>", $space_details);
?>

<a class="col mb-5 text-decoration-none text-dark" href="javascript:void(0);" onclick="openUniModal('<?php echo $row['id'] ?>')">
    <div class="card bike-item shadow rounded-3 overflow-hidden position-relative">
        <div class="card-body">
            <center>
                <h5 class="fw-bolder text-uppercase"><?php echo $row['category'] ?></h5>
                <p class="small text-muted"><?php echo $row['description'] ?></p>
            </center>
            <p class="m-0"><b>Total Space: </b><?php echo number_format($row['total_rows']) ?></p>
            <p class="m-0"><b>Taken: </b><?php echo number_format($row['total_quantity_leq_zero']) ?></p>
            <p class="m-0"><b>Available: </b><?php echo number_format($row['total_quantity_gt_zero']) ?></p>
        </div>
        <img class="card-img-top bike-cover" src="<?php echo validate_image($img) ?>" alt="Bike Image" />

        <!-- Overlay Text: Show Space Names and Availability -->
        <div class="card-overlay position-absolute top-0 start-0 w-100 h-100 d-flex justify-content-center align-items-center">
            <div class="text-center text-white">
                <h5 class="text-uppercase"><?php echo $row['category'] ?></h5>
                <p class="mt-2"><b>Spaces:</b></p> <!-- Add "Space:" as a title -->
                <p>
                    <?php 
                        // Check if there are space names to display
                        if (!empty($space_details_str)) {
                            echo $space_details_str; // Display space details with status
                        } else {
                            echo "<div><b>No Spaces Listed</b></div>";
                        }
                    ?>
                </p>
            </div>
        </div>
    </div>
</a>

<?php
    endwhile;
else:
?>
<div class="col-12 text-center">
    <p class="lead fw-normal">No categories available at the moment.</p>
</div>
<?php endif; ?>

    </div>
</div>


    <script>
    function openUniModal(id) {
        uni_modal("", './../admin/adminbikes.php?id=' + id);
    }
</script>

    <!-- CSS for Background Slider and Buttons -->
    <style>
        #main-headers {
            position: relative;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            transition: background-image 1s ease-in-out;
            min-height: 300px; /* Adjust the height as needed */
        }

        /* Dark overlay */
        #main-headers::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.4); /* Dark overlay */
            z-index: 1;
        }

        /* Button Styling */
        .slider-btn {
            position: absolute;
            top: 50%;
            padding: 10px 20px;
            background-color: rgba(0, 0, 0, 0.5);
            color: white;
            border: none;
            cursor: pointer;
            transform: translateY(-50%);
            z-index: 2; /* Ensure buttons are above overlay */
        }

        .slider-btn-left {
            left: 10px;
        }

        .slider-btn-right {
            right: 10px;
        }

        .bike-item {
            transition: transform 0.3s ease-in-out;
            height: 100%;
            display: flex;
            flex-direction: column;
            position: relative;
        }

        .bike-item:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2); /* More prominent shadow */
        }

        .bike-cover {
            object-fit: cover;
            height: 200px;
            width: 100%;
        }

        .card-body {
            flex-grow: 1;
        }

        .card-body p {
            margin-bottom: 10px;
        }

        .card-body h5 {
            font-size: 1.2rem;
        }

        .card-body small {
            font-size: 0.9rem;
        }

        .card-img-top {
            transition: transform 0.3s ease;
        }

        .card-img-top:hover {
            transform: scale(1);
        }

        .card-overlay {
            background: rgba(0, 0, 0, 0.5);
            color: white;
            opacity: 0;
            transition: opacity 0.3s;
            z-index: 2; /* Ensure the overlay is above other content */
        }

        .bike-item:hover .card-overlay {
            opacity: 1;
        }

        .row {
            display: flex;
            flex-wrap: wrap;
        }

        .col {
            display: flex;
            justify-content: center;
        }

        @media (max-width: 768px) {
            .card-body h5 {
                font-size: 1rem;
            }

            .bike-cover {
                height: 180px;
            }
        }
    </style>
