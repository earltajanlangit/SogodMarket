<?php 
$title = "";
$sub_title = "";
$cat_description = "";
$google_map_embed_url = ""; // Default value for the Google Map embed URL

// Fetch category details if 'c' parameter is set
if (isset($_GET['c'])) {
    $cat_qry = $conn->query("SELECT * FROM categories WHERE md5(id) = '{$_GET['c']}'");
    if ($cat_qry->num_rows > 0) {
        $result = $cat_qry->fetch_assoc();
        $title = $result['category'];
        $cat_description = $result['description'];
        $google_map_embed_url = $result['google_map_embed_url']; // Fetch the Google Map embed URL
    }
}

// Fetch space type details if 's' parameter is set
if (isset($_GET['s'])) {
    $sub_cat_qry = $conn->query("SELECT * FROM space_type_list WHERE md5(id) = '{$_GET['s']}'");
    if ($sub_cat_qry->num_rows > 0) {
        $result = $sub_cat_qry->fetch_assoc();
        $title = $result['name'];
    }
}
?>
<!-- Header -->
<header class="bg-dark py-5" id="main-header">
    <div class="container px-4 px-lg-5 my-5">
        <div class="text-center text-white">
            <h1 class="display-4 fw-bolder"><?php echo $title ?></h1>
            <p class="lead fw-normal text-white-50 mb-0"><?php echo $sub_title ?></p>
        </div>
    </div>
</header>
<!-- Section -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <div class="<?php echo (isset($_GET['c'])) ? 'col-md-8' : 'col-md-12' ?>">
                <?php 
                    if (isset($_GET['search'])) {
                        echo "<h4 class='text-center'><b>Search Result for '".$_GET['search']."'</b></h4><hr/>";
                    }
                ?>
                <div class="row gx-2 gx-lg-2 row-cols-1 row-cols-sm-1 row-cols-md-3 row-cols-xl-4">
                    <?php 
                        $whereData = "";
                        if (isset($_GET['search'])) {
                            if (!empty($whereData)) $whereData .= " and ";
                            $whereData .= " and (b.space_name LIKE '%{$_GET['search']}%' or c.category LIKE '%{$_GET['search']}%' or b.description LIKE '%{$_GET['search']}%' or bb.name LIKE '%{$_GET['search']}%')";
                        }
                        if (isset($_GET['c'])) {
                            if (!empty($whereData)) $whereData .= " and ";
                            $whereData .= " and (md5(category_id) = '{$_GET['c']}')";
                        }
                        if (isset($_GET['s'])) {
                            if (!empty($whereData)) $whereData .= " and ";
                            $whereData .= " and (md5(space_type_id) = '{$_GET['s']}')";
                        }
                        $sql = "SELECT b.*, c.category, bb.name as brand 
                                FROM space_list b 
                                INNER JOIN categories c ON b.category_id = c.id 
                                INNER JOIN space_type_list bb ON b.space_type_id = bb.id  
                                WHERE b.status = 1 {$whereData} 
                                ORDER BY rand()";
                        $bikes = $conn->query($sql);
                        while ($row = $bikes->fetch_assoc()):
                    ?>
                  <a class="col-md-12 mb-5 text-decoration-none text-dark <?php echo ($row['quantity'] <= 0) ? 'pointer-events-none' : ''; ?>" 
                   href="<?php echo ($row['quantity'] > 0) ? '.?p=view_bike&id=' . md5($row['id']) : '#'; ?>">
                        <div class="card bike-item position-relative">
                            <!-- bike image -->
                            <img class="card-img-top w-100" src="<?php echo validate_image('uploads/thumbnails/'.$row['id'].'.png') ?>" loading="lazy" alt="..." />
                            <!-- Not Available overlay -->
                            <?php if ($row['quantity'] <= 0): ?>
                            <div class="not-available-overlay d-flex align-items-center justify-content-center">
                                <span class="text-white fw-bold">NOT AVAILABLE</span>
                            </div>
                            <?php endif; ?>
                            <!-- bike details -->
                            <div class="card-body p-4">
                                <div class="">
                                    <!-- bike name -->
                                    <h5 class="fw-bolder"><?php echo $row['space_name'] ?></h5>
                                    <!-- bike price -->
                                    <span><b>Monthly Rate: </b><?php echo number_format($row['monthly_rate']) ?></span>
                                </div>
                                <p class="m-0">
                                    <small>Space Type: <?php echo $row['brand'] ?></small> <br>
                                    <small><?php echo $row['category'] ?></small>
                                </p>
                                <p class="m-0 truncate-3">
                                    <small><?php echo strip_tags(html_entity_decode(stripslashes($row['description']))) ?></small>
                                </p>
                            </div>
                        </div>
                    </a>
                    <?php endwhile; ?>
                    <?php 
                        if ($bikes->num_rows <= 0) {
                            echo "<h4 class='text-center'><b>No Space Listed Yet.</b></h4>";
                        }
                    ?>
                </div>       
            </div>
            <?php if (isset($_GET['c'])): ?>
                <div class="col-md-4 border-left border-2">
                <h3 class="text-center"><?php echo $title. " Category" ?></h3>
                <hr>
                <div>
                    <?php echo isset($cat_description) ? stripslashes(html_entity_decode($cat_description)) : '' ?>
                </div>

                <!-- Add Google Map -->
                <div class="mt-3">
                    <h4 class="text-center">Location</h4>
                    <div class="map-container">
                        <?php if (!empty($google_map_embed_url)): ?>
                            <iframe src="<?php echo htmlspecialchars($google_map_embed_url); ?>" 
                                    width="400" height="300" style="border:0;" allowfullscreen="" loading="lazy" 
                                    referrerpolicy="no-referrer-when-downgrade">
                            </iframe>
                        <?php else: ?>
                            <p class="text-center text-muted">No location map available for this category.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<style>
/* Style for the Not Available overlay */
.not-available-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(0, 0, 0, 0.7);
    opacity: 0;
    transition: opacity 0.3s ease;
    z-index: 2;
}

.card.bike-item:hover .not-available-overlay {
    opacity: 1;
}
</style>
