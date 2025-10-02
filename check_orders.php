<?php
// Check order statuses and details
include 'components/connect.php';

echo "Checking order statuses and details...\n\n";

try {
    // Check all orders
    $select_orders = $conn->prepare("SELECT id, payment_status, status, total_products FROM `orders` ORDER BY id DESC LIMIT 10");
    $select_orders->execute();
    
    echo "Recent Orders:\n";
    echo "==============\n";
    while($order = $select_orders->fetch(PDO::FETCH_ASSOC)) {
        echo "Order ID: {$order['id']}\n";
        echo "  - Payment Status: " . ($order['payment_status'] ?? 'NULL') . "\n";
        echo "  - Order Status: " . ($order['status'] ?? 'NULL') . "\n";
        echo "  - Products: {$order['total_products']}\n\n";
    }
    
    // Check order_details
    echo "Order Details:\n";
    echo "==============\n";
    $select_details = $conn->prepare("
        SELECT od.*, p.name as product_name, o.payment_status, o.status 
        FROM `order_details` od 
        JOIN `products` p ON od.product_id = p.id 
        JOIN `orders` o ON od.order_id = o.id 
        ORDER BY od.order_id DESC
    ");
    $select_details->execute();
    
    while($detail = $select_details->fetch(PDO::FETCH_ASSOC)) {
        echo "Order ID: {$detail['order_id']}\n";
        echo "  - Product: {$detail['product_name']}\n";
        echo "  - Quantity: {$detail['quantity']}\n";
        echo "  - Payment Status: " . ($detail['payment_status'] ?? 'NULL') . "\n";
        echo "  - Order Status: " . ($detail['status'] ?? 'NULL') . "\n\n";
    }
    
} catch(PDOException $e) {
    echo "Check failed: " . $e->getMessage() . "\n";
}
?>
