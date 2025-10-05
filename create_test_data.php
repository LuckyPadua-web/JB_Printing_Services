<?php
include 'components/connect.php';

echo "<h2>Creating Test Data for Messaging System</h2>";

try {
    // Check if we have users
    $check_users = $conn->prepare("SELECT COUNT(*) as count FROM users");
    $check_users->execute();
    $user_count = $check_users->fetch(PDO::FETCH_ASSOC);
    
    if ($user_count['count'] == 0) {
        echo "<p>No users found. Creating a test user...</p>";
        
        $insert_user = $conn->prepare("
            INSERT INTO users (name, email, password, number) 
            VALUES ('Test User', 'testuser@example.com', 'password123', '1234567890')
        ");
        $insert_user->execute();
        $test_user_id = $conn->lastInsertId();
        echo "<p>✓ Test user created with ID: $test_user_id</p>";
    } else {
        // Get first user
        $get_user = $conn->prepare("SELECT id FROM users LIMIT 1");
        $get_user->execute();
        $user = $get_user->fetch(PDO::FETCH_ASSOC);
        $test_user_id = $user['id'];
        echo "<p>✓ Using existing user ID: $test_user_id</p>";
    }
    
    // Check if we have admins
    $check_admin = $conn->prepare("SELECT COUNT(*) as count FROM admin");
    $check_admin->execute();
    $admin_count = $check_admin->fetch(PDO::FETCH_ASSOC);
    
    if ($admin_count['count'] == 0) {
        echo "<p>No admin found. Creating a test admin...</p>";
        
        $insert_admin = $conn->prepare("
            INSERT INTO admin (name, email, password) 
            VALUES ('Test Admin', 'admin@example.com', 'admin123')
        ");
        $insert_admin->execute();
        $test_admin_id = $conn->lastInsertId();
        echo "<p>✓ Test admin created with ID: $test_admin_id</p>";
    } else {
        // Get first admin
        $get_admin = $conn->prepare("SELECT id FROM admin LIMIT 1");
        $get_admin->execute();
        $admin = $get_admin->fetch(PDO::FETCH_ASSOC);
        $test_admin_id = $admin['id'];
        echo "<p>✓ Using existing admin ID: $test_admin_id</p>";
    }
    
    // Create a test conversation
    $check_conversation = $conn->prepare("SELECT id FROM conversations WHERE user_id = ? AND admin_id = ?");
    $check_conversation->execute([$test_user_id, $test_admin_id]);
    
    if ($check_conversation->rowCount() == 0) {
        $insert_conversation = $conn->prepare("
            INSERT INTO conversations (user_id, admin_id, created_at, updated_at) 
            VALUES (?, ?, NOW(), NOW())
        ");
        $insert_conversation->execute([$test_user_id, $test_admin_id]);
        $conversation_id = $conn->lastInsertId();
        echo "<p>✓ Test conversation created with ID: $conversation_id</p>";
    } else {
        $conversation = $check_conversation->fetch(PDO::FETCH_ASSOC);
        $conversation_id = $conversation['id'];
        echo "<p>✓ Using existing conversation ID: $conversation_id</p>";
    }
    
    // Create test messages
    $test_messages = [
        ['sender_type' => 'user', 'sender_id' => $test_user_id, 'message' => 'Hello, I need help with my printing order.'],
        ['sender_type' => 'admin', 'sender_id' => $test_admin_id, 'message' => 'Hello! I\'d be happy to help you. What specific issue are you having?'],
        ['sender_type' => 'user', 'sender_id' => $test_user_id, 'message' => 'I want to know about your business card printing services.'],
        ['sender_type' => 'admin', 'sender_id' => $test_admin_id, 'message' => 'We offer high-quality business card printing. What type of finish are you looking for?']
    ];
    
    foreach ($test_messages as $msg) {
        $check_msg = $conn->prepare("
            SELECT COUNT(*) as count FROM conversation_messages 
            WHERE conversation_id = ? AND message = ?
        ");
        $check_msg->execute([$conversation_id, $msg['message']]);
        $msg_exists = $check_msg->fetch(PDO::FETCH_ASSOC);
        
        if ($msg_exists['count'] == 0) {
            $insert_msg = $conn->prepare("
                INSERT INTO conversation_messages (conversation_id, sender_type, sender_id, message, created_at) 
                VALUES (?, ?, ?, ?, NOW())
            ");
            $insert_msg->execute([$conversation_id, $msg['sender_type'], $msg['sender_id'], $msg['message']]);
            echo "<p>✓ Added message: " . substr($msg['message'], 0, 50) . "...</p>";
        }
    }
    
    echo "<h3>✓ Test data creation complete!</h3>";
    echo "<p><a href='admin/messages.php'>Test Admin Messages</a> | <a href='contact.php'>Test User Contact</a></p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}
?>
