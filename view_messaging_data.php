<?php
include 'components/connect.php';

echo "<h2>Current Messaging System Data</h2>";

try {
    // Show conversations
    echo "<h3>Conversations</h3>";
    $conversations = $conn->query("
        SELECT c.*, u.name as user_name, u.email as user_email, a.name as admin_name 
        FROM conversations c 
        LEFT JOIN users u ON c.user_id = u.id 
        LEFT JOIN admin a ON c.admin_id = a.id 
        ORDER BY c.updated_at DESC
    ");
    
    if ($conversations->rowCount() > 0) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>ID</th><th>User</th><th>Admin</th><th>Created</th><th>Updated</th></tr>";
        while ($conv = $conversations->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>";
            echo "<td>" . $conv['id'] . "</td>";
            echo "<td>" . ($conv['user_name'] ?? 'Unknown') . " (" . ($conv['user_email'] ?? 'N/A') . ")</td>";
            echo "<td>" . ($conv['admin_name'] ?? 'Unknown') . "</td>";
            echo "<td>" . $conv['created_at'] . "</td>";
            echo "<td>" . $conv['updated_at'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "No conversations found.<br>";
    }
    
    echo "<br><h3>Messages</h3>";
    $messages = $conn->query("
        SELECT cm.*, 
               CASE 
                   WHEN cm.sender_type = 'user' THEN u.name 
                   WHEN cm.sender_type = 'admin' THEN a.name 
               END as sender_name
        FROM conversation_messages cm
        LEFT JOIN users u ON cm.sender_type = 'user' AND cm.sender_id = u.id
        LEFT JOIN admin a ON cm.sender_type = 'admin' AND cm.sender_id = a.id
        ORDER BY cm.created_at DESC
        LIMIT 20
    ");
    
    if ($messages->rowCount() > 0) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>ID</th><th>Conv ID</th><th>Sender</th><th>Type</th><th>Message</th><th>Read</th><th>Created</th></tr>";
        while ($msg = $messages->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>";
            echo "<td>" . $msg['id'] . "</td>";
            echo "<td>" . $msg['conversation_id'] . "</td>";
            echo "<td>" . ($msg['sender_name'] ?? 'Unknown') . "</td>";
            echo "<td>" . $msg['sender_type'] . "</td>";
            echo "<td>" . htmlspecialchars(substr($msg['message'], 0, 50)) . (strlen($msg['message']) > 50 ? '...' : '') . "</td>";
            echo "<td>" . ($msg['is_read'] ? 'Yes' : 'No') . "</td>";
            echo "<td>" . $msg['created_at'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        echo "<br><em>Showing latest 20 messages</em>";
    } else {
        echo "No messages found.<br>";
    }
    
    echo "<br><h3>Statistics</h3>";
    $stats = $conn->query("
        SELECT 
            (SELECT COUNT(*) FROM conversations) as total_conversations,
            (SELECT COUNT(*) FROM conversation_messages) as total_messages,
            (SELECT COUNT(*) FROM conversation_messages WHERE is_read = 0) as unread_messages,
            (SELECT COUNT(*) FROM users) as total_users,
            (SELECT COUNT(*) FROM admin) as total_admins
    ")->fetch(PDO::FETCH_ASSOC);
    
    echo "<ul>";
    echo "<li>Total Conversations: " . $stats['total_conversations'] . "</li>";
    echo "<li>Total Messages: " . $stats['total_messages'] . "</li>";
    echo "<li>Unread Messages: " . $stats['unread_messages'] . "</li>";
    echo "<li>Total Users: " . $stats['total_users'] . "</li>";
    echo "<li>Total Admins: " . $stats['total_admins'] . "</li>";
    echo "</ul>";
    
    echo "<br><a href='debug_messaging.php'>← Back to Debug Tool</a>";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "<br>";
}
?>
