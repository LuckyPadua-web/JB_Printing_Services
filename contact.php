<?php

include 'components/connect.php';

session_start();

if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
}else{
   $user_id = '';
};


?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>contact</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<!-- header section starts  -->
<?php include 'components/user_header.php'; ?>
<!-- header section ends -->

<div class="heading">
   <h3>Contact us</h3>
</div>

<!-- contact section starts  -->

<section class="contact">
   <?php if($user_id): ?>
  
   
   <div class="chat-container">
      <div class="conversations-list">
         <div class="conversation-header">
            <div class="user-info">
               <div class="avatar">
                  <i class="fas fa-user"></i>
               </div>
               <div class="user-details">
                  <h3>Your Conversations</h3>
                  <p>Chat with admin support</p>
               </div>
            </div>
         </div>
         
         <div class="conversation-item active" id="mainConversation">
            <div class="avatar">
               <i class="fas fa-user-shield"></i>
            </div>
            <div class="conversation-details">
               <h4>Admin Support</h4>
               <p id="lastMessagePreview">Click to start chatting...</p>
            </div>
            <div class="conversation-status">
               <span class="online-indicator"></span>
            </div>
         </div>
      </div>
      
      <div class="chat-area">
         <div class="chat-header">
            <div class="user-info">
               <div class="avatar">
                  <i class="fas fa-user-shield"></i>
               </div>
               <div class="user-details">
                  <h3>Admin Support</h3>
                  <p>JB Printing Services • Online</p>
               </div>
            </div>
         </div>
         
         <div class="chat-messages" id="chatMessages">
            <div class="welcome-message">
               <i class="fas fa-comments"></i>
               <p>Welcome! Feel free to ask any questions about our services.</p>
            </div>
         </div>
         
         <div class="chat-input">
            <form id="messageForm">
               <input type="hidden" id="conversationId" value="">
               <div class="input-group">
                  <textarea id="messageText" placeholder="Type your message..." rows="1"></textarea>
                  <button type="submit"><i class="fas fa-paper-plane"></i></button>
               </div>
            </form>
         </div>
      </div>
   </div>
   <?php else: ?>
   <div class="login-required">
      <i class="fas fa-lock"></i>
      <h3>Login Required</h3>
      <p>Please log in to contact our support team.</p>
      <a href="login.php" class="btn">Login</a>
   </div>
   <?php endif; ?>
</section>

<style>
/* Override any yellow backgrounds from global styles */
body {
   background: white !important;
}

/* Override the heading section that has black background */
.heading {
   background: white !important;
   min-height: auto !important;
   padding: 2rem !important;
}

.heading h3 {
   color: #2c3e50 !important;
   font-size: 3rem !important;
}

section.contact {
   background: white !important;
}

.contact {
   max-width: 1200px;
   margin: 0 auto;
   padding: 2rem;
   background: white;
}

/* Override any message notifications that might have yellow background */
.message {
   background: white !important;
}

/* Ensure all elements use proper colors */
* {
   background-color: initial;
}

/* Override any global yellow selections */
*::selection {
   background-color: #2c3e50 !important;
   color: white !important;
}

/* Override any buttons that might have yellow background */
.btn {
   background: #2c3e50 !important;
   color: white !important;
}

/* Override scrollbar if it has yellow */
::-webkit-scrollbar-thumb {
   background-color: #2c3e50 !important;
}

.chat-container {
   display: flex;
   height: 600px;
   border: 1px solid #ddd;
   border-radius: 10px;
   overflow: hidden;
   background: white;
   margin: 2rem 0;
}

.conversations-list {
   width: 350px;
   border-right: 1px solid #ddd;
   background: #f8f9fa;
   display: flex;
   flex-direction: column;
}

.conversation-header {
   padding: 1rem;
   border-bottom: 1px solid #ddd;
   background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
   color: white;
}

.conversation-header .user-info {
   display: flex;
   align-items: center;
   gap: 1rem;
}

