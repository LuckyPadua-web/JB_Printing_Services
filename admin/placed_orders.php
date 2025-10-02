<?php
include '../components/connect.php';
require_once 'email_functions.php';
session_start();

if (isset($_SESSION['admin_id'])) {
   $admin_id = $_SESSION['admin_id'];
} else {
   $admin_id = '';
   header('location:admin_login.php');
   exit;
}

// Initialize messages array
$messages = [];

// Search functionality
$search_query = '';
$where_clause = '';
if (isset($_POST['search']) || isset($_GET['search'])) {
   $search_query = isset($_POST['search_term']) ? $_POST['search_term'] : $_GET['search'];
   if (!empty($search_query)) {
      $where_clause = "WHERE name LIKE ? OR gcash_ref LIKE ?";
   }
}

// Update order status and delivery date
if (isset($_POST['update_order'])) {
   $order_id = $_POST['order_id'];
   $order_status = $_POST['order_status'];
   $expected_delivery_date = $_POST['expected_delivery_date'];
   
   // Get current order details for email
   $get_order = $conn->prepare("SELECT * FROM `orders` WHERE id = ?");
   $get_order->execute([$order_id]);
   $order_data = $get_order->fetch(PDO::FETCH_ASSOC);
   
   if ($order_data) {
      // Update the order
      $update_order = $conn->prepare("UPDATE `orders` SET status = ?, expected_delivery_date = ? WHERE id = ?");
      $update_order->execute([$order_status, $expected_delivery_date, $order_id]);
      
      // Send email notification to customer
      $customer_email = $order_data['email'];
      $customer_name = $order_data['name'];
      
      // Prepare order details for email
      $order_details = "Order ID: #" . $order_id . "\n";
      $order_details .= "Total Amount: ₱" . number_format($order_data['total_price'], 2) . "\n";
      $order_details .= "Payment Method: " . $order_data['method'] . "\n";
      
      if (!empty($expected_delivery_date)) {
         $order_details .= "Expected Delivery: " . date('F j, Y', strtotime($expected_delivery_date)) . "\n";
      }
      
      if (!empty($order_data['total_products'])) {
         $order_details .= "Products: " . $order_data['total_products'];
      }
      
      // Send email notification
      $email_sent = sendOrderStatusEmail($customer_email, $customer_name, $order_id, $order_status, $order_details);
      
      if ($email_sent) {
         $messages[] = ['text' => 'Order updated successfully and email notification sent to customer!', 'type' => 'success'];
      } else {
         $messages[] = ['text' => 'Order updated successfully, but email notification failed to send.', 'type' => 'warning'];
      }
   } else {
      $messages[] = ['text' => 'Order not found!', 'type' => 'error'];
   }
}

