<?php

include 'components/connect.php';

session_start();
if (!isset($_SESSION['login_attempts'])) {
   $_SESSION['login_attempts'] = 0;
}

if (!isset($_SESSION['lockout_time'])) {
   $_SESSION['lockout_time'] = null;
}

// Check if locked
$is_locked = false;
if ($_SESSION['login_attempts'] >= 3) {
   $lockout_time = $_SESSION['lockout_time'];
   if ($lockout_time && time() < $lockout_time + 30) {
      $is_locked = true;
   } else {
      // Reset after 30 seconds
      $_SESSION['login_attempts'] = 0;
      $_SESSION['lockout_time'] = null;
   }
}

if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
}else{
   $user_id = '';
};

if(isset($_POST['submit'])){

   if (!$is_locked) {
      $email = $_POST['email'];
      $email = filter_var($email, FILTER_SANITIZE_STRING);
      $pass = sha1($_POST['pass']);
      $pass = filter_var($pass, FILTER_SANITIZE_STRING);

      $select_user = $conn->prepare("SELECT * FROM `users` WHERE email = ? AND password = ?");
      $select_user->execute([$email, $pass]);
      $row = $select_user->fetch(PDO::FETCH_ASSOC);

      if($select_user->rowCount() > 0){
         $_SESSION['user_id'] = $row['id'];
         $_SESSION['login_attempts'] = 0; // reset attempts on success
         $_SESSION['lockout_time'] = null;
         header('location:index.php');
      } else {
         $_SESSION['login_attempts'] += 1;
         if ($_SESSION['login_attempts'] >= 3) {
            $_SESSION['lockout_time'] = time();
         }
         $message[] = 'Incorrect username or password!';
      }

   } else {
      $remaining = ($_SESSION['lockout_time'] + 30) - time();
      $message[] = "Too many failed attempts. Please wait <span id='countdown'>$remaining</span> seconds.";
   }
}

if ($_SERVER["REQUEST_METHOD"] == "post") 
{
   $email = $_POST["email"];
   $pass = $_POST["pass"];

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Login</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<!-- header section starts  -->
<?php include 'components/user_header.php'; ?>
<!-- header section ends -->

<section class="form-container">

   <form action="" method="post">
      <h3>Login now</h3>
      <input type="email" name="email" required placeholder="Enter your email" class="box" maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="password" id="myInput" name="pass" required placeholder="Enter your password" class="box" maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')">
      <!--Show password start-->
      
      <p><input type="checkbox" onclick="myFunction()">   Show password</p>
      <script>
         function myFunction() {
         var x = document.getElementById("myInput");
         if (x.type === "password") {
            x.type = "text";
         } else {
            x.type = "password";
         }
         }
      </script>
      <!--Show password end-->
      <br>
     <input type="submit" value="login now" name="submit" class="btn" id="loginBtn" <?php if($is_locked) echo 'disabled'; ?>>

      <p>Don't have an account? <a href="register.php">Register now</a></p>
   </form>

</section>

<?php include 'components/footer.php'; ?>

<!-- custom js file link  -->
<script src="js/script.js"></script>
<?php if($is_locked): ?>
<script>
   let countdown = <?php echo ($_SESSION['lockout_time'] + 30) - time(); ?>;
   const btn = document.getElementById('loginBtn');

   const interval = setInterval(() => {
      if (countdown <= 0) {
         clearInterval(interval);
         btn.disabled = false;
         document.getElementById('countdown').textContent = '';
         location.reload(); // Optional: reload to re-enable PHP state
      } else {
         document.getElementById('countdown').textContent = countdown;
         countdown--;
      }
   }, 1000);
</script>
<?php endif; ?>

</body>
</html>