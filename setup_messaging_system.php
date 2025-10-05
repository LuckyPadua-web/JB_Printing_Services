<?php
// Create new messaging system tables

include 'components/connect.php';

try {
    // Create conversations table
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
    echo "Conversations table created successfully.<br>";

    // Create conversation_messages table
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
    echo "Conversation messages table created successfully.<br>";

    echo "Messaging system tables created successfully!";

} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
