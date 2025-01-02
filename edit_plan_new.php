<?php
require 'functions.php';
$planId = $_GET['plan_id'];
//echo $planId;
$plan_details = view_plan_data( $planId );
$plan_seats = $plan_details['plan_details'];

/*echo "<pre>";
var_dump($plan_details['plan_name']);*/
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Drag, Drop & Resize Multiple Divs</title>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="assets/index_new.css">
</head>
<body>

<h1>Drag, Drop & Resize Multiple Divs</h1>
<p>Click to select multiple divs, resize them, and drag them together.</p>

<div class="controls">
    <input type="text" id="plan-name" placeholder="Plan Name" value="<?php echo $plan_details['plan_name']?>">
    <input type="hidden" id="plan_id" name="plan_id" value="<?php echo $planId;?>">
    <label>
        Set Color:
        <input type="color" id="setColor">
    </label>
    <label>
        Set Price:
        <input type="number" id="setPrice" placeholder="Enter price">
    </label>
    <button id="applyChanges">Apply</button>
    <div class="setSeatNumber">
        <button class="set_seat_number" id="set_seat_number">Set Seat Number</button>
        <input type="text" id="seat_number_prefix" placeholder="Prefix Like A ">
        <input type="number" id="seat_number_count" placeholder="1" value="0">

    </div>

    <button class="set_multiselect" id="set_multiselect">Multiselect</button>
    <button class="make_circle" id="enable_resize">Resize</button>
    <button class="drag_drop" id="enable_drag_drop">Drag & Drop</button>
    <button id="clearAll">Clear</button>
    <button id="savePlan">Update Plan</button>
</div>


<!--<div id="parentDiv">-->

<?php
$box_size = 35;
$rows = 20;
$columns = 15;
$childWidth = $box_size;
$childHeight = $box_size + 5;
$gap = 5;

$seats = [];
for ( $row = 0; $row < $rows; $row++ ) {
    for ($col = 0; $col < $columns; $col++) {
        $seats[] = ['col' => $row, 'row' => $col];
    }
}

/*echo "<pre>";
var_dump( $seats);*/

echo '<div class="parentDiv" id="parentDiv" style="position: relative; width: ' . ($columns * ($childWidth + $gap) - $gap) . 'px; height: ' . ($rows * ($childHeight + $gap) - $gap) . 'px;">';
foreach ( $seats as $seat ) {
    $isSelected = false;
    $row = $seat['row'];
    $col = $seat['col'];
    $left = $row * ($childWidth + $gap) + 10;
    $top = $col * ($childHeight + $gap) + 10;
    $seat_number = $col * $columns + $row;
    $seat_num = '';
    $seat_price = 0;
    $background_color = '';
    $zindex = 'auto';
    $to = $top;
    $le = $left ;
    $width = $childWidth;
    $height = $childHeight;
    foreach ($plan_seats as $plan_seat) {
        if ($plan_seat['col'] == $row && $plan_seat['row'] == $col) {
            $isSelected = true;
            $background_color = $plan_seat['color'];
            $seat_num = isset( $plan_seat['seat_number'] ) ? $plan_seat['seat_number'] : '';
            $seat_price = $plan_seat['price'];
            $width = (int)$plan_seat['width'];
            $height = (int)$plan_seat['height'];
            $zindex = $plan_seat['z_index'];
            $to = (int)$plan_seat['top'] ;
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
    }
    else{
        $class = '';
        $color = '';
        $wi = $childWidth;
        $hi = $childHeight;
    }
//    echo '<div class="childDiv"  data-row="'.$col.'" data-col="'.$row.'" data-id="' . $col . '-'. $row. ' " data-price="0" style="position: absolute; width: ' . $childWidth . 'px; height: ' . $childHeight . 'px; left: ' . $top . 'px; top: ' . $left . 'px;">' . $id . '</div>';
    echo '<div class=" childDiv ' . $class . '"
              data-id="' . $col . '-' . $row . '" 
              data-row="' . $col . '" 
              data-col="' . $row . '" 
              data-seat-num=" ' . $seat_num . ' " 
              data-price=" ' . $seat_price . ' " 
              style="
              left: ' . $left . 'px; 
              top: ' . $top . 'px;
              width: ' . $wi . 'px;
              height: ' . $hi . 'px;
              background-color: '.$color.';
              z-index: '.$zindex.';
              "> '.$seat_number.'
          </div>';
}
echo '</div>';
?>

<!--</div>-->
<!--<h3>Saved Plans</h3>
<div id="plans"></div>-->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<script src="assets/edit_new.js"></script>
</body>
</html>
