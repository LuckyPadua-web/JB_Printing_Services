<?php
require_once '../components/email_config.php';

function sendOrderStatusEmail($user_email, $user_name, $order_id, $status, $order_details) {
    // Email subject based on status
    $status_subjects = [
        'pending' => 'Order Received - Order #' . $order_id,
        'Pre Order' => 'Order Pre-Ordered - Order #' . $order_id,
        'To Received' => 'Order Ready for Pickup - Order #' . $order_id,
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
                <p>$order_details</p>";
                
    // Add specific message based on status
    if ($status == 'To Received') {
        $message .= "
                <div style='background: #d4edda; padding: 15px; margin: 15px 0; border-radius: 5px; border-left: 4px solid #28a745;'>
                    <h4 style='color: #155724; margin: 0 0 10px 0;'>🎉 Great News!</h4>
                    <p style='color: #155724; margin: 0;'>Your order is ready for pickup! Please visit our store to collect your items.</p>
                </div>";
    }
    
    $message .= "
                <p>You can view your order details by logging into your account on our website.</p>
                
                <p>Thank you for choosing JB Printing Services!</p>
            </div>
            <div class='footer'>
                <p>This is an automated message. Please do not reply to this email.</p>
                <p>&copy; " . date('Y') . " JB Printing Services. All rights reserved.</p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    // Send email using PHPMailer for better reliability
    return sendEmailWithPHPMailer($user_email, $subject, $message);
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
    require_once '../PHPMailer/src/PHPMailer.php';
    require_once '../PHPMailer/src/SMTP.php';
    require_once '../PHPMailer/src/Exception.php';
    
    $mail = new PHPMailer\PHPMailer\PHPMailer(true);
    
    try {
        $mail->isSMTP();
        $mail->Host = SMTP_HOST;
        $mail->Port = SMTP_PORT;
        $mail->SMTPAuth = true;
        $mail->Username = SMTP_USERNAME;
        $mail->Password = SMTP_PASSWORD;
        $mail->SMTPSecure = SMTP_SECURE;
        
        // Enable debug mode for troubleshooting (remove in production)
        // $mail->SMTPDebug = 2; // Uncomment to see detailed debug info
        
        $mail->setFrom(SMTP_USERNAME, SMTP_FROM_NAME);
        $mail->addAddress($to);
        $mail->isHTML(true);
        
        $mail->Subject = $subject;
        $mail->Body = $message;
        
        return $mail->send();
    } catch (Exception $e) {
        error_log("Email sending failed: " . $e->getMessage());
        return false;
    }
}
?>

