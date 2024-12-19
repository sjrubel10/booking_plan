<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Plan</title>
    <?php


    ?>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
<?php
$planId = $_GET['plan_id'];


$conn = new mysqli('localhost', 'root', '', 'seat_plan');
if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

$sql = "SELECT * FROM seat_plan_names WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $planId);
$stmt->execute();
$result = $stmt->get_result();

$seats = [];
$plan_details = [];
while ($row = $result->fetch_assoc()) {
    // Populate the seats array
    $plan_details = array(
        'plan_name' => $row['plan_name'],
        'plan_details' => unserialize($row['plan_details']), // Unserialize here
    );
}



/*$sql = "SELECT * FROM seat_plan_details WHERE plan_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $planId);
$stmt->execute();
$result = $stmt->get_result();

$seats = [];
while ($row = $result->fetch_assoc()) {
    $seats[] = $row;
}*/

$seats = $plan_details['plan_details'];

//$seats = $plan_details['plan_details'];
/*echo "<pre>".$planId;
var_dump($seats);*/
echo count( $seats );

$stmt->close();
$conn->close();

$width = 36 ;
$height=36;

/*$child_width = $width - 6;
$child_height = $height - 6;*/
$box_size = 35;
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
        border-radius: 3px;
        margin: 3px 3px 3px 3px;
    }
    .box_selected{
        background-color: #333333;
    }
</style>
<h1><?php echo $plan_details['plan_name']?></h1>
<div id="seat-grid">
<!--    <div class="boxHolder">-->
    <?php
    $start = 1;
    $start_col = 0;
    $start_row = 0;
    foreach ($seats as $seat):
        $width = isset( $seat['width'] ) ? (int)$seat['width'] : 0;
        $height = isset( $seat['height'] ) ? (int)$seat['height'] : 0;

        $child_width = $width - 6;
        $child_height = $height - 4;
        if ( preg_match('/^(\d+)-(\d+)$/', $seat['id'], $matches ) ) {
            $row = intval($matches[1]);
            $col = intval($matches[2]);

            if( $start === 1 ){
                $start_row = $row;
                $start_col = $col;
            }

            $col = $col - $start_col;
            $row = $row - $start_row;
            $uniqueId = "seat-{$seat['id']}"; // Unique ID for each seat
            ?>
            <div class="box"
                 id="<?= $uniqueId ?>"
                 data-price="<?= htmlspecialchars($seat['price']) ?>"
                 style="
                     width: <?php echo $width?>px;
                     height: <?php echo $height?>px;
                     left: <?= $col * $box_size ?>px;
                     top: <?= $row * $box_size ?>px;"
                 title="Price: $<?= htmlspecialchars($seat['price']) ?>">
                <div class="boxChild"
                     style="
                             background-color: <?= htmlspecialchars($seat['color']) ?>;
                             width: <?php echo $child_width?>px;
                             height: <?php echo $child_height?>px;
                             ">

                </div>
            </div>
        <?php } ?>
    <?php $start++; endforeach; ?>
    </div>
<!--</div>-->

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
                    "position": "absolute",
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
