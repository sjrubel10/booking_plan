<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Seat Plan</title>
    <link rel="stylesheet" href="assets/style.css">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
</head>
<body>
<h1>Make Seat Plan</h1>
<div class="toolbar">
    <input type="text" id="plan-name" placeholder="Plan Name">
    <input type="color" id="color" value="#ff0000">
    <input type="number" class="set_price" id="price" placeholder="Price">
    <button id="apply-tool">Apply</button>
    <button class="make_circle" id="make_circle">Circle</button>
    <input type="text" id="seat_number" placeholder="A-1">
    <button class="set_seat_number" id="set_seat_number">Set Seat Number</button>
    <button class="drag_drop" id="enable_drag_drop">Drag & Drop</button>
<!--    <button class="make_circle" id="make_rectangle">Rectangle</button>-->
    <button id="save-plan">Save Plan</button>
</div>
<div id="seat-grid" class="seat-grid"></div>
<h3>Saved Plans</h3>
<div id="plans"></div>

<script src="assets/script.js"></script>
<!--<script src="assets/new_script.js"></script>-->
</body>
</html>
