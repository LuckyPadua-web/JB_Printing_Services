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

</head>
<body>

<?php include 'components/user_header.php'; ?>

<section class="quick-view">

   <h1 class="title">quick view</h1>

   <?php
   if (isset($_GET['pid'])) {
      $pid = $_GET['pid'];
      $select_products = $conn->prepare("SELECT * FROM `products` WHERE id = ?");
      $select_products->execute([$pid]);
      if ($select_products->rowCount() > 0) {
         while ($fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)) {
   ?>
   <form action="" method="post" class="box">
      <input type="hidden" name="pid" value="<?= $fetch_products['id']; ?>">
      <input type="hidden" name="name" value="<?= $fetch_products['name']; ?>">
      <input type="hidden" name="price" value="<?= $fetch_products['price']; ?>">
      <input type="hidden" name="image" value="<?= $fetch_products['image']; ?>">
      <img src="uploaded_img/<?= $fetch_products['image']; ?>" alt="">
      
      <div class="name"><?= $fetch_products['name']; ?></div>
      <div class="flex">
         <div class="price"><span>&#8369;</span><?= $fetch_products['price']; ?></div>
         <input type="number" name="qty" class="qty" min="1" max="99" value="1" maxlength="2">
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
