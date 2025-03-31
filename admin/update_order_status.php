<?php
session_start();
header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "shoevibe_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(['success' => false, 'error' => 'Connection failed']));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate inputs
    if (!isset($_POST['order_id']) || !isset($_POST['status'])) {
        die(json_encode(['success' => false, 'error' => 'Missing required fields']));
    }

    $order_id = intval($_POST['order_id']);
    $status = $_POST['status'];

    // Validate status
    $valid_statuses = ['pending', 'processing', 'completed'];
    if (!in_array($status, $valid_statuses)) {
        die(json_encode(['success' => false, 'error' => 'Invalid status']));
    }
    
    $sql = "UPDATE orders SET status = ? WHERE order_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $status, $order_id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $stmt->error]);
    }
    
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
}

$conn->close();