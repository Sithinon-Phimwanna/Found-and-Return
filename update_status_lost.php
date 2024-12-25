<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $item_id = $_POST['item_id'];
    $status_id = $_POST['status_id'];

    $query = "UPDATE lost_items SET status_id = ? WHERE item_id = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('ii', $status_id, $item_id);
    $stmt->execute();
    $stmt->close();

    $mysqli->close();
    header('Location: lost_items_list.php');
    exit;
}
?>
