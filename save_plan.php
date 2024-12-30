<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $planName = $_POST['planName'];
    $selectedSeats = $_POST['selectedSeats'];

    $conn = new mysqli('localhost', 'root', '', 'seat_plan');
    if ($conn->connect_error) {
        die('Connection failed: ' . $conn->connect_error);
    }

    $serializedSeats = serialize($selectedSeats);
    // Save plan name
    $stmt = $conn->prepare("INSERT INTO seat_plan_names (plan_name, plan_details) VALUES ( ?, ? )");
    $stmt->bind_param('ss', $planName, $serializedSeats );
    $stmt->execute();
    $planId = $stmt->insert_id;
    $stmt->close();

    echo 'Plan saved successfully!';
}
