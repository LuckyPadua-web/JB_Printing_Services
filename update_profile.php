<?php

include 'components/connect.php';

session_start();

if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
}else{
   $user_id = '';
   header('location:login.php');
}

if(isset($_POST['submit'])){

   $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
   $email = filter_var($_POST['email'], FILTER_SANITIZE_STRING);
   $number = filter_var($_POST['number'], FILTER_SANITIZE_STRING);

   if(!empty($name)){
      $update_name = $conn->prepare("UPDATE `users` SET name = ? WHERE id = ?");
      $update_name->execute([$name, $user_id]);
   }

   if(!empty($email)){
      $select_email = $conn->prepare("SELECT * FROM `users` WHERE email = ? AND id != ?");
      $select_email->execute([$email, $user_id]);
      if($select_email->rowCount() > 0){
         $message[] = 'Email already taken!';
      }else{
         $update_email = $conn->prepare("UPDATE `users` SET email = ? WHERE id = ?");
         $update_email->execute([$email, $user_id]);
      }
   }

   if(!empty($number)){
      $select_number = $conn->prepare("SELECT * FROM `users` WHERE number = ? AND id != ?");
      $select_number->execute([$number, $user_id]);
      if($select_number->rowCount() > 0){
         $message[] = 'Number already taken!';
      }else{
         $update_number = $conn->prepare("UPDATE `users` SET number = ? WHERE id = ?");
         $update_number->execute([$number, $user_id]);
      }
   }

   // Update password
   $empty_pass = 'da39a3ee5e6b4b0d3255bfef95601890afd80709';
   $select_prev_pass = $conn->prepare("SELECT password FROM `users` WHERE id = ?");
   $select_prev_pass->execute([$user_id]);
   $fetch_prev_pass = $select_prev_pass->fetch(PDO::FETCH_ASSOC);
   $prev_pass = $fetch_prev_pass['password'];

   $old_pass = sha1(filter_var($_POST['old_pass'], FILTER_SANITIZE_STRING));
   $new_pass = sha1(filter_var($_POST['new_pass'], FILTER_SANITIZE_STRING));
   $confirm_pass = sha1(filter_var($_POST['confirm_pass'], FILTER_SANITIZE_STRING));

   if($old_pass != $empty_pass){
      if($old_pass != $prev_pass){
         $message[] = 'Current password not matched!';
      } elseif($new_pass != $confirm_pass){
         $message[] = 'Confirm password not matched!';
      } elseif($new_pass == $empty_pass){
         $message[] = 'Please enter a new password!';
      } else {
         $update_pass = $conn->prepare("UPDATE `users` SET password = ? WHERE id = ?");
         $update_pass->execute([$confirm_pass, $user_id]);
         $message[] = 'Password updated successfully!';
      }
   }

   // Handle valid ID upload
   if(isset($_FILES['valid_id']) && $_FILES['valid_id']['error'] == 0){
      $get_old = $conn->prepare("SELECT valid_id FROM `users` WHERE id = ?");
      $get_old->execute([$user_id]);
      $old_file = $get_old->fetch(PDO::FETCH_ASSOC)['valid_id'];

      $valid_id = $_FILES['valid_id']['name'];
      $valid_id_tmp = $_FILES['valid_id']['tmp_name'];
      $valid_id_renamed = uniqid() . '_' . basename($valid_id);
      $valid_id_folder = 'uploaded_ids/' . $valid_id_renamed;

      move_uploaded_file($valid_id_tmp, $valid_id_folder);

      $update_valid_id = $conn->prepare("UPDATE `users` SET valid_id = ? WHERE id = ?");
      $update_valid_id->execute([$valid_id_folder, $user_id]);

      if(file_exists($old_file)){
         unlink($old_file);
      }

      $message[] = 'Valid ID updated successfully!';
   }

   // Update security questions
   $sec_q1 = filter_var($_POST['security_question_1'], FILTER_SANITIZE_STRING);
   $sec_a1 = filter_var($_POST['security_answer_1'], FILTER_SANITIZE_STRING);
   $sec_q2 = filter_var($_POST['security_question_2'], FILTER_SANITIZE_STRING);
   $sec_a2 = filter_var($_POST['security_answer_2'], FILTER_SANITIZE_STRING);
   $sec_q3 = filter_var($_POST['security_question_3'], FILTER_SANITIZE_STRING);
   $sec_a3 = filter_var($_POST['security_answer_3'], FILTER_SANITIZE_STRING);

   if(!empty($sec_q1) && !empty($sec_a1) && !empty($sec_q2) && !empty($sec_a2) && !empty($sec_q3) && !empty($sec_a3)){
      $update_sec = $conn->prepare("UPDATE `users` SET 
         security_question_1 = ?, security_answer_1 = ?, 
         security_question_2 = ?, security_answer_2 = ?, 
         security_question_3 = ?, security_answer_3 = ? 
         WHERE id = ?");
      $update_sec->execute([$sec_q1, $sec_a1, $sec_q2, $sec_a2, $sec_q3, $sec_a3, $user_id]);
      $message[] = 'Security questions updated successfully!';
   }

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Update Profile</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php include 'components/user_header.php'; ?>

<section class="form-container update-form">

<form action="" method="post" enctype="multipart/form-data">
   <h3>Update Profile</h3>
   <input type="text" name="name" placeholder="<?= $fetch_profile['name']; ?>" class="box" maxlength="50">
   <input type="email" name="email" placeholder="<?= $fetch_profile['email']; ?>" class="box" maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')">
   <input type="number" name="number" placeholder="<?= $fetch_profile['number']; ?>" class="box" maxlength="11">

   <input type="password" name="old_pass" placeholder="Current password" class="box" maxlength="50">
   <input type="password" name="new_pass" placeholder="New password" class="box" maxlength="50">
   <input type="password" name="confirm_pass" placeholder="Confirm new password" class="box" maxlength="50">

   <label style="font-size: 18px; font-weight: bold;">Update Valid ID (Image or PDF)</label>
   <?php if(!empty($fetch_profile['valid_id'])): ?>
      <?php if(preg_match('/\.(jpg|jpeg|png)$/i', $fetch_profile['valid_id'])): ?>
         <img src="<?= $fetch_profile['valid_id']; ?>" style="max-width: 300px; border:1px solid #ccc; border-radius:10px; margin-bottom:10px;">
      <?php elseif(preg_match('/\.pdf$/i', $fetch_profile['valid_id'])): ?>
         <a href="<?= $fetch_profile['valid_id']; ?>" target="_blank">View Current PDF</a>
      <?php endif; ?>
   <?php endif; ?>
   <input type="file" name="valid_id" accept=".jpg,.jpeg,.png,.pdf" class="box">

   <label style="font-size: 18px; font-weight: bold;">Security Question</label>

   <!-- Question 1 -->
<select name="security_question_1" class="box" required>
   <option value="">Select Question 1</option>
   <option value="What is your favorite movie?" <?= ($fetch_profile['security_question_1'] == 'What is your favorite movie?') ? 'selected' : '' ?>>What is your favorite movie?</option>
   <option value="What is your mother's maiden name?" <?= ($fetch_profile['security_question_1'] == 'What is your mother\'s maiden name?') ? 'selected' : '' ?>>What is your mother's maiden name?</option>
   <option value="What was your childhood nickname?" <?= ($fetch_profile['security_question_1'] == 'What was your childhood nickname?') ? 'selected' : '' ?>>What was your childhood nickname?</option>
</select>
<input type="text" name="security_answer_1" placeholder="Answer for Question 1" class="box" required value="<?= $fetch_profile['security_answer_1'] ?? '' ?>">

<!-- Question 2 -->
<select name="security_question_2" class="box" required>
   <option value="">Select Question 2</option>
   <option value="What is your dream job?" <?= ($fetch_profile['security_question_2'] == 'What is your dream job?') ? 'selected' : '' ?>>What is your dream job?</option>
   <option value="What city were you born in?" <?= ($fetch_profile['security_question_2'] == 'What city were you born in?') ? 'selected' : '' ?>>What city were you born in?</option>
   <option value="What is your favorite food?" <?= ($fetch_profile['security_question_2'] == 'What is your favorite food?') ? 'selected' : '' ?>>What is your favorite food?</option>
</select>
<input type="text" name="security_answer_2" placeholder="Answer for Question 2" class="box" required value="<?= $fetch_profile['security_answer_2'] ?? '' ?>">

<!-- Question 3 -->
<select name="security_question_3" class="box" required>
   <option value="">Select Question 3</option>
   <option value="What is your pet's name?" <?= ($fetch_profile['security_question_3'] == 'What is your pet\'s name?') ? 'selected' : '' ?>>What is your pet's name?</option>
   <option value="What school did you go to?" <?= ($fetch_profile['security_question_3'] == 'What school did you go to?') ? 'selected' : '' ?>>What school did you go to?</option>
   <option value="Who is your childhood hero?" <?= ($fetch_profile['security_question_3'] == 'Who is your childhood hero?') ? 'selected' : '' ?>>Who is your childhood hero?</option>
</select>
<input type="text" name="security_answer_3" placeholder="Answer for Question 3" class="box" required value="<?= $fetch_profile['security_answer_3'] ?? '' ?>">
   <input type="submit" value="Update Now" name="submit" class="btn">
</form>

</section>

<?php include 'components/footer.php'; ?>

<script src="js/script.js"></script>
</body>
</html>
