<!-- Header -->
<headclassr class="bg-dark py-5" id="main-header">
    <div class="container px-4 px-lg-5 my-5">
        <div class="text-center text-white">
            <h1 class="display-4 fw-bolder">Sogod Market Vendor's Leasing and Renewal Management System</h1>
            <p class="lead fw-normal text-white-50 mb-0">Rent Now!</p>
        </div>
    </div>
</headclassr>
<section class="py-5">
    <div class="container px-4 px-lg-5 mt-5">
        <div class="row gx-4 gx-lg-5 row-cols-md-3 row-cols-xl-4 row-cols-sm-1 justify-content-center">
            <?php 
              $bikes = $conn->query("SELECT b.*, c.category, bb.name as brand 
                       FROM `space_list` b 
                       INNER JOIN categories c ON b.category_id = c.id 
                       INNER JOIN space_type_list bb ON b.space_type_id = bb.id 
                       WHERE b.status = 1 AND b.quantity > 0");
                if($bikes->num_rows > 0): 
                    while($row = $bikes->fetch_assoc()):
                        $upload_path = base_app.'/uploads/'.$row['id'];
                        $img = "/uploads/thumbnails/".$row['id'].'.png';
            ?>
            <a class="col mb-5 text-decoration-none text-dark" href=".?p=view_bike&id=<?php echo md5($row['id']) ?>">
                <div class="card bike-item shadow">
                    <!-- bike image -->
                    <img class="card-img-top w-100 bike-cover" src="<?php echo validate_image($img) ?>" alt="..." />
                    <!-- bike details -->
                    <div class="card-body p-4">
                        <div class="">
                            <!-- bike name -->
                            <h5 class="fw-bolder"><?php echo $row['space_name'] ?></h5>
                            <!-- bike price -->
                            <span><b>Daily Rate: </b><?php echo number_format($row['daily_rate']) ?></span>
                        </div>
                        <p class="m-0"><small>Space Type: <?php echo $row['brand'] ?></small><br>
                        <small><?php echo $row['category'] ?></small></p>
                        <p class="m-0 truncate-3"><small><?php echo strip_tags(html_entity_decode(stripslashes($row['description']))) ?></small></p>
                    </div>
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
