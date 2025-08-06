<?php
include '../components/connect.php';
session_start();

if (isset($_SESSION['admin_id'])) {
   $admin_id = $_SESSION['admin_id'];
} else {
   $admin_id = '';
   header('location:admin_login.php');
   exit;
}

// Update payment status and delivery date
if (isset($_POST['update_order'])) {
   $order_id = $_POST['order_id'];
   $payment_status = $_POST['payment_status'];
   $expected_delivery_date = $_POST['expected_delivery_date'];
   
   $update_order = $conn->prepare("UPDATE `orders` SET payment_status = ?, expected_delivery_date = ? WHERE id = ?");
   $update_order->execute([$payment_status, $expected_delivery_date, $order_id]);
   $message[] = 'Order updated successfully!';
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
      }

      .badge.pending { background: #eafce3; color: #2ecc71; }
      .badge.preorder { background: #fff6db; color: #f39c12; }
      .badge.received { background: #dff0ff; color: #3498db; }
      .badge.delivered { background: #ffe5e5; color: #e74c3c; }

      form.inline-form {
         display: flex;
         flex-direction: column;
         gap: 0.5rem;
      }

      form.inline-form select,
      form.inline-form input[type="date"] {
         padding: 0.5rem;
         font-size: 1.3rem;
      }

      .btn, .delete-btn {
         display: inline-block;
         padding: 0.6rem 1.2rem;
         font-size: 1.3rem;
         color: #fff;
         border: none;
         border-radius: 5px;
         cursor: pointer;
      }

      .btn {
         background: #27ae60;
      }

      .delete-btn {
         background: #e74c3c;
         text-decoration: none;
         margin-top: 0.5rem;
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
   </style>
</head>
<body>

<?php include '../components/admin_header.php'; ?>

<section class="placed-orders">
   <h1 class="heading">Placed Orders</h1>

   <div class="orders-wrapper">
      <table class="orders-table">
         <thead>
            <tr>
               <th>Order ID</th>
               <th>Customer</th>
               <th>Method</th>
               <th>Placed On</th>
               <th>Delivery</th>
               <th>Status</th>
               <th class="actions-cell">Actions</th>
            </tr>
         </thead>
         
         <tbody>
         <?php
            $select_orders = $conn->prepare("SELECT * FROM `orders` ORDER BY placed_on DESC");
            $select_orders->execute();
            if ($select_orders->rowCount() > 0) {
               while ($order = $select_orders->fetch(PDO::FETCH_ASSOC)) {
                  $placed_on = date('d M Y, h:i A', strtotime($order['placed_on']));
                  $expected_delivery = empty($order['expected_delivery_date']) ? 'Not set' : date('d M Y', strtotime($order['expected_delivery_date']));

                  $status_class = 'Pending';
                  if ($order['payment_status'] == 'Pre Order') $status_class = 'preorder';
                  elseif ($order['payment_status'] == 'To received') $status_class = 'received';
                  elseif ($order['payment_status'] == 'Delivered') $status_class = 'delivered';
         ?>
            <tr>
               <td class="order-id"><?= htmlspecialchars($order['id']) ?></td>
               <td><?= htmlspecialchars($order['name']) ?></td>
               <td><?= htmlspecialchars($order['method']) ?></td>
               <td><?= $placed_on ?></td>
               <td><?= $expected_delivery ?></td>
               <td><span class="badge <?= $status_class ?>"><?= htmlspecialchars($order['payment_status']) ?></span></td>
               <td>
                  <form action="" method="POST" class="inline-form">
                     <input type="hidden" name="order_id" value="<?= $order['id']; ?>">
                     <select name="payment_status">
                        <option disabled selected>Update status</option>
                        <option value="Pending" <?= $order['payment_status'] == 'Pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="Pre Order" <?= $order['payment_status'] == 'Pre Order' ? 'selected' : '' ?>>Pre Order</option>
                        <option value="To received" <?= $order['payment_status'] == 'To received' ? 'selected' : '' ?>>To received</option>
                        <option value="Delivered" <?= $order['payment_status'] == 'Delivered' ? 'selected' : '' ?>>Delivered</option>
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

                  <a href="placed_orders.php?delete=<?= $order['id']; ?>" class="delete-btn" onclick="return confirm('Delete this order?')">Delete</a>
               </td>
            </tr>
         <?php
               }
            } else {
               echo '<tr><td colspan="7">No orders placed yet!</td></tr>';
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
</script>
</body>
</html>
