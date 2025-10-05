<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Debug Admin Messaging</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .test { border: 1px solid #ddd; margin: 10px 0; padding: 15px; border-radius: 5px; }
        .success { background-color: #d4edda; border-color: #c3e6cb; }
        .error { background-color: #f8d7da; border-color: #f5c6cb; }
        .info { background-color: #e2e3e5; border-color: #d6d8db; }
        pre { background: #f8f9fa; padding: 10px; border-radius: 3px; overflow-x: auto; }
        button { background: #007bff; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; margin: 5px; }
        button:hover { background: #0056b3; }
    </style>
</head>
<body>
    <h1>Debug Admin Messaging System</h1>
    
    <div class="test info">
        <h3>Step 1: Test Admin Session</h3>
        <p>Admin ID: <?php 
            session_start();
            include '../components/connect.php';
            $admin_id = $_SESSION['admin_id'] ?? 'NOT SET';
            echo $admin_id;
        ?></p>
    </div>
    
    <div class="test">
        <h3>Step 2: Test API Connections</h3>
        <button onclick="testConversationsAPI()">Test Get Conversations</button>
        <button onclick="testMessagesAPI()">Test Get Messages</button>
        <div id="apiResults"></div>
    </div>
    
    <div class="test">
        <h3>Step 3: Database Check</h3>
        <?php
        try {
            // Check conversations
            $conv_query = $conn->prepare("
                SELECT c.*, u.name as user_name, u.email as user_email
                FROM conversations c
                JOIN users u ON c.user_id = u.id
                LIMIT 5
            ");
            $conv_query->execute();
            $conversations = $conv_query->fetchAll(PDO::FETCH_ASSOC);
            
            echo "<p><strong>Conversations found:</strong> " . count($conversations) . "</p>";
            if (count($conversations) > 0) {
                echo "<pre>" . print_r($conversations, true) . "</pre>";
            }
            
            // Check messages
            $msg_query = $conn->prepare("SELECT COUNT(*) as count FROM conversation_messages");
            $msg_query->execute();
            $msg_count = $msg_query->fetch(PDO::FETCH_ASSOC);
            
            echo "<p><strong>Total messages:</strong> " . $msg_count['count'] . "</p>";
            
        } catch (Exception $e) {
            echo "<p class='error'>Database Error: " . $e->getMessage() . "</p>";
        }
        ?>
    </div>
    
    <div class="test">
        <h3>Step 4: Live Test</h3>
        <button onclick="window.open('../admin/messages.php', '_blank')">Open Admin Messages Page</button>
        <button onclick="window.open('../contact.php', '_blank')">Open User Contact Page</button>
    </div>

    <script>
        const adminId = '<?= $admin_id ?>';
        
        function testConversationsAPI() {
            const resultsDiv = document.getElementById('apiResults');
            resultsDiv.innerHTML = '<p>Testing conversations API...</p>';
            
            fetch(`../components/messaging_api.php?action=get_conversations&user_type=admin&user_id=${adminId}`)
                .then(response => {
                    console.log('Response status:', response.status);
                    return response.json();
                })
                .then(data => {
                    console.log('API Response:', data);
                    resultsDiv.innerHTML = `
                        <h4>Conversations API Test Results:</h4>
                        <pre>${JSON.stringify(data, null, 2)}</pre>
                    `;
                })
                .catch(error => {
                    console.error('Error:', error);
                    resultsDiv.innerHTML = `<p class="error">Error: ${error.message}</p>`;
                });
        }
        
        function testMessagesAPI() {
            const resultsDiv = document.getElementById('apiResults');
            resultsDiv.innerHTML = '<p>Testing messages API...</p>';
            
            fetch(`../components/messaging_api.php?action=get_messages&conversation_id=1&last_message_id=0`)
                .then(response => {
                    console.log('Response status:', response.status);
                    return response.json();
                })
                .then(data => {
                    console.log('API Response:', data);
                    resultsDiv.innerHTML = `
                        <h4>Messages API Test Results:</h4>
                        <pre>${JSON.stringify(data, null, 2)}</pre>
                    `;
                })
                .catch(error => {
                    console.error('Error:', error);
                    resultsDiv.innerHTML = `<p class="error">Error: ${error.message}</p>`;
                });
        }
        
        // Auto-test on page load
        setTimeout(() => {
            testConversationsAPI();
        }, 1000);
    </script>
</body>
</html>
