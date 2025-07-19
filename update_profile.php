<?php

include 'components/connect.php';

session_start();

if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
}else{
   $user_id = '';
   header('location:login.php');
};

if(isset($_POST['submit'])){

   $name = $_POST['name'];
   $name = filter_var($name, FILTER_SANITIZE_STRING);

   $email = $_POST['email'];
   $email = filter_var($email, FILTER_SANITIZE_STRING);
   $number = $_POST['number'];
   $number = filter_var($number, FILTER_SANITIZE_STRING);

   if(!empty($name)){
      $update_name = $conn->prepare("UPDATE `users` SET name = ? WHERE id = ?");
      $update_name->execute([$name, $user_id]);
   }

   if(!empty($email)){
      $select_email = $conn->prepare("SELECT * FROM `users` WHERE email = ?");
      $select_email->execute([$email]);
      if($select_email->rowCount() > 0){
         $message[] = 'email already taken!';
      }else{
         $update_email = $conn->prepare("UPDATE `users` SET email = ? WHERE id = ?");
         $update_email->execute([$email, $user_id]);
      }
   }

   if(!empty($number)){
      $select_number = $conn->prepare("SELECT * FROM `users` WHERE number = ?");
      $select_number->execute([$number]);
      if($select_number->rowCount() > 0){
         $message[] = 'number already taken!';
      }else{
         $update_number = $conn->prepare("UPDATE `users` SET number = ? WHERE id = ?");
         $update_number->execute([$number, $user_id]);
      }
   }
   
   $empty_pass = 'da39a3ee5e6b4b0d3255bfef95601890afd80709';
   $select_prev_pass = $conn->prepare("SELECT password FROM `users` WHERE id = ?");
   $select_prev_pass->execute([$user_id]);
   $fetch_prev_pass = $select_prev_pass->fetch(PDO::FETCH_ASSOC);
   $prev_pass = $fetch_prev_pass['password'];
   $old_pass = sha1($_POST['old_pass']);
   $old_pass = filter_var($old_pass, FILTER_SANITIZE_STRING);
   $new_pass = sha1($_POST['new_pass']);
   $new_pass = filter_var($new_pass, FILTER_SANITIZE_STRING);
   $confirm_pass = sha1($_POST['confirm_pass']);
   $confirm_pass = filter_var($confirm_pass, FILTER_SANITIZE_STRING);

   if($old_pass != $empty_pass){
      if($old_pass != $prev_pass){
         $message[] = 'Current password not matched!';
      }elseif($new_pass != $confirm_pass){
         $message[] = 'Confirm password not matched!';
      }else{
         if($new_pass != $empty_pass){
            $update_pass = $conn->prepare("UPDATE `users` SET password = ? WHERE id = ?");
            $update_pass->execute([$confirm_pass, $user_id]);
            $message[] = 'Password updated successfully!';
         }else{
            $message[] = 'Please enter a new password!';
         }
      }
   }  

   // Handle new valid ID upload
if(isset($_FILES['valid_id']) && $_FILES['valid_id']['error'] == 0){

   // Get current valid ID to delete later
   $get_old = $conn->prepare("SELECT valid_id FROM `users` WHERE id = ?");
   $get_old->execute([$user_id]);
   $old_file = $get_old->fetch(PDO::FETCH_ASSOC)['valid_id'];

   // Upload new file with unique name
   $valid_id = $_FILES['valid_id']['name'];
   $valid_id_tmp = $_FILES['valid_id']['tmp_name'];
   $valid_id_renamed = uniqid() . '_' . basename($valid_id);
   $valid_id_folder = 'uploaded_ids/' . $valid_id_renamed;

   move_uploaded_file($valid_id_tmp, $valid_id_folder);

   // Update database with new file path
   $update_valid_id = $conn->prepare("UPDATE `users` SET valid_id = ? WHERE id = ?");
   $update_valid_id->execute([$valid_id_folder, $user_id]);

   // Delete old file (optional)
   if(file_exists($old_file)){
      unlink($old_file);
   }

   $message[] = 'Valid ID updated successfully!';
}


}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>update profile</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<!-- header section starts  -->
<?php include 'components/user_header.php'; ?>
<!-- header section ends -->

<section class="form-container update-form">

  <form action="" method="post" enctype="multipart/form-data">
      <h3>update profile</h3>
      <input type="text" name="name" placeholder="<?= $fetch_profile['name']; ?>" class="box" maxlength="50">
      <input type="email" name="email" placeholder="<?= $fetch_profile['email']; ?>" class="box" maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="number" name="number" placeholder="<?= $fetch_profile['number']; ?>"" class="box" min="0" max="9999999999" maxlength="11">
      <input type="password" name="old_pass" placeholder="Enter your current password" class="box" maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="password" name="new_pass" placeholder="Enter your new password" class="box" maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="password" name="confirm_pass" placeholder="Confirm your new password" class="box" maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')">
      <label style="font-size: 18px; font-weight: bold;">Update Valid ID (Image or PDF)</label>
      <?php if(!empty($fetch_profile['valid_id'])): ?>
   <p>Current Uploaded ID:</p>
   <?php if (preg_match('/\.(jpg|jpeg|png)$/i', $fetch_profile['valid_id'])): ?>
      <img src="<?= $fetch_profile['valid_id']; ?>" alt="Valid ID" style="max-width: 300px; border: 1px solid #ccc; border-radius: 10px; margin-bottom: 10px;">
   <?php elseif (preg_match('/\.pdf$/i', $fetch_profile['valid_id'])): ?>
      <a href="<?= $fetch_profile['valid_id']; ?>" target="_blank">View PDF</a>
   <?php endif; ?>
<?php endif; ?>

      <input type="file" name="valid_id" accept=".jpg,.jpeg,.png,.pdf" class="box" onchange="previewValidID(event)">
      <div id="valid-id-preview" style="margin-top: 10px;"></div>


      <input type="submit" value="update now" name="submit" class="btn">
   </form>

</section>










<?php include 'components/footer.php'; ?>






<!-- custom js file link  -->
<script src="js/script.js"></script>
<script>
function previewValidID(event) {
    const file = event.target.files[0];
    const preview = document.getElementById('valid-id-preview');
    preview.innerHTML = '';

    if (file) {
        const fileType = file.type;
        const validImageTypes = ['image/jpeg', 'image/png', 'image/jpg'];

        if (validImageTypes.includes(fileType)) {
            const img = document.createElement('img');
            img.src = URL.createObjectURL(file);
            img.style.maxWidth = '300px';
            img.style.border = '1px solid #ccc';
            img.style.borderRadius = '10px';
            img.style.marginTop = '10px';
            preview.appendChild(img);
        } else if (fileType === 'application/pdf') {
            const text = document.createElement('p');
            text.textContent = "PDF file selected: " + file.name;
            text.style.fontWeight = 'bold';
            text.style.color = '#333';
            preview.appendChild(text);
        } else {
            const error = document.createElement('p');
            error.textContent = "Invalid file type!";
            error.style.color = 'red';
            preview.appendChild(error);
        }
    }
}
</script>

</body>
</html>