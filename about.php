<?php
include 'components/connect.php';
session_start();
if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
}else{
   $user_id = '';
};
?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>About Us | JB Printing Services</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="css/style.css">
   <style>
      /* Popup Modal Styles */
      .modal {
         display: none;
         position: fixed;
         z-index: 1000;
         left: 0;
         top: 0;
         width: 100%;
         height: 100%;
         overflow: auto;
         background-color: rgba(0,0,0,0.6);
      }
      .modal-content {
         background-color: #fff;
         margin: 10% auto;
         padding: 2rem;
         border-radius: 1rem;
         width: 90%;
         max-width: 500px;
         text-align: center;
         box-shadow: 0 4px 12px rgba(0,0,0,0.2);
         position: relative;
      }
      .modal-content img {
         width: 120px;
         height: 120px;
         border-radius: 50%;
         object-fit: cover;
         margin-bottom: 1rem;
      }
      .close {
         position: absolute;
         top: 10px;
         right: 20px;
         color: #aaa;
         font-size: 28px;
         font-weight: bold;
         cursor: pointer;
      }
      .close:hover {
         color: #000;
      }
   </style>
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
    
    <!-- Developer 1 -->
    <div class="dev-card" onclick="openModal('modal1')" style="cursor:pointer; background:#fff; border:var(--border); border-radius:1rem; box-shadow:0 2px 8px rgba(0,0,0,0.07); padding:2rem; max-width:350px; flex:1 1 300px; text-align:center;">
      <img src="images/ATTY.png" alt="Developer 1" style="width:100px; height:100px; border-radius:50%; object-fit:cover; margin-bottom:1rem;">
      <h3 style="font-size:1.8rem; color:var(--black); margin-bottom:0.5rem;">Lucky Keith B. Padua</h3>
      <p style="color:var(--light-color); font-size:1.2rem;">Lead Developer</p>
    </div>

    <!-- Developer 2 -->
    <div class="dev-card" onclick="openModal('modal2')" style="cursor:pointer; background:#fff; border:var(--border); border-radius:1rem; box-shadow:0 2px 8px rgba(0,0,0,0.07); padding:2rem; max-width:350px; flex:1 1 300px; text-align:center;">
      <img src="images/joren.png" alt="Developer 2" style="width:100px; height:100px; border-radius:50%; object-fit:cover; margin-bottom:1rem;">
      <h3 style="font-size:1.8rem; color:var(--black); margin-bottom:0.5rem;">Joren L. Duerme</h3>
      <p style="color:var(--light-color); font-size:1.2rem;">Co-Developer</p>
    </div>

  </div>
</section>

<!-- Modal for Developer 1 -->
<div id="modal1" class="modal">
  <div class="modal-content">
    <span class="close" onclick="closeModal('modal1')">&times;</span>
    <img src="images/ATTY.png" alt="Lucky Keith B. Padua">
    <h2 style="font-size:2.3rem; color:var(--black); margin-bottom:0.5rem;">Lucky Keith B. Padua</h2>
  <p class="dev-info" style="font-size:1.5rem; color:var(--dark-color); margin:1rem 0;"><strong>Age:</strong> 22</p>
  <p class="dev-info" style="font-size:1.5rem; color:var(--dark-color); margin:1rem 0;"><strong>Gender:</strong> Male</p>
  <p class="dev-info" style="font-size:1.5rem; color:var(--dark-color); margin:1rem 0;"><strong>Citizenship:</strong> Filipino</p>
  <p class="dev-info" style="font-size:1.5rem; color:var(--dark-color); margin:1rem 0;"><strong>Civil Status:</strong> Single</p>
  <p class="dev-info" style="font-size:1.5rem; color:var(--dark-color); margin:1rem 0;"><strong>Address:</strong> Centro Sur, Sto.Nino, Cagayan</p>
  <p class="dev-info" style="font-size:1.5rem; color:var(--dark-color); margin:1rem 0;"><strong>Email:</strong> luckybaltazar21@gmail.com</p>
  <p class="dev-info" style="font-size:1.5rem; color:var(--dark-color); margin:1rem 0;"><strong>Contact No.:</strong> 0905 935 3906</p>
    <div style="margin-top:1rem;">
      <a href="https://web.facebook.com/Luckybaltazar21" target="_blank" style="color:#4267B2; margin:0 1rem;"><i class="fab fa-facebook fa-2x"></i></a>
      <a href="https://github.com/LuckyPadua-web" target="_blank" style="color:#333; margin:0 1rem;"><i class="fab fa-github fa-2x"></i></a>
      <a href="https://linkedin.com/" target="_blank" style="color:#0077B5; margin:0 1rem;"><i class="fab fa-linkedin fa-2x"></i></a>
    </div>
  </div>
</div>

<!-- Modal for Developer 2 -->
<div id="modal2" class="modal">
  <div class="modal-content">
    <span class="close" onclick="closeModal('modal2')">&times;</span>
    <img src="images/joren.png" alt="Joren L. Duerme">
    <h2 style="font-size:2.3rem; color:var(--black); margin-bottom:0.5rem;">Joren L. Duerme</h2>
  <p class="dev-info" style="font-size:1.5rem; color:var(--dark-color); margin:1rem 0;"><strong>Age:</strong> 23</p>
  <p class="dev-info" style="font-size:1.5rem; color:var(--dark-color); margin:1rem 0;"><strong>Gender:</strong> Male</p>
  <p class="dev-info" style="font-size:1.5rem; color:var(--dark-color); margin:1rem 0;"><strong>Citizenship:</strong> Filipino</p>
  <p class="dev-info" style="font-size:1.5rem; color:var(--dark-color); margin:1rem 0;"><strong>Civil Status:</strong> Single</p>
  <p class="dev-info" style="font-size:1.5rem; color:var(--dark-color); margin:1rem 0;"><strong>Address:</strong> Virginia, Sto.Nino, Cagayan</p>
  <p class="dev-info" style="font-size:1.5rem; color:var(--dark-color); margin:1rem 0;"><strong>Email:</strong> duermejoren@gmail.com</p>
  <p class="dev-info" style="font-size:1.5rem; color:var(--dark-color); margin:1rem 0;"><strong>Contact No.:</strong> 0905 175 1081</p>
    <div style="margin-top:1rem;">
      <a href="https://web.facebook.com/joren.duerme.14" target="_blank" style="color:#4267B2; margin:0 1rem;"><i class="fab fa-facebook fa-2x"></i></a>
      <a style="color:#333; margin:0 1rem; cursor:default;">
  <i class="fab fa-github fa-2x"></i>
</a>
 <a href="https://linkedin.com/" target="_blank" style="color:#0077B5; margin:0 1rem;"><i class="fab fa-linkedin fa-2x"></i></a>

    </div>
  </div>
</div>

<?php include 'components/footer.php'; ?>

<script>
  function openModal(id) {
    document.getElementById(id).style.display = 'block';
  }
  function closeModal(id) {
    document.getElementById(id).style.display = 'none';
  }
  window.onclick = function(event) {
    let modals = document.querySelectorAll('.modal');
    modals.forEach(modal => {
      if (event.target == modal) {
        modal.style.display = "none";
      }
    });
  }
</script>

<script src="js/script.js"></script>
</body>
</html>
