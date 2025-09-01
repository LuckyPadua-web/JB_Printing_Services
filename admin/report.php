<?php
include '../components/connect.php';
session_start();

if(isset($_SESSION['admin_id'])){
   $admin_id = $_SESSION['admin_id'];
}else{
   $admin_id = '';
   header('location:admin_login.php');
   exit;
}

// Get current month and year
$current_month = date('Y-m');
$current_year = date('Y');

// Total Delivered Orders
$select_delivered = $conn->prepare("SELECT COUNT(*) as count, SUM(total_price) as total FROM `orders` WHERE payment_status = ?");
$select_delivered->execute(['delivered']);
$delivered_data = $select_delivered->fetch(PDO::FETCH_ASSOC);
$total_delivered = $delivered_data['count'];
$delivered_amount = $delivered_data['total'] ?? 0;

// Total Orders
$select_total_orders = $conn->prepare("SELECT COUNT(*) as count FROM `orders`");
$select_total_orders->execute();
$total_orders = $select_total_orders->fetch(PDO::FETCH_ASSOC)['count'];

// Total Pending Orders
$select_pending = $conn->prepare("SELECT COUNT(*) as count, SUM(total_price) as total FROM `orders` WHERE payment_status = ?");
$select_pending->execute(['pending']);
$pending_data = $select_pending->fetch(PDO::FETCH_ASSOC);
$total_pending = $pending_data['count'];
$pending_amount = $pending_data['total'] ?? 0;

// Total Pre Orders (assuming pre-order status exists)
$select_preorder = $conn->prepare("SELECT COUNT(*) as count, SUM(total_price) as total FROM `orders` WHERE payment_status = ?");
$select_preorder->execute(['pre-order']);
$preorder_data = $select_preorder->fetch(PDO::FETCH_ASSOC);
$total_preorder = $preorder_data['count'];
$preorder_amount = $preorder_data['total'] ?? 0;

// Total Sales for Current Month
$select_monthly_sales = $conn->prepare("SELECT SUM(total_price) as total FROM `orders` WHERE DATE_FORMAT(placed_on, '%Y-%m') = ? AND payment_status = 'delivered'");
$select_monthly_sales->execute([$current_month]);
$monthly_sales = $select_monthly_sales->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

// Total Customers
$select_customers = $conn->prepare("SELECT COUNT(*) as count FROM `users`");
$select_customers->execute();
$total_customers = $select_customers->fetch(PDO::FETCH_ASSOC)['count'];

// Monthly sales data for chart (last 6 months)
$monthly_chart_data = [];
$monthly_labels = [];
for($i = 5; $i >= 0; $i--) {
    $month = date('Y-m', strtotime("-$i months"));
    $label = date('M Y', strtotime("-$i months"));
    
    $select_month_sales = $conn->prepare("SELECT SUM(total_price) as total FROM `orders` WHERE DATE_FORMAT(placed_on, '%Y-%m') = ? AND payment_status = 'delivered'");
    $select_month_sales->execute([$month]);
    $month_sales = $select_month_sales->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
    
    $monthly_chart_data[] = $month_sales;
    $monthly_labels[] = $label;
}

// Recent orders for table
$select_recent_orders = $conn->prepare("SELECT * FROM `orders` ORDER BY placed_on DESC LIMIT 10");
$select_recent_orders->execute();
$recent_orders = $select_recent_orders->fetchAll(PDO::FETCH_ASSOC);

