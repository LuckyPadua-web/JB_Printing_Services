<?php
include 'components/connect.php';

echo "<h2>Creating Sample Messaging Data</h2>";

try {
    // First ensure we have users and admin
    $check_users = $conn->query("SELECT COUNT(*) as count FROM users")->fetch(PDO::FETCH_ASSOC);
    $check_admin = $conn->query("SELECT COUNT(*) as count FROM admin")->fetch(PDO::FETCH_ASSOC);
    
    echo "Found " . $check_users['count'] . " users and " . $check_admin['count'] . " admins.<br>";
    
    if ($check_users['count'] == 0) {
        echo "Creating sample user...<br>";
        $create_user = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
        $create_user->execute(['Test User', 'test@example.com', sha1('password')]);
        echo "Sample user created.<br>";
    }
    
    if ($check_admin['count'] == 0) {
        echo "Creating sample admin...<br>";
        $create_admin = $conn->prepare("INSERT INTO admin (name, password) VALUES (?, ?)");
        $create_admin->execute(['admin', sha1('admin')]);
        echo "Sample admin created.<br>";
    }
    
    // Get user and admin IDs
    $user = $conn->query("SELECT id FROM users LIMIT 1")->fetch(PDO::FETCH_ASSOC);
    $admin = $conn->query("SELECT id FROM admin LIMIT 1")->fetch(PDO::FETCH_ASSOC);
    
    if (!$user || !$admin) {
        throw new Exception("Could not find user or admin records");
    }
    
    $user_id = $user['id'];
    $admin_id = $admin['id'];
    
    echo "Using User ID: " . $user_id . " and Admin ID: " . $admin_id . "<br>";
    
    // Create or find conversation
    $check_conversation = $conn->prepare("SELECT id FROM conversations WHERE user_id = ? AND admin_id = ?");
    $check_conversation->execute([$user_id, $admin_id]);
    
    if ($check_conversation->rowCount() > 0) {
        $conversation = $check_conversation->fetch(PDO::FETCH_ASSOC);
        $conversation_id = $conversation['id'];
        echo "Using existing conversation ID: " . $conversation_id . "<br>";
    } else {
        $create_conversation = $conn->prepare("INSERT INTO conversations (user_id, admin_id) VALUES (?, ?)");
        $create_conversation->execute([$user_id, $admin_id]);
        $conversation_id = $conn->lastInsertId();
        echo "Created new conversation ID: " . $conversation_id . "<br>";
    }
    
    // Create sample messages
    $messages = [
        ['sender_type' => 'user', 'sender_id' => $user_id, 'message' => 'Hello, I need help with my printing order.'],
        ['sender_type' => 'admin', 'sender_id' => $admin_id, 'message' => 'Hi! I\'d be happy to help you. What specific issue are you having?'],
        ['sender_type' => 'user', 'sender_id' => $user_id, 'message' => 'I placed an order yesterday but haven\'t received any confirmation email.'],
        ['sender_type' => 'admin', 'sender_id' => $admin_id, 'message' => 'Let me check that for you. Can you provide your order number or the email address you used?'],
        ['sender_type' => 'user', 'sender_id' => $user_id, 'message' => 'I used test@example.com for the order.']
    ];
    
    $insert_message = $conn->prepare("
        INSERT INTO conversation_messages (conversation_id, sender_type, sender_id, message, created_at) 
        VALUES (?, ?, ?, ?, NOW() - INTERVAL ? MINUTE)
    ");
    
    foreach ($messages as $index => $msg) {
        $minutes_ago = (count($messages) - $index) * 5; // Space messages 5 minutes apart
        $insert_message->execute([
            $conversation_id, 
            $msg['sender_type'], 
            $msg['sender_id'], 
            $msg['message'],
            $minutes_ago
        ]);
    }
    
    echo "Created " . count($messages) . " sample messages.<br>";
    echo "<br><strong>Sample data creation completed successfully!</strong><br>";
    echo "<a href='debug_messaging.php'>← Back to Debug Tool</a> | ";
    echo "<a href='admin/messages.php'>View Admin Messages</a> | ";
    echo "<a href='contact.php'>View User Contact</a>";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "<br>";
}
?>
