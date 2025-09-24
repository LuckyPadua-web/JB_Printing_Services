<?php
include 'components/connect.php';
session_start();
if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
}else{
   $user_id = '';
};
?>
<!--Sample Change Codessss-->
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>About Us | JB Printing Services</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php include 'components/user_header.php'; ?>

<section class="about">
  <div class="row">
    <div class="image">
      <img src="images/JB/Logo-A.png" alt="JB Printing Services Logo">
    </div>
    <div class="content">
      <h3>About JB Printing Services</h3>
      <p>JB Printing Services is your trusted partner for high-quality printing solutions in Piat, Cagayan. We offer a wide range of products, from business cards and flyers to custom designs and large format prints. Our commitment is to deliver excellent service, fast turnaround, and affordable prices for individuals and businesses alike.</p>
      <p>Founded with a passion for creativity and customer satisfaction, we use the latest technology and materials to ensure every project meets your expectations. Whether you need promotional materials, personalized gifts, or professional prints, JB Printing Services is here to help you make a lasting impression.</p>
    </div>
  </div>
</section>

<section class="steps">
  <div class="box-container">
    <div class="box">
      <img src="images/step-1.png" alt="Step 1">
      <h3>Choose Your Product</h3>
      <p>Browse our wide selection of printing products and find the perfect fit for your needs.</p>
    </div>
    <div class="box">
      <img src="images/step-2.png" alt="Step 2">
      <h3>Upload Your Design</h3>
      <p>Send us your artwork or let our team help you create a custom design that stands out.</p>
    </div>
    <div class="box">
      <img src="images/contact-img.svg" alt="Step 3">
      <h3>Fast Delivery</h3>
      <p>Enjoy quick turnaround and reliable delivery to your doorstep or pick up at our shop.</p>
    </div>
  </div>
</section>


<section class="developers">
  <h2 style="text-align:center; font-size:2.5rem; margin-bottom:2rem; color:var(--black);">Meet the Developers</h2>
  <div class="dev-row" style="display:flex; flex-wrap:wrap; gap:2rem; justify-content:center;">
    <div class="dev-card" style="background:#fff; border:var(--border); border-radius:1rem; box-shadow:0 2px 8px rgba(0,0,0,0.07); padding:2rem; max-width:350px; flex:1 1 300px; text-align:center;">
      <img src="images/ATTY.png" alt="Developer 1" style="width:100px; height:100px; border-radius:50%; object-fit:cover; margin-bottom:1rem;">
      <h3 style="font-size:1.8rem; color:var(--black); margin-bottom:0.5rem;">Lucky Keith B. Padua</h3>
      <p style="color:var(--light-color); font-size:1.2rem; margin-bottom:1rem;">Pindot Pindot Lang.</p>
      <div style="display:flex; justify-content:center; gap:1rem;">
        <a href="#" style="color:#4267B2;"><i class="fab fa-facebook" style="font-size:2.2rem;"></i></a>
        <a href="#" style="color:#333;"><i class="fab fa-github" style="font-size:2.2rem;"></i></a>
        <a href="#" style="color:#0077B5;"><i class="fab fa-linkedin" style="font-size:2.2rem;"></i></a>
      </div>
    </div>
    <div class="dev-card" style="background:#fff; border:var(--border); border-radius:1rem; box-shadow:0 2px 8px rgba(0,0,0,0.07); padding:2rem; max-width:350px; flex:1 1 300px; text-align:center;">
      <img src="images/user-icon.png" alt="Developer 2" style="width:100px; height:100px; border-radius:50%; object-fit:cover; margin-bottom:1rem;">
      <h3 style="font-size:1.8rem; color:var(--black); margin-bottom:0.5rem;">Joren L. Duerme</h3>
      <p style="color:var(--light-color); font-size:1.2rem; margin-bottom:1rem;">Role or short bio goes here. You can update this later with details.</p>
      <div style="display:flex; justify-content:center; gap:1rem;">
        <a href="#" style="color:#4267B2;"><i class="fab fa-facebook" style="font-size:2.2rem;"></i></a>
        <a href="#" style="color:#333;"><i class="fab fa-github" style="font-size:2.2rem;"></i></a>
        <a href="#" style="color:#0077B5;"><i class="fab fa-linkedin" style="font-size:2.2rem;"></i></a>
      </div>
    </div>
  </div>
</section>

<?php include 'components/footer.php'; ?>
<!--Comment-->

<script src="js/script.js"></script>
</body>
</html>
