<?php
// Test script to check if ratings are displaying correctly
include 'components/connect.php';

echo "Testing product ratings display...\n\n";

try {
    // Test the same query used in the product pages
    $select_products = $conn->prepare("
        SELECT p.*, 
               COALESCE((
                 SELECT SUM(od2.quantity) 
                 FROM `order_details` od2 
                 JOIN `orders` o2 ON od2.order_id = o2.id 
                 WHERE od2.product_id = p.id AND o2.status IN ('delivered', 'received')
               ), 0) as total_sold,
               COALESCE(AVG(r.rating), 0) as average_rating,
               COUNT(DISTINCT r.id) as total_reviews
        FROM `products` p
        LEFT JOIN `order_ratings` r ON p.id = r.product_id
        GROUP BY p.id
        ORDER BY p.id DESC
    ");
    $select_products->execute();
    
    echo "Product ratings summary:\n";
    echo "========================\n";
    
    while($product = $select_products->fetch(PDO::FETCH_ASSOC)) {
        $average_rating = round($product['average_rating'], 1);
        $total_reviews = $product['total_reviews'];
        $total_sold = $product['total_sold'];
        
        echo "Product: {$product['name']}\n";
        echo "  - Average Rating: {$average_rating}/5\n";
        echo "  - Total Reviews: {$total_reviews}\n";
        echo "  - Total Sold: {$total_sold}\n";
        
        if($total_reviews > 0) {
            echo "  - Status: ✅ HAS RATINGS!\n";
        } else {
            echo "  - Status: ❌ No ratings yet\n";
        }
        echo "\n";
    }
    
    // Also check the order_ratings table directly
    echo "\nDirect check of order_ratings table:\n";
    echo "=====================================\n";
    
    $check_ratings = $conn->prepare("
        SELECT r.*, p.name as product_name 
        FROM `order_ratings` r 
        LEFT JOIN `products` p ON r.product_id = p.id
        ORDER BY r.created_at DESC
    ");
    $check_ratings->execute();
    
    while($rating = $check_ratings->fetch(PDO::FETCH_ASSOC)) {
        echo "Rating ID: {$rating['id']}\n";
        echo "  - Product: {$rating['product_name']}\n";
        echo "  - Rating: {$rating['rating']}/5\n";
        echo "  - Review: {$rating['review']}\n";
        echo "  - Date: {$rating['created_at']}\n\n";
    }
    
} catch(PDOException $e) {
    echo "Test failed: " . $e->getMessage() . "\n";
}
?>
