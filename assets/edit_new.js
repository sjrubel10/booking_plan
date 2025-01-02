$(document).ready(function () {
    $(document).on('click', '#enable_resize', function () {
        $(this).toggleClass('enable_resize_selected');
        if( !$(this).hasClass( 'enable_resize_selected' )){
            $(".childDiv").each(function () {
                if ($(this).data("ui-resizable")) {
                    $(this).resizable("destroy");
                }
            });
        }
    });

    $(document).on('click', '#set_multiselect', function () {
        $(this).toggleClass('enable_set_multiselect');
    });

    $(document).on('click', '#set_seat_number', function () {
        $(this).toggleClass('enable_set_seat_number');
    });

    $(document).on('click', '#enable_drag_drop', function () {
        $(this).toggleClass('enable_drag_drop');
        if( !$(this).hasClass( 'enable_drag_drop' )){
            $(".childDiv").removeClass("ui-draggable ui-draggable-handle");
            // selectedDivs = [];
        }
        //ui-draggable ui-draggable-handle
    });

    let selectedDivs = [];

    $(document).on('click', '#clearAll', function () {
        $('.childDiv').removeClass('save');
        $('.childDiv').removeClass('selected');
        $('.childDiv').css('background', '');
        selectedDivs = [];
    });
    // Handle selection
    $(".childDiv").on("click", function (e) {
        e.stopPropagation();

        let count = parseInt($("#seat_number_count").val(), 10);
        if( $("#set_seat_number").hasClass('enable_set_seat_number') ){
            count++;
            let seat_prefix = $("#seat_number_prefix").val();
            $("#seat_number_count").val(count);
            if( seat_prefix !== '' ){
                var seat_number = seat_prefix+'-'+count;
            }else{
                seat_number = count;
            }
            // console.log( count );
            $(this).text( seat_number );
            $(this).attr('data-seat-num', seat_number);
        }

        $(this).toggleClass("selected");
        const $this = $(this);

        /*if ($this.hasClass("save ") && !($this.hasClass("selected") ){
            $(this).css(){
                'backgroun'
            }
        }*/

        if ($this.hasClass("selected")) {

            selectedDivs.push($this);
            if( $('#enable_resize').hasClass('enable_resize_selected')) {
                if (!$this.data("ui-resizable")) {
                    $this.resizable({
                        containment: "#parentDiv",
                        handles: "all",
                        start: function (event, ui) {
                            isResizing = true;

                            let maxZIndex = 0;
                            $('.childDiv').each(function () {
                                const currentZIndex = parseInt($(this).css('z-index')) || 0;
                                if (currentZIndex > maxZIndex) {
                                    maxZIndex = currentZIndex;
                                }
                            });

                            const newZIndex = maxZIndex + 10;
                            $(this).css('z-index', newZIndex); // Apply the new z-index
                        },
                        stop: debounce(300)
                    });
                }
            }
            else{
                if ($this.data("ui-resizable")) {
                    $this.resizable("destroy");
                }
            }

            if( $('#enable_drag_drop').hasClass( 'enable_drag_drop' )) {
                $(this).draggable({
                    containment: "#parentDiv",
                    drag: function (event, ui) {
                        const current = $(this);
                        const offsetX = ui.position.left - current.position().left;
                        const offsetY = ui.position.top - current.position().top;

                        selectedDivs.forEach(div => {
                            if (div[0] !== current[0]) {
                                div.css({
                                    top: div.position().top + offsetY + "px",
                                    left: div.position().left + offsetX + "px"
                                });
                            }
                        });
                    },
                    stop: debounce(300)
                });
            }
        } else {
            // Remove resizable functionality for deselected div

            selectedDivs = selectedDivs.filter(div => div[0] !== $this[0]);
        }
    });

    // Drag functionality


    // Apply color and price changes
    $("#applyChanges").on("click", function () {
        const color = $("#setColor").val();
        const price = $("#setPrice").val();

        selectedDivs.forEach(div => {
            if( div.hasClass('selected')){
                div.addClass("save").removeClass('selected');
                if (color) div.css("background-color", color);
                if (price) div.attr("data-price", price)/*.text(price)*/;
            }

        });
        selectedDivs = [];
    });

    $('#savePlan').on('click', function () {
        const plan_id = $('#plan_id').val();
        /*const planName = $('#plan-name').val();
        if (!planName) {
            alert('Please enter a plan name!');
            return;
        }*/
        var selectedSeats = [];
        $('.childDiv.save').each(function () {
            if ( $(this).css('background-color') !== 'rgb(255, 255, 255)') { // Not default white
                const id = $(this).data('id');
                const row = $(this).data('row');
                const col = $(this).data('col');
                const seat_number = $(this).data('seat-num');
                const color = $(this).css('background-color');
                const price = $(this).data('price') || 0;
                const width =$(this).css('width') || 0;
                const height = $(this).css('height') || 0;
                const z_index = $(this).css('z-index') || 0;
                const left = $(this).css('left') || 0;
                const top = $(this).css('top') || 0;

                selectedSeats.push({ id, row, col, color, price, width, height, seat_number, left, top, z_index });
            }
        });

        if ( selectedSeats.length === 0 ) {
            alert('No seats selected to save!');
            return;
        }
        // selectedSeats.sort((a, b) => a.col - b.col);
        // console.log( selectedSeats );

        $.ajax({
            url: 'save_edited_plan.php',
            type: 'POST',
            data: { plan_id, selectedSeats },
            success: function (response) {
                alert(' Edited Plan saved successfully!');
                loadPlans(); // Reload saved plans
                selectedSeats = [];
            },
            error: function (xhr, status, error) {
                console.error(error);
            }
        });
    });

    // Save positions to server

    // Debounce function to optimize frequent calls
    function debounce(func, wait) {
        let timeout;
        return function (...args) {
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(this, args), wait);
        };
    }

    // Deselect on outside click
    /*$(document).on("click", function () {
        $(".childDiv").removeClass("selected").each(function () {
            if ($(this).data("ui-resizable")) {
                $(this).resizable("destroy");
            }
        });
        selectedDivs = [];
    });*/


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
    // loadPlans();

    let isMultiSelecting = false;
    let startPoint = { x: 0, y: 0 };
    let selectionBox = null;
    let $seatGrid = $("#parentDiv");
    $seatGrid.on('mousedown', function (e) {
        if (!$("#enable_drag_drop").hasClass("enable_drag_drop")) {
            if( $("#set_multiselect").hasClass('enable_set_multiselect')){
                isMultiSelecting = true;
            }

            $('.childDiv').removeClass('hovered'); // Clear previous hover highlights

            startPoint = { x: e.pageX, y: e.pageY };

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
        if (isMultiSelecting) {
            const currentPoint = { x: e.pageX, y: e.pageY };

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

            $('.childDiv').each(function () {
                const $box = $(this);
                const boxOffset = $box.offset();
                const boxPosition = {
                    left: boxOffset.left,
                    top: boxOffset.top,
                    right: boxOffset.left + $box.outerWidth(),
                    bottom: boxOffset.top + $box.outerHeight(),
                };

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
    // let count = 0;
    $(document).on('mouseup', function () {
        if (isMultiSelecting) {
            isMultiSelecting = false;

            let count = parseInt($("#seat_number_count").val(), 10);
            $('.childDiv.hovered').each(function ( div ) {

                if( $("#set_seat_number").hasClass('enable_set_seat_number') ){
                    count++;
                    let seat_prefix = $("#seat_number_prefix").val();
                    $("#seat_number_count").val(count);
                    if( seat_prefix !== '' ){
                        var seat_number = seat_prefix+'-'+count;
                    }else{
                        seat_number = count;
                    }
                    // console.log( count );
                    $(this).text( seat_number );
                    $(this).attr('data-seat-num', seat_number);
                }

                // console.log( seat_prefix );
                selectedDivs.push($(this));
                $(this).toggleClass('selected').removeClass('hovered');
            });

            // Remove the selection box
            if (selectionBox) {
                selectionBox.remove();
                selectionBox = null;
            }
        }
    });

    /*function seat_number(){

    }*/

});