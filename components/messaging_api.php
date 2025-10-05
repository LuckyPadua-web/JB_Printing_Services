<?php
// Handle different include paths depending on where the API is called from
if (file_exists('../components/connect.php')) {
    include '../components/connect.php';
} elseif (file_exists('components/connect.php')) {
    include 'components/connect.php';
} else {
    include 'connect.php';
}

session_start();

header('Content-Type: application/json');

$action = $_POST['action'] ?? $_GET['action'] ?? '';

try {
    switch ($action) {
        case 'send_message':
            $conversation_id = $_POST['conversation_id'];
            $message = trim($_POST['message']);
            $sender_type = $_POST['sender_type']; // 'user' or 'admin'
            $sender_id = $_POST['sender_id'];

            if (empty($message)) {
                throw new Exception('Message cannot be empty');
            }

            // Insert message
            $insert_message = $conn->prepare("
                INSERT INTO conversation_messages (conversation_id, sender_type, sender_id, message, created_at) 
                VALUES (?, ?, ?, ?, NOW())
            ");
            $insert_message->execute([$conversation_id, $sender_type, $sender_id, $message]);

            // Update conversation timestamp
            $update_conversation = $conn->prepare("UPDATE conversations SET updated_at = NOW() WHERE id = ?");
            $update_conversation->execute([$conversation_id]);

            echo json_encode(['success' => true, 'message' => 'Message sent successfully']);
            break;

        case 'get_messages':
            $conversation_id = $_GET['conversation_id'];
            $last_message_id = $_GET['last_message_id'] ?? 0;

            if (empty($conversation_id)) {
                throw new Exception('Missing conversation_id parameter');
            }

            $query = "
                SELECT cm.*, 
                       CASE 
                           WHEN cm.sender_type = 'user' THEN u.name 
                           WHEN cm.sender_type = 'admin' THEN a.name 
                       END as sender_name
                FROM conversation_messages cm
                LEFT JOIN users u ON cm.sender_type = 'user' AND cm.sender_id = u.id
                LEFT JOIN admin a ON cm.sender_type = 'admin' AND cm.sender_id = a.id
                WHERE cm.conversation_id = ? AND cm.id > ?
                ORDER BY cm.created_at ASC
            ";
            
            $get_messages = $conn->prepare($query);
            $get_messages->execute([$conversation_id, $last_message_id]);
            $messages = $get_messages->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode([
                'success' => true, 
                'messages' => $messages,
                'debug' => [
                    'conversation_id' => $conversation_id,
                    'last_message_id' => $last_message_id,
                    'count' => count($messages)
                ]
            ]);
            break;

        case 'get_conversations':
            $user_type = $_GET['user_type']; // 'admin' or 'user'
            $user_id = $_GET['user_id'];

            if (empty($user_type) || empty($user_id)) {
                throw new Exception('Missing user_type or user_id parameter');
            }

            if ($user_type === 'admin') {
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
            } else {
                $query = "
                    SELECT c.*, 
                           (SELECT message FROM conversation_messages WHERE conversation_id = c.id ORDER BY created_at DESC LIMIT 1) as last_message,
                           (SELECT created_at FROM conversation_messages WHERE conversation_id = c.id ORDER BY created_at DESC LIMIT 1) as last_message_time,
                           (SELECT COUNT(*) FROM conversation_messages WHERE conversation_id = c.id AND sender_type = 'admin' AND is_read = 0) as unread_count
                    FROM conversations c
                    WHERE c.user_id = ?
                    ORDER BY c.updated_at DESC
                ";
                $get_conversations = $conn->prepare($query);
                $get_conversations->execute([$user_id]);
            }

            $conversations = $get_conversations->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode([
                'success' => true, 
                'conversations' => $conversations,
                'debug' => [
                    'user_type' => $user_type,
                    'user_id' => $user_id,
                    'count' => count($conversations)
                ]
            ]);
            break;

        case 'create_conversation':
            $user_id = $_POST['user_id'];
            $admin_id = $_POST['admin_id'] ?? 1;

            // Check if conversation already exists
            $check_conversation = $conn->prepare("SELECT id FROM conversations WHERE user_id = ? AND admin_id = ?");
            $check_conversation->execute([$user_id, $admin_id]);
            
            if ($check_conversation->rowCount() > 0) {
                $conversation = $check_conversation->fetch(PDO::FETCH_ASSOC);
                echo json_encode(['success' => true, 'conversation_id' => $conversation['id'], 'existing' => true]);
            } else {
                $create_conversation = $conn->prepare("INSERT INTO conversations (user_id, admin_id) VALUES (?, ?)");
                $create_conversation->execute([$user_id, $admin_id]);
                $conversation_id = $conn->lastInsertId();
                echo json_encode(['success' => true, 'conversation_id' => $conversation_id, 'existing' => false]);
            }
            break;

        case 'mark_read':
            $conversation_id = $_POST['conversation_id'];
            $sender_type = $_POST['sender_type']; // Mark messages from this sender type as read
            
            $mark_read = $conn->prepare("
                UPDATE conversation_messages 
                SET is_read = 1 
                WHERE conversation_id = ? AND sender_type = ?
            ");
            $mark_read->execute([$conversation_id, $sender_type]);
            
            echo json_encode(['success' => true]);
            break;

        default:
            throw new Exception('Invalid action');
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
