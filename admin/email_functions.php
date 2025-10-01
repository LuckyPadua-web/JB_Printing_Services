<?php
require_once 'config/email_config.php';

function sendOrderStatusEmail($user_email, $user_name, $order_id, $status, $order_details) {
    // Email subject based on status
    $status_subjects = [
        'confirmed' => 'Order Confirmed - Order #' . $order_id,
        'processing' => 'Order Processing - Order #' . $order_id,
        'shipped' => 'Order Shipped - Order #' . $order_id,
        'delivered' => 'Order Delivered - Order #' . $order_id,
        'cancelled' => 'Order Cancelled - Order #' . $order_id
    ];
    
    $subject = $status_subjects[$status] ?? 'Order Update - Order #' . $order_id;
    
    // Email message template
    $message = "
    <!DOCTYPE html>
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: #007bff; color: white; padding: 20px; text-align: center; }
            .content { padding: 20px; background: #f9f9f9; }
            .status-update { background: white; padding: 15px; margin: 15px 0; border-left: 4px solid #007bff; }
            .footer { text-align: center; padding: 20px; font-size: 12px; color: #666; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>Order Status Update</h1>
            </div>
            <div class='content'>
                <p>Dear $user_name,</p>
                
                <div class='status-update'>
                    <h3>Your Order #$order_id</h3>
                    <p><strong>Status:</strong> " . ucfirst($status) . "</p>
                    <p><strong>Update Time:</strong> " . date('F j, Y g:i A') . "</p>
                </div>
                
                <p><strong>Order Details:</strong></p>
                <p>$order_details</p>
                
                <p>You can view your order details by logging into your account on our website.</p>
                
                <p>Thank you for shopping with us!</p>
            </div>
            <div class='footer'>
                <p>This is an automated message. Please do not reply to this email.</p>
                <p>&copy; " . date('Y') . " Your Store Name. All rights reserved.</p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    // Send email using PHP's mail function or PHPMailer
    return sendEmail($user_email, $subject, $message);
}

function sendEmail($to, $subject, $message) {
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: " . SMTP_FROM_NAME . " <" . SMTP_FROM_EMAIL . ">" . "\r\n";
    $headers .= "Reply-To: " . SMTP_FROM_EMAIL . "\r\n";
    
    return mail($to, $subject, $message, $headers);
}

// Alternative using PHPMailer (Recommended for better reliability)
function sendEmailWithPHPMailer($to, $subject, $message) {
    require_once 'path/to/PHPMailer/PHPMailerAutoload.php'; // Download PHPMailer
    
    $mail = new PHPMailer;
    $mail->isSMTP();
    $mail->Host = SMTP_HOST;
    $mail->Port = SMTP_PORT;
    $mail->SMTPAuth = true;
    $mail->Username = SMTP_USERNAME;
    $mail->Password = SMTP_PASSWORD;
    $mail->SMTPSecure = SMTP_SECURE;
    
    $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
    $mail->addAddress($to);
    $mail->isHTML(true);
    
    $mail->Subject = $subject;
    $mail->Body = $message;
    
    return $mail->send();
}
?>

