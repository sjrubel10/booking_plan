<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $seats = $_POST['seats'];
    $plan_id = 1; // Example plan ID; change as needed.

    $conn = new mysqli('localhost', 'root', '', 'seat_plan');
    if ($conn->connect_error) {
        die('Connection failed: ' . $conn->connect_error);
    }

    foreach ($seats as $seat) {
        $seat_id = $conn->real_escape_string($seat['seat_id']);
        $color = $conn->real_escape_string($seat['color']);
        $price = $conn->real_escape_string($seat['price']);

        $sql = "INSERT INTO seat_plans (seat_id, color, price, plan_id)
                VALUES ('$seat_id', '$color', '$price', $plan_id)";
        $conn->query($sql);
    }

    $conn->close();
    echo 'Seat plan saved successfully!';
}
