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

if (isset($_POST['submit'])) {
   $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
   $number = filter_var($_POST['number'], FILTER_SANITIZE_STRING);
   $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
   $method = filter_var($_POST['method'], FILTER_SANITIZE_STRING);
   $address = filter_var($_POST['address'], FILTER_SANITIZE_STRING);
   $total_products = $_POST['total_products'];
   $total_price = $_POST['total_price'];
   $gcash_ref = !empty($_POST['gcash_ref']) ? filter_var($_POST['gcash_ref'], FILTER_SANITIZE_STRING) : null;

   $check_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
   $check_cart->execute([$user_id]);

   if ($check_cart->rowCount() > 0) {
      if ($address == '') {
         $message[] = 'please add your address!';
      } else {
         $insert_order = $conn->prepare("INSERT INTO `orders` (user_id, name, number, email, method, address, total_products, total_price, gcash_ref) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
         $insert_order->execute([$user_id, $name, $number, $email, $method, $address, $total_products, $total_price, $gcash_ref]);

         $delete_cart = $conn->prepare("DELETE FROM `cart` WHERE user_id = ?");
         $delete_cart->execute([$user_id]);

         $message[] = 'Order placed successfully!';
      }
   } else {
      $message[] = 'Your cart is empty';
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
         <h3>Cart Items</h3>
         <?php
         $grand_total = 0;
         $cart_items = [];
         $total_products = '';
         $select_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
         $select_cart->execute([$user_id]);
         if ($select_cart->rowCount() > 0) {
            while ($fetch_cart = $select_cart->fetch(PDO::FETCH_ASSOC)) {
               $cart_items[] = $fetch_cart['name'] . ' (' . $fetch_cart['price'] . ' x ' . $fetch_cart['quantity'] . ')';
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
         ?>
         <p class="grand-total"><span class="name">Grand total :</span>
            <span class="price">&#8369;<?= $grand_total; ?></span></p>
         <a href="cart.php" class="btn">View Cart</a>
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
   <input type="file" name="design_file" id="design_file" accept="image/*,application/pdf" 
      style="padding: 15px; font-size: 2rem; border: 2px solid #333; border-radius: 8px; width: 80%; max-width: 400px;">
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
