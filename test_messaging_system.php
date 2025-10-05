<?php
// Test messaging system functionality

include 'components/connect.php';

echo "<h2>Messaging System Diagnostic</h2>";

try {
    // Test database connection
    echo "<h3>1. Database Connection</h3>";
    echo "✓ Database connection successful<br><br>";
    
    // Check if messaging tables exist
    echo "<h3>2. Table Structure Check</h3>";
    
    // Check conversations table
    $check_conversations = $conn->query("SHOW TABLES LIKE 'conversations'");
    if ($check_conversations->rowCount() > 0) {
        echo "✓ Conversations table exists<br>";
        
        // Show table structure
        $describe = $conn->query("DESCRIBE conversations");
        echo "<strong>Conversations table structure:</strong><br>";
        while ($row = $describe->fetch(PDO::FETCH_ASSOC)) {
            echo "- " . $row['Field'] . " (" . $row['Type'] . ")<br>";
        }
    } else {
        echo "❌ Conversations table does not exist<br>";
        echo "<strong>Creating conversations table...</strong><br>";
        
        $create_conversations = "
        CREATE TABLE IF NOT EXISTS `conversations` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `user_id` int(11) NOT NULL,
            `admin_id` int(11) DEFAULT 1,
            `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
            `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            KEY `user_id` (`user_id`),
            KEY `admin_id` (`admin_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";
        
        $conn->exec($create_conversations);
        echo "✓ Conversations table created<br>";
    }
    
    echo "<br>";
    
    // Check conversation_messages table
    $check_messages = $conn->query("SHOW TABLES LIKE 'conversation_messages'");
    if ($check_messages->rowCount() > 0) {
        echo "✓ Conversation_messages table exists<br>";
        
        // Show table structure
        $describe = $conn->query("DESCRIBE conversation_messages");
        echo "<strong>Conversation_messages table structure:</strong><br>";
        while ($row = $describe->fetch(PDO::FETCH_ASSOC)) {
            echo "- " . $row['Field'] . " (" . $row['Type'] . ")<br>";
        }
    } else {
        echo "❌ Conversation_messages table does not exist<br>";
        echo "<strong>Creating conversation_messages table...</strong><br>";
        
        $create_messages = "
        CREATE TABLE IF NOT EXISTS `conversation_messages` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `conversation_id` int(11) NOT NULL,
            `sender_type` enum('user','admin') NOT NULL,
            `sender_id` int(11) NOT NULL,
            `message` text NOT NULL,
            `is_read` tinyint(1) DEFAULT 0,
            `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            KEY `conversation_id` (`conversation_id`),
            KEY `sender_id` (`sender_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";
        
        $conn->exec($create_messages);
        echo "✓ Conversation_messages table created<br>";
    }
    
    echo "<br>";
    
    // Check users table
    echo "<h3>3. Related Tables Check</h3>";
    $check_users = $conn->query("SHOW TABLES LIKE 'users'");
    if ($check_users->rowCount() > 0) {
        echo "✓ Users table exists<br>";
        $count_users = $conn->query("SELECT COUNT(*) FROM users")->fetchColumn();
        echo "- User count: " . $count_users . "<br>";
    } else {
        echo "❌ Users table does not exist<br>";
    }
    
    $check_admin = $conn->query("SHOW TABLES LIKE 'admin'");
    if ($check_admin->rowCount() > 0) {
        echo "✓ Admin table exists<br>";
        $count_admin = $conn->query("SELECT COUNT(*) FROM admin")->fetchColumn();
        echo "- Admin count: " . $count_admin . "<br>";
    } else {
        echo "❌ Admin table does not exist<br>";
    }
    
    echo "<br>";
    
    // Check existing conversations
    echo "<h3>4. Existing Data Check</h3>";
    $count_conversations = $conn->query("SELECT COUNT(*) FROM conversations")->fetchColumn();
    echo "Existing conversations: " . $count_conversations . "<br>";
    
    $count_messages = $conn->query("SELECT COUNT(*) FROM conversation_messages")->fetchColumn();
    echo "Existing messages: " . $count_messages . "<br>";
    
    if ($count_conversations > 0) {
        echo "<br><strong>Sample conversations:</strong><br>";
        $sample_conversations = $conn->query("
            SELECT c.*, u.name as user_name, u.email as user_email 
            FROM conversations c 
            LEFT JOIN users u ON c.user_id = u.id 
            LIMIT 5
        ");
        while ($conv = $sample_conversations->fetch(PDO::FETCH_ASSOC)) {
            echo "- Conversation ID: " . $conv['id'] . " | User: " . ($conv['user_name'] ?? 'Unknown') . " | Created: " . $conv['created_at'] . "<br>";
        }
    }
    
    echo "<br>";
    
    // Test API endpoints
    echo "<h3>5. API Endpoint Test</h3>";
    echo "Testing messaging API endpoints...<br>";
    
    // Test get_conversations for admin
    echo "<strong>Testing get_conversations for admin:</strong><br>";
    $_GET['action'] = 'get_conversations';
    $_GET['user_type'] = 'admin';
    $_GET['user_id'] = '1';
    
    ob_start();
    include 'components/messaging_api.php';
    $output = ob_get_clean();
    
    $data = json_decode($output, true);
    if ($data && $data['success']) {
        echo "✓ Admin conversations API working - Found " . count($data['conversations']) . " conversations<br>";
    } else {
        echo "❌ Admin conversations API failed: " . ($data['error'] ?? 'Unknown error') . "<br>";
    }
    
    // Clean up $_GET
    unset($_GET['action'], $_GET['user_type'], $_GET['user_id']);
    
    echo "<br><h3>6. Console Logs Suggestions</h3>";
    echo "To debug the frontend JavaScript:<br>";
    echo "1. Open browser developer tools (F12)<br>";
    echo "2. Go to Console tab<br>";
    echo "3. Look for errors when loading conversations or sending messages<br>";
    echo "4. Check Network tab for failed API requests<br>";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
}
?>
