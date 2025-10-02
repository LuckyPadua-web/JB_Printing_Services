<?php
include 'components/connect.php';

try {
    // Check if columns already exist
    $check_columns = $conn->query("SHOW COLUMNS FROM `orders` LIKE 'cancel_reason'");
    
    if ($check_columns->rowCount() == 0) {
        // Add cancel_reason column
        $conn->exec("ALTER TABLE `orders` ADD COLUMN `cancel_reason` TEXT DEFAULT NULL");
        echo "Added cancel_reason column.<br>";
    } else {
        echo "cancel_reason column already exists.<br>";
    }
    
    $check_columns = $conn->query("SHOW COLUMNS FROM `orders` LIKE 'cancelled_at'");
    
    if ($check_columns->rowCount() == 0) {
        // Add cancelled_at column
        $conn->exec("ALTER TABLE `orders` ADD COLUMN `cancelled_at` DATETIME DEFAULT NULL");
        echo "Added cancelled_at column.<br>";
    } else {
        echo "cancelled_at column already exists.<br>";
    }
    
    $check_columns = $conn->query("SHOW COLUMNS FROM `orders` LIKE 'cancel_approval_status'");
    
    if ($check_columns->rowCount() == 0) {
        // Add cancel_approval_status column
        $conn->exec("ALTER TABLE `orders` ADD COLUMN `cancel_approval_status` ENUM('pending', 'approved', 'disapproved') DEFAULT NULL");
        echo "Added cancel_approval_status column.<br>";
    } else {
        echo "cancel_approval_status column already exists.<br>";
    }
    
    $check_columns = $conn->query("SHOW COLUMNS FROM `orders` LIKE 'admin_response_message'");
    
    if ($check_columns->rowCount() == 0) {
        // Add admin_response_message column
        $conn->exec("ALTER TABLE `orders` ADD COLUMN `admin_response_message` TEXT DEFAULT NULL");
        echo "Added admin_response_message column.<br>";
    } else {
        echo "admin_response_message column already exists.<br>";
    }
    
    $check_columns = $conn->query("SHOW COLUMNS FROM `orders` LIKE 'cancel_processed_at'");
    
    if ($check_columns->rowCount() == 0) {
        // Add cancel_processed_at column
        $conn->exec("ALTER TABLE `orders` ADD COLUMN `cancel_processed_at` DATETIME DEFAULT NULL");
        echo "Added cancel_processed_at column.<br>";
    } else {
        echo "cancel_processed_at column already exists.<br>";
    }
    
    echo "<br>Database schema updated successfully!";
    echo "<br><a href='admin/placed_orders.php'>Go to Admin Orders</a>";
    
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
