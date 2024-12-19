<?php
$plan_id = 1; // Example plan ID.

$conn = new mysqli('localhost', 'root', '', 'seat_plan');
if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

$sql = "SELECT * FROM seat_plans WHERE plan_id = $plan_id";
$result = $conn->query($sql);

$seats = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $seats[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Seat Plan</title>
    <style>
        .box {
            width: 20px;
            height: 20px;
            border: 1px solid #aaa;
            display: inline-block;
            margin: 2px;
        }
    </style>
</head>
<body>
<div id="seat-grid">
    <?php foreach ($seats as $seat): ?>
        <div class="box" style="background-color: <?= htmlspecialchars($seat['color']) ?>;"
             title="Price: $<?= htmlspecialchars($seat['price']) ?>">
        </div>
    <?php endforeach; ?>
</div>
</body>
</html>
