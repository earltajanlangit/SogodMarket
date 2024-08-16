<?php 
$user = $conn->query("SELECT * FROM clients");
$meta = [];
foreach($user->fetch_array() as $k => $v){
    $meta[$k] = $v;
}
?>
<?php if($_settings->chk_flashdata('success')): ?>
<script>
    alert_toast("<?php echo $_settings->flashdata('success') ?>", 'success')
</script>    
<?php endif; ?>
<div class="card card-outline card-primary">
    <div class="card-body">
        <div class="container-fluid">
            <div id="msg"></div>
            <form action="" id="manage-user">    
                
                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" name="name" id="name" class="form-control" value="<?php echo isset($meta['name']) ? $meta['name'] : '' ?>" required>
                </div>
                <div class="form-group">
                    <label for="contact_number">Contact Number</label>
                    <input type="text" name="contact_number" id="contact_number" class="form-control" value="<?php echo isset($meta['contact_number']) ? $meta['contact_number'] : '' ?>" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="text" name="email" id="email" class="form-control" value="<?php echo isset($meta['email']) ? $meta['email'] : '' ?>" required autocomplete="off">
                </div>
                
            </form>
        </div>
    </div>
    <div class="card-footer">
        <div class="col-md-12">
            <div class="row">
                <button class="btn btn-sm btn-primary" form="manage-user">Update</button>
            </div>
        </div>
    </div>
</div>
<style>
    img#cimg {
        height: 15vh;
        width: 15vh;
        object-fit: cover;
        border-radius: 100%;
    }
</style>
<script>
    function displayImg(input, _this) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#cimg').attr('src', e.target.result);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
    $('#manage-user').submit(function(e) {
        e.preventDefault();
        start_loader();
        $.ajax({
            url: _base_url_ + 'classes/Clients.php?f=save',
            data: new FormData($(this)[0]),
            cache: false,
            contentType: false,
            processData: false,
            method: 'POST',
            type: 'POST',
            success: function(resp) {
                if (resp == 1) {
                    location.reload();
                } else {
                    $('#msg').html('<div class="alert alert-danger">' + resp + '</div>');
                    end_loader();
                }
            }
        });
    });
</script>
