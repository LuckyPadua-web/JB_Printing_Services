<?php
include 'components/connect.php';

echo "<h2>Testing Messaging System Database Tables</h2>";

try {
    // Test conversations table
    $test_conversations = $conn->prepare("SELECT COUNT(*) as count FROM conversations");
    $test_conversations->execute();
    $conv_count = $test_conversations->fetch(PDO::FETCH_ASSOC);
    echo "<p>✓ Conversations table exists - Records: " . $conv_count['count'] . "</p>";

    // Test conversation_messages table
    $test_messages = $conn->prepare("SELECT COUNT(*) as count FROM conversation_messages");
    $test_messages->execute();
    $msg_count = $test_messages->fetch(PDO::FETCH_ASSOC);
    echo "<p>✓ Conversation messages table exists - Records: " . $msg_count['count'] . "</p>";

    // Test users table
    $test_users = $conn->prepare("SELECT COUNT(*) as count FROM users");
    $test_users->execute();
    $user_count = $test_users->fetch(PDO::FETCH_ASSOC);
    echo "<p>✓ Users table exists - Records: " . $user_count['count'] . "</p>";

    // Test admin table
    $test_admin = $conn->prepare("SELECT COUNT(*) as count FROM admin");
    $test_admin->execute();
    $admin_count = $test_admin->fetch(PDO::FETCH_ASSOC);
    echo "<p>✓ Admin table exists - Records: " . $admin_count['count'] . "</p>";

    echo "<h3>Database Setup Complete!</h3>";
    echo "<p>The messaging system is ready to use.</p>";
    echo "<p><a href='contact.php'>Test User Contact Page</a> | <a href='admin/messages.php'>Test Admin Messages Page</a></p>";

} catch(PDOException $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}
?>
