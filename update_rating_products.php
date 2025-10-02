<?php
// Script to update existing order_ratings with product_id
include 'components/connect.php';

echo "Updating existing ratings with product IDs...\n";

try {
    // Get all ratings without product_id
    $select_ratings = $conn->prepare("
        SELECT r.*, od.product_id 
        FROM `order_ratings` r
        LEFT JOIN `order_details` od ON r.order_id = od.order_id
        WHERE r.product_id IS NULL AND od.product_id IS NOT NULL
    ");
    $select_ratings->execute();
    
    $updated_count = 0;
    
    while($rating = $select_ratings->fetch(PDO::FETCH_ASSOC)) {
        // Update the rating with the product_id
        $update_rating = $conn->prepare("UPDATE `order_ratings` SET product_id = ? WHERE id = ?");
        $update_rating->execute([$rating['product_id'], $rating['id']]);
        
        echo "Updated rating {$rating['id']} with product_id {$rating['product_id']}\n";
        $updated_count++;
    }
    
    echo "\nCompleted! Updated {$updated_count} ratings with product IDs.\n";
    
} catch(PDOException $e) {
    echo "Update failed: " . $e->getMessage() . "\n";
}
?>
