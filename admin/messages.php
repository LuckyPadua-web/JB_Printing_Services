<?php

include '../components/connect.php';

session_start();

if(isset($_SESSION['admin_id'])){
   $admin_id = $_SESSION['admin_id'];
}else{
   $admin_id = '';
   header('location:admin_login.php');
};


?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>messages</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="../css/admin_style.css">

   
</head>
<body>

<?php include '../components/admin_header.php' ?>

<!-- messages section starts  -->

<section class="messages">
   <h1 class="heading">Messages</h1>
   
   <div class="chat-container">
      <div class="conversations-list">
         <div class="search-box">
            <input type="text" id="searchConversations" placeholder="Search conversations...">
            <i class="fas fa-search"></i>
         </div>
         <div id="conversationsList" class="conversations">
            <!-- Conversations will be loaded here -->
         </div>
      </div>
      
      <div class="chat-area">
         <div class="chat-header" id="chatHeader" style="display: none;">
            <div class="user-info">
               <div class="avatar">
                  <i class="fas fa-user"></i>
               </div>
               <div class="user-details">
                  <h3 id="userName">Select a conversation</h3>
                  <p id="userEmail"></p>
               </div>
            </div>
         </div>
         
         <div class="chat-messages" id="chatMessages">
            <div class="no-chat-selected">
               <i class="fas fa-comments"></i>
               <p>Select a conversation to start messaging</p>
            </div>
         </div>
         
         <div class="chat-input" id="chatInput" style="display: none;">
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
</section>

<style>
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
}

.search-box {
   padding: 1rem;
   border-bottom: 1px solid #ddd;
   position: relative;
}

.search-box input {
   width: 100%;
   padding: 0.8rem 2.5rem 0.8rem 1rem;
   border: 1px solid #ddd;
   border-radius: 25px;
   outline: none;
}

.search-box i {
   position: absolute;
   right: 1.5rem;
   top: 50%;
   transform: translateY(-50%);
   color: #666;
}

.conversations {
   height: calc(100% - 80px);
   overflow-y: auto;
}

.conversation-item {
   padding: 1rem;
   border-bottom: 1px solid #eee;
   cursor: pointer;
   transition: background 0.3s;
   display: flex;
   align-items: center;
   gap: 1rem;
}

.conversation-item:hover {
   background: #e9ecef;
}

.conversation-item.active {
   background: #007bff;
   color: white;
}

.conversation-item .avatar {
   width: 45px;
   height: 45px;
   border-radius: 50%;
   background: #007bff;
   display: flex;
   align-items: center;
   justify-content: center;
   color: white;
   font-size: 1.2rem;
}

.conversation-item.active .avatar {
   background: white;
   color: #007bff;
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
   max-width: 200px;
}

.conversation-time {
   font-size: 0.75rem;
   opacity: 0.6;
   margin-left: auto;
}

.unread-count {
   background: #dc3545;
   color: white;
   border-radius: 50%;
   width: 20px;
   height: 20px;
   font-size: 0.7rem;
   display: flex;
   align-items: center;
   justify-content: center;
   margin-left: 0.5rem;
}

.chat-area {
   flex: 1;
   display: flex;
   flex-direction: column;
}

.chat-header {
   padding: 1rem;
   border-bottom: 1px solid #ddd;
   background: #f8f9fa;
   display: flex;
   align-items: center;
   gap: 1rem;
}

.chat-header .avatar {
   width: 40px;
   height: 40px;
   border-radius: 50%;
   background: #007bff;
   display: flex;
   align-items: center;
   justify-content: center;
   color: white;
}

.user-details h3 {
   margin: 0 0 0.2rem 0;
   font-size: 1.1rem;
}

.user-details p {
   margin: 0;
   font-size: 0.9rem;
   color: #666;
}

.chat-messages {
   flex: 1;
   padding: 1rem;
   overflow-y: auto;
   background: #f8f9fa;
}

