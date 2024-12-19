<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Drag, Drop & Resize Multiple Divs</title>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <style>
        #parentDiv {
            width: 600px;
            height: 600px;
            border: 1px solid #333;
            position: relative;
        }
        .childDiv {
            width: 100px;
            height: 100px;
            background-color: lightblue;
            border: 1px solid #000;
            position: absolute; /* Allows free positioning */
            cursor: move;
            text-align: center;
            line-height: 100px;
            user-select: none;
        }
        .selected {
            border: 2px dashed red;
            background-color: lightcoral;
        }
    </style>
</head>
<body>

<h1>Drag, Drop & Resize Multiple Divs</h1>
<p>Click to select multiple divs, resize them, and drag them together.</p>

<div id="parentDiv">
    <?php for( $i= 0; $i< 24; $i++){
        $j = $i + 1;
        ?>
    <div class="childDiv" data-id="<?php echo $i?>"><?php echo $i?></div>
    <?php }?>
</div>

<!-- jQuery and jQuery UI -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>

<script>
    $(document).ready(function () {

        let $parentDiv = $("#parentDiv");
        let childWidth = 100; // Width of each child div
        let childHeight = 100; // Height of each child div
        let gap = 10; // Gap between divs
        let parentWidth = $parentDiv.width();

        function arrangeDivs() {
            let x = 0; // Start position left
            let y = 0; // Start position top

            $(".childDiv").each(function (index) {
                $(this).css({
                    left: x + "px",
                    top: y + "px"
                });

                // Calculate next position
                x += childWidth + gap;

                // If next position exceeds parent width, move to next row
                if (x + childWidth > parentWidth) {
                    x = 0;
                    y += childHeight + gap;
                }
            });
        }

        arrangeDivs(); // Arrange divs on page load

        let selectedDivs = []; // Array to track selected divs

        // Single-click to toggle selection
        $(".childDiv").on("click", function (e) {
            e.stopPropagation();

            if (!e.ctrlKey) { // Clear selection if Ctrl is not pressed
                $(".childDiv").removeClass("selected");
                selectedDivs = [];
            }

            $(this).toggleClass("selected");
            let id = $(this).data("id");

            if ($(this).hasClass("selected")) {
                selectedDivs.push($(this));
            } else {
                selectedDivs = selectedDivs.filter(div => div.data("id") !== id);
            }
        });

        // Draggable group functionality
        $(".childDiv").draggable({
            containment: "#parentDiv",
            drag: function (event, ui) {
                let current = $(this);
                let offsetX = ui.position.left - current.position().left;
                let offsetY = ui.position.top - current.position().top;

                selectedDivs.forEach(div => {
                    if (div[0] !== current[0]) { // Exclude the dragged element
                        div.css({
                            top: div.position().top + offsetY + "px",
                            left: div.position().left + offsetX + "px"
                        });
                    }
                });
            },
            stop: savePositions
        });

        // Enable resizing for child divs
        $(".childDiv").resizable({
            containment: "#parentDiv",
            handles: "all",
            resize: function (event, ui) {
                let current = $(this);
                let deltaWidth = ui.size.width - ui.originalSize.width;
                let deltaHeight = ui.size.height - ui.originalSize.height;

                // Resize other selected divs proportionally
                selectedDivs.forEach(div => {
                    if (div[0] !== current[0]) {
                        div.css({
                            width: div.width() + deltaWidth + "px",
                            height: div.height() + deltaHeight + "px"
                        });
                    }
                });
            },
            stop: savePositions
        });

        // Save positions and dimensions via AJAX
        function savePositions() {
            selectedDivs.forEach(div => {
                let position = div.position();
                let width = div.width();
                let height = div.height();
                let divID = div.data("id");

                $.ajax({
                    url: "save_position.php",
                    type: "POST",
                    data: {
                        id: divID,
                        top: position.top,
                        left: position.left,
                        width: width,
                        height: height
                    },
                    success: function (response) {
                        console.log(`Position saved for div ${divID}`);
                    },
                    error: function (xhr, status, error) {
                        console.error(`Error saving div ${divID}: `, error);
                    }
                });
            });
        }

        // Deselect all on clicking outside parent div
        $(document).on("click", function () {
            $(".childDiv").removeClass("selected");
            selectedDivs = [];
        });
    });
</script>
</body>
</html>