// Top products
$select_top_products = $conn->prepare("
    SELECT p.name, p.image, COUNT(o.id) as order_count, SUM(o.total_price) as total_sales 
    FROM `orders` o 
    JOIN `products` p ON FIND_IN_SET(p.name, o.total_products)
    WHERE o.payment_status = 'delivered'
    GROUP BY p.name 
    ORDER BY order_count DESC 
    LIMIT 5
");
$select_top_products->execute();
$top_products = $select_top_products->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Reports - Admin Panel</title>

   <!-- Font Awesome -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   
   <!-- Chart.js -->
   <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
   
   <!-- Custom CSS -->
   <link rel="stylesheet" href="../css/admin_style.css">

   <style>
      .reports-container {
         padding: 2rem;
         max-width: 1400px;
         margin: 0 auto;
      }

      .stats-grid {
         display: grid;
         grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
         gap: 2rem;
         margin-bottom: 3rem;
      }

      .stat-card {
         background: linear-gradient(135deg, var(--main-color), #6c5ce7);
         color: white;
         padding: 2rem;
         border-radius: 15px;
         box-shadow: 0 10px 30px rgba(0,0,0,0.1);
         position: relative;
         overflow: hidden;
         transition: transform 0.3s ease, box-shadow 0.3s ease;
      }

      .stat-card:hover {
         transform: translateY(-5px);
         box-shadow: 0 15px 40px rgba(0,0,0,0.15);
      }

      .stat-card.delivered {
         background: linear-gradient(135deg, #27ae60, #2ecc71);
      }

      .stat-card.pending {
         background: linear-gradient(135deg, #f39c12, #e67e22);
      }

      .stat-card.preorder {
         background: linear-gradient(135deg, #8e44ad, #9b59b6);
      }

      .stat-card.customers {
         background: linear-gradient(135deg, #3498db, #2980b9);
      }

      .stat-card.monthly {
         background: linear-gradient(135deg, #e74c3c, #c0392b);
      }

      .stat-card::before {
         content: '';
         position: absolute;
         top: 0;
         right: 0;
         width: 100px;
         height: 100px;
         background: rgba(255,255,255,0.1);
         border-radius: 50%;
         transform: translate(30px, -30px);
      }

      .stat-icon {
         font-size: 3rem;
         margin-bottom: 1rem;
         opacity: 0.9;
      }

      .stat-number {
         font-size: 2.5rem;
         font-weight: bold;
         margin-bottom: 0.5rem;
      }

      .stat-label {
         font-size: 1.4rem;
         opacity: 0.9;
         text-transform: uppercase;
         letter-spacing: 1px;
      }

      .stat-amount {
         font-size: 1.2rem;
         opacity: 0.8;
         margin-top: 0.5rem;
      }

      .charts-section {
         display: grid;
         grid-template-columns: 2fr 1fr;
         gap: 2rem;
         margin-bottom: 3rem;
      }

      .chart-container {
         background: white;
         padding: 2rem;
         border-radius: 15px;
         box-shadow: 0 5px 20px rgba(0,0,0,0.08);
      }

      .chart-title {
         font-size: 1.8rem;
         color: var(--black);
         margin-bottom: 2rem;
         text-align: center;
         font-weight: 600;
      }

      .tables-section {
         display: grid;
         grid-template-columns: 1fr 1fr;
         gap: 2rem;
      }

      .table-container {
         background: white;
         border-radius: 15px;
         box-shadow: 0 5px 20px rgba(0,0,0,0.08);
         overflow: hidden;
      }

      .table-header {
         background: var(--main-color);
         color: white;
         padding: 1.5rem 2rem;
         font-size: 1.6rem;
         font-weight: 600;
      }

      .table-content {
         padding: 1rem;
         max-height: 400px;
         overflow-y: auto;
      }

      .recent-order {
         display: flex;
         align-items: center;
         justify-content: space-between;
         padding: 1rem;
         border-bottom: 1px solid #f0f0f0;
         transition: background 0.3s ease;
      }

      .recent-order:hover {
         background: #f8f9fa;
      }

      .order-info h4 {
         color: var(--black);
         font-size: 1.4rem;
         margin-bottom: 0.5rem;
      }

      .order-info p {
         color: var(--light-color);
         font-size: 1.2rem;
      }

      .order-info {
         display: flex;
         flex-direction: column;
         gap: 0.3rem;
      }

      .order-info .status-badge {
         margin-top: 0.5rem;
         align-self: flex-start;
         min-width: 100px;
         text-align: center;
         white-space: nowrap;
      }

      .order-amount {
         font-weight: bold;
         color: var(--main-color);
         font-size: 1.4rem;
      }

      .product-item {
         display: flex;
         align-items: center;
         padding: 1rem;
         border-bottom: 1px solid #f0f0f0;
      }

      .product-img {
         width: 50px;
         height: 50px;
         border-radius: 8px;
         object-fit: cover;
         margin-right: 1rem;
      }

      .product-info h4 {
         color: var(--black);
         font-size: 1.3rem;
         margin-bottom: 0.3rem;
      }

      .product-info p {
         color: var(--light-color);
         font-size: 1.1rem;
      }

      .status-badge {
         padding: 0.3rem 1rem;
         border-radius: 20px;
         font-size: 1.1rem;
         font-weight: 500;
         text-transform: capitalize;
      }

      .status-delivered {
         background: #d4edda;
         color: #155724;
      }

      .status-pending {
         background: #fff3cd;
         color: #856404;
      }

      .status-pre-order {
         background: #e7e3ff;
         color: #6f42c1;
      }

      @media (max-width: 768px) {
         .charts-section,
         .tables-section {
            grid-template-columns: 1fr;
         }
         
         .stats-grid {
            grid-template-columns: 1fr;
         }
      }

      .summary-section {
         background: white;
         padding: 2rem;
         border-radius: 15px;
         box-shadow: 0 5px 20px rgba(0,0,0,0.08);
         margin-bottom: 2rem;
         text-align: center;
      }

      .summary-title {
         font-size: 2.2rem;
         color: var(--black);
         margin-bottom: 1rem;
         font-weight: 600;
      }

      .summary-stats {
         display: flex;
         justify-content: space-around;
         flex-wrap: wrap;
         gap: 2rem;
      }

      .summary-item {
         text-align: center;
      }

      .summary-value {
         font-size: 2rem;
         font-weight: bold;
         color: var(--main-color);
      }

      .summary-label {
         font-size: 1.2rem;
         color: var(--light-color);
         margin-top: 0.5rem;
      }
   </style>
</head>
<body>

<?php include '../components/admin_header.php'; ?>

<div class="reports-container">
   <h1 class="heading">📊 Business Reports & Analytics</h1>

   <!-- Summary Section -->
   <div class="summary-section">
      <h2 class="summary-title">Quick Overview</h2>
      <div class="summary-stats">
         <div class="summary-item">
            <div class="summary-value">₱<?= number_format($delivered_amount + $pending_amount + $preorder_amount, 2); ?></div>
            <div class="summary-label">Total Revenue</div>
         </div>
         <div class="summary-item">
            <div class="summary-value"><?= $total_orders; ?></div>
            <div class="summary-label">Total Orders</div>
         </div>
         <div class="summary-item">
            <div class="summary-value"><?= $total_customers; ?></div>
            <div class="summary-label">Total Customers</div>
         </div>
         <div class="summary-item">
            <div class="summary-value"><?= date('M Y'); ?></div>
            <div class="summary-label">Current Period</div>
         </div>
      </div>
   </div>

   <!-- Stats Grid -->
   <div class="stats-grid">
      <div class="stat-card delivered">
         <div class="stat-icon">
            <i class="fas fa-check-circle"></i>
         </div>
         <div class="stat-number"><?= $total_delivered; ?></div>
         <div class="stat-label">Total Delivered</div>
         <div class="stat-amount">₱<?= number_format($delivered_amount, 2); ?></div>
      </div>

      <div class="stat-card">
         <div class="stat-icon">
            <i class="fas fa-shopping-cart"></i>
         </div>
         <div class="stat-number"><?= $total_orders; ?></div>
         <div class="stat-label">Total Orders</div>
         <div class="stat-amount">All Time</div>
      </div>

      <div class="stat-card pending">
         <div class="stat-icon">
            <i class="fas fa-clock"></i>
         </div>
         <div class="stat-number"><?= $total_pending; ?></div>
         <div class="stat-label">Total Pending</div>
         <div class="stat-amount">₱<?= number_format($pending_amount, 2); ?></div>
      </div>

      <div class="stat-card preorder">
         <div class="stat-icon">
            <i class="fas fa-calendar-alt"></i>
         </div>
         <div class="stat-number"><?= $total_preorder; ?></div>
         <div class="stat-label">Total Pre Order</div>
         <div class="stat-amount">₱<?= number_format($preorder_amount, 2); ?></div>
      </div>

      <div class="stat-card monthly">
         <div class="stat-icon">
            <i class="fas fa-chart-line"></i>
         </div>
         <div class="stat-number">₱<?= number_format($monthly_sales, 2); ?></div>
         <div class="stat-label">Sales This Month</div>
         <div class="stat-amount"><?= date('F Y'); ?></div>
      </div>

      <div class="stat-card customers">
         <div class="stat-icon">
            <i class="fas fa-users"></i>
         </div>
         <div class="stat-number"><?= $total_customers; ?></div>
         <div class="stat-label">Total Customers</div>
         <div class="stat-amount">Registered Users</div>
      </div>
   </div>

   <!-- Charts Section -->
   <div class="charts-section">
      <div class="chart-container">
         <h3 class="chart-title">📈 Monthly Sales Trend (Last 6 Months)</h3>
         <canvas id="salesChart" width="400" height="200"></canvas>
      </div>
      
      <div class="chart-container">
         <h3 class="chart-title">📊 Order Status Distribution</h3>
         <canvas id="statusChart" width="300" height="300"></canvas>
      </div>
   </div>

   <!-- Tables Section -->
   <div class="tables-section">
      <div class="table-container">
         <div class="table-header">
            <i class="fas fa-history"></i> Recent Orders
         </div>
         <div class="table-content">
            <?php if(count($recent_orders) > 0): ?>
               <?php foreach($recent_orders as $order): ?>
                  <div class="recent-order">
                     <div class="order-info">
                        <h4><?= htmlspecialchars($order['name']); ?></h4>
                        <p><?= date('M d, Y', strtotime($order['placed_on'])); ?></p>
                        <span class="status-badge status-<?= strtolower(str_replace(' ', '-', $order['payment_status'])); ?>">
                           <?= $order['payment_status']; ?>
                        </span>
                     </div>
                     <div class="order-amount">₱<?= number_format($order['total_price'], 2); ?></div>
                  </div>
               <?php endforeach; ?>
            <?php else: ?>
               <p class="empty">No recent orders found!</p>
            <?php endif; ?>
         </div>
      </div>

      <div class="table-container">
         <div class="table-header">
            <i class="fas fa-star"></i> Top Products
         </div>
         <div class="table-content">
            <?php if(count($top_products) > 0): ?>
               <?php foreach($top_products as $product): ?>
                  <div class="product-item">
                     <img src="../uploaded_img/<?= $product['image']; ?>" alt="<?= htmlspecialchars($product['name']); ?>" class="product-img">
                     <div class="product-info">
                        <h4><?= htmlspecialchars($product['name']); ?></h4>
                        <p><?= $product['order_count']; ?> orders • ₱<?= number_format($product['total_sales'], 2); ?></p>
                     </div>
                  </div>
               <?php endforeach; ?>
            <?php else: ?>
               <p class="empty">No product data available!</p>
            <?php endif; ?>
         </div>
      </div>
   </div>
</div>

<script>
// Sales Chart
const salesCtx = document.getElementById('salesChart').getContext('2d');
const salesChart = new Chart(salesCtx, {
    type: 'line',
    data: {
        labels: <?= json_encode($monthly_labels); ?>,
        datasets: [{
            label: 'Monthly Sales (₱)',
            data: <?= json_encode($monthly_chart_data); ?>,
            borderColor: '#4834d4',
            backgroundColor: 'rgba(72, 52, 212, 0.1)',
            borderWidth: 3,
            fill: true,
            tension: 0.4,
            pointBackgroundColor: '#4834d4',
            pointBorderColor: '#fff',
            pointBorderWidth: 2,
            pointRadius: 6
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return '₱' + value.toLocaleString();
                    }
                }
            }
        }
    }
});

// Status Chart
const statusCtx = document.getElementById('statusChart').getContext('2d');
const statusChart = new Chart(statusCtx, {
    type: 'doughnut',
    data: {
        labels: ['Delivered', 'Pending', 'Pre-Order'],
        datasets: [{
            data: [<?= $total_delivered; ?>, <?= $total_pending; ?>, <?= $total_preorder; ?>],
            backgroundColor: [
                '#27ae60',
                '#f39c12',
                '#8e44ad'
            ],
            borderWidth: 0,
            hoverOffset: 4
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    padding: 20,
                    usePointStyle: true
                }
            }
        }
    }
});

// Add some interactive animations
document.addEventListener('DOMContentLoaded', function() {
    const statCards = document.querySelectorAll('.stat-card');
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    });

    statCards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = `opacity 0.6s ease ${index * 0.1}s, transform 0.6s ease ${index * 0.1}s`;
        observer.observe(card);
    });
});
</script>

<script src="../js/admin_script.js"></script>

</body>
</html>
