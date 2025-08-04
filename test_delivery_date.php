<?php
include 'components/connect.php';

// Test if the expected_delivery_date column exists
try {
    $test_query = $conn->prepare("DESCRIBE `orders`");
    $test_query->execute();
    $columns = $test_query->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h2>Orders Table Structure:</h2>";
    echo "<table border='1' style='border-collapse: collapse; width: 100%; margin: 20px 0;'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    
    foreach ($columns as $column) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($column['Field']) . "</td>";
        echo "<td>" . htmlspecialchars($column['Type']) . "</td>";
        echo "<td>" . htmlspecialchars($column['Null']) . "</td>";
        echo "<td>" . htmlspecialchars($column['Key']) . "</td>";
        echo "<td>" . htmlspecialchars($column['Default']) . "</td>";
        echo "<td>" . htmlspecialchars($column['Extra']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Check if expected_delivery_date column exists
    $has_delivery_date = false;
    foreach ($columns as $column) {
        if ($column['Field'] === 'expected_delivery_date') {
            $has_delivery_date = true;
            break;
        }
    }
    
    if ($has_delivery_date) {
        echo "<p style='color: green; font-weight: bold;'>✅ Expected delivery date column exists!</p>";
    } else {
        echo "<p style='color: red; font-weight: bold;'>❌ Expected delivery date column is missing!</p>";
    }
    
    // Test a sample query
    $test_orders = $conn->prepare("SELECT id, name, expected_delivery_date, placed_on FROM `orders` LIMIT 3");
    $test_orders->execute();
    $orders = $test_orders->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h2>Sample Orders (showing delivery dates):</h2>";
    if (count($orders) > 0) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%; margin: 20px 0;'>";
        echo "<tr><th>ID</th><th>Name</th><th>Expected Delivery Date</th><th>Placed On</th></tr>";
        
        foreach ($orders as $order) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($order['id']) . "</td>";
            echo "<td>" . htmlspecialchars($order['name']) . "</td>";
            echo "<td>" . (!empty($order['expected_delivery_date']) ? date('F d, Y', strtotime($order['expected_delivery_date'])) : 'Not set') . "</td>";
            echo "<td>" . date('F d, Y g:i A', strtotime($order['placed_on'])) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No orders found in the database.</p>";
    }
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>Database error: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Delivery Date Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { margin: 20px 0; }
        th, td { padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>JB Printing Services - Delivery Date Feature Test</h1>
</body>
</html>
