<?php
require 'functions.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Plan</title>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
<?php
$planId = $_GET['plan_id'];
$plan_details = view_plan_data( $planId );
$seats = $plan_details['plan_details'];
$width = 36 ;
$height=36;

/*echo "<pre>";
var_dump($seats);*/

$leastLeft = PHP_INT_MAX;
$leastTop = PHP_INT_MAX;
foreach ($seats as $item) {

    if( isset( $item["left"] )) {

        $currentLeft = (int)rtrim($item["left"], "px");
        $currentTop = (int)rtrim($item["top"], "px");

        if ($currentLeft < $leastLeft) {
            $leastLeft = $currentLeft;
        }
        if ($currentTop < $leastTop) {
            $leastTop = $currentTop;
        }
    }
}
?>
<style>
    #seat-grid {
        position: relative;
        width: 600px;
        height: auto;
        margin: auto;
        display: flex;
        top: 100px;
    }
    .box {
        position: absolute;
        text-align: center;
        line-height: 20px;
        color: #fff;
        cursor: pointer;
    }
    .boxChild{
        /*border-radius: 3px;*/
        margin: 3px 3px 3px 3px;
        font-size: 12px;
        /*border: 1px solid #000;*/
        border-top-right-radius: 30px;
        border-top-left-radius: 30px;
    }
    .box_selected{
        background-color: #333333;
    }
    .seat_number{
        position: absolute;
        top: 10px;
        left: 8px;
    }
</style>
<h1><?php echo $plan_details['plan_name']?></h1>
<div class="edit_plan" id="<?php echo $plan_details['plan_id']?>"><a href="edit_plan_new.php?plan_id=<?php echo $plan_details['plan_id']?>">Edit</a></div>
<div id="seat-grid">
    <div class="boxHolder">
    <?php
    $start = 1;
    $start_col = 0;
    $start_row = 0;
    foreach ($seats as $seat):
    if( isset( $seat["left"] )) {
        $width = isset( $seat['width'] ) ? (int)$seat['width'] : 0;
        $height = isset( $seat['height'] ) ? (int)$seat['height'] : 0;

        $child_width = $width;
        $child_height = $height;
            $uniqueId = "seat-{$seat['id']}"; // Unique ID for each seat
            ?>
            <div class="box"
                 id="<?= $uniqueId ?>"
                 data-price="<?= htmlspecialchars($seat['price']) ?>"
                 data-seat-num="<?= 1 ?>"
                 style="
                     width: <?php echo $width?>px;
                     height: <?php echo $height?>px;
                     left: <?php echo (int)$seat['left'] - $leastLeft?>px;
                     top: <?php echo  (int)$seat['top'] - $leastTop ?>px;"
                 title="Price: $<?= htmlspecialchars($seat['price']) ?>">
                <div class="boxChild"
                     style="
                             background-color: <?= htmlspecialchars($seat['color']) ?>;
                             width: <?php echo $child_width - 4?>px;
                             height: <?php echo $child_height - 3?>px;
                             ">

                    <span class="seat_number"><?php echo isset( $seat['seat_number'] ) ? $seat['seat_number'] : ''?></span> </div>
            </div>
<!--        --><?php //} ?>
    <?php $start++;
    }
    endforeach;

    ?>
    </div>
</div>

<div id="seat-info" style="margin-top: 20px; font-size: 16px;">
    <strong>Seat Info:</strong> <span id="info"></span>
</div>

<script>
    $(document).ready(function () {
        $('.box').on('click', function () {
            const seatId = $(this).attr('id');
            $(this).css('background-color', '#cacd1e');
            const price = $(this).data('price');
            $('#info').text(`Seat ID: ${seatId}, Price: $${price}`);
        });

        function applyLeastLeftMargin() {
            let leastLeftValue = Infinity;
            $(".box").each(function () {
                const leftValue = parseInt($(this).css("left"), 10); // Get the left value of the box
                if (leftValue < leastLeftValue) {
                    leastLeftValue = leftValue;
                }
            });
            if (leastLeftValue < 0) {
                leastLeftValue = Math.abs(leastLeftValue);
                $("#seat-grid").css({
                    "position": "relative",
                    "margin-left": leastLeftValue + "px"
                });
            }

        }

// Call the function to apply the margin
        applyLeastLeftMargin();

    });
</script>
</body>
</html>