.conversation-header .avatar {
   width: 40px;
   height: 40px;
   border-radius: 50%;
   background: rgba(255,255,255,0.2);
   display: flex;
   align-items: center;
   justify-content: center;
   font-size: 1.2rem;
}

.conversation-header .user-details h3 {
   margin: 0 0 0.2rem 0;
   font-size: 1rem;
}

.conversation-header .user-details p {
   margin: 0;
   font-size: 0.8rem;
   opacity: 0.8;
}

.conversation-item {
   padding: 1rem;
   cursor: pointer;
   transition: background 0.3s;
   display: flex;
   align-items: center;
   gap: 1rem;
   border-bottom: 1px solid #eee;
}

.conversation-item:hover {
   background: #e9ecef;
}

.conversation-item.active {
   background: #2c3e50;
   color: white;
}

.conversation-item .avatar {
   width: 45px;
   height: 45px;
   border-radius: 50%;
   background: #2c3e50;
   display: flex;
   align-items: center;
   justify-content: center;
   color: white;
   font-size: 1.2rem;
   flex-shrink: 0;
}

.conversation-item.active .avatar {
   background: white;
   color: #2c3e50;
}

.conversation-details {
   flex: 1;
}

.conversation-details h4 {
   margin: 0 0 0.3rem 0;
   font-size: 1rem;
}

.conversation-details p {
   margin: 0;
   font-size: 0.85rem;
   opacity: 0.7;
   overflow: hidden;
   text-overflow: ellipsis;
   white-space: nowrap;
}

.conversation-status {
   display: flex;
   align-items: center;
}

.online-indicator {
   width: 10px;
   height: 10px;
   border-radius: 50%;
   background: #28a745;
   animation: pulse 2s infinite;
}

@keyframes pulse {
   0% {
      box-shadow: 0 0 0 0 rgba(40, 167, 69, 0.7);
   }
   70% {
      box-shadow: 0 0 0 10px rgba(40, 167, 69, 0);
   }
   100% {
      box-shadow: 0 0 0 0 rgba(40, 167, 69, 0);
   }
}

.chat-area {
   flex: 1;
   display: flex;
   flex-direction: column;
}

.chat-header {
   padding: 1rem;
   border-bottom: 1px solid #ddd;
   background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
   color: white;
   display: flex;
   align-items: center;
   gap: 1rem;
}

.chat-header .avatar {
   width: 50px;
   height: 50px;
   border-radius: 50%;
   background: rgba(255,255,255,0.2);
   display: flex;
   align-items: center;
   justify-content: center;
   font-size: 1.5rem;
}

.user-details h3 {
   margin: 0 0 0.2rem 0;
   font-size: 1.2rem;
}

.user-details p {
   margin: 0;
   font-size: 0.9rem;
   opacity: 0.8;
}

.chat-messages {
   flex: 1;
   padding: 1rem;
   overflow-y: auto;
   background: #f8f9fa;
}

.welcome-message {
   text-align: center;
   padding: 2rem;
   color: #666;
}

.welcome-message i {
   font-size: 3rem;
   margin-bottom: 1rem;
   color: #2c3e50;
}

.message {
   margin-bottom: 1rem;
   display: flex;
   width: 100%;
   clear: both;
}

.message.received {
   justify-content: flex-start;
   align-items: flex-start;
}

.message.sent {
   justify-content: flex-end;
   align-items: flex-start;
}

.message-wrapper {
   display: flex;
   align-items: flex-start;
   gap: 0.5rem;
   max-width: 75%;
}

.message.sent .message-wrapper {
   flex-direction: row-reverse;
}

.message.received .message-wrapper {
   flex-direction: row;
}

.message-content {
   background: white;
   padding: 0.8rem 1rem;
   border-radius: 18px;
   box-shadow: 0 1px 2px rgba(0,0,0,0.1);
   position: relative;
   word-wrap: break-word;
   flex: 1;
}

.message.received .message-content {
   background: white;
   color: #333;
   border: 1px solid #e9ecef;
}

.message.sent .message-content {
   background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
   color: white;
}