.no-chat-selected {
   display: flex;
   flex-direction: column;
   align-items: center;
   justify-content: center;
   height: 100%;
   color: #666;
   text-align: center;
}

.no-chat-selected i {
   font-size: 3rem;
   margin-bottom: 1rem;
   opacity: 0.5;
}

.message {
   margin-bottom: 1rem;
   display: flex;
   align-items: flex-end;
   gap: 0.5rem;
}

.message.sent {
   flex-direction: row-reverse;
}

.message-content {
   background: white;
   padding: 0.8rem 1rem;
   border-radius: 18px;
   max-width: 70%;
   box-shadow: 0 1px 2px rgba(0,0,0,0.1);
}

.message.sent .message-content {
   background: #007bff;
   color: white;
}

.message-time {
   font-size: 0.7rem;
   color: #666;
   margin-top: 0.3rem;
}

.message.sent .message-time {
   color: rgba(255,255,255,0.8);
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
}

.input-group button {
   background: #007bff;
   border: none;
   border-radius: 50%;
   width: 40px;
   height: 40px;
   color: white;
   cursor: pointer;
   display: flex;
   align-items: center;
   justify-content: center;
   transition: background 0.3s;
}

.input-group button:hover {
   background: #0056b3;
}

.input-group button:disabled {
   background: #ccc;
   cursor: not-allowed;
}
</style>

<!-- custom js file link  -->
<script src="../js/admin_script.js"></script>

<script>
let currentConversationId = null;
let lastMessageId = 0;
let messagePolling = null;

// Helper function for time formatting - moved to top to avoid reference errors
function formatTimeAgo(dateString) {
    const now = new Date();
    const date = new Date(dateString);
    const diffInSeconds = Math.floor((now - date) / 1000);
    
    if (diffInSeconds < 60) return 'Just now';
    if (diffInSeconds < 3600) return Math.floor(diffInSeconds / 60) + 'm';
    if (diffInSeconds < 86400) return Math.floor(diffInSeconds / 3600) + 'h';
    if (diffInSeconds < 604800) return Math.floor(diffInSeconds / 86400) + 'd';
    
    return date.toLocaleDateString();
}

document.addEventListener('DOMContentLoaded', function() {
    console.log('Admin messages page loaded');
    loadConversations();
    
    // Check if elements exist before adding listeners
    const messageText = document.getElementById('messageText');
    const messageForm = document.getElementById('messageForm');
    const searchConversations = document.getElementById('searchConversations');
    
    if (messageText) {
        // Auto-resize textarea
        messageText.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = this.scrollHeight + 'px';
        });
        
        // Send message on Enter (Shift+Enter for new line)
        messageText.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendMessage();
            }
        });
    }
    
    if (messageForm) {
        // Send message on form submit
        messageForm.addEventListener('submit', function(e) {
            e.preventDefault();
            sendMessage();
        });
    }
    
    if (searchConversations) {
        // Search conversations
        searchConversations.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const conversations = document.querySelectorAll('.conversation-item');
            conversations.forEach(conv => {
                const name = conv.querySelector('h4');
                const email = conv.querySelector('p');
                if (name && email) {
                    const nameText = name.textContent.toLowerCase();
                    const emailText = email.textContent.toLowerCase();
                    if (nameText.includes(searchTerm) || emailText.includes(searchTerm)) {
                        conv.style.display = 'flex';
                    } else {
                        conv.style.display = 'none';
                    }
                }
            });
        });
    }
});

