$(document).ready(function () {
    const $seatGrid = $('#seat-grid');
    const rows = 20, cols = 20, boxSize = 30;

    // Generate the seat grid
    for (let row = 0; row < rows; row++) {
        for (let col = 0; col < cols; col++) {
            $seatGrid.append(
                `<div class="box" 
                      data-id="${row}-${col}" 
                      data-row="${row}" 
                      data-col="${col}" 
                      data-seat-num="" 
                      style="left: ${col * boxSize}px; top: ${row * boxSize}px;">
                </div>`
            );
        }
    }


    // Function to initialize draggable for selected boxes
    function enableDraggableIfSelected() {
        $(".box").draggable({
            revert: "invalid", // Return if drop is invalid
            helper: function () {
                // Create a custom helper containing all selected elements
                if ($(".box.selected").length > 1) {
                    const $helper = $("<div class='multi-drag-helper'></div>");
                    $(".box.selected").each(function () {
                        const $clone = $(this).clone();
                        $clone.css({ position: "absolute", top: 0, left: 0 });
                        $helper.append($clone);
                    });
                    return $helper;
                }
                return "clone"; // Use clone for a single element
            },
            start: function (event, ui) {
                if (!$("#enable_drag_drop").hasClass("enable_drag_drop")) {
                    $(this).draggable("option", "disabled", true);
                    return false; // Disable dragging if not allowed
                }
                $(this).addClass("dragging"); // Add dragging class
            },
            stop: function () {
                $(this).removeClass("dragging"); // Remove dragging class
                $(this).draggable("option", "disabled", false); // Re-enable draggable
            }
        });
    }

// Initialize droppable functionality
    $(".box").droppable({
        accept: ".box", // Accept only elements with class 'box'
        hoverClass: "drop-hover", // Add hover effect
        drop: function (event, ui) {
            const $draggedBoxes = $(".box.selected"); // The boxes being dragged
            const $targetBox = $(this); // The box where it's dropped

            // Calculate the offset for positioning
            const targetOffset = $targetBox.offset();
            const parentOffset = $targetBox.parent().offset();
            const baseLeft = targetOffset.left - parentOffset.left;
            const baseTop = targetOffset.top - parentOffset.top;

            // Reposition each selected box relative to the drop target
            $draggedBoxes.each(function (index) {
                const $box = $(this);
                const offsetX = parseInt($box.css("left"), 10) || 0;
                const offsetY = parseInt($box.css("top"), 10) || 0;

                $box.css({
                    left: `${baseLeft + offsetX}px`,
                    top: `${baseTop + offsetY}px`,
                    position: "absolute"
                });
            });
        }
    });

// Handle click event for selecting/deselecting boxes
    $(".box").on("click", function () {
        $(this).toggleClass("selected"); // Toggle selected class

        // Re-initialize draggable functionality
        $(".box").draggable("destroy"); // Destroy existing draggable
        enableDraggableIfSelected(); // Re-enable draggable with condition
    });

// Initialize draggable functionality
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
                const color = $(this).css('background-color');
                const price = $(this).data('price') || 0;
                const width =$(this).css('width') || 0;
                const height = $(this).css('height') || 0;

                selectedSeats.push({ id, row, col, color, price, width, height });
            }
        });

        if ( selectedSeats.length === 0 ) {
            alert('No seats selected to save!');
            return;
        }

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
    let isMultiSelecting = false;
    let isResizing = false; // Flag to track resizing state

// Drag-based multi-selection
    $seatGrid.on('mousedown', function (e) {
        if (isResizing) return; // Prevent dragging while resizing
        isMultiSelecting = true;
        $('.box').removeClass('hovered'); // Clear previous hover highlights
        e.preventDefault();
    });

    $seatGrid.on('mousemove', function (e) {
        if (isMultiSelecting && !isResizing) {
            const hoveredElement = $(document.elementFromPoint(e.pageX, e.pageY));
            if (hoveredElement.hasClass('box') && !hoveredElement.hasClass('hovered')) {
                hoveredElement.addClass('hovered');
            }
        }
    });

    $(document).on('mouseup', function () {
        if (isMultiSelecting) {
            isMultiSelecting = false;
            $('.box.hovered').each(function () {
                $(this).toggleClass('selected').removeClass('hovered');
            });
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