.message-avatar {
   width: 35px;
   height: 35px;
   border-radius: 50%;
   display: flex;
   align-items: center;
   justify-content: center;
   color: white;
   font-size: 0.9rem;
   flex-shrink: 0;
   margin-top: 0.2rem;
}

.message.received .message-avatar {
   background: #2c3e50;
}

.message.sent .message-avatar {
   background: #495057;
}

.message-time {
   font-size: 0.7rem;
   color: #666;
   margin-top: 0.3rem;
   text-align: right;
}

.message.sent .message-time {
   color: rgba(255,255,255,0.8);
   text-align: left;
}

.chat-input {
   padding: 1rem;
   border-top: 1px solid #ddd;
   background: white;
}

.input-group {
   display: flex;
   gap: 0.5rem;
   align-items: flex-end;
}

.input-group textarea {
   flex: 1;
   border: 1px solid #ddd;
   border-radius: 20px;
   padding: 0.8rem 1rem;
   resize: none;
   outline: none;
   font-family: inherit;
   max-height: 100px;
   transition: border-color 0.3s;
}

.input-group textarea:focus {
   border-color: #2c3e50;
}

.input-group button {
   background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
   border: none;
   border-radius: 50%;
   width: 45px;
   height: 45px;
   color: white;
   cursor: pointer;
   display: flex;
   align-items: center;
   justify-content: center;
   transition: transform 0.2s;
   font-size: 1.1rem;
}

.input-group button:hover {
   transform: scale(1.05);
}

.input-group button:disabled {
   background: #ccc;
   cursor: not-allowed;
   transform: none;
}

.login-required {
   text-align: center;
   padding: 4rem 2rem;
   color: #666;
}

.login-required i {
   font-size: 4rem;
   margin-bottom: 1rem;
   color: #2c3e50;
}

.login-required h3 {
   margin-bottom: 1rem;
   color: #333;
}

.login-required .btn {
   background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
   color: white;
   padding: 1rem 2rem;
   border-radius: 25px;
   text-decoration: none;
   display: inline-block;
   margin-top: 1rem;
   transition: transform 0.2s;
}

.login-required .btn:hover {
   transform: translateY(-2px);
}

/* Responsive design */
@media (max-width: 768px) {
   .chat-container {
      height: 500px;
      margin: 1rem 0;
      flex-direction: column;
   }
   
   .conversations-list {
      width: 100%;
      height: 120px;
      border-right: none;
      border-bottom: 1px solid #ddd;
   }
   
   .conversation-header {
      padding: 0.5rem 1rem;
   }
   
   .conversation-item {
      padding: 0.5rem 1rem;
   }
   
   .conversation-item .avatar {
      width: 35px;
      height: 35px;
      font-size: 1rem;
   }
   
   .conversation-header .avatar {
      width: 30px;
      height: 30px;
      font-size: 1rem;
   }
   
   .message-content {
      max-width: 85%;
   }
   
   .chat-header {
      padding: 0.8rem;
   }
   
   .chat-header .avatar {
      width: 40px;
      height: 40px;
      font-size: 1.2rem;
   }
   
   .contact {
      padding: 1rem;
   }
   
   .conversation-details h4 {
      font-size: 0.9rem;
   }
   
   .conversation-details p {
      font-size: 0.8rem;
   }
}

@media (max-width: 480px) {
   .chat-container {
      height: 450px;
   }
   
   .conversations-list {
      height: 100px;
   }
   
   .conversation-header .user-details h3 {
      font-size: 0.9rem;
   }
   
   .conversation-header .user-details p {
      font-size: 0.7rem;
   }
   
   .input-group button {
      width: 40px;
      height: 40px;
   }
}
</style>

<!-- contact section ends -->










<!-- footer section starts  -->
<?php include 'components/footer.php'; ?>
<!-- footer section ends -->








<!-- custom js file link  -->
<script src="js/script.js"></script>

<?php if($user_id): ?>
<script>
let currentConversationId = null;
let lastMessageId = 0;
let messagePolling = null;

