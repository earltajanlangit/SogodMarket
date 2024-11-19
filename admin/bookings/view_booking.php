<?php 
require_once('../../config.php');
?>
<?php 
if(!isset($_GET['id'])) {
    $_settings->set_flashdata('error','No Booking ID Provided.');
    redirect('admin/?page=bookings');
}

$booking = $conn->query("SELECT r.*, 
                        CONCAT(c.firstname, ' ', c.lastname) AS client, 
                        c.email, 
                        c.contact, 
                        r.months_to_rent
                        FROM `rent_list` r 
                        INNER JOIN clients c ON c.id = r.client_id 
                        WHERE r.id = '{$_GET['id']}' ");

if($booking->num_rows > 0){
    foreach($booking->fetch_assoc() as $k => $v){
        $$k = $v;
    }
}else{
    $_settings->set_flashdata('error','Booking ID provided is Unknown');
    redirect('admin/?page=bookings');
}

if(isset($space_id)){
    $bike = $conn->query("SELECT b.*, c.category, bb.name as brand, b.monthly_rate 
                          FROM `space_list` b 
                          INNER JOIN categories c ON b.category_id = c.id 
                          INNER JOIN space_type_list bb ON b.space_type_id = bb.id 
                          WHERE b.id = '{$space_id}' ");
    if($bike->num_rows > 0){
        foreach($bike->fetch_assoc() as $k => $v){
            $bike_meta[$k]=stripslashes($v);
        }
    }
}
?>
<div class="container-fluid px-3 py-2">
    <div class="row">
        <div class="col-md-6">
            <p><b>Client Name:</b> <?php echo $client ?></p>
            <p><b>Client Email:</b> <?php echo $email ?></p>
            <p><b>Client Contact:</b> <?php echo $contact ?></p>
            <p><b>Rent Start Date:</b> <?php echo date("M d,Y" ,strtotime($date_start)) ?></p>
            <p><b>Rent End Date:</b> <?php echo date("M d,Y" ,strtotime($date_end)) ?></p>
        </div>
        <div class="col-md-6">
            <p><b>Category:</b> <?php echo $bike_meta['category'] ?></p>
            <p><b>Type of Space:</b> <?php echo $bike_meta['brand'] ?></p>
            <p><b>Monthly Rate:</b> <?php echo number_format($bike_meta['monthly_rate'], 2) ?></p>
            <p><b>Months to Rent:</b> <?php echo $months_to_rent ?></p>
            <p><b>Client Payable Amount:</b> <?php echo number_format($bike_meta['monthly_rate'] * $months_to_rent, 2) ?></p>
        </div>
    </div>
    <div class="row">
        <div class="col-3">Booking Status:</div>
        <div class="col-auto">
        <?php 
            switch($status){
                case '0':
                    echo '<span class="badge badge-light text-dark">Pending</span>';
                break;
                case '1':
                    echo '<span class="badge badge-primary">Confirmed</span>';
                break;
                case '2':
                    echo '<span class="badge badge-danger">Cancelled</span>';
                break;
                case '3':
                    echo '<span class="badge badge-success">Done</span>';
                break;
                
                default:
                    echo '<span class="badge badge-danger">Cancelled</span>';
                break;
            }
        ?>
        </div>
    
</div>
    <div class="modal-footer">
        <?php if(!isset($_GET['view'])): ?>
        <button type="button" id="update" class="btn btn-sm btn-flat btn-primary">Edit</button>
        <?php endif; ?>
        <button type="button" class="btn btn-secondary btn-sm btn-flat" data-dismiss="modal">Close</button>
    </div>
</div>

<style>
    #uni_modal>.modal-dialog>.modal-content>.modal-footer{
        display:none;
    }
    #uni_modal .modal-body{
        padding:0;
    }
</style>

<script>
    $(function(){
        $('#update').click(function(){
            uni_modal("Edit Booking Details", "./bookings/manage_booking.php?id=<?php echo $id ?>")
        })
    })
</script>
