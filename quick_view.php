<?php

include 'components/connect.php';

session_start();

if (isset($_SESSION['user_id'])) {
   $user_id = $_SESSION['user_id'];
} else {
   $user_id = '';
};

include 'components/add_cart.php';

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>quick view</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

   <style>
      .star-rating {
         color: #ffc107;
         margin: 10px 0;
         font-size: 16px;
      }
      .star-rating .far {
         color: #ddd;
      }
      .total-sales {
         background: #f8f9fa;
         padding: 5px 10px;
         border-radius: 10px;
         font-size: 14px;
         color: #666;
         display: inline-block;
         margin: 5px 0;
      }
      .product-info {
         display: flex;
         justify-content: space-between;
         align-items: center;
         margin: 10px 0;
         flex-wrap: wrap;
      }
      .rating-number {
         font-size: 14px;
         color: #666;
         margin-left: 8px;
      }
      .product-stats {
         width: 100%;
         margin: 15px 0;
         padding: 15px;
         background: #f9f9f9;
         border-radius: 10px;
      }
      .quick-view .box {
         max-width: 500px;
         margin: 0 auto;
         padding: 2rem;
      }
      .quick-view .box img {
         width: 100%;
         max-height: 400px;
         object-fit: contain;
      }
      .description {
         margin: 15px 0;
         color: #555;
         line-height: 1.6;
      }
   </style>

</head>
<body>

<?php include 'components/user_header.php'; ?>

<section class="quick-view">

   <h1 class="title">quick view</h1>

   <?php
   if (isset($_GET['pid'])) {
      $pid = $_GET['pid'];
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
         WHERE p.id = ?
         GROUP BY p.id
      ");
      $select_products->execute([$pid]);
      if ($select_products->rowCount() > 0) {
         while ($fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)) {
            
            // Calculate average rating
            $average_rating = round($fetch_products['average_rating'], 1);
            $total_sold = $fetch_products['total_sold'];
            $total_reviews = $fetch_products['total_reviews'];
            
            // Generate star rating HTML
            $star_rating = '';
            $full_stars = floor($average_rating);
            $has_half_star = ($average_rating - $full_stars) >= 0.5;
            
            for($i = 1; $i <= 5; $i++){
               if($i <= $full_stars){
                  $star_rating .= '<i class="fas fa-star"></i>';
               } elseif($i == $full_stars + 1 && $has_half_star){
                  $star_rating .= '<i class="fas fa-star-half-alt"></i>';
               } else {
                  $star_rating .= '<i class="far fa-star"></i>';
               }
            }
   ?>
   <form action="" method="post" class="box">
      <input type="hidden" name="pid" value="<?= $fetch_products['id']; ?>">
      <input type="hidden" name="name" value="<?= $fetch_products['name']; ?>">
      <input type="hidden" name="price" value="<?= $fetch_products['price']; ?>">
      <input type="hidden" name="image" value="<?= $fetch_products['image']; ?>">
      
      <img src="uploaded_img/<?= $fetch_products['image']; ?>" alt="">
      
      <div class="name" style="font-size: 24px; margin: 15px 0;"><?= $fetch_products['name']; ?></div>
      
      <!-- Product Description -->
      <?php if(!empty($fetch_products['description'])): ?>
      <div class="description">
         <strong>Description:</strong><br>
         <?= $fetch_products['description']; ?>
      </div>
      <?php endif; ?>
      
      <!-- Star Rating -->
      <div class="star-rating">
         <?= $star_rating ?>
         <span class="rating-number">
            <?= $average_rating > 0 ? "($average_rating)" : "No ratings yet" ?>
         </span>
      </div>
      
      <div class="flex">
         <div class="price" style="font-size: 20px;"><span>&#8369;</span><?= $fetch_products['price']; ?></div>
         <input type="number" name="qty" class="qty" min="1" max="99" value="1" maxlength="2">
      </div>
      
      <!-- Product Stats -->
      <div class="product-stats">
         <div class="product-info">
            <div class="total-sales">
               <i class="fas fa-chart-line"></i> Total Sold: <?= $total_sold ?> items
            </div>
            <?php if($total_reviews > 0): ?>
            <div style="font-size: 14px; color: #666;">
               <i class="fas fa-comment"></i> <?= $total_reviews ?> customer review<?= $total_reviews > 1 ? 's' : '' ?>
            </div>
            <?php else: ?>
            <div style="font-size: 14px; color: #666;">
               <i class="fas fa-comment"></i> No reviews yet
            </div>
            <?php endif; ?>
         </div>
      </div>
      
      <div class="btn-group" style="display: flex; gap: 1rem; margin-top: 1rem;">
         <button type="submit" name="add_to_cart" class="cart-btn" style="flex: 1;">Add to Cart</button>
         <a href="#" class="cart-btn order-btn" style="flex: 1; text-align: center;">Order Now</a>
      </div>
   </form>
   <?php
         }
      } else {
         echo '<p class="empty">No products found!</p>';
      }
   } else {
      echo '<p class="empty">No product selected!</p>';
   }
   ?>

</section>

<?php include 'components/footer.php'; ?>

<script src="https://unpkg.com/swiper@8/swiper-bundle.min.js"></script>
<script src="js/script.js"></script>

<!-- Order Now Button Script -->
<script>
document.querySelectorAll('.box').forEach(form => {
   const qtyInput = form.querySelector('.qty');
   const orderBtn = form.querySelector('.order-btn');

   if(orderBtn && qtyInput){
      orderBtn.addEventListener('click', function(e){
         e.preventDefault();
         const pid = form.querySelector('input[name="pid"]').value;
         const qty = qtyInput.value;
         window.location.href = `checkout.php?pid=${pid}&qty=${qty}`;
      });
   }
});
</script>

</body>
</html>