document.addEventListener('DOMContentLoaded', function() {
    initializeChat();
    
    // Auto-resize textarea
    document.getElementById('messageText').addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = this.scrollHeight + 'px';
    });
    
    // Send message on form submit
    document.getElementById('messageForm').addEventListener('submit', function(e) {
        e.preventDefault();
        sendMessage();
    });
    
    // Send message on Enter (Shift+Enter for new line)
    document.getElementById('messageText').addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
        }
    });
});

function initializeChat() {
    console.log('Initializing chat for user ID: <?= $user_id ?>');
    
    // Check for existing conversation or create one
    const url = 'components/messaging_api.php?action=get_conversations&user_type=user&user_id=<?= $user_id ?>';
    console.log('API URL:', url);
    
    fetch(url)
        .then(response => {
            console.log('Response status:', response.status);
            return response.text(); // Get text first to debug
        })
        .then(text => {
            console.log('Raw response:', text);
            try {
                const data = JSON.parse(text);
                console.log('Parsed response:', data);
                if (data.success && data.conversations.length > 0) {
                    // Load existing conversation
                    currentConversationId = data.conversations[0].id;
                    document.getElementById('conversationId').value = currentConversationId;
                    loadMessages();
                    startPolling();
                } else {
                    // Create new conversation
                    createConversation();
                }
            } catch (parseError) {
                console.error('JSON Parse Error:', parseError);
                console.error('Response was not valid JSON:', text);
                createConversation(); // Try to create conversation anyway
            }
        })
        .catch(error => {
            console.error('Error:', error);
            createConversation();
        });
}

function createConversation() {
    console.log('Creating conversation for user ID: <?= $user_id ?>');
    
    const formData = new FormData();
    formData.append('action', 'create_conversation');
    formData.append('user_id', '<?= $user_id ?>');
    formData.append('admin_id', '1');
    
    fetch('components/messaging_api.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        console.log('Create conversation response status:', response.status);
        return response.text();
    })
    .then(text => {
        console.log('Create conversation raw response:', text);
        try {
            const data = JSON.parse(text);
            console.log('Create conversation parsed response:', data);
            if (data.success) {
                currentConversationId = data.conversation_id;
                document.getElementById('conversationId').value = currentConversationId;
                
                if (!data.existing) {
                    // Send welcome message if new conversation
                    setTimeout(() => {
                        const welcomeMsg = "Hello! Welcome to JB Printing Services. How can we help you today?";
                        displayWelcomeMessage(welcomeMsg);
                    }, 500);
                } else {
                    loadMessages();
                }
                startPolling();
            } else {
                console.error('Create conversation API error:', data.error);
                displayErrorMessage('Failed to create conversation: ' + (data.error || 'Unknown error'));
            }
        } catch (parseError) {
            console.error('Create conversation JSON parse error:', parseError);
            console.error('Response was not valid JSON:', text);
            displayErrorMessage('Server error: Invalid response format');
        }
    })
    .catch(error => {
        console.error('Create conversation fetch error:', error);
        displayErrorMessage('Network error: ' + error.message);
    });
}

function displayErrorMessage(message) {
    const container = document.getElementById('chatMessages');
    container.innerHTML = `
        <div style="text-align: center; padding: 2rem; color: #dc3545;">
            <i class="fas fa-exclamation-triangle" style="font-size: 2rem; margin-bottom: 1rem;"></i>
            <p>${message}</p>
            <button onclick="initializeChat()" style="background: #007bff; color: white; border: none; padding: 0.5rem 1rem; border-radius: 5px; cursor: pointer;">
                Try Again
            </button>
        </div>
    `;
}

