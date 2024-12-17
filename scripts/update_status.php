<?php
require_once('C:/xampp/htdocs/SogodMarket/config.php');


// Connect to the database
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

try {
    // Get the current date
    $currentDate = date('Y-m-d');

    // Update `status` to 3 where `date_end` is less than the current date and `status` is not already 3
    $qry = "UPDATE `rent_list` 
            SET `status` = 3 
            WHERE `status` != 3 
              AND `date_end` < '$currentDate'";

    $result = $conn->query($qry);

    if ($result) {
        echo "Status updated successfully for expired rentals.\n";
    } else {
        echo "Error updating status: " . $conn->error . "\n";
    }
} catch (Exception $e) {
    echo "An error occurred: " . $e->getMessage() . "\n";
}

// Close the database connection
$conn->close();
