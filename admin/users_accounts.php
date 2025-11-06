<?php
include '../components/connect.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if(!isset($admin_id)){
   header('location:admin_login.php');
}

// Function to highlight search terms
function highlightSearchTerm($text, $search) {
   if(empty($search)) return $text;
   $pattern = '/(' . preg_quote($search, '/') . ')/i';
   return preg_replace($pattern, '<span class="highlight">$1</span>', $text);
}

// Search functionality - ONLY in name field
$search_query = '';
if(isset($_GET['search']) && !empty($_GET['search'])) {
   $search_query = $_GET['search'];
   $select_account = $conn->prepare("SELECT * FROM `users` WHERE name LIKE ?");
   $search_param = "%$search_query%";
   $select_account->execute([$search_param]);
} else {
   $select_account = $conn->prepare("SELECT * FROM `users`");
   $select_account->execute();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Users Accounts</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="../css/admin_style.css">

   <style>
      .search-container {
         margin: 20px 0;
         display: flex;
         justify-content: center;
         gap: 10px;
      }
      
      .search-box {
         padding: 12px 20px;
         width: 100%;
         max-width: 500px;
         border: 2px solid #2980b9;
         border-radius: 25px;
         font-size: 16px;
         outline: none;
         transition: all 0.3s ease;
      }
      
      .search-box:focus {
         box-shadow: 0 0 10px rgba(41, 128, 185, 0.3);
      }
      
      .search-btn {
         background: #2980b9;
         color: white;
         border: none;
         padding: 12px 25px;
         border-radius: 25px;
         cursor: pointer;
         font-size: 16px;
         transition: all 0.3s ease;
      }
      
      .search-btn:hover {
         background: #1c5d8a;
         transform: translateY(-2px);
      }
      
      .clear-search {
         background: #95a5a6;
         color: white;
         border: none;
         padding: 12px 20px;
         border-radius: 25px;
         cursor: pointer;
         font-size: 16px;
         transition: all 0.3s ease;
         text-decoration: none;
         display: inline-block;
      }
      
      .clear-search:hover {
         background: #7f8c8d;
         transform: translateY(-2px);
         text-decoration: none;
         color: white;
      }
      
      .search-results-info {
         text-align: center;
         margin: 15px 0;
         color: #7f8c8d;
         font-size: 16px;
      }
      
      .accounts-table {
         width: 100%;
         border-collapse: collapse;
         margin-top: 20px;
         background: white;
         border-radius: 10px;
         overflow: hidden;
         box-shadow: 0 0 10px rgba(0,0,0,0.1);
      }
      
      .accounts-table th,
      .accounts-table td {
         padding: 12px 15px;
         text-align: left;
         border-bottom: 1px solid #e0e0e0;
      }
      
      .accounts-table th {
         background-color: #2980b9;
         color: white;
         font-weight: 600;
         font-size: 16px;
      }
      
      .accounts-table tr {
         cursor: pointer;
         transition: all 0.3s ease;
      }
      
      .accounts-table tr:hover {
         background-color: #f5f5f5;
         transform: translateY(-2px);
         box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      }
      
      .accounts-table tr:last-child td {
         border-bottom: none;
      }
      
      .accounts-table td {
         font-size: 15px;
      }
      
      .valid-id-preview {
         max-width: 80px;
         max-height: 60px;
         border-radius: 4px;
         cursor: pointer;
         transition: all 0.3s ease;
      }
      
      .valid-id-preview:hover {
         transform: scale(1.8);
         z-index: 10;
         position: relative;
         box-shadow: 0 5px 15px rgba(0,0,0,0.3);
      }
      
      .pdf-link {
         color: #3498db;
         text-decoration: none;
         padding: 6px 12px;
         border: 1px solid #3498db;
         border-radius: 4px;
         transition: all 0.3s ease;
         font-size: 14px;
      }
      
      .pdf-link:hover {
         background-color: #3498db;
         color: white;
         text-decoration: none;
      }
      
      .empty-message {
         text-align: center;
         padding: 40px;
         background: white;
         border-radius: 10px;
         box-shadow: 0 0 10px rgba(0,0,0,0.1);
         color: #7f8c8d;
         font-size: 18px;
      }
      
      .table-container {
         overflow-x: auto;
      }
      
      .address-cell {
         max-width: 200px;
         overflow: hidden;
         text-overflow: ellipsis;
         white-space: nowrap;
         font-size: 15px;
      }
      
      .address-cell:hover {
         white-space: normal;
         overflow: visible;
         background: #f8f9fa;
         position: relative;
         z-index: 1;
      }

      /* Highlight search results */
      .highlight {
         background-color: #fff3cd;
         padding: 2px 4px;
         border-radius: 3px;
         font-weight: bold;
      }

      /* Modal Styles */
      .modal {
         display: none;
         position: fixed;
         z-index: 1000;
         left: 0;
         top: 0;
         width: 100%;
         height: 100%;
         overflow: auto;
         background-color: rgba(0,0,0,0.5);
      }
      
      .modal-content {
         background-color: #fefefe;
         margin: 5% auto;
         padding: 35px;
         border-radius: 12px;
         width: 90%;
         max-width: 750px;
         box-shadow: 0 5px 25px rgba(0,0,0,0.3);
         position: relative;
      }
      
      .close {
         color: #aaa;
         position: absolute;
         top: 20px;
         right: 25px;
         font-size: 32px;
         font-weight: bold;
         cursor: pointer;
      }
      
      .close:hover {
         color: #000;
      }
      
      .modal-header {
         border-bottom: 2px solid #e0e0e0;
         padding-bottom: 20px;
         margin-bottom: 25px;
      }
      
      .modal-header h2 {
         font-size: 28px;
         color: #2980b9;
         margin: 0;
      }
      
      .modal-body {
         padding: 15px 0;
      }
      
      .user-detail {
         display: flex;
         margin-bottom: 20px;
         padding: 15px;
         background: #f8f9fa;
         border-radius: 10px;
         border-left: 4px solid #2980b9;
      }
      
      .detail-label {
         font-weight: bold;
         width: 160px;
         color: #555;
         min-width: 160px;
         font-size: 17px;
      }
      
      .detail-value {
         flex: 1;
         color: #333;
         font-size: 17px;
         line-height: 1.5;
      }
      
      .address-value {
         line-height: 1.6;
      }
      
      .valid-id-container {
         text-align: center;
         margin-top: 25px;
         padding: 25px;
         background: #f8f9fa;
         border-radius: 10px;
         border: 2px dashed #ddd;
      }
      
      .valid-id-container h3 {
         font-size: 22px;
         color: #2980b9;
         margin-bottom: 15px;
      }
      
      .valid-id-full {
         max-width: 100%;
         max-height: 450px;
         border-radius: 10px;
         box-shadow: 0 4px 15px rgba(0,0,0,0.2);
      }
      
      .modal-footer-text {
         text-align: center;
         margin-top: 15px;
         color: #666;
         font-size: 15px;
         font-style: italic;
      }
      
      @media (max-width: 768px) {
         .search-container {
            flex-direction: column;
            align-items: center;
         }
         
         .search-box {
            max-width: 100%;
         }
         
         .accounts-table {
            font-size: 14px;
         }
         
         .accounts-table th,
         .accounts-table td {
            padding: 10px 12px;
         }
         
         .modal-content {
            margin: 8% auto;
            padding: 25px;
            width: 95%;
         }
         
         .modal-header h2 {
            font-size: 24px;
         }
         
         .user-detail {
            flex-direction: column;
            padding: 12px;
         }
         
         .detail-label {
            width: 100%;
            margin-bottom: 8px;
            font-size: 16px;
         }
         
         .detail-value {
            font-size: 16px;
         }
         
         .address-cell {
            max-width: 150px;
         }
         
         .close {
            font-size: 28px;
            top: 15px;
            right: 20px;
         }
      }
      
      @media (max-width: 480px) {
         .accounts-table {
            font-size: 13px;
         }
         
         .accounts-table th {
            font-size: 14px;
         }
         
         .accounts-table td {
            font-size: 13px;
         }
         
         .address-cell {
            max-width: 120px;
            font-size: 13px;
         }
         
         .modal-content {
            padding: 20px;
         }
         
         .modal-header h2 {
            font-size: 22px;
         }
         
         .detail-label,
         .detail-value {
            font-size: 15px;
         }
      }
   </style>
</head>
<body>

<?php include '../components/admin_header.php' ?>

<!-- user accounts section starts  -->
<section class="accounts">

   <h1 class="heading">Users Account Details</h1>

   <!-- Search Form -->
   <div class="search-container">
      <form method="GET" action="" style="display: flex; gap: 10px; width: 100%; max-width: 600px; align-items: center;">
         <input type="text" name="search" class="search-box" 
                placeholder="Search users by name..." 
                value="<?= htmlspecialchars($search_query) ?>">
         <button type="submit" class="search-btn">
            <i class="fas fa-search"></i> Search
         </button>
         <?php if(!empty($search_query)): ?>
            <a href="users_accounts.php" class="clear-search">
               <i class="fas fa-times"></i> Clear
            </a>
         <?php endif; ?>
      </form>
   </div>

   <?php
      if($select_account->rowCount() > 0){
         $total_users = $select_account->rowCount();
   ?>
   
   <!-- Search Results Info -->
   <?php if(!empty($search_query)): ?>
      <div class="search-results-info">
         <i class="fas fa-info-circle"></i> 
         Found <?= $total_users ?> user(s) matching "<?= htmlspecialchars($search_query) ?>"
         <?php if($total_users === 0): ?>
            - <a href="users_accounts.php" style="color: #2980b9;">Show all users</a>
         <?php endif; ?>
      </div>
   <?php else: ?>
      <div class="search-results-info">
         <i class="fas fa-users"></i> 
         Total <?= $total_users ?> user(s) in the system
      </div>
   <?php endif; ?>
   
   <div class="table-container">
      <table class="accounts-table">
         <thead>
            <tr>
               <th>User ID</th>
               <th>Name</th>
               <th>Email</th>
               <th>Phone Number</th>
               <th>Address</th>
               <th>Valid ID</th>
            </tr>
         </thead>
         <tbody>
            <?php
               while($fetch_accounts = $select_account->fetch(PDO::FETCH_ASSOC)){  
                  $address = $fetch_accounts['address'] ?? 'No address provided';
            ?>
            <tr onclick="openUserModal(
               '<?= $fetch_accounts['id']; ?>',
               '<?= addslashes($fetch_accounts['name']); ?>',
               '<?= addslashes($fetch_accounts['email']); ?>',
               '<?= addslashes($fetch_accounts['number']); ?>',
               '<?= addslashes($address); ?>',
               '<?= addslashes($fetch_accounts['valid_id']); ?>'
            )">
               <td><?= $fetch_accounts['id']; ?></td>
               <td><?= !empty($search_query) ? highlightSearchTerm($fetch_accounts['name'], $search_query) : $fetch_accounts['name']; ?></td>
               <td><?= $fetch_accounts['email']; ?></td>
               <td><?= $fetch_accounts['number']; ?></td>
               <td class="address-cell" title="<?= htmlspecialchars($address) ?>">
                  <?= htmlspecialchars($address); ?>
               </td>
               <td>
                  <?php if (!empty($fetch_accounts['valid_id'])): ?>
                     <?php if (preg_match('/\.(jpg|jpeg|png)$/i', $fetch_accounts['valid_id'])): ?>
                        <img src="../<?= $fetch_accounts['valid_id']; ?>" alt="Valid ID" class="valid-id-preview">
                     <?php elseif (preg_match('/\.pdf$/i', $fetch_accounts['valid_id'])): ?>
                        <a href="../<?= $fetch_accounts['valid_id']; ?>" target="_blank" class="pdf-link">
                           <i class="fas fa-file-pdf"></i> View PDF
                        </a>
                     <?php else: ?>
                        <span style="color: #95a5a6;">Unsupported format</span>
                     <?php endif; ?>
                  <?php else: ?>
                     <span style="color: #95a5a6;">No ID uploaded</span>
                  <?php endif; ?>
               </td>
            </tr>
            <?php } ?>
         </tbody>
      </table>
   </div>
   
   <?php } else { ?>
      <div class="empty-message">
         <i class="fas fa-users" style="font-size: 48px; margin-bottom: 20px;"></i>
         <?php if(!empty($search_query)): ?>
            <p>No users found matching "<?= htmlspecialchars($search_query) ?>"</p>
            <a href="users_accounts.php" class="btn" style="margin-top: 15px;">View All Users</a>
         <?php else: ?>
            <p>No user accounts available</p>
         <?php endif; ?>
      </div>
   <?php } ?>

