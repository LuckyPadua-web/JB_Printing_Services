<?php
include 'components/connect.php';
session_start();
if (isset($_SESSION['user_id'])) {
   $user_id = $_SESSION['user_id'];
} else {
   $user_id = '';
}
$email = '';
$show_questions = false;
$errors = [];
$success = '';

// Step 1: Email submission
if (isset($_POST['check_email'])) {
   $email = filter_var($_POST['email'], FILTER_SANITIZE_STRING);
   $stmt = $conn->prepare("SELECT * FROM `users` WHERE email = ?");
   $stmt->execute([$email]);
   if ($stmt->rowCount() > 0) {
      $user = $stmt->fetch(PDO::FETCH_ASSOC);
      $_SESSION['reset_user_id'] = $user['id'];
      $show_questions = true;
   } else {
      $errors[] = "No account found with that email.";
   }
}

// Step 2: Answer verification and password reset
if (isset($_POST['reset_password'])) {
   $user_id = $_SESSION['reset_user_id'];
   $stmt = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
   $stmt->execute([$user_id]);
   $user = $stmt->fetch(PDO::FETCH_ASSOC);

   $a1 = filter_var($_POST['answer1'], FILTER_SANITIZE_STRING);
   $a2 = filter_var($_POST['answer2'], FILTER_SANITIZE_STRING);
   $a3 = filter_var($_POST['answer3'], FILTER_SANITIZE_STRING);
   $new_pass = filter_var(sha1($_POST['new_pass']), FILTER_SANITIZE_STRING);
   $confirm_pass = filter_var(sha1($_POST['confirm_pass']), FILTER_SANITIZE_STRING);

   if ($a1 !== $user['security_answer_1'] || $a2 !== $user['security_answer_2'] || $a3 !== $user['security_answer_3']) {
      $errors[] = "Security answers did not match.";
      $show_questions = true;
   } elseif ($new_pass !== $confirm_pass) {
      $errors[] = "Passwords do not match.";
      $show_questions = true;
   } else {
      $update = $conn->prepare("UPDATE `users` SET password = ? WHERE id = ?");
      $update->execute([$confirm_pass, $user_id]);
      $success = "Password successfully reset! <a href='login.php'>Login now</a>.";
      unset($_SESSION['reset_user_id']);
   }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <title>Forgot Password</title>
   <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php include 'components/user_header.php'; ?>

<section class="form-container">
   <form action="" method="post">
      <h3>Forgot Password</h3>

      <?php foreach($errors as $e): ?>
         <label style="font-size: 18px; font-weight: bold;"><div style="color: red;"><?= $e ?></div></label>
      <?php endforeach; ?>

      <?php if($success): ?>
         <label style="font-size: 18px; font-weight: bold;"> <div style="color: green;"><?= $success ?></div></label>
      <?php endif; ?>

      <?php if (!$show_questions): ?>
         <input type="email" name="email" placeholder="Enter your email" class="box" required>
         <input type="submit" name="check_email" value="Next" class="btn">
      <?php else: ?>
        <label style="font-size: 18px; font-weight: bold;"><?= $user['security_question_1'] ?></label>
         <input type="text" name="answer1" class="box" required>

       <label style="font-size: 18px; font-weight: bold;"><?= $user['security_question_2'] ?></label>
         <input type="text" name="answer2" class="box" required>

        <label style="font-size: 18px; font-weight: bold;"><?= $user['security_question_3'] ?></label>
         <input type="text" name="answer3" class="box" required>

         <input type="password" name="new_pass" placeholder="Enter new password" class="box" required>
         <input type="password" name="confirm_pass" placeholder="Confirm new password" class="box" required>

         <input type="submit" name="reset_password" value="Reset Password" class="btn">
      <?php endif; ?>
   </form>
</section>

<?php include 'components/footer.php'; ?>
<script src="js/script.js"></script>
</body>
</html>
