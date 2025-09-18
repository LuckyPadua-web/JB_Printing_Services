<?php
// Query all clients with their order details for printing
$select_all_clients = $conn->prepare("
    SELECT DISTINCT 
        u.id, 
        u.name, 
        u.email, 
        u.number,
        o.method as payment_method,
        o.placed_on as date_ordered,
        CASE 
            WHEN o.payment_status = 'delivered' THEN o.placed_on 
            ELSE NULL 
        END as date_delivered
    FROM users u 
    LEFT JOIN orders o ON u.id = o.user_id 
    ORDER BY u.name ASC, o.placed_on DESC
");
$select_all_clients->execute();
$all_clients = $select_all_clients->fetchAll(PDO::FETCH_ASSOC);
?>
