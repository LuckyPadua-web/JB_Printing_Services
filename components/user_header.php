<?php
if(isset($message)){
   foreach($message as $message){
      echo '
      <div class="message">
         <span>'.$message.'</span>
         <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
      </div>
      ';
   }
}
?>

<header class="header">

   <section class="flex">
      <img src="images/JB/sticker zone logo 01.png" >
      <a href="index.php" class="logo" >JB Printing Services</a>

      <nav class="navbar">
         <a href="index.php">HOME</a>
         <a href="menu.php">PRODUCTS</a>
         <a href="orders.php">ORDERS</a>
      </nav>

      <div class="icons">
         <?php
            $count_cart_items = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
            $count_cart_items->execute([$user_id]);
            $total_cart_items = $count_cart_items->rowCount();
         ?>
         <a href="search.php"><i class="fas fa-search"></i></a>
         <a href="cart.php"><i class="fas fa-shopping-cart"></i><span>(<?= $total_cart_items; ?>)</span></a>
         <div id="user-btn" class="fas fa-user"></div>
         <div id="menu-btn" class="fas fa-bars"></div>
      </div>

      <div class="profile">
         <?php
            $select_profile = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
            $select_profile->execute([$user_id]);
            if($select_profile->rowCount() > 0){
               $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
         ?>
         <p class="name"><?= $fetch_profile['name']; ?></p>
         <div class="flex">
            <a href="profile.php" class="btn">Profile</a>
            <a href="components/user_logout.php" onclick="return confirm('logout from this website?');" class="delete-btn">Logout</a>
         </div>
         <p class="account">
            <a href="login.php">Login</a> or
            <a href="register.php">Register</a>
         </p> 
         <?php
            }else{
         ?>
            <p class="name">Login First!</p>
            <a href="login.php" class="btn">Customer Login</a>
            <br>
            <br>
            <a href="admin/admin_login.php" class="btn">Admin Login</a>
         <?php
          }
         ?>
      </div>

   </section>

</header>

