<?php

include '../components/connect.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if(!isset($admin_id)){
   header('location:admin_login.php');
}

if(isset($_GET['delete'])){
   $delete_id = $_GET['delete'];

   // Delete the uploaded valid ID file from the server
   $get_file = $conn->prepare("SELECT valid_id FROM `users` WHERE id = ?");
   $get_file->execute([$delete_id]);
   $old_file = $get_file->fetch(PDO::FETCH_ASSOC)['valid_id'];
   if($old_file && file_exists('../' . $old_file)){
      unlink('../' . $old_file);
   }

   // Delete user data
   $delete_users = $conn->prepare("DELETE FROM `users` WHERE id = ?");
   $delete_users->execute([$delete_id]);

   $delete_order = $conn->prepare("DELETE FROM `orders` WHERE user_id = ?");
   $delete_order->execute([$delete_id]);

   $delete_cart = $conn->prepare("DELETE FROM `cart` WHERE user_id = ?");
   $delete_cart->execute([$delete_id]);

   header('location:users_accounts.php');
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Users Accounts</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="../css/admin_style.css">

   <style>
      .box img {
         max-width: 200px;
         border: 1px solid #ccc;
         border-radius: 10px;
         margin-top: 10px;
      }
      .box a {
         display: inline-block;
         margin-top: 10px;
      }
   </style>
</head>
<body>

<?php include '../components/admin_header.php' ?>

<!-- user accounts section starts  -->

<section class="accounts">

   <h1 class="heading">Users Account Details</h1>

   <div class="box-container">

   <?php
      $select_account = $conn->prepare("SELECT * FROM `users`");
      $select_account->execute();
      if($select_account->rowCount() > 0){
         while($fetch_accounts = $select_account->fetch(PDO::FETCH_ASSOC)){  
   ?>
   <div class="box">
      <p><strong>User ID:</strong> <span><?= $fetch_accounts['id']; ?></span></p>
      <p><strong>Name:</strong> <span><?= $fetch_accounts['name']; ?></span></p>
      <p><strong>Email:</strong> <span><?= $fetch_accounts['email']; ?></span></p>
      <p><strong>Phone Number:</strong> <span><?= $fetch_accounts['number']; ?></span></p>
      <p><strong>Valid ID:</strong></p>
      <?php if (!empty($fetch_accounts['valid_id'])): ?>
         <?php if (preg_match('/\.(jpg|jpeg|png)$/i', $fetch_accounts['valid_id'])): ?>
            <img src="../<?= $fetch_accounts['valid_id']; ?>" alt="Valid ID">
         <?php elseif (preg_match('/\.pdf$/i', $fetch_accounts['valid_id'])): ?>
            <a href="../<?= $fetch_accounts['valid_id']; ?>" target="_blank">View PDF</a>
         <?php else: ?>
            <p>No valid preview available.</p>
         <?php endif; ?>
      <?php else: ?>
         <p>No ID uploaded</p>
      <?php endif; ?>
      
      <a href="users_accounts.php?delete=<?= $fetch_accounts['id']; ?>" class="delete-btn" onclick="return confirm('Delete this user?');">Delete</a>
   </div>
   <?php
      }
   } else {
      echo '<p class="empty">No accounts available</p>';
   }
   ?>

   </div>

</section>

<!-- user accounts section ends -->

<!-- custom js file link  -->
<script src="../js/admin_script.js"></script>

</body>
</html>
