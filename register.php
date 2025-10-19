<?php
include 'components/connect.php';
session_start();

$message = [];

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

if (isset($_SESSION['user_id'])) {
   $user_id = $_SESSION['user_id'];
} else {
   $user_id = '';
}

if (isset($_POST['submit'])) {

   $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
   $email = filter_var($_POST['email'], FILTER_SANITIZE_STRING);
   $number = filter_var($_POST['number'], FILTER_SANITIZE_STRING);
   $address = filter_var($_POST['address'], FILTER_SANITIZE_STRING);
   $pass = $_POST['pass'];
   $cpass = $_POST['cpass'];

   $sec_q1 = filter_var($_POST['security_question_1'], FILTER_SANITIZE_STRING);
   $sec_a1 = filter_var($_POST['security_answer_1'], FILTER_SANITIZE_STRING);
   $sec_q2 = filter_var($_POST['security_question_2'], FILTER_SANITIZE_STRING);
   $sec_a2 = filter_var($_POST['security_answer_2'], FILTER_SANITIZE_STRING);
   $sec_q3 = filter_var($_POST['security_question_3'], FILTER_SANITIZE_STRING);
   $sec_a3 = filter_var($_POST['security_answer_3'], FILTER_SANITIZE_STRING);

   $valid_id = $_FILES['valid_id']['name'];
   $valid_id_tmp = $_FILES['valid_id']['tmp_name'];
   $valid_id_renamed = uniqid() . '_' . basename($valid_id);
   $valid_id_folder = 'uploaded_ids/' . $valid_id_renamed;
   move_uploaded_file($valid_id_tmp, $valid_id_folder);

   // ✅ Gmail only check
   if (!preg_match("/^[a-zA-Z0-9._%+-]+@gmail\.com$/", $email)) {
      $message[] = 'Only Gmail addresses are allowed!';
   }
   // ✅ Digits only for number
   elseif (!preg_match("/^[0-9]+$/", $number)) {
      $message[] = 'Phone number must contain digits only!';
   }
   // ✅ Length check for PH number (11 digits)
   elseif (strlen($number) != 11) {
      $message[] = 'Phone number must be exactly 11 digits!';
   }
   // ✅ Password match
   elseif ($pass !== $cpass) {
      $message[] = 'Passwords do not match!';
   } else {
      // ✅ Check if email/number exists
      $select_user = $conn->prepare("SELECT * FROM `users` WHERE email = ? OR number = ?");
      $select_user->execute([$email, $number]);

      if ($select_user->rowCount() > 0) {
         $message[] = 'Email or number already exists!';
      } else {
         // ✅ Secure password hashing - KEEP THIS AS password_hash
         $hashed_pass = password_hash($pass, PASSWORD_DEFAULT);

         $insert_user = $conn->prepare("INSERT INTO `users`
            (name, email, number, address, password, valid_id, 
             security_question_1, security_answer_1, 
             security_question_2, security_answer_2, 
             security_question_3, security_answer_3) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

         $insert_user->execute([
            $name, $email, $number, $address, $hashed_pass, $valid_id_folder,
            $sec_q1, $sec_a1, $sec_q2, $sec_a2, $sec_q3, $sec_a3
         ]);

         // ✅ Send Gmail Notification
         $mail = new PHPMailer(true);

         try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'luckybaltazar21@gmail.com'; // 🔴 Replace with your Gmail
            $mail->Password   = 'hkrv uzzx saik zlpm';   // 🔴 Replace with Gmail App Password
            $mail->SMTPSecure = 'tls';
            $mail->Port       = 587;

            $mail->setFrom('yourgmail@gmail.com', 'JB Printing Services');
            $mail->addAddress($email, $name);

            $mail->isHTML(true);
            $mail->Subject = "Account Approval - JB Printing Services";
            $mail->Body    = "
               <h3>Hi $name,</h3>
               <p>Your account has been successfully registered and approved.</p>
               <p>You can now login by clicking the link below:</p>
               <a href='http://localhost/JB_Printing_Services/login.php' 
                  style='display:inline-block; background:#28a745; color:#fff; padding:10px 20px; text-decoration:none; border-radius:5px;'>
                  Login Now
               </a>
               <br><br>
               <p>Thank you,<br>JB Printing Services</p>
            ";

            $mail->send();
            $message[] = 'Registration successful! Please check your Gmail inbox for confirmation.';

         } catch (Exception $e) {
            $message[] = "Registration complete, but email could not be sent. Error: {$mail->ErrorInfo}";
         }
      }
   }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Register</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="css/style.css">

   <style>
      .password-field { position: relative; }
      .password-field .toggle-eye {
         position: absolute;
         right: 15px;
         top: 50%;
         transform: translateY(-50%);
         cursor: pointer;
         color: #555;
      }
      .message {
         background: #f8d7da;
         color: #721c24;
         padding: 10px;
         border-radius: 5px;
         margin-bottom: 10px;
      }
   </style>
</head>

<body>

<?php include 'components/user_header.php'; ?>

<section class="form-container">
   <form action="" method="post" enctype="multipart/form-data">
      <h3>Register Now</h3>

      <?php
      if (!empty($message) && is_array($message)) {
         foreach ($message as $msg) {
            echo '<div class="message">'.$msg.'</div>';
         }
      }
      ?>

      <input type="text" name="name" required placeholder="Enter your Full Name" class="box" maxlength="50">

      <!-- ✅ Gmail only -->
      <input type="email" name="email" required 
         placeholder="Enter your Email Address" 
         class="box" maxlength="50" 
         pattern="[a-zA-Z0-9._%+-]+@gmail\.com$"
         title="Only Gmail addresses are allowed"
         oninput="this.value = this.value.replace(/\s/g, '')">

      <!-- ✅ Digits only -->
      <input type="text" name="number" required 
         placeholder="Enter your Number" 
         class="box" maxlength="11"
         oninput="this.value = this.value.replace(/[^0-9]/g, '')">

      <input type="text" name="address" required placeholder="Enter your Address" class="box" maxlength="255">

      <div class="password-field">
         <input type="password" id="pass" name="pass" required placeholder="Enter your Password" class="box" maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')">
         <i class="fas fa-eye toggle-eye" onclick="togglePassword('pass', this)"></i>
      </div>

      <div class="password-field">
         <input type="password" id="cpass" name="cpass" required placeholder="Confirm your Password" class="box" maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')">
         <i class="fas fa-eye toggle-eye" onclick="togglePassword('cpass', this)"></i>
      </div>

      <label style="font-size: 16px; font-weight: bold;">Upload Valid ID (Image or PDF)</label>
      <input type="file" name="valid_id" accept=".jpg,.jpeg,.png,.pdf" class="box" required>

      <label style="font-size: 16px; font-weight: bold;">Security Questions</label>

      <select name="security_question_1" class="box" required>
         <option value="">Select Question 1</option>
         <option value="What is your favorite movie?">What is your favorite movie?</option>
         <option value="What is your mother's maiden name?">What is your mother's maiden name?</option>
         <option value="What was your childhood nickname?">What was your childhood nickname?</option>
      </select>
      <input type="text" name="security_answer_1" placeholder="Answer for Question 1" class="box" required>

      <select name="security_question_2" class="box" required>
         <option value="">Select Question 2</option>
         <option value="What is your dream job?">What is your dream job?</option>
         <option value="What city were you born in?">What city were you born in?</option>
         <option value="What is your favorite food?">What is your favorite food?</option>
      </select>
      <input type="text" name="security_answer_2" placeholder="Answer for Question 2" class="box" required>

      <select name="security_question_3" class="box" required>
         <option value="">Select Question 3</option>
         <option value="What is your pet's name?">What is your pet's name?</option>
         <option value="What school did you go to?">What school did you go to?</option>
         <option value="Who is your childhood hero?">Who is your childhood hero?</option>
      </select>
      <input type="text" name="security_answer_3" placeholder="Answer for Question 3" class="box" required>

      <input type="submit" value="Register Now" name="submit" class="btn">
      <p>Already have an account? <a href="login.php">Login now</a></p>
   </form>
</section>

<?php include 'components/footer.php'; ?>

<script src="js/script.js"></script>

<script>
function togglePassword(inputId, icon) {
   const input = document.getElementById(inputId);
   const isHidden = input.type === 'password';
   input.type = isHidden ? 'text' : 'password';
   icon.classList.toggle('fa-eye');
   icon.classList.toggle('fa-eye-slash');
}
</script>

</body>
</html>