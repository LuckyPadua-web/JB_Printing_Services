<?php

include '../components/connect.php';

session_start();



if(isset($_SESSION['admin_id'])){
   $admin_id = $_SESSION['admin_id'];
}else{
   $admin_id = '';
   header('location:admin_login.php');
};


?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>dashboard</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="../css/admin_style.css">

</head>
<body>

<?php include '../components/admin_header.php' ?>


<!-- admin dashboard walkthrough starts  -->

<style>
.modern-dashboard {
   background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
   min-height: 90vh;
   padding: 2rem;
   font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.modern-heading {
   font-size: 3.5rem;
   font-weight: 700;
   color: #ffffff;
   text-align: center;
   margin-bottom: 3rem;
   text-shadow: 0 4px 15px rgba(0,0,0,0.2);
   letter-spacing: 1px;
}

.modern-container {
   display: grid;
   grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
   gap: 2rem;
   max-width: 1200px;
   margin: 0 auto;
}

.welcome-card {
   background: linear-gradient(145deg, #ffffff, #f8f9fa);
   border-radius: 25px;
   padding: 2.5rem;
   box-shadow: 0 20px 40px rgba(0,0,0,0.1), 0 0 0 1px rgba(255,255,255,0.1);
   grid-column: 1 / -1;
   text-align: center;
   backdrop-filter: blur(10px);
}

.feature-card {
   background: linear-gradient(145deg, #ffffff, #f1f3f4);
   border-radius: 20px;
   padding: 2rem;
   box-shadow: 0 15px 35px rgba(0,0,0,0.08);
   transition: all 0.3s ease;
   border: 1px solid rgba(255,255,255,0.2);
   position: relative;
   overflow: hidden;
}

.feature-card::before {
   content: '';
   position: absolute;
   top: 0;
   left: 0;
   right: 0;
   height: 4px;
   background: linear-gradient(90deg, #667eea, #764ba2);
}

.feature-card:hover {
   transform: translateY(-8px);
   box-shadow: 0 25px 50px rgba(0,0,0,0.15);
}

.feature-icon {
   font-size: 3rem;
   margin-bottom: 1rem;
   display: block;
}

.feature-title {
   font-size: 1.4rem;
   font-weight: 600;
   margin-bottom: 0.8rem;
   color: #2c3e50;
}

.feature-description {
   color: #5a6c7d;
   line-height: 1.6;
   font-size: 1rem;
}

.welcome-title {
   font-size: 2.5rem;
   color: #2c3e50;
   margin-bottom: 1rem;
   font-weight: 600;
}

.welcome-subtitle {
   font-size: 1.2rem;
   color: #5a6c7d;
   margin-bottom: 2rem;
   line-height: 1.6;
}

.brand-text {
   background: linear-gradient(135deg, #667eea, #764ba2);
   -webkit-background-clip: text;
   -webkit-text-fill-color: transparent;
   font-weight: bold;
}

.help-section {
   background: linear-gradient(145deg, #4f46e5, #6366f1);
   color: white;
   border-radius: 15px;
   padding: 1.5rem;
   margin-top: 2rem;
   text-align: center;
}

@media (max-width: 768px) {
   .modern-heading {
      font-size: 2.5rem;
   }
   .modern-container {
      grid-template-columns: 1fr;
      gap: 1.5rem;
   }
}
</style>

<section class="modern-dashboard">
   <h1 class="modern-heading">
      <i class="fa-solid fa-shield-halved"></i> Admin Control Center
   </h1>
   
   <div class="modern-container">
      <!-- Welcome Card -->
      <div class="welcome-card">
         <h2 class="welcome-title">
            <i class="fa-solid fa-rocket" style="color:#667eea; margin-right:15px;"></i>
            Welcome to the Future!
         </h2>
         <p class="welcome-subtitle">
            Experience the next generation of <span class="brand-text">JB Printing Services</span> admin management. 
            Your command center for seamless business operations.
         </p>
         
         <div class="help-section">
            <i class="fa-solid fa-lightbulb" style="margin-right:10px;"></i>
            Navigate through the sections using the menu above. Need assistance? Contact your system administrator.
         </div>
      </div>

      <!-- Feature Cards -->
      <div class="feature-card">
         <i class="fa-solid fa-cube feature-icon" style="color:#ff6b6b;"></i>
         <h3 class="feature-title">Product Management</h3>
         <p class="feature-description">
            Effortlessly add, edit, and organize your product catalog with our intuitive interface.
         </p>
      </div>

      <div class="feature-card">
         <i class="fa-solid fa-shopping-cart feature-icon" style="color:#4ecdc4;"></i>
         <h3 class="feature-title">Order Processing</h3>
         <p class="feature-description">
            Track, manage, and fulfill customer orders with real-time status updates and payment monitoring.
         </p>
      </div>

      <div class="feature-card">
         <i class="fa-solid fa-users-gear feature-icon" style="color:#45b7d1;"></i>
         <h3 class="feature-title">User Administration</h3>
         <p class="feature-description">
            Comprehensive user management tools to handle customer accounts and permissions.
         </p>
      </div>

      <div class="feature-card">
         <i class="fa-solid fa-comments feature-icon" style="color:#f39c12;"></i>
         <h3 class="feature-title">Communication Hub</h3>
         <p class="feature-description">
            Stay connected with customers through our integrated messaging system.
         </p>
      </div>

      <div class="feature-card">
         <i class="fa-solid fa-chart-pie feature-icon" style="color:#e74c3c;"></i>
         <h3 class="feature-title">Analytics & Reports</h3>
         <p class="feature-description">
            Generate insightful reports and track your business performance with advanced analytics.
         </p>
      </div>

      <div class="feature-card">
         <i class="fa-solid fa-cog feature-icon" style="color:#9b59b6;"></i>
         <h3 class="feature-title">Profile Settings</h3>
         <p class="feature-description">
            Customize your admin profile, security settings, and personal preferences.
         </p>
      </div>
   </div>
</section>

<!-- admin dashboard walkthrough ends -->









<!-- custom js file link  -->
<script src="../js/admin_script.js"></script>

</body>
</html>