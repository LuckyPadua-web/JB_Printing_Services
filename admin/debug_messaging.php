<?php
echo "<h2>Debug Messaging API</h2>";

// Test the API from admin context
echo "<h3>Testing from Admin Context</h3>";
echo "<p>Current directory: " . getcwd() . "</p>";

// Test include paths
$paths = [
    '../components/connect.php',
    'components/connect.php', 
    'connect.php'
];

foreach ($paths as $path) {
    echo "<p>Testing path: $path - " . (file_exists($path) ? "EXISTS" : "NOT FOUND") . "</p>";
}

// Test actual connection
try {
    include '../components/connect.php';
    echo "<p>✓ Database connection successful</p>";
    
    // Test conversations query
    $test_query = $conn->prepare("SELECT COUNT(*) as count FROM conversations");
    $test_query->execute();
    $result = $test_query->fetch(PDO::FETCH_ASSOC);
    echo "<p>✓ Conversations table accessible - Count: " . $result['count'] . "</p>";
    
    // Test admin user
    if (isset($_GET['admin_id'])) {
        $admin_id = $_GET['admin_id'];
        echo "<p>Admin ID from GET: $admin_id</p>";
        
        // Test get_conversations query
        $query = "
            SELECT c.*, u.name as user_name, u.email as user_email,
                   (SELECT message FROM conversation_messages WHERE conversation_id = c.id ORDER BY created_at DESC LIMIT 1) as last_message,
                   (SELECT created_at FROM conversation_messages WHERE conversation_id = c.id ORDER BY created_at DESC LIMIT 1) as last_message_time,
                   (SELECT COUNT(*) FROM conversation_messages WHERE conversation_id = c.id AND sender_type = 'user' AND is_read = 0) as unread_count
            FROM conversations c
            JOIN users u ON c.user_id = u.id
            ORDER BY c.updated_at DESC
        ";
        $get_conversations = $conn->prepare($query);
        $get_conversations->execute();
        $conversations = $get_conversations->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<p>✓ Conversations query successful - Found: " . count($conversations) . " conversations</p>";
        echo "<pre>" . print_r($conversations, true) . "</pre>";
    } else {
        echo "<p>Add ?admin_id=1 to test with admin ID</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}
?>
