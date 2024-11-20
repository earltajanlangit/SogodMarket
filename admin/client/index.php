<?php if ($_settings->chk_flashdata('success')) : ?>
    <script>
        alert_toast("<?php echo $_settings->flashdata('success') ?>", 'success')
    </script>
<?php endif; ?>

<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title">List of Clients</h3>
    </div>
    <div class="card-body">
        <div class="container-fluid">
            <div class="row">
                <?php 
                $qry = $conn->query("
                SELECT 
                    c.*, 
                    r.id as rent_id, 
                    r.space_id, 
                    r.status, 
                    DATEDIFF(r.date_end, CURDATE()) as remaining_days,
                    s.space_name,
                    s.description
                FROM `clients` c
                JOIN `rent_list` r ON c.id = r.client_id
                JOIN `space_list` s ON r.space_id = s.id
                WHERE r.status = 1 AND DATEDIFF(r.date_end, CURDATE()) > 0
                ORDER BY c.id ASC
            ");
                while ($row = $qry->fetch_assoc()) :
                    foreach ($row as $k => $v) {
                        $row[$k] = trim(stripslashes($v));
                    }
                ?>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100 shadow-sm">
                            <div class="card-body">
                                <div class="text-center mb-3">
                                    <img class="rounded-circle w-25" 
                                        src="<?php echo validate_image('uploads/blank-profile.png'); ?>" 
                                        alt="Profile Image" 
                                        onerror="this.onerror=null;this.src='uploads/blank-profile.png';">
                                </div>
                                <h5 class="text-center font-weight-bold">
                                    <?php echo $row['firstname'] . ' ' . $row['lastname']; ?>
                                </h5>
                                <p class="text-muted text-center mb-3">
                                    <strong>Contact:</strong> <?php echo $row['contact']; ?><br>
                                    <strong>Address:</strong> <?php echo $row['address']; ?>
                                </p>
                                <div class="text-center mb-3">
                                    <h6><?php echo $row['space_name']; ?></h6>
                                    <p class="text-muted"><?php echo $row['description']; ?></p>
                                    <img class="card-img-top w-50" src="<?php echo validate_image("uploads/thumbnails/" . $row['space_id'] . ".png") ?>" alt="Space Image" />
                                </div>
                                <div class="text-center mb-3">
                                    <?php if ($row['status'] == 1): ?>
                                        <p class="text-success font-weight-bold">
                                            <i class="fa fa-check-circle"></i> Contract is Active - <?php echo $row['remaining_days']; ?> days remaining
                                        </p>
                                    <?php endif; ?>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <button type="button" class="btn btn-danger btn-sm delete_data" data-id="<?php echo $row['rent_id']; ?>">
                                        <i class="fas fa-trash-alt"></i> Delete
                                    </button>
                                    <button type="button" class="btn btn-primary btn-sm edit_data" data-id="<?php echo $row['rent_id']; ?>">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        $('.delete_data').click(function () {
            _conf("Are you sure you want to delete this record permanently?", "delete_rent", [$(this).attr('data-id')]);
        });

        function delete_rent(id) {
            start_loader();
            $.ajax({
                url: _base_url_ + "classes/Master.php?f=delete_vendor",
                method: "POST",
                data: { id: id },
                dataType: "json",
                error: err => {
                    console.log(err);
                    alert_toast("An error occurred.", 'error');
                    end_loader();
                },
                success: function (resp) {
                    if (resp && resp.status === 'success') {
                        alert_toast("Record deleted successfully.", 'success');
                        location.reload();
                    } else {
                        alert_toast("An error occurred.", 'error');
                        end_loader();
                    }
                }
            });
        }
    });
</script>
