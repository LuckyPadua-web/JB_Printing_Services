<?php
include 'components/connect.php';
session_start();

if (isset($_SESSION['user_id'])) {
   $user_id = $_SESSION['user_id'];
} else {
   $user_id = '';
   header('location:login.php');
   exit;
}

$fetch_profile = [];
$select_profile = $conn->prepare("SELECT * FROM users WHERE id = ?");
$select_profile->execute([$user_id]);
if ($select_profile->rowCount() > 0) {
   $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
}

// Handle direct product ordering from quick view
$direct_order = false;
$direct_product = null;
if (isset($_GET['pid']) && isset($_GET['qty'])) {
   $direct_order = true;
   $pid = filter_var($_GET['pid'], FILTER_SANITIZE_NUMBER_INT);
   $qty = filter_var($_GET['qty'], FILTER_SANITIZE_NUMBER_INT);
   
   $select_product = $conn->prepare("SELECT * FROM `products` WHERE id = ?");
   $select_product->execute([$pid]);
   if ($select_product->rowCount() > 0) {
      $direct_product = $select_product->fetch(PDO::FETCH_ASSOC);
      $direct_product['quantity'] = $qty;
   }
}

if (isset($_POST['submit'])) {
   $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
   $number = filter_var($_POST['number'], FILTER_SANITIZE_STRING);
   $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
   $method = filter_var($_POST['method'], FILTER_SANITIZE_STRING);
   $address = filter_var($_POST['address'], FILTER_SANITIZE_STRING);
   $total_products = $_POST['total_products'];
   $total_price = $_POST['total_price'];
   $gcash_ref = !empty($_POST['gcash_ref']) ? filter_var($_POST['gcash_ref'], FILTER_SANITIZE_STRING) : null;
   $expected_delivery_date = !empty($_POST['expected_delivery_date']) ? $_POST['expected_delivery_date'] : null;

   // Handle design file upload
   $design_file = null;
   if (isset($_POST['design_option']) && $_POST['design_option'] == 'yes' && isset($_FILES['design_file']) && $_FILES['design_file']['error'] == 0) {
      $design_filename = $_FILES['design_file']['name'];
      $design_filename = filter_var($design_filename, FILTER_SANITIZE_STRING);
      $design_size = $_FILES['design_file']['size'];
      $design_tmp_name = $_FILES['design_file']['tmp_name'];
      
      // Get file extension
      $file_extension = strtolower(pathinfo($design_filename, PATHINFO_EXTENSION));
      $allowed_extensions = ['jpg', 'jpeg', 'png', 'webp', 'pdf'];
      
      // Validate file extension
      if (!in_array($file_extension, $allowed_extensions)) {
         $message[] = 'Invalid file format. Only JPG, JPEG, PNG, WEBP, and PDF files are allowed.';
      } else if ($design_size > 5000000) {
         $message[] = 'Design file size is too large (maximum 5MB allowed)';
      } else {
         // Create unique filename to prevent conflicts
         $unique_filename = uniqid() . '_' . $design_filename;
         $design_folder = 'uploaded_designs/' . $unique_filename;
         
         if (move_uploaded_file($design_tmp_name, $design_folder)) {
            $design_file = $unique_filename;
         } else {
            $message[] = 'Failed to upload design file';
         }
      }
   }

   // Check if it's a direct order or cart order
   $has_items = false;
   if ($direct_order && $direct_product) {
      $has_items = true;
   } else {
      $check_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
      $check_cart->execute([$user_id]);
      $has_items = $check_cart->rowCount() > 0;
   }

   if ($has_items) {
      if ($address == '') {
         $message[] = 'please add your address!';
      } else {
         $insert_order = $conn->prepare("INSERT INTO `orders` (user_id, name, number, email, method, address, total_products, total_price, gcash_ref, design_file, expected_delivery_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
         $insert_order->execute([$user_id, $name, $number, $email, $method, $address, $total_products, $total_price, $gcash_ref, $design_file, $expected_delivery_date]);

         // Only delete cart if it's not a direct order
         if (!$direct_order) {
            $delete_cart = $conn->prepare("DELETE FROM `cart` WHERE user_id = ?");
            $delete_cart->execute([$user_id]);
         }

         $message[] = 'Order placed successfully!';
      }
   } else {
      $message[] = 'No items to order';
   }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Checkout</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php include 'components/user_header.php'; ?>

<div class="heading">
   <h3>Checkout</h3>
</div>

<section class="checkout">
   <h1 class="title">Order Summary</h1>
   <form action="" method="post" enctype="multipart/form-data">

      <div class="cart-items">
         <h3>Order Items</h3>
         <?php
         $grand_total = 0;
         $cart_items = [];
         $total_products = '';
         
         if ($direct_order && $direct_product) {
            // Handle direct product order
            $cart_items[] = $direct_product['name'] . ' (₱' . $direct_product['price'] . ' x ' . $direct_product['quantity'] . ')';
            $grand_total = ($direct_product['price'] * $direct_product['quantity']);
            ?>
            <p><span class="name"><?= htmlspecialchars($direct_product['name']); ?></span>
               <span class="price">&#8369;<?= $direct_product['price']; ?> x <?= $direct_product['quantity']; ?></span></p>
            <?php
            $total_products = implode(', ', $cart_items);
         } else {
            // Handle cart orders
            $select_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
            $select_cart->execute([$user_id]);
            if ($select_cart->rowCount() > 0) {
               while ($fetch_cart = $select_cart->fetch(PDO::FETCH_ASSOC)) {
                  $cart_items[] = $fetch_cart['name'] . ' (₱' . $fetch_cart['price'] . ' x ' . $fetch_cart['quantity'] . ')';
                  $grand_total += ($fetch_cart['price'] * $fetch_cart['quantity']);
                  ?>
                  <p><span class="name"><?= htmlspecialchars($fetch_cart['name']); ?></span>
                     <span class="price">&#8369;<?= $fetch_cart['price']; ?> x <?= $fetch_cart['quantity']; ?></span></p>
               <?php
               }
               $total_products = implode(', ', $cart_items);
            } else {
               echo '<p class="empty">Your cart is empty!</p>';
            }
         }
         ?>
         <p class="grand-total"><span class="name">Grand total :</span>
            <span class="price">&#8369;<?= $grand_total; ?></span></p>
         <?php if (!$direct_order): ?>
         <a href="cart.php" class="btn">View Cart</a>
         <?php endif; ?>
      </div>

      <input type="hidden" name="total_products" value="<?= htmlspecialchars($total_products); ?>">
      <input type="hidden" name="total_price" value="<?= $grand_total; ?>">
      <input type="hidden" name="name" value="<?= htmlspecialchars($fetch_profile['name'] ?? '') ?>">
      <input type="hidden" name="number" value="<?= htmlspecialchars($fetch_profile['number'] ?? '') ?>">
      <input type="hidden" name="email" value="<?= htmlspecialchars($fetch_profile['email'] ?? '') ?>">
      <input type="hidden" name="address" value="<?= htmlspecialchars($fetch_profile['address'] ?? '') ?>">

      <div class="user-info">
         <h3>Your Info</h3>
         <p><i class="fas fa-user"></i><span><?= htmlspecialchars($fetch_profile['name'] ?? '') ?></span></p>
         <p><i class="fas fa-phone"></i><span><?= htmlspecialchars($fetch_profile['number'] ?? '') ?></span></p>
         <p><i class="fas fa-envelope"></i><span><?= htmlspecialchars($fetch_profile['email'] ?? '') ?></span></p>
         <a href="update_profile.php" class="btn">Update Info</a>

         <h3>Delivery Address</h3>
         <p><i class="fas fa-map-marker-alt"></i><span><?= !empty($fetch_profile['address']) ? htmlspecialchars($fetch_profile['address']) : 'Please enter your address'; ?></span></p>
         <a href="update_address.php" class="btn">Update Address</a>

         <h3>Payment Method</h3>
         <select name="method" class="box" id="payment-method" required onchange="toggleGcashDetails()">
            <option value="" disabled selected>Select payment method --</option>
            <option value="cash on delivery">Cash on delivery</option>
            <option value="gcash">GCash</option>
         </select>

         <div id="gcash-details" style="display:none; margin-top: 10px;">
            <p>Please scan the QR code below and enter the GCash Reference Number:</p>
            <div style="text-align: center; margin-top:10px;">
               <img src="images/gcash_qr.jpg" alt="GCash QR Code" style="max-width:450px; border:1px solid #ccc; border-radius:8px;">
            </div><br>
            <label style="font-size: 18px;">GCash Reference Number:</label>
            <input type="text" name="gcash_ref" id="gcash-ref" class="box">
         </div>

         <h3>Expected Delivery Date</h3>
         <input type="date" name="expected_delivery_date" class="box" min="<?= date('Y-m-d', strtotime('+1 day')); ?>" 
                style="font-size: 1.8rem; padding: 1rem;">
         <p style="font-size: 1.4rem; color: #666; margin-top: 5px;">Select your preferred delivery date (minimum 1 day from today)</p>

         <h3 style="text-align:center;">Do you already have a design?</h3>
<div style="display: flex; justify-content: center; align-items: center; gap: 40px; margin: 15px 0;">
   <label style="font-size: 2rem;">
      <input type="radio" name="design_option" id="has_design" value="yes" style="transform: scale(1.5); margin-right: 10px;" onclick="document.getElementById('upload-design').style.display='block'">
      Yes
   </label>

   <label style="font-size: 2rem;">
      <input type="radio" name="design_option" id="no_design" value="no" style="transform: scale(1.5); margin-right: 10px;" onclick="document.getElementById('upload-design').style.display='none'">
      No
   </label>
</div>


         <div id="upload-design" style="display: none; text-align: center; margin-top: 20px;">
   <label for="design_file" style="display: block; font-size: 2rem; margin-bottom: 15px;">
      Upload Your Design
   </label>
   <input type="file" name="design_file" id="design_file" accept="image/jpg, image/jpeg, image/png, image/webp, application/pdf" 
      style="padding: 15px; font-size: 2rem; border: 2px solid #333; border-radius: 8px; width: 80%; max-width: 400px;">
   <p style="font-size: 1.4rem; color: #666; margin-top: 10px;">Accepted formats: JPG, JPEG, PNG, WEBP, PDF (Max size: 5MB)</p>
</div>


         <input type="submit" value="Place Order" class="btn <?= empty($fetch_profile['address']) ? 'disabled' : '' ?>" style="width:100%; background:var(--red); color:var(--white);" name="submit">
      </div>
   </form>
</section>

<?php include 'components/footer.php'; ?>

<!-- ✅ JavaScript section -->
<script>
   const navbar = document.querySelector(".header .flex .navbar");
   const profile = document.querySelector(".header .flex .profile");

   document.querySelector("#menu-btn").onclick = () => {
      navbar.classList.toggle("active");
      profile.classList.remove("active");
   };

   document.querySelector("#user-btn").onclick = () => {
      profile.classList.toggle("active");
      navbar.classList.remove("active");
   };

   window.onscroll = () => {
      navbar.classList.remove("active");
      profile.classList.remove("active");
   };

   function loader() {
      document.querySelector(".loader").style.display = "none";
   }

   function fadeOut() {
      setInterval(loader, 2000);
   }

   window.onload = fadeOut;

   document.querySelectorAll('input[type="number"]').forEach((numberInput) => {
      numberInput.oninput = () => {
         if (numberInput.value.length > numberInput.maxLength)
            numberInput.value = numberInput.value.slice(0, numberInput.maxLength);
      };
   });

   function toggleGcashDetails() {
      const method = document.getElementById('payment-method').value;
      const gcashDetails = document.getElementById('gcash-details');
      const gcashRef = document.getElementById('gcash-ref');

      if (method === 'gcash') {
         gcashDetails.style.display = 'block';
         gcashRef.setAttribute('required', 'required');
      } else {
         gcashDetails.style.display = 'none';
         gcashRef.removeAttribute('required');
      }
   }
</script>

</body>
</html>
