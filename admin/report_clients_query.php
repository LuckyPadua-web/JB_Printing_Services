<?php
// Query all clients for printing
$select_all_clients = $conn->prepare("SELECT id, name, email, number FROM users ORDER BY name ASC");
$select_all_clients->execute();
$all_clients = $select_all_clients->fetchAll(PDO::FETCH_ASSOC);
?>
