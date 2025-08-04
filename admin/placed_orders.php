<?php

include '../components/connect.php';

session_start();


if(isset($_SESSION['admin_id'])){
   $admin_id = $_SESSION['admin_id'];
}else{
   $admin_id = '';
   header('location:admin_login.php');
};


if(isset($_POST['update_payment'])){

   $order_id = $_POST['order_id'];
   $payment_status = $_POST['payment_status'];
   $update_status = $conn->prepare("UPDATE `orders` SET payment_status = ? WHERE id = ?");
   $update_status->execute([$payment_status, $order_id]);
   $message[] = 'Order status updated!';

}

if(isset($_POST['update_delivery_date'])){

   $order_id = $_POST['order_id'];
   $expected_delivery_date = $_POST['expected_delivery_date'];
   $update_delivery = $conn->prepare("UPDATE `orders` SET expected_delivery_date = ? WHERE id = ?");
   $update_delivery->execute([$expected_delivery_date, $order_id]);
   $message[] = 'Delivery date updated!';

}

if(isset($_GET['delete'])){
   $delete_id = $_GET['delete'];
   
   // Get the design file name before deleting the order
   $get_design_file = $conn->prepare("SELECT design_file FROM `orders` WHERE id = ?");
   $get_design_file->execute([$delete_id]);
   $design_data = $get_design_file->fetch(PDO::FETCH_ASSOC);
   
   // Delete the design file if it exists
   if (!empty($design_data['design_file']) && file_exists('../uploaded_designs/' . $design_data['design_file'])) {
      unlink('../uploaded_designs/' . $design_data['design_file']);
   }
   
   $delete_order = $conn->prepare("DELETE FROM `orders` WHERE id = ?");
   $delete_order->execute([$delete_id]);
   header('location:placed_orders.php');
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>placed orders</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="../css/admin_style.css">

</head>
<body>

<?php include '../components/admin_header.php' ?>

<!-- placed orders section starts  -->

<section class="placed-orders">

   <h1 class="heading">placed orders</h1>

   <div class="box-container">

   <?php
      $select_orders = $conn->prepare("SELECT * FROM `orders`");
      $select_orders->execute();
      if($select_orders->rowCount() > 0){
         while($fetch_orders = $select_orders->fetch(PDO::FETCH_ASSOC)){
   ?>
   <div class="box">
      <p> user id : <span><?= $fetch_orders['user_id']; ?></span> </p>
      <p> placed on : <span><?= $fetch_orders['placed_on']; ?></span> </p>
      <p> name : <span><?= $fetch_orders['name']; ?></span> </p>
      <p> email : <span><?= $fetch_orders['email']; ?></span> </p>
      <p> number : <span><?= $fetch_orders['number']; ?></span> </p>
      <p> address : <span><?= $fetch_orders['address']; ?></span> </p>
      <p> total products : <span><?= $fetch_orders['total_products']; ?></span> </p>
      <p> total price : <span>&#8369;<?= $fetch_orders['total_price']; ?>/-</span> </p>
      <p> payment method : <span><?= $fetch_orders['method']; ?></span> </p>
      <p> expected delivery : <span style="color: <?= empty($fetch_orders['expected_delivery_date']) ? '#e74c3c' : '#27ae60'; ?>;">
         <?= empty($fetch_orders['expected_delivery_date']) ? 'Not set' : date('F d, Y', strtotime($fetch_orders['expected_delivery_date'])); ?>
      </span> </p>
      <?php if (!empty($fetch_orders['design_file'])): ?>
      <p> design file : <span><a href="../uploaded_designs/<?= $fetch_orders['design_file']; ?>" target="_blank" style="color: #007bff; text-decoration: underline;">View Design</a></span> </p>
      <?php else: ?>
      <p> design file : <span>No design uploaded</span> </p>
      <?php endif; ?>
      
      <!-- Payment Status Update Form -->
      <form action="" method="POST" class="status-form">
         <input type="hidden" name="order_id" value="<?= $fetch_orders['id']; ?>">
         <select name="payment_status" class="drop-down">
            <option value="" selected disabled><?= $fetch_orders['payment_status']; ?></option>
            <option value="Pending">Pending</option>
            <option value="To received">To received</option>
            <option value="Delivered">Delivered</option>
         </select>
         <div class="flex-btn">
            <input type="submit" value="Update Status" class="btn" name="update_payment">
         </div>
      </form>
      <br>
      <!-- Delivery Date Update Form -->
      <form action="" method="POST" class="delivery-form">
         <input type="hidden" name="order_id" value="<?= $fetch_orders['id']; ?>">
         <input type="date" name="expected_delivery_date" value="<?= $fetch_orders['expected_delivery_date']; ?>" 
                min="<?= date('Y-m-d'); ?>" class="box" placeholder="Select delivery date">
         <input type="submit" value="Update Delivery Date" class="btn" name="update_delivery_date">
         <a href="placed_orders.php?delete=<?= $fetch_orders['id']; ?>" class="delete-btn" onclick="return confirm('delete this order?');">Delete</a>
      </form>
   </div>
   <?php
      }
   }else{
      echo '<p class="empty">no orders placed yet!</p>';
   }
   ?>

   </div>

</section>

<!-- placed orders section ends -->









<!-- custom js file link  -->
<script src="../js/admin_script.js"></script>

</body>
</html>