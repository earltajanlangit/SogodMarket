<?php
require_once('../../config.php');
if(isset($_GET['id']) && $_GET['id'] > 0){
    $qry = $conn->query("SELECT * from `categories` where id = '{$_GET['id']}' ");
    if($qry->num_rows > 0){
        foreach($qry->fetch_assoc() as $k => $v){
            $$k=$v;
        }
    }
}
?>
<div class="card-body">
	<form action="" id="category-form" method="POST" enctype="multipart/form-data">
		<input type="hidden" name="id" value="<?php echo isset($id) ? $id : '' ?>">
		<div class="form-group">
			<label for="category" class="control-label">Category Name</label>
			<input name="category" id="category" class="form-control form no-resize" value="<?php echo isset($category) ? $category : ''; ?>" required>
		</div>
		<div class="form-group">
			<label for="description" class="control-label">Description</label>
			<textarea name="description" id="description" cols="30" rows="2" class="form-control form no-resize"><?php echo isset($description) ? $description : ''; ?></textarea>
		</div>
		<div class="form-group">
			<label for="status" class="control-label">Status</label>
			<select name="status" id="status" class="custom-select select">
				<option value="1" <?php echo isset($status) && $status == 1 ? 'selected' : '' ?>>Active</option>
				<option value="0" <?php echo isset($status) && $status == 0 ? 'selected' : '' ?>>Inactive</option>
			</select>
		</div>
		<div class="form-group">
			<label for="category_image" class="control-label">Category Image</label>
			<input type="file" name="category_image" id="category_image" class="form-control form no-resize">
		</div>
		
	</form>
</div>

<script>
	$(document).ready(function(){
		$('#category-form').submit(function(e){
			e.preventDefault();
			var _this = $(this);
			$('.err-msg').remove();
			start_loader();
			$.ajax({
				url: _base_url_+"classes/Master.php?f=save_category",
				data: new FormData($(this)[0]),
				cache: false,
				contentType: false,
				processData: false,
				method: 'POST',
				type: 'POST',
				dataType: 'json',
				error: function(err){
					console.log(err);
					alert_toast("An error occurred", 'error');
					end_loader();
				},
				success: function(resp){
					if (typeof resp == 'object' && resp.status == 'success') {
						location.reload();
					} else if (resp.status == 'failed' && !!resp.msg) {
						var el = $('<div>')
							.addClass("alert alert-danger err-msg")
							.text(resp.msg);
						_this.prepend(el);
						el.show('slow');
						$("html, body").animate({ scrollTop: _this.closest('.card').offset().top }, "fast");
						end_loader();
					} else {
						alert_toast("An error occurred", 'error');
						end_loader();
						console.log(resp);
					}
				}
			});
		});
	});
</script>
