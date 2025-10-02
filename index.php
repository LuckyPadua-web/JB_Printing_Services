<?php

include 'components/connect.php';

session_start();

if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
}else{
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
   <title>home</title>

   <link rel="stylesheet" href="https://unpkg.com/swiper@8/swiper-bundle.min.css" />

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

   <style>
      .star-rating {
         color: #ffc107;
         margin: 5px 0;
         font-size: 14px;
      }
      .star-rating .far {
         color: #ddd;
      }
      .total-sales {
         background: #f8f9fa;
         padding: 3px 8px;
         border-radius: 10px;
         font-size: 12px;
         color: #666;
         display: inline-block;
         margin-top: 5px;
      }
      .product-info {
         display: flex;
         justify-content: space-between;
         align-items: center;
         margin-top: 5px;
         flex-wrap: wrap;
      }
      .rating-number {
         font-size: 12px;
         color: #666;
         margin-left: 5px;
      }
      .product-stats {
         width: 100%;
         margin-top: 8px;
      }
   </style>

</head>
<body>

<?php include 'components/user_header.php'; ?>

<section class="hero">

   <div class="swiper hero-slider">

      <div class="swiper-wrapper">

         <div class="swiper-slide slide">
            <div class="content">
               <h3>Welcome to</h3>
            </div>
            <div class="image">
               <img src="images/JB/LOGO-A.png" alt="JB Logo">
            </div>
         </div>

         <div class="swiper-slide slide">
            <div class="content">
               <span>Order Online</span>
               <h3>Polo Shirt</h3>
            </div>
            <div class="image">
               <img src="images/JB/Educ ColSC mockup copy.jpg" alt="Featured">
            </div>
         </div>

         <div class="swiper-slide slide">
            <div class="content">
               <span>Order Online</span>
               <h3>Full Sublimation Polo Shirt</h3>
            </div>
            <div class="image">
               <img src="images/JB/CA ML mockup copy.jpg" alt="Featured">
            </div>
         </div>

      </div>

      <div class="swiper-pagination"></div>

   </div>

</section>

<section class="category">

   <h1 class="title">product category</h1>

   <div class="box-container">

      <a href="category.php?category=Customize stickers " class="box">
         <img src="images/JB/02.png" alt="">
         <h3>Customize stickers </h3>
      </a>

      <a href="category.php?category=Full sublimation" class="box">
         <img src="images/JB/01.png" alt="">
         <h3>Full sublimation</h3>
      </a>

      <a href="category.php?category=Tarpaulin" class="box">
         <img src="images/JB/03.png" alt="">
         <h3>Tarpaulin</h3>
      </a>

      <a href="category.php?category=Souvenirs" class="box">
         <img src="images/JB/04.png" alt="">
         <h3>Souvenirs</h3>
      </a>

   </div>

</section>

<section class="products">

   <h1 class="title">What we offer</h1>

   <div class="box-container">

      <?php
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
            LIMIT 6
         ");
         $select_products->execute();
         
         if($select_products->rowCount() > 0){
            while($fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)){
               
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
         
         <a href="quick_view.php?pid=<?= $fetch_products['id']; ?>" class="fas fa-eye"></a>
         <button type="submit" class="fas fa-shopping-cart" name="add_to_cart"></button>
         <img src="uploaded_img/<?= $fetch_products['image']; ?>" alt="">
         
         <div class="name"><?= $fetch_products['name']; ?></div>
         
         <!-- Star Rating -->
         <div class="star-rating">
            <?= $star_rating ?>
            <span class="rating-number">
               <?= $average_rating > 0 ? "($average_rating)" : "No ratings" ?>
            </span>
         </div>
         
         <div class="flex">
            <div class="price"><span>&#8369; </span><?= $fetch_products['price']; ?></div>
            <input type="number" name="qty" class="qty" min="1" max="99" value="1" maxlength="2">
         </div>
         
         <!-- Product Stats -->
         <div class="product-stats">
            <div class="product-info">
               <div class="total-sales">
                  <i class="fas fa-chart-line"></i> Sold: <?= $total_sold ?>
               </div>
               <?php if($total_reviews > 0): ?>
               <div style="font-size: 12px; color: #666;">
                  <i class="fas fa-comment"></i> <?= $total_reviews ?> review<?= $total_reviews > 1 ? 's' : '' ?>
               </div>
               <?php endif; ?>
            </div>
         </div>
      </form>
      <?php
            }
         }else{
            echo '<p class="empty">Wala pang product</p>';
         }
      ?>

   </div>

   <div class="more-btn">
      <a href="menu.php" class="btn">veiw all</a>
   </div>

</section>

<?php include 'components/footer.php'; ?>

<script src="https://unpkg.com/swiper@8/swiper-bundle.min.js"></script>

<!-- custom js file link  -->
<script src="js/script.js"></script>

<script>
var swiper = new Swiper(".hero-slider", {
   loop: true,
   grabCursor: true,
   effect: "flip",
   autoplay: {
      delay: 3000, // 3 seconds delay between slides
      disableOnInteraction: false, // Continue autoplay even when user interacts
   },
   speed: 800, // Transition speed in milliseconds
   pagination: {
      el: ".swiper-pagination",
      clickable: true,
   },
});
</script>

</body>
</html>