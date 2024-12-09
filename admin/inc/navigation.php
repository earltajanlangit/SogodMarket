<style>
/* Add any additional styles if needed */
</style>

<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary bg-navy elevation-4 sidebar-no-expand">
  <!-- Brand Logo -->
  <a href="<?php echo base_url ?>admin" class="brand-link bg-primary text-sm">
    <img src="<?php echo validate_image($_settings->info('logo')) ?>" alt="Store Logo" class="brand-image img-circle elevation-3" style="width: 1.8rem;height: 1.8rem;max-height: unset">
    <span class="brand-text font-weight-light">Sogod Market</span>
  </a>
  
  <!-- Sidebar -->
  <div class="sidebar os-host os-theme-light os-host-overflow os-host-overflow-y os-host-resize-disabled os-host-transition os-host-scrollbar-horizontal-hidden">
    <div class="os-resize-observer-host observed">
      <div class="os-resize-observer" style="left: 0px; right: auto;"></div>
    </div>
    <div class="os-size-auto-observer observed" style="height: calc(100% + 1px); float: left;">
      <div class="os-resize-observer"></div>
    </div>
    <div class="os-content-glue" style="margin: 0px -8px; width: 249px; height: 646px;"></div>
    <div class="os-padding">
      <div class="os-viewport os-viewport-native-scrollbars-invisible" style="overflow-y: scroll;">
        <div class="os-content" style="padding: 0px 8px; height: 100%; width: 100%;">
          
          <!-- PHP Query to Fetch New Booking Count -->
          <?php
          $bookingCountQuery = $conn->query("SELECT COUNT(*) as count 
          FROM `rent_list` 
          WHERE status = 0 
          AND is_viewed = 0");
          $bookingCount = $bookingCountQuery->fetch_assoc()['count'];

          ?>

          <!-- Sidebar user panel (optional) -->
          <div class="clearfix"></div>

          <!-- Sidebar Menu -->
          <nav class="mt-4">
            <ul class="nav nav-pills nav-sidebar flex-column text-sm nav-compact nav-flat nav-child-indent nav-collapse-hide-child" data-widget="treeview" role="menu" data-accordion="false">
              
              <li class="nav-item dropdown">
                <a href="./" class="nav-link nav-home">
                  <i class="nav-icon fas fa-tachometer-alt"></i>
                  <p>Dashboard</p>
                </a>
              </li>

              <!-- Booking List with New Booking Counter Badge -->
              <li class="nav-item dropdown">
                <a href="<?php echo base_url ?>admin/?page=bookings" 
                  class="nav-link nav-bookings" 
                  onclick="markBookingsAsViewed()">
                   <i class="nav-icon fas fa-th-list"></i>
                   <p>Booking List <span class="badge badge-danger"><?php echo $bookingCount; ?></span></p>
                </a>
              </li>
              

              <li class="nav-item dropdown">
                <a href="<?php echo base_url ?>admin/?page=users" class="nav-link nav-maintenance/users">
                  <i class="nav-icon fas fa-users"></i>
                  <p>Users List</p>
                </a>
              </li>
              
              <li class="nav-item dropdown">
                <a href="<?php echo base_url ?>admin/?page=report" class="nav-link nav-report">
                  <i class="nav-icon fas fa-file"></i>
                  <p>Booking Report</p>
                </a>
              </li>
              <!-- <li class="nav-item dropdown">
                <a href="<?php echo base_url ?>admin/?page=sms" class="nav-link nav-sms">
                  <i class="nav-icon fas fa-file"></i>
                  <p>Send SMS</p>
                </a>
              </li> -->

              <li class="nav-item dropdown">
                <a href="<?php echo base_url ?>admin/?page=system_info" class="nav-link nav-system_info">
                  <i class="nav-icon fas fa-cogs"></i>
                  <p>Settings</p>
                </a>
              </li>

              <li class="nav-header">Manage Space</li>
              
              <li class="nav-item dropdown">
                <a href="<?php echo base_url ?>admin/?page=bike" class="nav-link nav-bike">
                  <i class="nav-icon fas fa-store"></i>
                  <p>Space List</p>
                </a>
              </li>

              <li class="nav-item dropdown">
                <a href="<?php echo base_url ?>admin/?page=maintenance/brands" class="nav-link nav-maintenance/brands">
                  <i class="nav-icon fas fa-store"></i>
                  <p>Type of Space</p>
                </a>
              </li>

              <li class="nav-item dropdown">
                <a href="<?php echo base_url ?>admin/?page=maintenance/category" class="nav-link nav-maintenance/category">
                  <i class="nav-icon fas fa-th-list"></i>
                  <p>Category List</p>
                </a>
              </li>
            </ul>
          </nav>
          <!-- /.sidebar-menu -->
        </div>
      </div>
    </div>
  </div>
</aside>

<!-- JavaScript for Active Page Highlighting -->
<script>
  $(document).ready(function(){
    var page = '<?php echo isset($_GET['page']) ? $_GET['page'] : 'home' ?>';
    var s = '<?php echo isset($_GET['s']) ? $_GET['s'] : '' ?>';
    page = page.split('/');
    page = page[0];
    if(s != '') page = page + '_' + s;

    if($('.nav-link.nav-' + page).length > 0) {
      $('.nav-link.nav-' + page).addClass('active');
      if($('.nav-link.nav-' + page).hasClass('tree-item') == true) {
        $('.nav-link.nav-' + page).closest('.nav-treeview').siblings('a').addClass('active');
        $('.nav-link.nav-' + page).closest('.nav-treeview').parent().addClass('menu-open');
      }
      if($('.nav-link.nav-' + page).hasClass('nav-is-tree') == true) {
        $('.nav-link.nav-' + page).parent().addClass('menu-open');
      }
    }
  });

  function markBookingsAsViewed() {
  $.ajax({
    url: _base_url_ + "classes/Master.php?f=mark_booking_as_viewed",
    method: 'POST',
    data: {},
    success: function(response) {
      console.log("Bookings marked as viewed");

      // After marking bookings as viewed, make another AJAX call to fetch the updated booking count
      $.ajax({
        url: '<?php echo base_url ?>admin/fetch_booking_count.php', // A new file that will return the new booking count
        method: 'GET',
        success: function(countResponse) {
          // Assuming countResponse is just the count of new bookings
          const newBookingCount = countResponse;
          
          // Update the badge text with the new count
          $('.nav-bookings .badge').text(newBookingCount);
        }
      });
    }
  });
}

</script>