function loadConversations() {
    const adminId = '<?= $admin_id ?>';
    if (!adminId) {
        console.error('Admin ID not found');
        return;
    }
    
    console.log('Loading conversations for admin ID:', adminId);
    
    const url = `../components/messaging_api.php?action=get_conversations&user_type=admin&user_id=${adminId}`;
    console.log('API URL:', url);
    
    fetch(url)
        .then(response => {
            console.log('Response status:', response.status);
            console.log('Response headers:', response.headers);
            return response.text(); // Get text first to see what we actually receive
        })
        .then(text => {
            console.log('Raw response:', text);
            try {
                const data = JSON.parse(text);
                console.log('Parsed API Response:', data);
                if (data.success) {
                    displayConversations(data.conversations);
                } else {
                    console.error('API Error:', data.error);
                    document.getElementById('conversationsList').innerHTML = '<div style="padding: 2rem; text-align: center; color: #dc3545;">Error loading conversations: ' + (data.error || 'Unknown error') + '</div>';
                }
            } catch (parseError) {
                console.error('JSON Parse Error:', parseError);
                console.error('Response was not valid JSON:', text);
                document.getElementById('conversationsList').innerHTML = '<div style="padding: 2rem; text-align: center; color: #dc3545;">Server error: Invalid response format</div>';
            }
        })
        .catch(error => {
            console.error('Fetch Error:', error);
            document.getElementById('conversationsList').innerHTML = '<div style="padding: 2rem; text-align: center; color: #dc3545;">Network error: ' + error.message + '</div>';
        });
}

function displayConversations(conversations) {
    const container = document.getElementById('conversationsList');
    container.innerHTML = '';
    
    if (conversations.length === 0) {
        container.innerHTML = '<div style="padding: 2rem; text-align: center; color: #666;">No conversations yet</div>';
        return;
    }
    
    conversations.forEach(conv => {
        const timeAgoText = conv.last_message_time ? formatTimeAgo(conv.last_message_time) : '';
        const unreadBadge = conv.unread_count > 0 ? `<span class="unread-count">${conv.unread_count}</span>` : '';
        
        const convElement = document.createElement('div');
        convElement.className = 'conversation-item';
        convElement.dataset.conversationId = conv.id;
        convElement.dataset.userName = conv.user_name;
        convElement.dataset.userEmail = conv.user_email;
        
        convElement.innerHTML = `
            <div class="avatar">
                <i class="fas fa-user"></i>
            </div>
            <div class="conversation-details">
                <h4>${conv.user_name}</h4>
                <p>${conv.last_message || 'No messages yet'}</p>
            </div>
            <div style="display: flex; flex-direction: column; align-items: flex-end;">
                <span class="conversation-time">${timeAgoText}</span>
                ${unreadBadge}
            </div>
        `;
        
        convElement.addEventListener('click', () => selectConversation(conv.id, conv.user_name, conv.user_email));
        container.appendChild(convElement);
    });
}

function selectConversation(conversationId, userName, userEmail) {
    currentConversationId = conversationId;
    lastMessageId = 0; // Reset to load all messages
    
    // Update active conversation
    document.querySelectorAll('.conversation-item').forEach(item => {
        item.classList.remove('active');
    });
    document.querySelector(`[data-conversation-id="${conversationId}"]`).classList.add('active');
    
    // Show chat header and input
    document.getElementById('chatHeader').style.display = 'flex';
    document.getElementById('chatInput').style.display = 'block';
    
    // Update header info
    document.getElementById('userName').textContent = userName;
    document.getElementById('userEmail').textContent = userEmail;
    document.getElementById('conversationId').value = conversationId;
    
    // Clear previous messages
    document.getElementById('chatMessages').innerHTML = '';
    
    // Load all messages for this conversation
    loadAllMessages();
    
    // Mark messages as read
    markMessagesAsRead('user');
    
    // Start polling for new messages
    if (messagePolling) clearInterval(messagePolling);
    messagePolling = setInterval(loadMessages, 2000);
}

