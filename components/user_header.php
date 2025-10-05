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
      <img src="images/JB/Logo-A.png" style="height: 50px; width: 50px; object-fit: contain;">
      <a href="index.php" class="logo" >JB Printing Services</a>

      <nav class="navbar">
         <a href="index.php">HOME</a>
         <a href="menu.php">PRODUCTS</a>
         <a href="orders.php">ORDERS</a>
         <a href="about.php">ABOUT US</a>
         <?php if(isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])): ?>
         <a href="contact.php" style="position: relative;">
            CONTACT
            <?php
            try {
               $unread_count = $conn->prepare("
                  SELECT COUNT(*) as count 
                  FROM conversation_messages cm
                  JOIN conversations c ON cm.conversation_id = c.id
                  WHERE c.user_id = ? AND cm.sender_type = 'admin' AND cm.is_read = 0
               ");
               $unread_count->execute([$user_id]);
               $unread = $unread_count->fetch(PDO::FETCH_ASSOC);
               if($unread['count'] > 0) {
                  echo '<span style="position: absolute; top: -5px; right: -10px; background: #ff4757; color: white; border-radius: 50%; width: 20px; height: 20px; font-size: 12px; display: flex; align-items: center; justify-content: center; font-weight: bold;">' . $unread['count'] . '</span>';
               }
            } catch(Exception $e) {
               // Table might not exist yet, ignore error
            }
            ?>
         </a>
         <?php endif; ?>
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

