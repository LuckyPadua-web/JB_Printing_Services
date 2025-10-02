<?php

include 'components/connect.php';

session_start();

if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
}else{
   $user_id = '';
   header('location:login.php');
};

if(isset($_POST['submit_rating'])){
   $order_id = $_POST['order_id'];
   $rating = $_POST['rating'];
   $review = $_POST['review'];
   
   // Check if rating already exists for this order
   $check_rating = $conn->prepare("SELECT * FROM `order_ratings` WHERE order_id = ?");
   $check_rating->execute([$order_id]);
   
   if($check_rating->rowCount() > 0){
      $message[] = 'You have already rated this order!';
   }else{
      $insert_rating = $conn->prepare("INSERT INTO `order_ratings` (order_id, user_id, rating, review, created_at) VALUES (?, ?, ?, ?, NOW())");
      $insert_rating->execute([$order_id, $user_id, $rating, $review]);
      $message[] = 'Thank you for your rating!';
   }
}

if(isset($_POST['confirm_receipt'])){
   $order_id = $_POST['order_id'];
   
   $update_order = $conn->prepare("UPDATE `orders` SET status = 'received' WHERE id = ? AND user_id = ?");
   $update_order->execute([$order_id, $user_id]);
   $message[] = 'Order marked as received! You can now rate your experience.';
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>orders</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

   <style>
      .orders-container {
         max-width: 800px;
         margin: 0 auto;
         padding: 20px;
      }
      
      .order-card {
         background: white;
         border-radius: 12px;
         box-shadow: 0 2px 15px rgba(0,0,0,0.1);
         margin-bottom: 25px;
         overflow: hidden;
         border: 1px solid #e0e0e0;
      }
      
      .order-header {
         background: #f8f9fa;
         padding: 20px;
         border-bottom: 1px solid #e0e0e0;
      }
      
      .order-id {
         font-size: 18px;
         font-weight: bold;
         color: #333;
         margin-bottom: 5px;
      }
      
      .order-date {
         color: #666;
         font-size: 14px;
      }
      
      .order-status {
         display: inline-block;
         padding: 4px 12px;
         border-radius: 20px;
         font-size: 12px;
         font-weight: bold;
         margin-left: 10px;
      }
      
      .status-pending { background: #fff3cd; color: #856404; }
      .status-confirmed { background: #d1ecf1; color: #0c5460; }
      .status-processing { background: #cce7ff; color: #004085; }
      .status-shipped { background: #d4edda; color: #155724; }
      .status-delivered { background: #d1e7dd; color: #0f5132; }
      .status-cancelled { background: #f8d7da; color: #721c24; }
      .status-received { background: #e8f5e8; color: #2d5016; }
      
      .order-content {
         padding: 20px;
      }
      
      .product-item {
         display: flex;
         align-items: flex-start;
         margin-bottom: 15px;
         padding-bottom: 15px;
         border-bottom: 1px solid #f0f0f0;
      }
      
      .product-item:last-child {
         border-bottom: none;
         margin-bottom: 0;
         padding-bottom: 0;
      }
      
      .product-image {
         width: 80px;
         height: 80px;
         object-fit: cover;
         border-radius: 8px;
         margin-right: 15px;
         flex-shrink: 0;
         border: 1px solid #e0e0e0;
      }
      
      .product-image-placeholder {
         width: 80px;
         height: 80px;
         background: #f0f0f0;
         border-radius: 8px;
         margin-right: 15px;
         flex-shrink: 0;
         display: flex;
         align-items: center;
         justify-content: center;
         color: #999;
         border: 1px solid #ddd;
      }
      
      .product-details {
         flex: 1;
         display: flex;
         flex-direction: column;
      }
      
      .product-name {
         font-weight: 700;
         color: #333;
         margin-bottom: 8px;
         line-height: 1.3;
         font-size: 18px;
      }
      
      .product-variation {
         color: #666;
         font-size: 14px;
         margin-bottom: 5px;
      }
      
      .product-quantity {
         color: #666;
         font-size: 14px;
         margin-bottom: 5px;
      }
      
      .product-price {
         color: #e74c3c;
         font-size: 16px;
         font-weight: 600;
         margin-top: auto;
      }
      
      .order-total {
         text-align: right;
         margin-top: 15px;
         padding-top: 15px;
         border-top: 2px solid #f0f0f0;
      }
      
      .total-amount {
         font-size: 20px;
         font-weight: bold;
         color: #e74c3c;
      }
      
      .order-actions {
         display: flex;
         gap: 10px;
         margin-top: 20px;
         flex-wrap: wrap;
      }
      
      .action-btn {
         padding: 10px 20px;
         border: 2px solid #007bff;
         border-radius: 6px;
         background: white;
         color: #007bff;
         cursor: pointer;
         font-weight: 600;
         transition: all 0.3s ease;
         text-decoration: none;
         display: inline-block;
         text-align: center;
         flex: 1;
         min-width: 120px;
      }
      
      .action-btn:hover {
         background: #007bff;
         color: white;
      }
      
      .btn-received {
         border-color: #28a745;
         color: #28a745;
      }
      
      .btn-received:hover {
         background: #28a745;
         color: white;
      }
      
      .btn-contact {
         border-color: #6c757d;
         color: #6c757d;
      }
      
      .btn-contact:hover {
         background: #6c757d;
         color: white;
      }
      
      .btn-rate {
         border-color: #ffc107;
         color: #ffc107;
      }
      
      .btn-rate:hover {
         background: #ffc107;
         color: white;
      }
      
      .empty-orders {
         text-align: center;
         padding: 60px 20px;
         color: #666;
      }
      
      .empty-orders i {
         font-size: 64px;
         color: #ddd;
         margin-bottom: 20px;
      }
      
      .design-file-link {
         color: #007bff;
         text-decoration: underline;
         font-weight: 500;
      }
      
      .delivery-info {
         background: #e8f5e8;
         padding: 10px 15px;
         border-radius: 6px;
         margin-top: 15px;
         border-left: 4px solid #28a745;
      }
      
      /* Rating Styles */
      .rating-section {
         margin-top: 20px;
         padding: 20px;
         background: #f8f9fa;
         border-radius: 8px;
         border-left: 4px solid #ffc107;
      }
      
      .rating-form {
         margin-top: 15px;
      }
      
      .rating-stars {
         display: flex;
         gap: 5px;
         margin-bottom: 15px;
      }
      
      .star {
         font-size: 24px;
         color: #ddd;
         cursor: pointer;
         transition: color 0.2s;
      }
      
      .star:hover,
      .star.active {
         color: #ffc107;
      }
      
      .rating-input {
         display: none;
      }
      
      .review-textarea {
         width: 100%;
         padding: 12px;
         border: 1px solid #ddd;
         border-radius: 6px;
         resize: vertical;
         min-height: 80px;
         margin-bottom: 15px;
         font-family: inherit;
      }
      
      .submit-rating {
         background: #ffc107;
         color: white;
         border: none;
         padding: 10px 20px;
         border-radius: 6px;
         cursor: pointer;
         font-weight: 600;
      }
      
      .submit-rating:hover {
         background: #e0a800;
      }
      
      .existing-rating {
         background: #e8f5e8;
         padding: 15px;
         border-radius: 6px;
         margin-top: 15px;
      }
      
      .rating-display {
         display: flex;
         align-items: center;
         gap: 10px;
         margin-bottom: 10px;
      }
      
      .rating-stars-static {
         color: #ffc107;
      }

      .product-info-container {
         display: flex;
         align-items: flex-start;
         gap: 15px;
         width: 100%;
      }

      .product-text-info {
         flex: 1;
         min-width: 0;
      }

      .product-meta {
         display: flex;
         flex-direction: column;
         gap: 4px;
      }

      .image-error {
         font-size: 10px;
         color: #e74c3c;
         margin-top: 5px;
      }
   </style>

</head>
<body>
   
<!-- header section starts  -->
<?php include 'components/user_header.php'; ?>
<!-- header section ends -->

<section class="orders">

   <h1 class="title">your orders</h1>

   <div class="orders-container">

   <?php
      if($user_id == ''){
         echo '<p class="empty">please login to see your orders</p>';
      }else{
         // Fixed SQL query without variation column
         $select_orders = $conn->prepare("
            SELECT o.*, 
                   GROUP_CONCAT(CONCAT(p.name, '|||', p.image, '|||', od.quantity, '|||', od.price) SEPARATOR ':::') as product_details,
                   r.rating as user_rating,
                   r.review as user_review,
                   r.created_at as rating_date
            FROM `orders` o
            LEFT JOIN `order_details` od ON o.id = od.order_id
            LEFT JOIN `products` p ON od.product_id = p.id
            LEFT JOIN `order_ratings` r ON o.id = r.order_id AND r.user_id = ?
            WHERE o.user_id = ?
            GROUP BY o.id
            ORDER BY o.placed_on DESC
         ");
         $select_orders->execute([$user_id, $user_id]);
         
         if($select_orders->rowCount() > 0){
            while($fetch_orders = $select_orders->fetch(PDO::FETCH_ASSOC)){
               
               $status_class = 'status-' . ($fetch_orders['status'] ?? 'pending');
               $product_details = $fetch_orders['product_details'];
               $has_rating = !empty($fetch_orders['user_rating']);
   ?>
   <div class="order-card">
      <div class="order-header">
         <div class="order-id">
            <span class="order-status <?= $status_class; ?>">
               <?= ucfirst($fetch_orders['status'] ?? 'pending'); ?>
            </span>
         </div>
         <div class="order-date">
            Placed on <?= date('F d, Y', strtotime($fetch_orders['placed_on'])); ?>
         </div>
      </div>
      
      <div class="order-content">
         <?php
         if(!empty($product_details)){
            $products = explode(':::', $product_details);
            foreach($products as $product){
               $product_data = explode('|||', $product);
               if(count($product_data) >= 4){
                  $product_name = $product_data[0];
                  $product_image = $product_data[1];
                  $quantity = $product_data[2];
                  $price = $product_data[3];
                  $total_product_price = $price * $quantity;
                  
                  // Check if image exists and is not empty
                  $image_path = 'uploaded_img/' . $product_image;
                  $image_exists = !empty($product_image) && file_exists($image_path);
         ?>



         <!--To Fix Image Display Issue-->
         <div class="product-item">
            <div class="product-info-container">
               <!-- Product Image on the Left -->
               <?php if($image_exists): ?>
               <img src="<?= $image_path; ?>" alt="<?= htmlspecialchars($product_name); ?>" class="product-image" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
               <div class="product-image-placeholder" style="display: none;">
                  <i class="fas fa-image"></i>
               </div>
               <?php else: ?>
               <div class="product-image-placeholder">
                  <i class="fas fa-image"></i>
               </div>
               <?php endif; ?>
               
               <!-- Product Details on the Right -->
               <div class="product-text-info">
                  <!-- Bigger Product Name -->
                  <div class="product-name"><?= htmlspecialchars($product_name); ?></div>
                  
                  <div class="product-meta">
                     <div class="product-quantity">Quantity: x<?= $quantity; ?></div>
                     <div class="product-price">Price: &#8369;<?= number_format($total_product_price, 2); ?></div>
                     <?php if(!$image_exists && !empty($product_image)): ?>
                     <div class="image-error">Image not found: <?= $product_image; ?></div>
                     <?php endif; ?>
                  </div>
               </div>
            </div>
         </div>

         
         <?php
               }
            }
         }else{
            // Fallback to show basic order info if no product details
            echo '<div class="product-item">';
            echo '<div class="product-details">';
            echo '<div class="product-name">' . $fetch_orders['total_products'] . '</div>';
            echo '</div>';
            echo '</div>';
         }
         ?>
         
         <div class="order-total">
            <div class="total-amount">Total: &#8369;<?= number_format($fetch_orders['total_price'], 2); ?></div>
         </div>
         
         <?php if (!empty($fetch_orders['expected_delivery_date'])): ?>
         <div class="delivery-info">
            <i class="fas fa-truck"></i> 
            Expected delivery: <?= date('F d, Y', strtotime($fetch_orders['expected_delivery_date'])); ?>
         </div>
         <?php endif; ?>
         
         <?php if (!empty($fetch_orders['design_file'])): ?>
         <div style="margin-top: 15px; padding: 10px; background: #f0f8ff; border-radius: 6px;">
            <i class="fas fa-file-image"></i> 
            Design file: <a href="uploaded_designs/<?= $fetch_orders['design_file']; ?>" target="_blank" class="design-file-link">View Your Design</a>
         </div>
         <?php endif; ?>
         
         <!-- Rating Section -->
         <?php if($fetch_orders['status'] == 'received' || $fetch_orders['status'] == 'delivered'): ?>
            <?php if($has_rating): ?>
            <div class="existing-rating">
               <div class="rating-display">
                  <strong>Your Rating:</strong>
                  <div class="rating-stars-static">
                     <?php for($i = 1; $i <= 5; $i++): ?>
                        <i class="fas fa-star<?= $i <= $fetch_orders['user_rating'] ? '' : '-o' ?>"></i>
                     <?php endfor; ?>
                     <span>(<?= $fetch_orders['user_rating'] ?>/5)</span>
                  </div>
               </div>
               <?php if(!empty($fetch_orders['user_review'])): ?>
               <div>
                  <strong>Your Review:</strong>
                  <p><?= htmlspecialchars($fetch_orders['user_review']); ?></p>
               </div>
               <?php endif; ?>
               <div style="color: #666; font-size: 12px;">
                  Rated on <?= date('F d, Y', strtotime($fetch_orders['rating_date'])); ?>
               </div>
            </div>
            <?php else: ?>
            <div class="rating-section">
               <h4><i class="fas fa-star"></i> Rate Your Order</h4>
               <form method="post" class="rating-form">
                  <input type="hidden" name="order_id" value="<?= $fetch_orders['id']; ?>">
                  
                  <div class="rating-stars" id="stars-<?= $fetch_orders['id']; ?>">
                     <?php for($i = 1; $i <= 5; $i++): ?>
                        <i class="far fa-star star" data-rating="<?= $i; ?>"></i>
                     <?php endfor; ?>
                  </div>
                  <input type="hidden" name="rating" id="rating-input-<?= $fetch_orders['id']; ?>" value="0" required>
                  
                  <textarea name="review" class="review-textarea" placeholder="Share your experience with this order (optional)"></textarea>
                  
                  <button type="submit" name="submit_rating" class="submit-rating">
                     <i class="fas fa-paper-plane"></i> Submit Rating
                  </button>
               </form>
            </div>
            <?php endif; ?>
         <?php endif; ?>
         
         <div class="order-actions">
            <?php if(($fetch_orders['status'] ?? '') == 'delivered'): ?>
            <form method="post" style="flex: 1;">
               <input type="hidden" name="order_id" value="<?= $fetch_orders['id']; ?>">
               <button type="submit" name="confirm_receipt" class="action-btn btn-received">
                  <i class="fas fa-check-circle"></i> Confirm Receipt
               </button>
            </form>
            <?php endif; ?>
            
            <a href="contact.php" class="action-btn btn-contact">
               <i class="fas fa-comment"></i> Contact Seller
            </a>
         </div>
      </div>
   </div>
   <?php
            }
         }else{
            echo '
            <div class="empty-orders">
               <i class="fas fa-shopping-bag"></i>
               <h3>No orders placed yet!</h3>
               <p>Start shopping and your orders will appear here.</p>
               <a href="menu.php" class="btn" style="margin-top: 20px;">Start Shopping</a>
            </div>
            ';
         }
      }
   ?>

   </div>

</section>

<!-- footer section starts  -->
<?php include 'components/footer.php'; ?>
<!-- footer section ends -->

<!-- custom js file link  -->
<script src="js/script.js"></script>

<script>
// Add functionality to action buttons
document.addEventListener('DOMContentLoaded', function() {
    // Star rating functionality
    document.querySelectorAll('.rating-stars').forEach(starsContainer => {
        const stars = starsContainer.querySelectorAll('.star');
        const input = document.getElementById('rating-input-' + starsContainer.id.split('-')[1]);
        
        stars.forEach(star => {
            star.addEventListener('click', function() {
                const rating = this.getAttribute('data-rating');
                input.value = rating;
                
                // Update star display
                stars.forEach((s, index) => {
                    if(index < rating) {
                        s.classList.remove('far');
                        s.classList.add('fas', 'active');
                    } else {
                        s.classList.remove('fas', 'active');
                        s.classList.add('far');
                    }
                });
            });
            
            // Hover effect
            star.addEventListener('mouseover', function() {
                const rating = this.getAttribute('data-rating');
                stars.forEach((s, index) => {
                    if(index < rating) {
                        s.classList.add('active');
                    } else {
                        s.classList.remove('active');
                    }
                });
            });
        });
        
        // Reset stars on mouse leave
        starsContainer.addEventListener('mouseleave', function() {
            const currentRating = input.value;
            stars.forEach((s, index) => {
                if(index < currentRating) {
                    s.classList.remove('far');
                    s.classList.add('fas', 'active');
                } else {
                    s.classList.remove('fas', 'active');
                    s.classList.add('far');
                }
            });
        });
    });

    // Image error handling
    document.querySelectorAll('.product-image').forEach(img => {
        img.addEventListener('error', function() {
            this.style.display = 'none';
            const placeholder = this.nextElementSibling;
            if(placeholder && placeholder.classList.contains('product-image-placeholder')) {
                placeholder.style.display = 'flex';
            }
        });
    });
});
</script>

</body>
</html>