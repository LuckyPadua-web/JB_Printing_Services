<?php
// Test email functionality
require_once 'email_functions.php';

// Test email sending
$test_email = "customer@example.com"; // Replace with a real email for testing
$test_name = "Test Customer";
$test_order_id = "12345";
$test_status = "To Received";
$test_order_details = "Test Order Details\nProduct: Business Cards\nQuantity: 100\nTotal: ₱500.00";

echo "<h2>Testing Email Functionality</h2>";
echo "<p>Attempting to send email to: " . $test_email . "</p>";

$result = sendOrderStatusEmail($test_email, $test_name, $test_order_id, $test_status, $test_order_details);

if ($result) {
    echo "<p style='color: green;'><strong>✓ Email sent successfully!</strong></p>";
} else {
    echo "<p style='color: red;'><strong>✗ Email sending failed!</strong></p>";
    echo "<p>Please check:</p>";
    echo "<ul>";
    echo "<li>Email configuration in email_config.php</li>";
    echo "<li>Gmail app password is correct</li>";
    echo "<li>PHPMailer files are in the correct location</li>";
    echo "<li>Server has internet connection</li>";
    echo "</ul>";
}

echo "<p><a href='placed_orders.php'>← Back to Placed Orders</a></p>";
?>
