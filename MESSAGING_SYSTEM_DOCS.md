# Messaging System Documentation

## Overview

This is a two-way messaging system between users and admin for JB Printing Services, featuring a modern Messenger-like UI.

## Features

### User Side (contact.php)

- **Modern Chat Interface**: Clean, responsive design with a gradient header
- **Real-time Messaging**: Messages update automatically every 3 seconds
- **Message Status**: Shows sent/received status with different styling
- **Auto-conversation Creation**: Automatically creates a conversation when first accessed
- **Notification Badge**: Shows unread message count in navigation
- **Login Required**: Only logged-in users can access messaging

### Admin Side (admin/messages.php)

- **Conversation List**: Shows all user conversations with preview and timestamps
- **Search Functionality**: Search through conversations by user name or email
- **Real-time Updates**: Messages and conversations update automatically
- **Unread Indicators**: Shows unread message counts for each conversation
- **Multiple Conversations**: Handle multiple user conversations simultaneously
- **Notification Badge**: Shows total unread message count in navigation

## Database Structure

### Tables Created

1. **conversations**

   - `id`: Primary key
   - `user_id`: Foreign key to users table
   - `admin_id`: Foreign key to admin table (default: 1)
   - `created_at`: Timestamp when conversation started
   - `updated_at`: Timestamp of last activity

2. **conversation_messages**
   - `id`: Primary key
   - `conversation_id`: Foreign key to conversations table
   - `sender_type`: Either 'user' or 'admin'
   - `sender_id`: ID of the sender
   - `message`: The message content
   - `is_read`: Read status (0 = unread, 1 = read)
   - `created_at`: Timestamp when message was sent

## Files Modified/Created

### New Files

- `setup_messaging_system.php`: Database setup script
- `components/messaging_api.php`: API for all messaging operations
- `test_messaging.php`: Testing and verification script

### Modified Files

- `admin/messages.php`: Complete redesign with Messenger UI
- `contact.php`: Complete redesign with chat interface
- `components/admin_header.php`: Added unread message notification
- `components/user_header.php`: Added unread message notification

## Setup Instructions

1. **Database Setup**: Run the setup script

   ```bash
   C:\xampp\php\php.exe setup_messaging_system.php
   ```

2. **Verify Installation**: Access test page
   ```
   http://localhost/JB_Printing_Services/test_messaging.php
   ```

## Usage

### For Users

1. Login to your account
2. Navigate to "CONTACT" in the main menu
3. Start typing your message in the chat interface
4. Messages are sent instantly and responses appear in real-time

### For Admin

1. Login to admin panel
2. Navigate to "MESSAGES" in the admin menu
3. See all user conversations in the left sidebar
4. Click on any conversation to start messaging
5. Use the search box to find specific conversations

## API Endpoints

The messaging system uses AJAX calls to `components/messaging_api.php` with the following actions:

- `send_message`: Send a new message
- `get_messages`: Retrieve messages for a conversation
- `get_conversations`: Get all conversations for user/admin
- `create_conversation`: Create a new conversation
- `mark_read`: Mark messages as read

## Styling

The system uses custom CSS for a modern, responsive design:

- **Color Scheme**: Blue gradient theme (#667eea to #764ba2)
- **Responsive**: Works on mobile and desktop
- **Clean UI**: Modern messaging interface with avatars and timestamps
- **Visual Feedback**: Different styles for sent/received messages

## Real-time Features

- **Auto-polling**: Messages refresh every 2-3 seconds
- **Instant Updates**: New messages appear immediately
- **Read Status**: Messages are marked as read automatically
- **Live Notifications**: Unread counts update in navigation

## Security Features

- **Session Validation**: Requires active user/admin session
- **SQL Injection Protection**: Uses prepared statements
- **Input Sanitization**: Messages are properly escaped
- **Access Control**: Users can only see their own conversations

## Future Enhancements

Possible improvements:

- File attachments
- Message reactions/emojis
- Typing indicators
- Message search within conversations
- Push notifications
- Admin message templates
- Conversation archiving
