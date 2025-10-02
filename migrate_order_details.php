<?php
// Migration script to populate order_details table from existing orders
include 'components/connect.php';

echo "Starting migration of existing orders to order_details table...\n";

try {
    // Get all existing orders that don't have order_details
    $select_orders = $conn->prepare("
        SELECT o.* FROM `orders` o 
        LEFT JOIN `order_details` od ON o.id = od.order_id 
        WHERE od.order_id IS NULL
    ");
    $select_orders->execute();
    
    $migrated_count = 0;
    
    while($order = $select_orders->fetch(PDO::FETCH_ASSOC)) {
        // Parse the total_products field which contains product names
        $total_products = $order['total_products'];
        
        // Try to extract product information from the total_products string
        // Format is usually: "product_name (₱price x quantity), product_name2 (₱price x quantity)"
        $products = explode(',', $total_products);
        
        foreach($products as $product_str) {
            $product_str = trim($product_str);
            
            // Try to extract product name, price, and quantity using regex
            if(preg_match('/^(.+?)\s*\(₱(\d+(?:\.\d+)?)\s*x\s*(\d+)\)/', $product_str, $matches)) {
                $product_name = trim($matches[1]);
                $price = floatval($matches[2]);
                $quantity = intval($matches[3]);
                
                // Find the product ID by name
                $find_product = $conn->prepare("SELECT id FROM `products` WHERE name = ?");
                $find_product->execute([$product_name]);
                
                if($product = $find_product->fetch(PDO::FETCH_ASSOC)) {
                    // Insert into order_details
                    $insert_detail = $conn->prepare("INSERT INTO `order_details` (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
                    $insert_detail->execute([$order['id'], $product['id'], $quantity, $price]);
                    
                    echo "Migrated: Order {$order['id']} - {$product_name} (qty: {$quantity}, price: {$price})\n";
                } else {
                    echo "Warning: Product '{$product_name}' not found for order {$order['id']}\n";
                }
            } else {
                echo "Warning: Could not parse product string '{$product_str}' for order {$order['id']}\n";
            }
        }
        $migrated_count++;
    }
    
    echo "\nMigration completed! Processed {$migrated_count} orders.\n";
    echo "Now existing ratings should show up on product pages.\n";
    
} catch(PDOException $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
}
?>
