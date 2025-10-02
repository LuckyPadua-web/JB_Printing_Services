<?php

include '../components/connect.php';

session_start();

if(isset($_SESSION['admin_id'])){
   $admin_id = $_SESSION['admin_id'];
}else{
   $admin_id = '';
   header('location:admin_login.php');
};

if(isset($_POST['update'])){

   $pid = $_POST['pid'];
   $pid = filter_var($pid, FILTER_SANITIZE_STRING);
   $name = $_POST['name'];
   $name = filter_var($name, FILTER_SANITIZE_STRING);
   $price = $_POST['price'];
   $price = filter_var($price, FILTER_SANITIZE_STRING);
   
   // Get old product details before update
   $get_old_product = $conn->prepare("SELECT * FROM `products` WHERE id = ?");
   $get_old_product->execute([$pid]);
   $old_product = $get_old_product->fetch(PDO::FETCH_ASSOC);

   $update_product = $conn->prepare("UPDATE `products` SET name = ?, price = ? WHERE id = ?");
   $update_product->execute([$name, $price, $pid]);

   $message[] = 'Product updated!';

   $old_image = $_POST['old_image'];
   $image = $_FILES['image']['name'];
   $image = filter_var($image, FILTER_SANITIZE_STRING);
   $image_size = $_FILES['image']['size'];
   $image_tmp_name = $_FILES['image']['tmp_name'];
   $image_folder = '../uploaded_img/'.$image;

   if(!empty($image)){
      if($image_size > 2000000){
         $message[] = 'Images size is too large!';
      }else{
         $update_image = $conn->prepare("UPDATE `products` SET image = ? WHERE id = ?");
         $update_image->execute([$image, $pid]);
         move_uploaded_file($image_tmp_name, $image_folder);
         unlink('../uploaded_img/'.$old_image);
         $message[] = 'Image updated!';
      }
   }

   // Send notification to customers who ordered this product
   try {
      // Get customers who ordered this product
      $select_customers = $conn->prepare("
         SELECT DISTINCT u.email, u.name as customer_name 
         FROM `users` u 
         JOIN `orders` o ON u.id = o.user_id 
         JOIN `order_details` od ON o.id = od.order_id 
         WHERE od.product_id = ? AND u.email IS NOT NULL AND u.email != ''
      ");
      $select_customers->execute([$pid]);
      
      if($select_customers->rowCount() > 0){
         // Include PHPMailer
         if(file_exists('../vendor/autoload.php')){
            require_once '../vendor/autoload.php';
            
            while($customer = $select_customers->fetch(PDO::FETCH_ASSOC)){
               try {
                  $mail = new PHPMailer\PHPMailer\PHPMailer(true);
                  
                  // SMTP configuration (update with your details)
                  $mail->isSMTP();
                  $mail->Host = 'smtp.gmail.com';
                  $mail->SMTPAuth = true;
                  $mail->Username = 'your-email@gmail.com'; // Your Gmail
                  $mail->Password = 'your-app-password'; // Gmail App Password
                  $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
                  $mail->Port = 587;
                  
                  // Recipients
                  $mail->setFrom('noreply@jbprintingservices.com', 'JB Printing Services');
                  $mail->addAddress($customer['email'], $customer['customer_name']);
                  $mail->addReplyTo('info@jbprintingservices.com', 'JB Printing Services');
                  
                  // Content
                  $mail->isHTML(true);
                  $mail->Subject = 'Product Update Notification - ' . $name;
                  
                  $email_body = "
                  <html>
                  <head>
                     <style>
                        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                        .header { background: #4285f4; color: white; padding: 20px; text-align: center; border-radius: 5px 5px 0 0; }
                        .content { background: #f9f9f9; padding: 20px; border-radius: 0 0 5px 5px; }
                        .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
                        .update-info { background: white; padding: 15px; border-left: 4px solid #34a853; margin: 15px 0; border-radius: 4px; }
                        .product-details { background: #e8f5e8; padding: 15px; border-radius: 4px; margin: 10px 0; }
                     </style>
                  </head>
                  <body>
                     <div class='container'>
                        <div class='header'>
                           <h2>JB Printing Services</h2>
                           <p>Product Update Notification</p>
                        </div>
                        <div class='content'>
                           <h3>Dear " . htmlspecialchars($customer['customer_name']) . ",</h3>
                           <p>We're writing to inform you about an update to a product you've previously ordered:</p>
                           
                           <div class='update-info'>
                              <h4>🔄 Product Updated: <strong>" . htmlspecialchars($name) . "</strong></h4>
                              <div class='product-details'>
                                 <p><strong>Product Name:</strong> " . htmlspecialchars($name) . "</p>
                                 <p><strong>New Price:</strong> ₱" . number_format($price, 2) . "</p>
                                 " . ($old_product['price'] != $price ? 
                                    "<p><strong>Previous Price:</strong> ₱" . number_format($old_product['price'], 2) . "</p>" : "") . "
                              </div>
                           </div>
                           
                           <p>This update may affect products in your previous orders. If you have any questions or concerns about this change, please don't hesitate to contact us.</p>
                           
                           <p>Thank you for your understanding and continued support!</p>
                           
                           <p>Best regards,<br><strong>JB Printing Services Team</strong></p>
                        </div>
                        <div class='footer'>
                           <p>This is an automated notification. Please do not reply to this email.</p>
                           <p>If you have questions, contact us at: info@jbprintingservices.com</p>
                        </div>
                     </div>
                  </body>
                  </html>
                  ";
                  
                  $mail->Body = $email_body;
                  
                  // Plain text version
                  $mail->AltBody = "Dear " . $customer['customer_name'] . ",\n\n" .
                                  "We're writing to inform you about an update to a product you've previously ordered:\n\n" .
                                  "Product Updated: " . $name . "\n" .
                                  "New Price: ₱" . number_format($price, 2) . "\n" .
                                  ($old_product['price'] != $price ? 
                                   "Previous Price: ₱" . number_format($old_product['price'], 2) . "\n" : "") .
                                  "\nThis update may affect products in your previous orders. If you have any questions, please contact us.\n\n" .
                                  "Best regards,\nJB Printing Services Team";
                  
                  $mail->send();
                  
               } catch (Exception $e) {
                  // Log error but don't stop the process
                  error_log("Email sending failed for " . $customer['email'] . ": " . $e->getMessage());
               }
            }
            
            $message[] = 'Product updated and notifications sent to customers!';
         }
      }
   } catch (Exception $e) {
      // If email sending fails, just continue
      $message[] = 'Product updated! (Email notifications skipped)';
   }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>update product</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="../css/admin_style.css">

   <style>
      .notification-info {
         background: #e8f5e8;
         border: 1px solid #34a853;
         border-radius: 8px;
         padding: 15px;
         margin: 15px 0;
         font-size: 1.4rem;
         color: #2d5016;
      }

      .notification-info i {
         color: #34a853;
         margin-right: 10px;
      }
   </style>
</head>
<body>

<?php include '../components/admin_header.php' ?>

<!-- update product section starts  -->

<section class="update-product">

   <h1 class="heading">update product</h1>

   <div class="notification-info">
      <i class="fas fa-info-circle"></i>
      <strong>Notification:</strong> When you update a product, customers who previously ordered this item will receive an email notification with the product details (no order numbers included).
   </div>

   <?php
      $update_id = $_GET['update'];
      $show_products = $conn->prepare("SELECT * FROM `products` WHERE id = ?");
      $show_products->execute([$update_id]);
      if($show_products->rowCount() > 0){
         while($fetch_products = $show_products->fetch(PDO::FETCH_ASSOC)){  
   ?>
   <form action="" method="POST" enctype="multipart/form-data">
      <input type="hidden" name="pid" value="<?= $fetch_products['id']; ?>">
      <input type="hidden" name="old_image" value="<?= $fetch_products['image']; ?>">
      <img src="../uploaded_img/<?= $fetch_products['image']; ?>" alt="">
      <span>Update name</span>
      <input type="text" required placeholder="enter product name" name="name" maxlength="100" class="box" value="<?= $fetch_products['name']; ?>">
      <span>Update price</span>
      <input type="number" min="0" max="9999999999" required placeholder="enter product price" name="price" onkeypress="if(this.value.length == 10) return false;" class="box" value="<?= $fetch_products['price']; ?>">
      <span>Update category</span>
      
      <span>update image</span>
      <input type="file" name="image" class="box" accept="image/jpg, image/jpeg, image/png, image/webp">
      <div class="flex-btn">
         <input type="submit" value="update" class="btn" name="update">
         <a href="products.php" class="option-btn">go back</a>
      </div>
   </form>
   <?php
         }
      }else{
         echo '<p class="empty">no products added yet!</p>';
      }
   ?>

</section>

<!-- update product section ends -->

<!-- custom js file link  -->
<script src="../js/admin_script.js"></script>

</body>
</html>