function loadMessages() {
    if (!currentConversationId) return;
    
    console.log('Loading messages for conversation:', currentConversationId, 'after message ID:', lastMessageId);
    
    const url = `components/messaging_api.php?action=get_messages&conversation_id=${currentConversationId}&last_message_id=${lastMessageId}`;
    console.log('Messages API URL:', url);
    
    fetch(url)
        .then(response => {
            console.log('Messages response status:', response.status);
            return response.text();
        })
        .then(text => {
            console.log('Messages raw response:', text);
            try {
                const data = JSON.parse(text);
                console.log('Messages parsed response:', data);
                if (data.success && data.messages.length > 0) {
                    displayMessages(data.messages);
                    lastMessageId = Math.max(...data.messages.map(m => m.id));
                    markMessagesAsRead('admin');
                } else {
                    console.log('No new messages or API error:', data.error);
                }
            } catch (parseError) {
                console.error('Messages JSON Parse Error:', parseError);
                console.error('Messages response was not valid JSON:', text);
            }
        })
        .catch(error => {
            console.error('Messages fetch error:', error);
        });
}

function displayMessages(messages) {
    const container = document.getElementById('chatMessages');
    
    // Clear welcome message if present
    if (container.querySelector('.welcome-message')) {
        container.innerHTML = '';
    }
    
    messages.forEach(message => {
        const messageElement = document.createElement('div');
        // Admin messages are 'received' (left), User messages are 'sent' (right)
        messageElement.className = `message ${message.sender_type === 'admin' ? 'received' : 'sent'}`;
        
        const time = new Date(message.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
        
        messageElement.innerHTML = `
            <div class="message-wrapper">
                <div class="message-avatar">
                    <i class="fas ${message.sender_type === 'admin' ? 'fa-user-shield' : 'fa-user'}"></i>
                </div>
                <div class="message-content">
                    ${message.message.replace(/\n/g, '<br>')}
                    <div class="message-time">${time}</div>
                </div>
            </div>
        `;
        
        container.appendChild(messageElement);
    });
    
    // Update last message preview in conversation list
    if (messages.length > 0) {
        const lastMessage = messages[messages.length - 1];
        const preview = lastMessage.message.length > 30 ? 
            lastMessage.message.substring(0, 30) + '...' : 
            lastMessage.message;
        document.getElementById('lastMessagePreview').textContent = preview;
    }
    
    // Scroll to bottom
    container.scrollTop = container.scrollHeight;
}

function displayWelcomeMessage(message) {
    const container = document.getElementById('chatMessages');
    container.innerHTML = '';
    
    const messageElement = document.createElement('div');
    messageElement.className = 'message received'; // Admin welcome message on left
    
    const time = new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
    
    messageElement.innerHTML = `
        <div class="message-wrapper">
            <div class="message-avatar">
                <i class="fas fa-user-shield"></i>
            </div>
            <div class="message-content">
                ${message}
                <div class="message-time">${time}</div>
            </div>
        </div>
    `;
    
    container.appendChild(messageElement);
    container.scrollTop = container.scrollHeight;
}

function sendMessage() {
    const messageText = document.getElementById('messageText').value.trim();
    if (!messageText || !currentConversationId) return;
    
    const formData = new FormData();
    formData.append('action', 'send_message');
    formData.append('conversation_id', currentConversationId);
    formData.append('message', messageText);
    formData.append('sender_type', 'user');
    formData.append('sender_id', '<?= $user_id ?>');
    
    fetch('components/messaging_api.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('messageText').value = '';
            document.getElementById('messageText').style.height = 'auto';
            loadMessages();
        }
    })
    .catch(error => console.error('Error:', error));
}

function markMessagesAsRead(senderType) {
    if (!currentConversationId) return;
    
    const formData = new FormData();
    formData.append('action', 'mark_read');
    formData.append('conversation_id', currentConversationId);
    formData.append('sender_type', senderType);
    
    fetch('components/messaging_api.php', {
        method: 'POST',
        body: formData
    });
}

function startPolling() {
    if (messagePolling) clearInterval(messagePolling);
    messagePolling = setInterval(loadMessages, 3000);
}

// Cleanup on page unload
window.addEventListener('beforeunload', function() {
    if (messagePolling) clearInterval(messagePolling);
});
</script>
<?php endif; ?>

</body>
</html>