</section>
<!-- user accounts section ends -->

<!-- User Details Modal -->
<div id="userModal" class="modal">
   <div class="modal-content">
      <span class="close">&times;</span>
      <div class="modal-header">
         <h2><i class="fas fa-user-circle"></i> User Account Details</h2>
      </div>
      <div class="modal-body">
         <div class="user-detail">
            <div class="detail-label">User ID:</div>
            <div class="detail-value" id="modal-user-id"></div>
         </div>
         <div class="user-detail">
            <div class="detail-label">Full Name:</div>
            <div class="detail-value" id="modal-user-name"></div>
         </div>
         <div class="user-detail">
            <div class="detail-label">Email Address:</div>
            <div class="detail-value" id="modal-user-email"></div>
         </div>
         <div class="user-detail">
            <div class="detail-label">Phone Number:</div>
            <div class="detail-value" id="modal-user-phone"></div>
         </div>
         <div class="user-detail">
            <div class="detail-label">Address:</div>
            <div class="detail-value address-value" id="modal-user-address"></div>
         </div>
         
         <div class="valid-id-container">
            <h3><i class="fas fa-id-card"></i> Valid ID</h3>
            <div id="modal-valid-id-content"></div>
         </div>
      </div>
      <div class="modal-footer-text">
         Click outside this window or press ESC to close
      </div>
   </div>
