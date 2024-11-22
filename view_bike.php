<?php 
$client_id = $_SESSION['id']; // Assuming you have this session variable set for the user

// Query to check if the session ID is in the rent_list table
$check_rent_list = $conn->query("SELECT * FROM rent_list WHERE client_id = '{$client_id}'");

// Check if the client_id already exists in rent_list
$is_rented = $check_rent_list->num_rows > 0;

// Fetch space details
$bikes = $conn->query("SELECT b.*, c.category, bb.name as brand 
                        FROM `space_list` b 
                        INNER JOIN categories c ON b.category_id = c.id 
                        INNER JOIN space_type_list bb ON b.space_type_id = bb.id 
                        WHERE md5(b.id) = '{$_GET['id']}' ");
 
if($bikes->num_rows > 0){
    foreach($bikes->fetch_assoc() as $k => $v){
        $$k = stripslashes($v);
    }
    $upload_path = base_app.'/uploads/'.$id;

    $img = "";
    if(is_dir($upload_path)){
        $fileO = scandir($upload_path);
        if(isset($fileO[2]))
            $img = "uploads/".$id."/".$fileO[2];
    }
}
?>
<section class="py-5">
    <div class="container px-4 px-lg-5 my-5">
        <div class="row gx-4 gx-lg-5 align-items-center">
            <div class="col-md-6">
                <img class="card-img-top mb-5 mb-md-0 border border-dark" loading="lazy" id="display-img" src="<?php echo validate_image($img) ?>" alt="..." />
                <div class="mt-2 row gx-2 gx-lg-3 row-cols-4 row-cols-md-3 row-cols-xl-4 justify-content-start">
                    <?php 
                        foreach($fileO as $k => $img):
                            if(in_array($img, array('.','..')))
                                continue;
                    ?>
                    <div class="col">
                        <a href="javascript:void(0)" class="view-image <?php echo $k == 2 ? "active":'' ?>"><img src="<?php echo validate_image('uploads/'.$id.'/'.$img) ?>" loading="lazy"  class="img-thumbnail" alt=""></a>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="col-md-6">
                <h1 class="display-5 fw-bolder border-bottom border-primary pb-1"><?php echo $space_name ?></h1>
                <p class="m-0"><small>Space Type: <?php echo $brand ?></small> <br>
                <small><?php echo $category ?></small>
                </p>
                <div class="fs-5 mb-5">
                &#8369; <span id="price"><?php echo number_format($monthly_rate) ?></span> <small>/ month</small>
                <br>
                <span><small><b>Available Unit:</b> <span id="avail"><?php echo $quantity ?></span></small></span>
                </div>
                <!-- If the user has already rented a space, show a message instead of the button -->
                <?php if($is_rented): ?>
                    <div class="alert alert-warning">
                        You already have a booking application.
                    </div>
                <?php else: ?>
                    <button class="btn btn-outline-dark flex-shrink-0" type="button" id="book_bike">
                        <i class="bi-cart-fill me-1"></i>
                        Apply
                    </button>
                <?php endif; ?>
                <p class="lead"><?php echo stripslashes(html_entity_decode($description)) ?></p>
            </div>
        </div>
    </div>
</section>

<!-- Related items section-->
<section class="py-5 bg-light">
    <div class="container px-4 px-lg-5 mt-5">
        <h2 class="fw-bolder mb-4">Related Spaces</h2>
        <div class="row gx-4 gy-2 gx-lg-5 row-cols-4 justify-content-center">
        <?php 
            $bikes = $conn->query("SELECT b.*, c.category, bb.name as brand 
                                   FROM `space_list` b 
                                   INNER JOIN categories c ON b.category_id = c.id 
                                   INNER JOIN space_type_list bb ON b.space_type_id = bb.id 
                                   WHERE b.status = 1 
                                   AND (b.category_id = '{$category_id}' OR b.space_type_id = '{$space_type_id}') 
                                   AND b.id !='{$id}' 
                                   AND b.quantity > 0  -- Ensure quantity is greater than 0
                                   ORDER BY rand() LIMIT 4 ");
            while($row = $bikes->fetch_assoc()):
        ?>
            <a class="col mb-5 text-decoration-none text-dark" href=".?p=view_bike&id=<?php echo md5($row['id']) ?>">
                <div class="card h-100 bike-item">
                    <img class="card-img-top w-100" src="<?php echo validate_image("uploads/thumbnails/".$row['id'].".png") ?>" alt="..." />
                    <div class="card-body p-4">
                        <div class="">
                            <h5 class="fw-bolder"><?php echo $row['space_name'] ?></h5>
                            <span><b>Price: </b><?php echo number_format($row['monthly_rate']) ?></span>
                            <p class="m-0"><small>Space Name: <?php echo $row['brand'] ?></small> <br>
                            <small><?php echo $row['category'] ?></small>
                            </p>
                            <p class="m-0 truncate-3"><small class="text-muted"><?php echo strip_tags(html_entity_decode(stripslashes($row['description']))) ?></small></p>
                        </div>
                    </div>
                </div>
            </a>
            <?php endwhile; ?>
        </div>
    </div>
</section>

<script>
    $(function(){
        $('.view-image').click(function(){
            var _img = $(this).find('img').attr('src');
            $('#display-img').attr('src', _img);
            $('.view-image').removeClass("active");
            $(this).addClass("active");
        });
        
        $('#book_bike').click(function(){
            if ('<?php echo $_SESSION['id'] ?>' <= 0) {
                window.location.href = "login.php";
                return false;
            }
            uni_modal("Rental Booking", 
                      "book_to_rent.php?id=<?php echo isset($id) ? $id : '' ?>&monthly_rate=<?php echo $monthly_rate; ?>", 
                      'mid-large');
        });
    });
</script>
