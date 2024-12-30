<?php
function view_plan_data( $planId ){
    $conn = new mysqli('localhost', 'root', '', 'seat_plan');
    if ($conn->connect_error) {
        die('Connection failed: ' . $conn->connect_error);
    }

    $sql = "SELECT * FROM seat_plan_names WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $planId);
    $stmt->execute();
    $result = $stmt->get_result();

    $plan_details = [];
    while ($row = $result->fetch_assoc()) {
        // Populate the seats array
        $plan_details = array(
            'plan_id' => $row['id'],
            'plan_name' => $row['plan_name'],
            'plan_details' => unserialize($row['plan_details']), // Unserialize here
        );
    }
    $stmt->close();
    $conn->close();

    return $plan_details;
}