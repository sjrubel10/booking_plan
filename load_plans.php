<?php
$conn = new mysqli('localhost', 'root', '', 'seat_plan');
if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

$sql = "SELECT * FROM seat_plan_names ORDER BY created_at DESC";
$result = $conn->query($sql);

while ($row = $result->fetch_assoc()) {
    echo '<a href="view_plan.php?plan_id=' . $row['id'] . '">' . htmlspecialchars($row['plan_name']) . '</a><br>';
}

$conn->close();
?>
