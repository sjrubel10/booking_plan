<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $plan_id = (int)$_POST['plan_id'];
    $selectedSeats = $_POST['selectedSeats'];

    $conn = new mysqli('localhost', 'root', '', 'seat_plan');

    if ($conn->connect_error) {
        die('Connection failed: ' . $conn->connect_error);
    }
    $serializedSeats = serialize($selectedSeats);
    $stmt = $conn->prepare("UPDATE `seat_plan_names` SET `plan_details` = ? WHERE `id` = ?");
    $stmt->bind_param('si', $serializedSeats, $plan_id);

    if ($stmt->execute()) {
        echo 'Edited Plan saved successfully!';
    } else {
        echo 'Error: ' . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
