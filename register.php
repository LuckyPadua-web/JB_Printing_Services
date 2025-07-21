<?php

include 'components/connect.php';

session_start();

if (isset($_SESSION['user_id'])) {
   $user_id = $_SESSION['user_id'];
} else {
   $user_id = '';
}

if (isset($_POST['submit'])) {

   $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
   $email = filter_var($_POST['email'], FILTER_SANITIZE_STRING);
   $number = filter_var($_POST['number'], FILTER_SANITIZE_STRING);
   $pass = filter_var(sha1($_POST['pass']), FILTER_SANITIZE_STRING);
   $cpass = filter_var(sha1($_POST['cpass']), FILTER_SANITIZE_STRING);

   // Security questions
   $sec_q1 = filter_var($_POST['security_question_1'], FILTER_SANITIZE_STRING);
   $sec_a1 = filter_var($_POST['security_answer_1'], FILTER_SANITIZE_STRING);
   $sec_q2 = filter_var($_POST['security_question_2'], FILTER_SANITIZE_STRING);
   $sec_a2 = filter_var($_POST['security_answer_2'], FILTER_SANITIZE_STRING);
   $sec_q3 = filter_var($_POST['security_question_3'], FILTER_SANITIZE_STRING);
   $sec_a3 = filter_var($_POST['security_answer_3'], FILTER_SANITIZE_STRING);

   // File upload
   $valid_id = $_FILES['valid_id']['name'];
   $valid_id_tmp = $_FILES['valid_id']['tmp_name'];
   $valid_id_renamed = uniqid() . '_' . basename($valid_id);
   $valid_id_folder = 'uploaded_ids/' . $valid_id_renamed;
   move_uploaded_file($valid_id_tmp, $valid_id_folder);

   // Check if email or number already exists
   $select_user = $conn->prepare("SELECT * FROM `users` WHERE email = ? OR number = ?");
   $select_user->execute([$email, $number]);

   if ($select_user->rowCount() > 0) {
      $message[] = 'Email or number already exists!';
   } else {
      if ($pass != $cpass) {
         $message[] = 'Confirm password not matched!';
      } else {
         $insert_user = $conn->prepare("INSERT INTO `users` (name, email, number, password, valid_id, security_question_1, security_answer_1, security_question_2, security_answer_2, security_question_3, security_answer_3) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
         $insert_user->execute([$name, $email, $number, $cpass, $valid_id_folder, $sec_q1, $sec_a1, $sec_q2, $sec_a2, $sec_q3, $sec_a3]);

         $select_user = $conn->prepare("SELECT * FROM `users` WHERE email = ? AND password = ?");
         $select_user->execute([$email, $pass]);
         $row = $select_user->fetch(PDO::FETCH_ASSOC);

         if ($select_user->rowCount() > 0) {
            $_SESSION['user_id'] = $row['id'];
            $message[] = 'Registration successful! You can now log in.';
            //header('location:register.php');
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

   <!-- font awesome cdn link -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link -->
   <link rel="stylesheet" href="css/style.css">

</head>

<body>

<?php include 'components/user_header.php'; ?>

<section class="form-container">
   <form action="" method="post" enctype="multipart/form-data">
      <h3>Register Now</h3>

      <input type="text" name="name" required placeholder="Enter your Full Name" class="box" maxlength="50">
      <input type="email" name="email" required placeholder="Enter your Email" class="box" maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="number" name="number" required placeholder="Enter your Number" class="box" maxlength="11">
      <input type="password" name="pass" required placeholder="Enter your Password" class="box" maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="password" name="cpass" required placeholder="Confirm your Password" class="box" maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')">

      <label style="font-size: 18px; font-weight: bold;">Upload Valid ID (Image or PDF)</label>
      <input type="file" name="valid_id" accept=".jpg,.jpeg,.png,.pdf" class="box" required>

      <label style="font-size: 18px; font-weight: bold;">Security Questions</label>

      <!-- Question 1 -->
      <select name="security_question_1" class="box" required>
         <option value="">Select Question 1</option>
         <option value="What is your favorite movie?">What is your favorite movie?</option>
         <option value="What is your mother's maiden name?">What is your mother's maiden name?</option>
         <option value="What was your childhood nickname?">What was your childhood nickname?</option>
      </select>
      <input type="text" name="security_answer_1" placeholder="Answer for Question 1" class="box" required>

      <!-- Question 2 -->
      <select name="security_question_2" class="box" required>
         <option value="">Select Question 2</option>
         <option value="What is your dream job?">What is your dream job?</option>
         <option value="What city were you born in?">What city were you born in?</option>
         <option value="What is your favorite food?">What is your favorite food?</option>
      </select>
      <input type="text" name="security_answer_2" placeholder="Answer for Question 2" class="box" required>

      <!-- Question 3 -->
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

<!-- custom js file -->
<script src="js/script.js"></script>

</body>
</html>
