<?php
error_reporting(0);
include '../components/connect.php';

session_start();

if (!isset($_SESSION["login_attempts"])) {
   $_SESSION["login_attempts"] = 0;
}

if (isset($_SESSION["locked"])) {
   $difference = time() - $_SESSION["locked"];
   if ($difference > 60) {
      unset($_SESSION["locked"]);
      unset($_SESSION["login_attempts"]);
   }
}

if (isset($_POST['submit'])) {
   $name = $_POST['name'];
   $name = filter_var($name, FILTER_SANITIZE_STRING);
   $pass = sha1($_POST['pass']);
   $pass = filter_var($pass, FILTER_SANITIZE_STRING);

   $select_admin = $conn->prepare("SELECT * FROM `admin` WHERE name = ? AND password = ?");
   $select_admin->execute([$name, $pass]);

   if ($select_admin->rowCount() > 0) {
      $fetch_admin_id = $select_admin->fetch(PDO::FETCH_ASSOC);
      $_SESSION['admin_id'] = $fetch_admin_id['id'];
      $_SESSION["login_attempts"] = 0; // reset on success
      unset($_SESSION["locked"]);
      header('location:dashboard.php');
   } else {
      $_SESSION["login_attempts"] += 1;
      if ($_SESSION["login_attempts"] >= 3 && !isset($_SESSION["locked"])) {
         $_SESSION["locked"] = time();
      }
      $message[] = 'Incorrect username or password!';
   }
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
   <link rel="stylesheet" href="../css/admin_style.css">
</head>
<body>

<?php
if (isset($message)) {
   foreach ($message as $msg) {
      echo '
      <div class="message">
         <span>' . $msg . '</span>
         <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
      </div>
      ';
   }
}
?>

<section class="form-container">

   <form action="" method="POST">
      <h3>Administrator Login</h3>
      
      <input type="text" name="name" maxlength="20" required placeholder="Enter your username" class="box" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="password" id="myInput" name="pass" maxlength="20" required placeholder="Enter your password" class="box" oninput="this.value = this.value.replace(/\s/g, '')">
      
      <p><input type="checkbox" onclick="myFunction()"> Show password</p>
      <script>
         function myFunction() {
            var x = document.getElementById("myInput");
            x.type = x.type === "password" ? "text" : "password";
         }
      </script>

      <div>
         <?php
         if (isset($_SESSION["locked"])) {
            $remaining = 60 - (time() - $_SESSION["locked"]);
            if ($remaining > 0) {
               echo "<p>Too many failed login attempts. Please wait <span id='countdown'>{$remaining}</span> seconds.</p>";
            } else {
               unset($_SESSION["locked"]);
               unset($_SESSION["login_attempts"]);
               echo '<input type="submit" value="LOGIN" name="submit" class="btn">';
            }
         } else {
            echo '<input type="submit" value="LOGIN" name="submit" class="btn">';
         }
         ?>
      </div>
   </form>

</section>

<script>
   let countdownElem = document.getElementById("countdown");
   if (countdownElem) {
      let timeLeft = parseInt(countdownElem.innerText);

      const interval = setInterval(() => {
         timeLeft--;
         countdownElem.innerText = timeLeft;
         if (timeLeft <= 0) {
            clearInterval(interval);
            location.reload(); // Refresh the page to re-enable the login form
         }
      }, 1000);
   }
</script>

</body>
</html>
