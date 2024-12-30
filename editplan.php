<?php
require 'functions.php';
$planId = $_GET['plan_id'];
//echo $planId;
$plan_details = view_plan_data( $planId );
$plan_seats = $plan_details['plan_details'];

/*echo "<pre>";
var_dump( $plan_details['plan_name'] );*/

?>

<!--<script>--><?php //echo json_encode( $seats )?><!--</script>-->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Seat Plan</title>
    <link rel="stylesheet" href="assets/style.css">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
</head>
<body>
<div class="editSeatPlan">
    <h1 class="edit_plan_title">Edit Seat Plan</h1>
    <div class="edit_toolbar">
        <input type="text" id="plan-name" placeholder="Plan Name" value="<?php echo $plan_details['plan_name']?>" readonly>
        <input type="color" id="color" value="#ff0000">
        <input type="number" class="set_price" id="price" placeholder="Price">
        <button id="apply-tool">Apply</button>
        <input type="text" id="seat_number" placeholder="A-1">
        <button class="set_seat_number" id="set_seat_number">Set Seat Number</button>
        <button class="drag_drop" id="enable_drag_drop">Drag & Drop</button>
        <button class="make_circle" id="enable_resize">Resize</button>
        <button class="enable_clear" id="enable_clear">Clear</button>
        <button id="update-plan">Update Plan</button>
    </div>

    <div id="edit-seat-grid" class="edit-seat-grid">
        <?php

        $rows = 10;
        $cols = 12;
        $boxSize = 45;

        // Create a flat array for all row and column combinations
        $seats = [];
        for ($row = 0; $row < $rows; $row++) {
            for ($col = 0; $col < $cols; $col++) {
                $seats[] = ['row' => $row, 'col' => $col];
            }
        }

        // Generate the seat grid with selected class for matching seats
        foreach ($seats as $seat) {
            $row = $seat['row'];
            $col = $seat['col'];
            $left = $col * $boxSize;
            $top = $row * $boxSize;
            $width = $boxSize - 3;
            $height = $boxSize - 2;

            // Check if the current seat is in the $plan_seats array
            $wi = $width;
            $hi = $height;
            $isSelected = false;
            $background_color = '';
            $seat_num = '';
            $zindex = 'auto';
            $seat_price = '';
            foreach ($plan_seats as $plan_seat) {
                if ($plan_seat['row'] == $row && $plan_seat['col'] == $col) {
                    $isSelected = true;
                    $background_color = $plan_seat['color'];
                    $seat_num = $plan_seat['seat_number'];
                    $seat_price = $plan_seat['price'];
                    $width = (int)$plan_seat['width'];
                    $height = (int)$plan_seat['height'];
                    $zindex = $plan_seat['z_index'];
                    $to = (int)$plan_seat['top'];
                    $le = (int)$plan_seat['left'];
                    break;
                }
            }
            if( $isSelected ){
                $class = ' save ';
                $color = $background_color;
                $seat_number = $seat_num;
                $wi = $width;
                $hi = $height;
                $zindex = is_numeric( $zindex ) ? $zindex : 'auto';
                $top = $to;
                $left = $le;
            }else{
                $class = '';
                $color = 'rgb(255, 255, 255)';
                $seat_number = '';
            }

            echo '<div class=" box ' . $class . '" 
              data-id="' . $row . '-' . $col . '" 
              data-row="' . $row . '" 
              data-col="' . $col . '" 
              data-seat-num=" ' . $seat_num . ' " 
              data-price=" ' . $seat_price . ' " 
              style="
              left: ' . $left . 'px; 
              top: ' . $top . 'px;
              width: ' . $wi . 'px;
              height: ' . $hi . 'px;
              background-color: '.$color.';
              z-index: '.$zindex.';
              ">
          </div>';
        }


        ?>

    </div>
</div>
<input type="hidden" id="plan_id" name="plan_id" value="<?php echo $planId;?>">

<script src="assets/edit_plan.js"></script>
</body>
</html>
