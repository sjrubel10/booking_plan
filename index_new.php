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
    <input type="text" id="plan-name" placeholder="Plan Name">
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
        <button class="set_seat_number" id="place_seat_number">Place Seat Number</button>

    </div>

    <button class="set_multiselect" id="set_multiselect">Multiselect</button>
    <button class="make_circle" id="enable_resize">Resize</button>
    <button class="drag_drop" id="enable_drag_drop">Drag & Drop</button>
    <button id="clearAll">Clear</button>
    <button id="savePlan">Save Plan</button>
</div>


<!--<div id="parentDiv">-->

    <?php
    $box_size = 35;
    $rows = 20;
    $columns = 15;
    $childWidth = $box_size;
    $childHeight = $box_size + 5;
    $gap = 5;

   /* $seats = [];
    for ($row = 0; $row < $rows; $row++) {
        for ($col = 0; $col < $columns; $col++) {
            $seats[] = ['row' => $row, 'col' => $col];
        }
    }*/

    /*echo "<pre>";
    var_dump($seats);*/

    echo '<div class="parentDiv" id="parentDiv" style="position: relative; width: ' . ($columns * ($childWidth + $gap) - $gap) . 'px; height: ' . ($rows * ($childHeight + $gap) - $gap) . 'px;">';
    for ($i = 0; $i < $rows; $i++) {
        for ($j = 0; $j < $columns; $j++) {
            $top = $j * ($childWidth + $gap) + 10;
            $left = $i * ($childHeight + $gap) + 10;
            $id = $i * $columns + $j;
            echo '<div class="childDiv"  data-row="'.$i.'" data-col="'.$j.'" data-id="' . $i . '-'. $j. ' " data-price="0" style="position: absolute; width: ' . $childWidth . 'px; height: ' . $childHeight . 'px; left: ' . $top . 'px; top: ' . $left . 'px;">' . $id . '</div>';
        }
    }
    echo '</div>';
    ?>

<!--</div>-->
<h3>Saved Plans</h3>
<div id="plans"></div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<script src="assets/index_new.js"></script>
</body>
</html>