function loadAllMessages() {
    if (!currentConversationId) {
        console.log('No conversation selected for loading all messages');
        return;
    }
    
    console.log('Loading ALL messages for conversation:', currentConversationId);
    
    fetch(`../components/messaging_api.php?action=get_messages&conversation_id=${currentConversationId}&last_message_id=0`)
        .then(response => {
            console.log('All messages response status:', response.status);
            return response.json();
        })
        .then(data => {
            console.log('All messages API Response:', data);
            if (data.success) {
                if (data.messages.length > 0) {
                    displayMessages(data.messages);
                    // Update last message ID
                    lastMessageId = Math.max(...data.messages.map(m => m.id));
                    console.log('Updated lastMessageId to:', lastMessageId);
                } else {
                    console.log('No messages found for this conversation');
                    document.getElementById('chatMessages').innerHTML = '<div style="text-align: center; padding: 2rem; color: #666;">No messages yet. Start the conversation!</div>';
                }
            } else {
                console.error('All messages API Error:', data.error);
            }
        })
        .catch(error => {
            console.error('All messages Fetch Error:', error);
        });
}

function loadMessages() {
    if (!currentConversationId) {
        console.log('No conversation selected');
        return;
    }
    
    console.log('Loading messages for conversation:', currentConversationId, 'after message ID:', lastMessageId);
    
    fetch(`../components/messaging_api.php?action=get_messages&conversation_id=${currentConversationId}&last_message_id=${lastMessageId}`)
        .then(response => {
            console.log('Messages response status:', response.status);
            return response.json();
        })
        .then(data => {
            console.log('Messages API Response:', data);
            if (data.success) {
                if (data.messages.length > 0) {
                    displayMessages(data.messages);
                    // Update last message ID
                    lastMessageId = Math.max(...data.messages.map(m => m.id));
                } else {
                    console.log('No new messages');
                }
            } else {
                console.error('Messages API Error:', data.error);
            }
        })
        .catch(error => {
            console.error('Messages Fetch Error:', error);
        });
}

function displayMessages(messages) {
    const container = document.getElementById('chatMessages');
    
    console.log('Displaying messages:', messages);
    
    // Clear "no chat selected" or "no messages" text if present
    const existingMessage = container.querySelector('.no-chat-selected, div[style*="text-align: center"]');
    if (existingMessage) {
        container.innerHTML = '';
    }
    
    if (!messages || messages.length === 0) {
        console.log('No messages to display');
        return;
    }
    
    messages.forEach(message => {
        // Check if message already exists to prevent duplicates
        const existingMsg = container.querySelector(`[data-message-id="${message.id}"]`);
        if (existingMsg) {
            console.log('Message already exists, skipping:', message.id);
            return;
        }
        
        console.log('Adding new message:', message);
        const messageElement = document.createElement('div');
        messageElement.className = `message ${message.sender_type === 'admin' ? 'sent' : 'received'}`;
        messageElement.dataset.messageId = message.id; // Add message ID for tracking
        
        const time = new Date(message.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
        
        messageElement.innerHTML = `
            <div class="message-content">
                ${message.message.replace(/\n/g, '<br>')}
                <div class="message-time">${time}</div>
            </div>
        `;
        
        container.appendChild(messageElement);
    });
    
    // Scroll to bottom
    container.scrollTop = container.scrollHeight;
}

function sendMessage() {
    const messageText = document.getElementById('messageText').value.trim();
    const adminId = '<?= $admin_id ?>';
    
    if (!messageText || !currentConversationId || !adminId) {
        console.error('Missing required data for sending message');
        return;
    }
    
    const formData = new FormData();
    formData.append('action', 'send_message');
    formData.append('conversation_id', currentConversationId);
    formData.append('message', messageText);
    formData.append('sender_type', 'admin');
    formData.append('sender_id', adminId);
    
    fetch('../components/messaging_api.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('messageText').value = '';
            document.getElementById('messageText').style.height = 'auto';
            loadMessages();
            loadConversations(); // Refresh conversations list
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
    
    fetch('../components/messaging_api.php', {
        method: 'POST',
        body: formData
    });
}

// Cleanup on page unload
window.addEventListener('beforeunload', function() {
    if (messagePolling) clearInterval(messagePolling);
});
</script>

</body>
</html>