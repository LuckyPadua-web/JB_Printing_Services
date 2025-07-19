<?php

include 'components/connect.php';

session_start();

if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
}else{
   $user_id = '';
};

if(isset($_POST['submit'])){

   $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
   $email = filter_var($_POST['email'], FILTER_SANITIZE_STRING);
   $number = filter_var($_POST['number'], FILTER_SANITIZE_STRING);
   $pass = filter_var(sha1($_POST['pass']), FILTER_SANITIZE_STRING);
   $cpass = filter_var(sha1($_POST['cpass']), FILTER_SANITIZE_STRING);

   // ✅ Handle file upload and rename to avoid duplicate file names
   $valid_id = $_FILES['valid_id']['name'];
   $valid_id_tmp = $_FILES['valid_id']['tmp_name'];

   // Create unique filename
   $valid_id_renamed = uniqid() . '_' . basename($valid_id);

   // Save to folder
   $valid_id_folder = 'uploaded_ids/' . $valid_id_renamed;

   // Move uploaded file
   move_uploaded_file($valid_id_tmp, $valid_id_folder);

   // ✅ Check if email or number already exists
   $select_user = $conn->prepare("SELECT * FROM `users` WHERE email = ? OR number = ?");
   $select_user->execute([$email, $number]);

   if($select_user->rowCount() > 0){
      $message[] = 'email or number already exists!';
   }else{
      if($pass != $cpass){
         $message[] = 'confirm password not matched!';
      }else{
         $insert_user = $conn->prepare("INSERT INTO `users`(name, email, number, password, valid_id) VALUES(?,?,?,?,?)");
         $insert_user->execute([$name, $email, $number, $cpass, $valid_id_folder]);

         $select_user = $conn->prepare("SELECT * FROM `users` WHERE email = ? AND password = ?");
         $select_user->execute([$email, $pass]);
         $row = $select_user->fetch(PDO::FETCH_ASSOC);

         if($select_user->rowCount() > 0){
            $_SESSION['user_id'] = $row['id'];
            header('location:login.php');
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
   <title>register</title>

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

   <form action="" method="post" enctype="multipart/form-data">
      <h3>register now</h3>
      <input type="text" name="name" required placeholder="Enter your Full name" class="box" maxlength="50">
      <input type="email" name="email" required placeholder="Enter your gmail" class="box" maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="number" name="number" required placeholder="Enter your number" class="box" min="0" max="9999999999" maxlength="11">
      <input type="password" name="pass" required placeholder="Enter your password" class="box" maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="password" name="cpass" required placeholder="Confirm your password" class="box" maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')">
      <label style="font-size: 18px; font-weight: bold;">Upload Valid ID (Image or PDF)</label>
      <input type="file" name="valid_id" accept=".jpg,.jpeg,.png,.pdf" class="box" required>
      <input type="submit" value="register now" name="submit" class="btn">
      <p>Already have an account? <a href="login.php">Login now</a></p>
   </form>

</section>











<?php include 'components/footer.php'; ?>







<!-- custom js file link  -->
<script src="js/script.js"></script>

</body>
</html>