<?php

include '../components/connect.php';

session_start();

if(isset($_SESSION['admin_id'])){
   $admin_id = $_SESSION['admin_id'];
}else{
   $admin_id = '';
   header('location:admin_login.php');
};

if(isset($_GET['delete'])){
   $delete_id = $_GET['delete'];
   $delete_message = $conn->prepare("DELETE FROM `messages` WHERE id = ?");
   $delete_message->execute([$delete_id]);
   header('location:messages.php');
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>messages</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="../css/admin_style.css">

   <style>
      .messages {
         padding: 2rem;
         max-width: 1200px;
         margin: 0 auto;
      }

      .heading {
         text-align: center;
         font-size: 2.5rem;
         margin-bottom: 2rem;
         color: #333;
         border-bottom: 2px solid #e0e0e0;
         padding-bottom: 1rem;
      }

      .messages-container {
         background: #fff;
         border-radius: 8px;
         box-shadow: 0 1px 3px rgba(0,0,0,0.1);
         overflow: hidden;
      }

      .message-item {
         display: flex;
         align-items: flex-start;
         padding: 1.5rem;
         border-bottom: 1px solid #f0f0f0;
         transition: background-color 0.2s ease;
         cursor: pointer;
         position: relative;
      }

      .message-item:hover {
         background-color: #f9f9f9;
      }

      .message-item:last-child {
         border-bottom: none;
      }

      .message-item.unread {
         background-color: #f0f8ff;
         border-left: 4px solid #4285f4;
      }

      .message-item.unread:hover {
         background-color: #e8f4ff;
      }

      .message-avatar {
         width: 40px;
         height: 40px;
         border-radius: 50%;
         background: linear-gradient(135deg, #4285f4, #34a853);
         display: flex;
         align-items: center;
         justify-content: center;
         color: white;
         font-weight: bold;
         font-size: 1.4rem;
         margin-right: 1.5rem;
         flex-shrink: 0;
      }

      .message-content {
         flex: 1;
         min-width: 0;
      }

      .message-header {
         display: flex;
         align-items: center;
         justify-content: between;
         margin-bottom: 0.5rem;
         gap: 1rem;
      }

      .message-sender {
         font-weight: 600;
         color: #333;
         font-size: 1.4rem;
         margin: 0;
      }

      .message-contact {
         display: flex;
         gap: 1.5rem;
         margin-bottom: 0.5rem;
         flex-wrap: wrap;
      }

      .contact-info {
         display: flex;
         align-items: center;
         gap: 0.5rem;
         color: #666;
         font-size: 1.2rem;
      }

      .contact-info i {
         color: #4285f4;
         width: 16px;
      }

      .message-preview {
         color: #666;
         font-size: 1.3rem;
         line-height: 1.4;
         margin: 0;
         display: -webkit-box;
         -webkit-line-clamp: 2;
         -webkit-box-orient: vertical;
         overflow: hidden;
      }

      .message-time {
         color: #999;
         font-size: 1.2rem;
         white-space: nowrap;
         margin-left: auto;
         padding-left: 1rem;
      }

      .message-actions {
         display: flex;
         gap: 0.5rem;
         margin-left: 1rem;
         opacity: 0;
         transition: opacity 0.2s ease;
      }

      .message-item:hover .message-actions {
         opacity: 1;
      }

      .action-btn {
         padding: 0.5rem;
         border: none;
         background: none;
         cursor: pointer;
         border-radius: 4px;
         transition: background-color 0.2s ease;
         color: #666;
      }

      .action-btn:hover {
         background-color: #f0f0f0;
      }

      .delete-btn {
         color: #d93025;
      }

      .delete-btn:hover {
         background-color: #fce8e6;
      }

      .view-btn {
         color: #4285f4;
      }

      .view-btn:hover {
         background-color: #e8f0fe;
      }

      /* Modal styles */
      .modal {
         display: none;
         position: fixed;
         z-index: 1000;
         left: 0;
         top: 0;
         width: 100%;
         height: 100%;
         background-color: rgba(0,0,0,0.5);
      }

      .modal-content {
         background-color: #fff;
         margin: 5% auto;
         padding: 0;
         border-radius: 8px;
         width: 90%;
         max-width: 600px;
         box-shadow: 0 4px 20px rgba(0,0,0,0.2);
         animation: modalSlideIn 0.3s ease;
      }

      @keyframes modalSlideIn {
         from {
            opacity: 0;
            transform: translateY(-50px);
         }
         to {
            opacity: 1;
            transform: translateY(0);
         }
      }

      .modal-header {
         padding: 2rem 2rem 1rem;
         border-bottom: 1px solid #e0e0e0;
      }

      .modal-title {
         margin: 0;
         font-size: 1.8rem;
         color: #333;
      }

      .modal-sender {
         color: #666;
         font-size: 1.3rem;
         margin-top: 0.5rem;
      }

      .modal-body {
         padding: 2rem;
      }

      .modal-message {
         font-size: 1.4rem;
         line-height: 1.6;
         color: #333;
         white-space: pre-wrap;
      }

      .modal-footer {
         padding: 1rem 2rem;
         border-top: 1px solid #e0e0e0;
         display: flex;
         justify-content: flex-end;
         gap: 1rem;
      }

      .close-modal {
         background: #4285f4;
         color: white;
         border: none;
         padding: 0.8rem 1.5rem;
         border-radius: 4px;
         cursor: pointer;
         font-size: 1.3rem;
      }

      .close-modal:hover {
         background: #3367d6;
      }

      .empty-state {
         text-align: center;
         padding: 4rem 2rem;
         color: #666;
      }

      .empty-state i {
         font-size: 4rem;
         color: #ddd;
         margin-bottom: 1rem;
      }

      .empty-state h3 {
         font-size: 1.8rem;
         margin-bottom: 1rem;
         color: #333;
      }

      .empty-state p {
         font-size: 1.4rem;
         margin: 0;
      }

      .messages-stats {
         display: flex;
         justify-content: space-between;
         align-items: center;
         margin-bottom: 1.5rem;
         padding: 1rem;
         background: #f8f9fa;
         border-radius: 6px;
      }

      .total-messages {
         font-size: 1.4rem;
         color: #666;
      }

      .refresh-btn {
         background: #4285f4;
         color: white;
         border: none;
         padding: 0.6rem 1.2rem;
         border-radius: 4px;
         cursor: pointer;
         font-size: 1.3rem;
         display: flex;
         align-items: center;
         gap: 0.5rem;
      }

      .refresh-btn:hover {
         background: #3367d6;
      }
   </style>
</head>
<body>

<?php include '../components/admin_header.php' ?>

<!-- messages section starts  -->

<section class="messages">

   <h1 class="heading">Messages</h1>

   <div class="messages-container">
      <?php
         // First, let's check what columns exist in the messages table
         try {
            $check_columns = $conn->prepare("SHOW COLUMNS FROM `messages`");
            $check_columns->execute();
            $columns = $check_columns->fetchAll(PDO::FETCH_COLUMN);
            
            // Use the appropriate timestamp column
            $timestamp_column = 'date'; // Default fallback
            if (in_array('created_at', $columns)) {
               $timestamp_column = 'created_at';
            } elseif (in_array('timestamp', $columns)) {
               $timestamp_column = 'timestamp';
            } elseif (in_array('date', $columns)) {
               $timestamp_column = 'date';
            }
            
            // Build the query with the correct timestamp column
            $select_messages = $conn->prepare("SELECT * FROM `messages` ORDER BY id DESC");
            $select_messages->execute();
            $total_messages = $select_messages->rowCount();
            
         } catch (PDOException $e) {
            // If there's an error, use a simple query without ordering
            $select_messages = $conn->prepare("SELECT * FROM `messages`");
            $select_messages->execute();
            $total_messages = $select_messages->rowCount();
         }
         
         if($total_messages > 0){
      ?>
      
      <div class="messages-stats">
         <div class="total-messages">
            <strong><?= $total_messages ?></strong> message<?= $total_messages > 1 ? 's' : '' ?> total
         </div>
         <button class="refresh-btn" onclick="window.location.reload()">
            <i class="fas fa-sync-alt"></i> Refresh
         </button>
      </div>

      <?php
            while($fetch_messages = $select_messages->fetch(PDO::FETCH_ASSOC)){
               $sender_name = $fetch_messages['name'];
               $initials = getInitials($sender_name);
               $message_preview = strlen($fetch_messages['message']) > 120 
                  ? substr($fetch_messages['message'], 0, 120) . '...' 
                  : $fetch_messages['message'];
               
               // Get timestamp - try different possible column names
               $timestamp = '';
               if (isset($fetch_messages['created_at'])) {
                  $timestamp = $fetch_messages['created_at'];
               } elseif (isset($fetch_messages['timestamp'])) {
                  $timestamp = $fetch_messages['timestamp'];
               } elseif (isset($fetch_messages['date'])) {
                  $timestamp = $fetch_messages['date'];
               } else {
                  $timestamp = 'Recent';
               }
               
               $time_ago = timeAgo($timestamp);
      ?>
      <div class="message-item" onclick="viewMessage(<?= htmlspecialchars(json_encode($fetch_messages)) ?>)">
         <div class="message-avatar">
            <?= $initials ?>
         </div>
         <div class="message-content">
            <div class="message-header">
               <h3 class="message-sender"><?= htmlspecialchars($sender_name) ?></h3>
               <div class="message-contact">
                  <div class="contact-info">
                     <i class="fas fa-phone"></i>
                     <span><?= htmlspecialchars($fetch_messages['number']) ?></span>
                  </div>
                  <div class="contact-info">
                     <i class="fas fa-envelope"></i>
                     <span><?= htmlspecialchars($fetch_messages['email']) ?></span>
                  </div>
               </div>
               <div class="message-time"><?= $time_ago ?></div>
            </div>
            <p class="message-preview"><?= htmlspecialchars($message_preview) ?></p>
         </div>
         <div class="message-actions">
            <button class="action-btn view-btn" onclick="event.stopPropagation(); viewMessage(<?= htmlspecialchars(json_encode($fetch_messages)) ?>)">
               <i class="fas fa-eye"></i>
            </button>
            <a href="messages.php?delete=<?= $fetch_messages['id']; ?>" class="action-btn delete-btn" onclick="event.stopPropagation(); return confirm('Delete this message?');">
               <i class="fas fa-trash"></i>
            </a>
         </div>
      </div>
      <?php
            }
         }else{
      ?>
      <div class="empty-state">
         <i class="fas fa-inbox"></i>
         <h3>No messages yet</h3>
         <p>When customers contact you, their messages will appear here.</p>
      </div>
      <?php
         }

         // Helper functions
         function getInitials($name) {
            $names = explode(' ', $name);
            $initials = '';
            foreach($names as $n) {
               if (!empty(trim($n))) {
                  $initials .= strtoupper($n[0]);
               }
            }
            return substr($initials, 0, 2);
         }

         function timeAgo($datetime) {
            if (empty($datetime) || $datetime == 'Recent') {
               return 'Recent';
            }
            
            try {
               $time = strtotime($datetime);
               if ($time === false) {
                  return 'Recent';
               }
               
               $time_diff = time() - $time;
               
               if ($time_diff < 60) {
                  return 'just now';
               } elseif ($time_diff < 3600) {
                  $mins = floor($time_diff / 60);
                  return $mins . ' min' . ($mins > 1 ? 's' : '') . ' ago';
               } elseif ($time_diff < 86400) {
                  $hours = floor($time_diff / 3600);
                  return $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago';
               } else {
                  return date('M j, Y', $time);
               }
            } catch (Exception $e) {
               return 'Recent';
            }
         }
      ?>
   </div>

</section>

<!-- Message View Modal -->
<div id="messageModal" class="modal">
   <div class="modal-content">
      <div class="modal-header">
         <h3 class="modal-title" id="modalMessageName"></h3>
         <div class="modal-sender">
            <div><strong>Phone:</strong> <span id="modalMessageNumber"></span></div>
            <div><strong>Email:</strong> <span id="modalMessageEmail"></span></div>
            <div><strong>Sent:</strong> <span id="modalMessageTime"></span></div>
         </div>
      </div>
      <div class="modal-body">
         <div class="modal-message" id="modalMessageContent"></div>
      </div>
      <div class="modal-footer">
         <button class="close-modal" onclick="closeModal()">Close</button>
      </div>
   </div>
</div>

<!-- custom js file link  -->
<script src="../js/admin_script.js"></script>

<script>
// Modal functionality
const modal = document.getElementById('messageModal');

function viewMessage(message) {
   document.getElementById('modalMessageName').textContent = message.name;
   document.getElementById('modalMessageNumber').textContent = message.number;
   document.getElementById('modalMessageEmail').textContent = message.email;
   
   // Format the timestamp for display
   let displayTime = 'Recent';
   if (message.created_at) {
      displayTime = new Date(message.created_at).toLocaleString();
   } else if (message.timestamp) {
      displayTime = new Date(message.timestamp).toLocaleString();
   } else if (message.date) {
      displayTime = new Date(message.date).toLocaleString();
   }
   document.getElementById('modalMessageTime').textContent = displayTime;
   
   document.getElementById('modalMessageContent').textContent = message.message;
   
   modal.style.display = 'block';
}

function closeModal() {
   modal.style.display = 'none';
}

// Close modal when clicking outside
window.onclick = function(event) {
   if (event.target == modal) {
      closeModal();
   }
}

// Close modal with Escape key
document.addEventListener('keydown', function(event) {
   if (event.key === 'Escape') {
      closeModal();
   }
});
</script>

</body>
</html>