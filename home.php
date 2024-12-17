<!-- Header with Next and Previous Buttons -->
<div class="bg-dark py-5 position-relative" id="main-headers">
    <div class="container px-4 px-lg-5 my-5">
        <div class="text-center text-white">
            <h1 class="display-4 fw-bold">Sogod Market Vendor's Leasing and Renewal Management System</h1>
            <p class="lead fw-normal text-white-50 mb-0">Rent Now!</p>
        </div>
    </div>
    <!-- Slider Control Buttons -->
    <button id="prevButton" class="slider-btn slider-btn-left" aria-label="Previous">
        <i class="fa fa-chevron-left"></i>
    </button>
    <button id="nextButton" class="slider-btn slider-btn-right" aria-label="Next">
        <i class="fa fa-chevron-right"></i>
    </button>
</div>

<section class="py-5">
    <div class="container px-4 px-lg-5 mt-5">
            <!-- Categories Title -->
            <div class="text-center mb-4">
                <h2 class="fw-bold text-dark">CATEGORIES</h2>
            </div>
        <div class="row gx-4 gx-lg-5 row-cols-md-3 row-cols-xl-4 row-cols-sm-1 justify-content-center">
            <?php 
                $bikes = $conn->query(" 
                    SELECT 
                        c.*, 
                        COALESCE(COUNT(sl.id), 0) AS total_rows,
                        COALESCE(COUNT(CASE WHEN sl.quantity <= 0 THEN 1 END), 0) AS total_quantity_leq_zero,
                        COALESCE(COUNT(CASE WHEN sl.quantity > 0 THEN 1 END), 0) AS total_quantity_gt_zero,
                        c.category_image
                    FROM 
                        categories c
                    LEFT JOIN 
                        space_list sl 
                    ON 
                        c.id = sl.category_id AND sl.status = 1
                    WHERE 
                        c.status = 1
                    GROUP BY 
                        c.id
                    ORDER BY 
                        c.category ASC
                ");
                
                if($bikes->num_rows > 0): 
                    while($row = $bikes->fetch_assoc()):
                        $upload_path = base_app.'/uploads/'.$row['category_image'];
                        $img = "/uploads/categories/".$row['category_image'];
            ?>
            <a class="col mb-5 text-decoration-none text-dark" href="./?p=bikes&c=<?php echo md5($row['id']) ?>">
                <div class="card bike-item shadow-lg rounded-3 overflow-hidden transition-transform transform-hover">
                    <div class="card-body">
                        <center>
                            <h5 class="fw-bold text-uppercase"><?php echo $row['category'] ?></h5>
                            <p class="small text-muted"><?php echo $row['description'] ?></p>
                        </center>
                        <p class="m-0"><b>Total Space: </b><?php echo number_format($row['total_rows']) ?></p>
                        <p class="m-0"><b>Taken: </b><?php echo number_format($row['total_quantity_leq_zero']) ?></p>
                        <p class="m-0"><b>Available: </b><?php echo number_format($row['total_quantity_gt_zero']) ?></p>
                    </div>
                    <img class="card-img-top bike-cover rounded" src="<?php echo validate_image($img) ?>" alt="Bike Image" />
                </div>
            </a>
            <?php 
                    endwhile;
                else: 
            ?>
            <div class="col-12 text-center">
                <p class="lead fw-normal">No bikes available at the moment.</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- JavaScript for Background Slider -->
<script>
    const header = document.getElementById("main-headers");
    const images = [
        'uploads/buyingvegetables.jpg',
        'uploads/thumbnails/1.png'
    ];
    let currentImageIndex = 0;

    function changeBackgroundImage() {
        header.style.backgroundImage = `url(${images[currentImageIndex]})`;
    }

    // Initial background image
    changeBackgroundImage();

    // Auto-change image every 5 seconds
    setInterval(() => {
        currentImageIndex = (currentImageIndex + 1) % images.length;
        changeBackgroundImage();
    }, 5000);

    // Next and Previous Button Functionality
    document.getElementById("nextButton").addEventListener("click", () => {
        currentImageIndex = (currentImageIndex + 1) % images.length;
        changeBackgroundImage();
    });

    document.getElementById("prevButton").addEventListener("click", () => {
        currentImageIndex = (currentImageIndex - 1 + images.length) % images.length;
        changeBackgroundImage();
    });
</script>

<!-- CSS for Background Slider and Buttons -->
<style>
    /* Button Styling */
    .slider-btn {
        position: absolute;
        top: 50%;
        padding: 15px;
        background-color: rgba(0, 0, 0, 0.6);
        color: white;
        border: none;
        cursor: pointer;
        transform: translateY(-50%);
        z-index: 2;
        border-radius: 50%;
        font-size: 20px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.5);
        transition: background-color 0.3s ease, box-shadow 0.3s ease;
    }

    /* Left Button */
    .slider-btn-left {
        left: 15px;
    }

    /* Right Button */
    .slider-btn-right {
        right: 15px;
    }

    /* Hover Effects */
    .slider-btn:hover {
        background-color: rgba(0, 0, 0, 0.8);
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.6);
    }

    /* Focus Effects */
    .slider-btn:focus {
        outline: none;
        box-shadow: 0 0 10px rgba(255, 255, 255, 0.7);
    }

    /* Icon Styling */
    .slider-btn i {
        font-size: 24px;
    }

    /* Dark overlay */
    #main-headers::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.4);
        z-index: 1;
    }

    /* Adjust the height of the header */
    #main-headers {
        position: relative;
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        transition: background-image 1s ease-in-out;
        min-height: 500px;
    }

    /* Optional: Adjust the padding inside the container for more space */
    #main-headers .container {
        padding-top: 150px;
        padding-bottom: 150px;
    }

    /* Card Hover Effect */
    .bike-item:hover {
        transform: translateY(-10px);
        box-shadow: 0 8px 15px rgba(0, 0, 0, 0.3);
    }

    /* Rounded image for category cards */
    .bike-cover {
        border-radius: 10px;
    }

    /* Transition on card hover */
    .transition-transform {
        transition: transform 0.3s ease-in-out;
    }

    .transform-hover:hover {
        transform: translateY(-10px);
    }
</style>

<!-- Add FontAwesome CDN for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