</div>

<!-- custom js file link  -->
<script src="../js/admin_script.js"></script>

<script>
   // Get the modal
   const modal = document.getElementById("userModal");
   const closeBtn = document.getElementsByClassName("close")[0];
   
   // Function to open the modal with user data
   function openUserModal(userId, userName, userEmail, userPhone, userAddress, validId) {
      // Set user details
      document.getElementById("modal-user-id").textContent = userId;
      document.getElementById("modal-user-name").textContent = userName;
      document.getElementById("modal-user-email").textContent = userEmail;
      document.getElementById("modal-user-phone").textContent = userPhone;
      document.getElementById("modal-user-address").textContent = userAddress;
      
      // Handle valid ID display
      const validIdContainer = document.getElementById("modal-valid-id-content");
      
      if (validId && validId !== '') {
         if (validId.match(/\.(jpg|jpeg|png)$/i)) {
            validIdContainer.innerHTML = `
               <img src="../${validId}" alt="Valid ID" class="valid-id-full">
            `;
         } else if (validId.match(/\.pdf$/i)) {
            validIdContainer.innerHTML = `
               <a href="../${validId}" target="_blank" class="pdf-link" style="font-size: 18px; padding: 12px 25px;">
                  <i class="fas fa-external-link-alt"></i> Open PDF Document
               </a>
            `;
         } else {
            validIdContainer.innerHTML = '<p style="color: #95a5a6; font-size: 16px;">Unsupported file format</p>';
         }
      } else {
         validIdContainer.innerHTML = '<p style="color: #95a5a6; font-size: 16px;">No valid ID uploaded</p>';
      }
      
      // Show the modal
      modal.style.display = "block";
   }
   
   // When the user clicks on <span> (x), close the modal
   closeBtn.onclick = function() {
      modal.style.display = "none";
   }
   
   // When the user clicks anywhere outside of the modal, close it
   window.onclick = function(event) {
      if (event.target == modal) {
         modal.style.display = "none";
      }
   }
   
   // Close modal with Escape key
   document.addEventListener('keydown', function(event) {
      if (event.key === 'Escape') {
         modal.style.display = "none";
      }
   });
</script>

</body>
</html>