<?php
include '../components/connect.php';
session_start();

header('Content-Type: application/json');

$action = $_GET['action'] ?? 'test';

try {
    if ($action === 'test_conversations') {
        // Simple test for admin conversations
        $query = "
            SELECT c.*, u.name as user_name, u.email as user_email
            FROM conversations c
            JOIN users u ON c.user_id = u.id
            ORDER BY c.updated_at DESC
            LIMIT 5
        ";
        $get_conversations = $conn->prepare($query);
        $get_conversations->execute();
        $conversations = $get_conversations->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode([
            'success' => true, 
            'count' => count($conversations),
            'conversations' => $conversations,
            'debug' => 'Simple conversation query worked'
        ]);
        
    } elseif ($action === 'test_messages') {
        $conversation_id = $_GET['conversation_id'] ?? 1;
        
        $query = "SELECT * FROM conversation_messages WHERE conversation_id = ? ORDER BY created_at ASC LIMIT 10";
        $get_messages = $conn->prepare($query);
        $get_messages->execute([$conversation_id]);
        $messages = $get_messages->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode([
            'success' => true,
            'count' => count($messages), 
            'messages' => $messages,
            'conversation_id' => $conversation_id
        ]);
        
    } else {
        echo json_encode(['success' => true, 'message' => 'API is working', 'action' => $action]);
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