// Delete order
if (isset($_GET['delete'])) {
   $delete_id = $_GET['delete'];
   $get_design_file = $conn->prepare("SELECT design_file FROM `orders` WHERE id = ?");
   $get_design_file->execute([$delete_id]);
   $design_data = $get_design_file->fetch(PDO::FETCH_ASSOC);

   if (!empty($design_data['design_file']) && file_exists('../uploaded_designs/' . $design_data['design_file'])) {
      unlink('../uploaded_designs/' . $design_data['design_file']);
   }

   $delete_order = $conn->prepare("DELETE FROM `orders` WHERE id = ?");
   $delete_order->execute([$delete_id]);
   header('location:placed_orders.php');
   exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <title>Placed Orders</title>
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="../css/admin_style.css">

   <style>
      .placed-orders {
         padding: 2rem;
      }

      .orders-wrapper {
         overflow-x: auto;
         background: #fff;
         border-radius: 10px;
         box-shadow: 0 0 10px rgba(0,0,0,0.05);
         margin: auto;
         max-width: 1200px;
      }

      table.orders-table {
         width: 100%;
         border-collapse: collapse;
         font-size: 1.4rem;
         min-width: 800px;
      }

      table.orders-table thead {
         background-color: #f9f9f9;
      }

      table.orders-table th, table.orders-table td {
         padding: 1.2rem 1.5rem;
         text-align: left;
         border-bottom: 1px solid #eee;
      }

      .order-id {
         font-weight: bold;
         color: #2ecc71;
         font-size: 1.5rem;
      }

      .badge {
         padding: 0.3rem 1rem;
         border-radius: 15px;
         font-size: 1.2rem;
         font-weight: 500;
         white-space: nowrap;
         display: inline-block;
         margin: 2px 0;
      }

      /* Order Status Badges */
      .badge.status-pending { background: #fff3cd; color: #856404; }
      .badge.status-confirmed { background: #d1ecf1; color: #0c5460; }
      .badge.status-processing { background: #cce7ff; color: #004085; }
      .badge.status-shipped { background: #d4edda; color: #155724; }
      .badge.status-delivered { background: #d1e7dd; color: #0f5132; }
      .badge.status-cancelled { background: #f8d7da; color: #721c24; }

      form.inline-form {
         display: flex;
         flex-direction: column;
         gap: 0.5rem;
      }

      form.inline-form select,
      form.inline-form input[type="date"] {
         padding: 0.5rem;
         font-size: 1.3rem;
         border: 1px solid #ddd;
         border-radius: 5px;
      }

      .btn, .delete-btn {
         display: inline-block;
         padding: 0.6rem 1.2rem;
         font-size: 1.3rem;
         color: #fff;
         border: none;
         border-radius: 5px;
         cursor: pointer;
         text-decoration: none;
         text-align: center;
      }

      .btn {
         background: #27ae60;
      }

      .btn:hover {
         background: #219a52;
      }

      .delete-btn {
         background: #e74c3c;
         margin-top: 0.5rem;
      }

      .delete-btn:hover {
         background: #c0392b;
      }

      .heading {
         text-align: center;
         font-size: 2.5rem;
         margin-bottom: 2rem;
         color: #333;
      }

      .actions-cell {
         min-width: 250px;
      }

      /* Modal styles for viewing design */
      .modal {
         display: none;
         position: fixed;
         z-index: 1000;
         left: 0;
         top: 0;
         width: 100%;
         height: 100%;
         overflow: auto;
         background-color: rgba(0,0,0,0.8);
      }

      .modal-content {
         background-color: #fefefe;
         margin: 5% auto;
         padding: 20px;
         border-radius: 10px;
         width: 80%;
         max-width: 800px;
         position: relative;
      }

      .close {
         color: #aaa;
         float: right;
         font-size: 28px;
         font-weight: bold;
         cursor: pointer;
         position: absolute;
         right: 15px;
         top: 10px;
      }

      .close:hover,
      .close:focus {
         color: #000;
         text-decoration: none;
      }

      .design-image {
         width: 100%;
         height: auto;
         max-height: 70vh;
         object-fit: contain;
         display: block;
         margin: 20px auto 0;
      }

      .modal-title {
         margin-top: 0;
         color: #333;
         text-align: center;
      }

      /* Search styles */
      .search-container {
         max-width: 1200px;
         margin: 0 auto 2rem auto;
         background: #fff;
         padding: 2rem;
         border-radius: 10px;
         box-shadow: 0 0 10px rgba(0,0,0,0.05);
      }

      .search-form {
         display: flex;
         gap: 1rem;
         align-items: center;
         flex-wrap: wrap;
      }

      .search-input {
         flex: 1;
         min-width: 300px;
         padding: 1rem;
         font-size: 1.4rem;
         border: 2px solid #ddd;
         border-radius: 5px;
      }

      .search-btn {
         padding: 1rem 2rem;
         font-size: 1.4rem;
         background: #3498db;
         color: white;
         border: none;
         border-radius: 5px;
         cursor: pointer;
      }

      .clear-btn {
         padding: 1rem 2rem;
         font-size: 1.4rem;
         background: #95a5a6;
         color: white;
         border: none;
         border-radius: 5px;
         cursor: pointer;
         text-decoration: none;
      }

      .search-info {
         margin-top: 1rem;
         font-size: 1.3rem;
         color: #666;
      }

      /* BIGGER AND MORE PROMINENT MESSAGE STYLES */
      .message {
         padding: 1rem 1rem;
         background: linear-gradient(135deg, #d4edda, #c3e6cb);
         color: #155724;
         border-radius: 8px;
         margin-bottom: 2rem;
         text-align: center;
         max-width: 1200px;
         margin-left: auto;
         margin-right: auto;
         border: 1px solid #28a745;
         box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
         font-size: 2rem;
         font-weight: bold;
         position: relative;
         animation: slideIn 0.5s ease-out;
      }

      .message::before {
         content: "✓";
         position: absolute;
         left: 2rem;
         top: 50%;
         transform: translateY(-50%);
         font-size: 2.5rem;
         color: #28a745;
      }

      @keyframes slideIn {
         from {
            opacity: 0;
            transform: translateY(-20px);
         }
         to {
            opacity: 1;
            transform: translateY(0);
         }
      }

      /* Auto-hide message after 5 seconds */
      .message.auto-hide {
         animation: slideIn 0.5s ease-out, slideOut 0.5s ease-in 4.5s forwards;
      }

      @keyframes slideOut {
         from {
            opacity: 1;
            transform: translateY(0);
         }
         to {
            opacity: 0;
            transform: translateY(-20px);
         }
      }

      /* Success message specific styles */
      .message.success {
         background: linear-gradient(135deg, #d4edda, #c3e6cb);
         border-color: #28a745;
         color: #155724;
      }

      .message.warning {
         background: linear-gradient(135deg, #fff3cd, #ffeaa7);
         border-color: #ffc107;
         color: #856404;
      }

      .message.warning::before {
         content: "⚠";
         color: #ffc107;
      }

      .message.error {
         background: linear-gradient(135deg, #f8d7da, #f5c6cb);
         border-color: #dc3545;
         color: #721c24;
      }

      .message.error::before {
         content: "⚠";
         color: #dc3545;
      }

      /* Email notification status */
      .email-status {
         font-size: 1.2rem;
         padding: 0.3rem 0.8rem;
         border-radius: 12px;
         display: inline-block;
         margin-left: 0.5rem;
      }

      .email-sent {
         background: #d4edda;
         color: #155724;
         border: 1px solid #c3e6cb;
      }

      .email-failed {
         background: #f8d7da;
         color: #721c24;
         border: 1px solid #f5c6cb;
      }
   </style>
</head>
<body>

<?php include '../components/admin_header.php'; ?>

<section class="placed-orders">
   <h1 class="heading">Placed Orders</h1>

   <?php
   if(!empty($messages)){
      foreach($messages as $message){
         $message_text = is_array($message) ? $message['text'] : $message;
         $message_type = is_array($message) ? $message['type'] : 'success';
         echo '<div class="message '.$message_type.' auto-hide">'.$message_text.'</div>';
      }
   }
   ?>

   <!-- Search Container -->
   <div class="search-container">
      <form action="" method="POST" class="search-form">
         <input type="text" name="search_term" class="search-input" 
                placeholder="Search by customer name or GCash reference number..." 
                value="<?= htmlspecialchars($search_query) ?>">
         <button type="submit" name="search" class="search-btn">
            <i class="fas fa-search"></i> Search
         </button>
         <a href="placed_orders.php" class="clear-btn">
            <i class="fas fa-times"></i> Clear
         </a>
      </form>
      <?php if (!empty($search_query)): ?>
         <div class="search-info">
            <i class="fas fa-info-circle"></i> 
            Showing results for: "<strong><?= htmlspecialchars($search_query) ?></strong>"
         </div>
      <?php endif; ?>
   </div>

   <div class="orders-wrapper">
      <table class="orders-table">
         <thead>
            <tr>
               <th>Order ID</th>
               <th>Customer</th>
               <th>Method</th>
               <th>GCash Ref</th>
               <th>Placed On</th>
               <th>Delivery</th>
               <th>Order Status</th>
               <th class="actions-cell">Actions</th>
            </tr>
         </thead>
         
         <tbody>
         <?php
            // Build the SQL query with search functionality
            $sql = "SELECT * FROM `orders` " . $where_clause . " ORDER BY placed_on DESC";
            $select_orders = $conn->prepare($sql);
            
            if (!empty($search_query)) {
               $search_param = "%{$search_query}%";
               $select_orders->execute([$search_param, $search_param]);
            } else {
               $select_orders->execute();
            }
            
            if ($select_orders->rowCount() > 0) {
               while ($order = $select_orders->fetch(PDO::FETCH_ASSOC)) {
                  $placed_on = date('d M Y, h:i A', strtotime($order['placed_on']));
                  $expected_delivery = empty($order['expected_delivery_date']) ? 'Not set' : date('d M Y', strtotime($order['expected_delivery_date']));

                  // Order status classes
                  $order_status = $order['status'] ?? 'pending';
                  $order_status_class = 'status-' . $order_status;
         ?>
            <tr>
               <td class="order-id"><?= htmlspecialchars($order['id']) ?></td>
               <td><?= htmlspecialchars($order['name']) ?></td>
               <td><?= htmlspecialchars($order['method']) ?></td>
               <td>
                  <?php if (!empty($order['gcash_ref'])): ?>
                     <span style="font-weight: bold; color: #27ae60;"><?= htmlspecialchars($order['gcash_ref']) ?></span>
                  <?php else: ?>
                     <span style="color: #999;">N/A</span>
                  <?php endif; ?>
               </td>
               <td><?= $placed_on ?></td>
               <td><?= $expected_delivery ?></td>
               <td>
                  <span class="badge <?= $order_status_class ?>">
                     <?= ucfirst($order_status) ?>
                  </span>
               </td>
               <td>
                  <form action="" method="POST" class="inline-form">
                     <input type="hidden" name="order_id" value="<?= $order['id']; ?>">
                     
                     <select name="order_status">
                        <option value="pending" <?= $order_status == 'pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="Pre Order" <?= $order_status == 'Pre Order' ? 'selected' : '' ?>>Pre Order</option>
                        <option value="To Received" <?= $order_status == 'To Received' ? 'selected' : '' ?>>To Received</option>
                        <option value="delivered" <?= $order_status == 'delivered' ? 'selected' : '' ?>>Delivered</option>

                     </select>
                     
                     <input type="date" name="expected_delivery_date" value="<?= $order['expected_delivery_date']; ?>" min="<?= date('Y-m-d'); ?>">
                     <input type="submit" name="update_order" value="Update Order" class="btn">
                  </form>

                  <?php if (!empty($order['design_file'])): ?>
                     <a href="#" class="btn" style="background: #3498db; margin-top: 0.5rem; display: block;" onclick="viewDesign('<?= htmlspecialchars($order['design_file']) ?>')">
                        <i class="fas fa-eye"></i> View Design
                     </a>
                  <?php else: ?>
                     <span style="color: #999; font-size: 1.2rem; margin-top: 0.5rem; display: block;">No design uploaded</span>
                  <?php endif; ?>

                  <a href="placed_orders.php?delete=<?= $order['id']; ?>" class="delete-btn" onclick="return confirm('Delete this order?')">
                     <i class="fas fa-trash"></i> Delete Order
                  </a>
               </td>
            </tr>
         <?php
               }
            } else {
               if (!empty($search_query)) {
                  echo '<tr><td colspan="8">No orders found matching your search criteria.</td></tr>';
               } else {
                  echo '<tr><td colspan="8">No orders placed yet!</td></tr>';
               }
            }
         ?>
         </tbody>
      </table>
   </div>
</section>

<!-- Design View Modal -->
<div id="designModal" class="modal">
   <div class="modal-content">
      <span class="close">&times;</span>
      <h3 class="modal-title">Design Preview</h3>
      <img id="designImage" class="design-image" src="" alt="Design Preview">
   </div>
</div>

<script src="../js/admin_script.js"></script>
<script>
// Modal functionality
const modal = document.getElementById('designModal');
const modalImg = document.getElementById('designImage');
const closeBtn = document.getElementsByClassName('close')[0];

function viewDesign(filename) {
   modal.style.display = 'block';
   modalImg.src = '../uploaded_designs/' + filename;
}

// Close modal when clicking the X
closeBtn.onclick = function() {
   modal.style.display = 'none';
}

// Close modal when clicking outside of it
window.onclick = function(event) {
   if (event.target == modal) {
      modal.style.display = 'none';
   }
}

// Close modal with Escape key
document.addEventListener('keydown', function(event) {
   if (event.key === 'Escape') {
      modal.style.display = 'none';
   }
});

// Auto-hide success messages after 5 seconds
document.addEventListener('DOMContentLoaded', function() {
   const messages = document.querySelectorAll('.message.auto-hide');
   messages.forEach(message => {
      setTimeout(() => {
         message.style.display = 'none';
      }, 5000);
   });
});
</script>
</body>
</html>