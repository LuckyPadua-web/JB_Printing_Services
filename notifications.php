<?php
include 'components/connect.php';
session_start();

if(!isset($_SESSION['user_id'])){
    header('location:login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Mark notifications as read when viewed
if(isset($_GET['mark_read'])){
    $update_notifications = $conn->prepare("UPDATE `notifications` SET is_read = 1 WHERE user_id = ?");
    $update_notifications->execute([$user_id]);
    header('location:notifications.php');
    exit;
}

// Fetch user notifications
$select_notifications = $conn->prepare("
    SELECT n.*, o.id as order_id 
    FROM `notifications` n 
    LEFT JOIN `orders` o ON n.order_id = o.id 
    WHERE n.user_id = ? 
    ORDER BY n.created_at DESC
");
$select_notifications->execute([$user_id]);
$notifications = $select_notifications->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php include 'components/user_header.php'; ?>

<section class="notifications">
    <h1 class="heading">Notifications</h1>
    
    <div class="box-container">
        <?php if(count($notifications) > 0): ?>
            <div class="header-actions">
                <a href="notifications.php?mark_read=all" class="btn">Mark All as Read</a>
            </div>
            
            <?php foreach($notifications as $notification): ?>
            <div class="notification-box <?= $notification['is_read'] ? 'read' : 'unread'; ?>">
                <div class="content">
                    <p><?= $notification['message'] ?></p>
                    <span class="date"><?= date('M j, Y g:i A', strtotime($notification['created_at'])) ?></span>
                    <?php if($notification['order_id']): ?>
                    <a href="order_details.php?order_id=<?= $notification['order_id'] ?>" class="btn">View Order</a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="empty">No notifications yet.</p>
        <?php endif; ?>
    </div>
</section>

</body>
</html>