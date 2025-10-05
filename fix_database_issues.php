<?php
include 'components/connect.php';

echo "<h2>Fixing Database Issues</h2>";

try {
    // First, let's check the current state
    echo "<h3>Current Database State</h3>";
    
    // Check conversations table structure
    $describe_conv = $conn->query("DESCRIBE conversations");
    echo "<strong>Conversations table structure:</strong><br>";
    while ($row = $describe_conv->fetch(PDO::FETCH_ASSOC)) {
        echo "- " . $row['Field'] . " (" . $row['Type'] . ") " . ($row['Key'] ? "[" . $row['Key'] . "]" : "") . "<br>";
    }
    
    echo "<br>";
    
    // Check for problematic data
    $bad_conversations = $conn->query("SELECT * FROM conversations WHERE id = '' OR id IS NULL");
    echo "Conversations with empty/null IDs: " . $bad_conversations->rowCount() . "<br>";
    
    $orphaned_messages = $conn->query("SELECT * FROM conversation_messages WHERE conversation_id = 0 OR conversation_id NOT IN (SELECT id FROM conversations WHERE id IS NOT NULL AND id != '')");
    echo "Orphaned messages: " . $orphaned_messages->rowCount() . "<br>";
    
    echo "<br><h3>Fixing Issues</h3>";
    
    // Fix 1: Clean up bad conversation records
    if ($bad_conversations->rowCount() > 0) {
        echo "Removing conversations with empty IDs...<br>";
        $conn->exec("DELETE FROM conversations WHERE id = '' OR id IS NULL");
        echo "✓ Cleaned up bad conversation records<br>";
    }
    
    // Fix 2: Recreate the conversations table with proper AUTO_INCREMENT
    echo "Recreating conversations table with proper structure...<br>";
    $conn->exec("DROP TABLE IF EXISTS conversations");
    $conn->exec("
    CREATE TABLE `conversations` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `user_id` int(11) NOT NULL,
        `admin_id` int(11) DEFAULT 1,
        `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
        `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        KEY `user_id` (`user_id`),
        KEY `admin_id` (`admin_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
    ");
    echo "✓ Recreated conversations table<br>";
    
    // Fix 3: Clean up orphaned messages
    echo "Cleaning up orphaned messages...<br>";
    $conn->exec("DELETE FROM conversation_messages WHERE conversation_id = 0 OR conversation_id NOT IN (SELECT id FROM conversations)");
    echo "✓ Cleaned up orphaned messages<br>";
    
    // Fix 4: Recreate the conversation for the existing user
    echo "Recreating conversation for existing user...<br>";
    $user_check = $conn->query("SELECT id FROM users WHERE email = 'luckypadua4@gmail.com'")->fetch(PDO::FETCH_ASSOC);
    if ($user_check) {
        $user_id = $user_check['id'];
        $admin_id = 1; // Default admin ID
        
        // Create new conversation
        $create_conv = $conn->prepare("INSERT INTO conversations (user_id, admin_id) VALUES (?, ?)");
        $create_conv->execute([$user_id, $admin_id]);
        $new_conversation_id = $conn->lastInsertId();
        
        echo "✓ Created new conversation ID: " . $new_conversation_id . " for user ID: " . $user_id . "<br>";
        
        // Create a welcome message
        $welcome_msg = $conn->prepare("
            INSERT INTO conversation_messages (conversation_id, sender_type, sender_id, message, created_at) 
            VALUES (?, 'admin', ?, 'Hello! Welcome to JB Printing Services. How can we help you today?', NOW())
        ");
        $welcome_msg->execute([$new_conversation_id, $admin_id]);
        echo "✓ Added welcome message<br>";
    }
    
    echo "<br><h3>Verification</h3>";
    
    // Verify the fixes
    $conv_count = $conn->query("SELECT COUNT(*) FROM conversations")->fetchColumn();
    $msg_count = $conn->query("SELECT COUNT(*) FROM conversation_messages")->fetchColumn();
    $orphaned_count = $conn->query("SELECT COUNT(*) FROM conversation_messages WHERE conversation_id NOT IN (SELECT id FROM conversations)")->fetchColumn();
    
    echo "Total conversations: " . $conv_count . "<br>";
    echo "Total messages: " . $msg_count . "<br>";
    echo "Orphaned messages: " . $orphaned_count . "<br>";
    
    if ($orphaned_count == 0) {
        echo "<br><strong>✅ All database issues have been fixed!</strong><br>";
    } else {
        echo "<br><strong>⚠️ Some orphaned messages still exist</strong><br>";
    }
    
    echo "<br><a href='debug_messaging.php'>← Back to Debug Tool</a> | ";
    echo "<a href='view_messaging_data.php'>View Updated Data</a>";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
}
?>
