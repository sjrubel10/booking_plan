$(document).ready(function () {
    const $seatGrid = $('#seat-grid');
    const rows = 15, cols = 21, boxSize = 35;

    // Generate the seat grid
    for (let row = 0; row < rows; row++) {
        for (let col = 0; col < cols; col++) {
            $seatGrid.append(
                `<div class="box" 
                      data-id="${row}-${col}" 
                      data-row="${row}" 
                      data-col="${col}" 
                      data-seat-num="" 
                      style="
                      left: ${col * boxSize}px; 
                      top: ${row * boxSize}px;
                      width: ${boxSize - 3}px;
                      height: ${boxSize -2}px;
                      ">
                </div>`
            );
        }
    }

    $('.s1ave').on( 'click', function(){
       // alert('clik');
    });

    $('.box').on("click", function () {
        if( $(this).hasClass('save') && $('#set_seat_number').hasClass('setSeatNumberSelected') ) {

            let seatNum = $('#seat_number').val();
            // alert('clik');
            $(this).text(seatNum);
            $(this).attr('data-seat-num', seatNum);
        }
    });

    // Function to initialize draggable if box has 'selected' class
    function enableDraggableIfSelected() {
        $(".box").draggable({
            revert: "invalid", // Return if drop is invalid
            helper: "clone",   // Use a clone to improve UX
            start: function (event, ui) {
                if ( !$("#enable_drag_drop").hasClass("enable_drag_drop" ) ) {
                    $(this).draggable("option", "disabled", true);
                    return false; // Disable dragging if not selected
                }
                $(this).addClass('dragging'); // Add dragging class
            },
            stop: function () {
                $(this).removeClass('dragging'); // Remove dragging class
                $(this).draggable("option", "disabled", false); // Re-enable draggable
            }
        });
    }
// Initialize droppable functionality
    $(".box").droppable({
        accept: ".box", // Accept only elements with class 'box'
        hoverClass: "drop-hover", // Add hover effect
        drop: function (event, ui) {
            const $draggedBox = $(ui.draggable); // The box being dragged
            const $targetBox = $(this); // The box where it's dropped
            // Swap positions
            swapBoxPositions($draggedBox, $targetBox);
        }
    });
    function swapBoxPositions($box1, $box2) {
        const box1Styles = { top: $box1.css("top"), left: $box1.css("left") };
        const box1Id = $box1.attr("data-id");

        const box2Styles = { top: $box2.css("top"), left: $box2.css("left") };
        const box2Id = $box2.attr("data-id");
        // Swap the CSS positions
        $box1.css({ top: box2Styles.top, left: box2Styles.left });
        $box2.css({ top: box1Styles.top, left: box1Styles.left });

        // Swap the IDs
        $box1.attr("data-id", box2Id);
        $box2.attr("data-id", box1Id);
    }
    $(".box").on("click", function () {
       /* $(".box").removeClass("selected"); // Remove 'selected' from other boxes
        $(this).addClass("selected"); // Add 'selected' to clicked box*/
        // Re-initialize draggable functionality
        $(".box").draggable("destroy"); // Destroy existing draggable
        enableDraggableIfSelected();    // Re-enable draggable with condition
    });
    enableDraggableIfSelected();

    // Apply color and price to selected boxes
    $('#apply-tool').on('click', function () {
        const color = $('#color').val();
        const price = $('#price').val();
        $('.box.selected').each(function () {
            $(this).css('background-color', color)
                .attr('data-price', price)
                .removeClass('selected').addClass('save');
        });
    });

    // Toggle Circle  click
    $(document).on('click', '#make_circle', function () {
        $(this).toggleClass('circleSelected');
    });

    $(document).on('click', '#set_seat_number', function () {
        $(this).toggleClass('setSeatNumberSelected');
    });

    $(document).on('click', '#enable_resize', function () {
        $(this).toggleClass('enable_resize_selected');
    });

    $(document).on('click', '#enable_drag_drop', function () {
        $(this).toggleClass('enable_drag_drop');
    });
    $(document).on('click', '.box', function() {
        if ($('#make_circle').hasClass('circleSelected')) {
            $(this).css('border-radius', '50%');
        }
    });

    // Toggle selection on click
    $seatGrid.on('click', '.box', function () {
        $(this).toggleClass('selected');
    });

    // Save plan functionality
    $('#save-plan').on('click', function () {
        const planName = $('#plan-name').val();
        if (!planName) {
            alert('Please enter a plan name!');
            return;
        }

        const selectedSeats = [];
        $('.box.save').each(function () {
            if ( $(this).css('background-color') !== 'rgb(255, 255, 255)') { // Not default white
                const id = $(this).data('id');
                const row = $(this).data('row');
                const col = $(this).data('col');
                const seat_number = $(this).data('seat-num');
                const color = $(this).css('background-color');
                const price = $(this).data('price') || 0;
                const width =$(this).css('width') || 0;
                const height = $(this).css('height') || 0;

                selectedSeats.push({ id, row, col, color, price, width, height, seat_number });
            }
        });

        if ( selectedSeats.length === 0 ) {
            alert('No seats selected to save!');
            return;
        }
        selectedSeats.sort((a, b) => a.col - b.col);
        console.log( selectedSeats );

        $.ajax({
            url: 'save_plan.php',
            type: 'POST',
            data: { planName, selectedSeats },
            success: function (response) {
                alert('Plan saved successfully!');
                loadPlans(); // Reload saved plans
            },
            error: function (xhr, status, error) {
                console.error(error);
            }
        });
    });

    // Drag-based multi-selection functionality
    // let isMultiSelecting = false;
    let isResizing = false; // Flag to track resizing state



    // Initialize variables
    let isMultiSelecting = false;
    let startPoint = { x: 0, y: 0 };
    let selectionBox = null;

    $seatGrid.on('mousedown', function (e) {
        if (!$("#enable_drag_drop").hasClass("enable_drag_drop")) {
            if (isResizing) return; // Prevent dragging while resizing
            isMultiSelecting = true;
            $('.box').removeClass('hovered'); // Clear previous hover highlights

            // Store starting point for selection
            startPoint = { x: e.pageX, y: e.pageY };

            // Create a selection box
            selectionBox = $('<div>').addClass('selection-box').appendTo($seatGrid);
            selectionBox.css({
                left: startPoint.x,
                top: startPoint.y,
                width: 0,
                height: 0,
            });

            e.preventDefault();
        }
    });

    $seatGrid.on('mousemove', function (e) {
        if (isMultiSelecting && !isResizing) {
            const currentPoint = { x: e.pageX, y: e.pageY };

            // Update the selection box
            const left = Math.min(startPoint.x, currentPoint.x);
            const top = Math.min(startPoint.y, currentPoint.y);
            const width = Math.abs(currentPoint.x - startPoint.x);
            const height = Math.abs(currentPoint.y - startPoint.y);

            selectionBox.css({
                left: left,
                top: top,
                width: width,
                height: height,
            });

            // Highlight elements within the selection box
            $('.box').each(function () {
                const $box = $(this);
                const boxOffset = $box.offset();
                const boxPosition = {
                    left: boxOffset.left,
                    top: boxOffset.top,
                    right: boxOffset.left + $box.outerWidth(),
                    bottom: boxOffset.top + $box.outerHeight(),
                };

                // Check if the box is within the selection area
                if (
                    boxPosition.left < left + width &&
                    boxPosition.right > left &&
                    boxPosition.top < top + height &&
                    boxPosition.bottom > top
                ) {
                    $box.addClass('hovered');
                } else {
                    $box.removeClass('hovered');
                }
            });
        }
    });

    $(document).on('mouseup', function () {
        if (isMultiSelecting) {
            isMultiSelecting = false;

            // Toggle the selection for all hovered elements
            $('.box.hovered').each(function () {
                $(this).toggleClass('selected').removeClass('hovered');
            });

            // Remove the selection box
            if (selectionBox) {
                selectionBox.remove();
                selectionBox = null;
            }
        }
    });



// Resizing functionality for selected boxes
    $seatGrid.on('mousedown', '.box.selected', function () {
        isResizing = true;
    });

    $seatGrid.on('mouseup', '.box.selected', function () {
        isResizing = false;
    });

    $seatGrid.on('click', '.box.selected', function () {
        if( $('#enable_resize').hasClass('enable_resize_selected')) {
            $(this).resizable({
                minHeight: 10,
                minWidth: 10,
                maxHeight: 1000,
                maxWidth: 800,
                handles: 'all',
                start: function (event, ui) {
                    isResizing = true;

                    let maxZIndex = 0;
                    $('.box').each(function () {
                        const currentZIndex = parseInt($(this).css('z-index')) || 0;
                        if (currentZIndex > maxZIndex) {
                            maxZIndex = currentZIndex;
                        }
                    });

                    const newZIndex = maxZIndex + 10;
                    $(this).css('z-index', newZIndex); // Apply the new z-index
                },
                resize: function (event, ui) {
                    const width = ui.size.width;
                    const height = ui.size.height;
                    $(this).data('width', width).data('height', height);
                },
                stop: function (event, ui) {
                    isResizing = false; // Reset flag when resizing stops
                    $(this).removeClass('ui-resizable ui-resizable-handle ui-resizable-all');
                    console.log('Resize complete:', ui.size.width, ui.size.height);
                }
            });
        }
    });



    // Resizing functionality for selected boxes
    /*$seatGrid.on('click', '.box.selected', function () {
        $(this).resizable({
            minHeight: 30,
            minWidth: 30,
            maxHeight: 300,
            maxWidth: 300,
            handles: 'all',
            resize: function ( event, ui ) {
                const width = ui.size.width;
                const height = ui.size.height;
                const zindex = 999;
                console.log( width, height, zindex );
                $(this).data('width', width).data('height', height).data('z-index', zindex );
            }
        });
    });*/


    // Load saved plans
    function loadPlans() {
        $.ajax({
            url: 'load_plans.php',
            type: 'GET',
            success: function (response) {
                $('#plans').html(response);
            },
            error: function (xhr, status, error) {
                console.error(error);
            }
        });
    }

    // Initialize
    loadPlans();
});
