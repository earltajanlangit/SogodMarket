<!-- Header with Next and Previous Buttons -->
<div class="bg-dark py-5 position-relative" id="main-headers">
    <div class="container px-4 px-lg-5 my-5">
        <div class="text-center text-white">
            <h1 class="display-4 fw-bolder">Sogod Market Vendor's Leasing and Renewal Management System</h1>
            <p class="lead fw-normal text-white-50 mb-0">Rent Now!</p>
        </div>
    </div>
    <!-- Slider Control Buttons -->
    <button id="prevButton" class="slider-btn slider-btn-left"><</button>
    <button id="nextButton" class="slider-btn slider-btn-right">></button>
</div>

<section class="py-5">
    <div class="container px-4 px-lg-5 mt-5">
        <div class="row gx-4 gx-lg-5 row-cols-md-3 row-cols-xl-4 row-cols-sm-1 justify-content-center">
            <?php 
                $bikes = $conn->query(" 
                    SELECT 
                        c.*, 
                        COALESCE(COUNT(sl.id), 0) AS total_rows,
                        COALESCE(COUNT(CASE WHEN sl.quantity <= 0 THEN 1 END), 0) AS total_quantity_leq_zero,
                        COALESCE(COUNT(CASE WHEN sl.quantity > 0 THEN 1 END), 0) AS total_quantity_gt_zero
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
                        $upload_path = base_app.'/uploads/'.$row['id'];
                        $img = "/uploads/thumbnails/".$row['id'].'.png';
            ?>
            <a class="col mb-5 text-decoration-none text-dark" href="./?p=bikes&c=<?php echo md5($row['id']) ?>">
                <div class="card bike-item shadow rounded-3 overflow-hidden">
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