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

    // Save seat details
    /*$stmt = $conn->prepare("INSERT INTO seat_plan_details (plan_id, seat_id, color, price, width, height) VALUES (?, ?, ?, ?, ?, ?)");
    foreach ($selectedSeats as $seat) {
        $seatId = $seat['id'];
        $color = $seat['color'];
        $price = $seat['price'];
        $width = $seat['width'];
        $height = $seat['height'];
        $stmt->bind_param('issdss', $planId, $seatId, $color, $price, $width, $height);
        $stmt->execute();
    }
    $stmt->close();
    $conn->close();*/

    echo 'Plan saved successfully!';
}
