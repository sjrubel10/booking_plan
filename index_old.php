<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seat Plan Editor</title>
    <style>
        /* Basic styles for the toolbox and seat grid */
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        #toolbox {
            margin-bottom: 20px;
        }
        #seat-grid {
            display: grid;
            grid-template-columns: repeat(60, 20px);
            gap: 2px;
            margin-top: 20px;
        }
        .box {
            width: 20px;
            height: 20px;
            background-color: #ddd;
            border: 1px solid #aaa;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            position: relative;
        }
        .box.selected {
            border: 2px solid #333;
        }
        .tooltip {
            position: absolute;
            top: -20px;
            left: 50%;
            transform: translateX(-50%);
            background-color: #333;
            color: #fff;
            padding: 2px 5px;
            font-size: 12px;
            display: none;
        }
        .box:hover .tooltip {
            display: block;
        }
    </style>
</head>
<body>
<div id="toolbox">
    <label for="color">Color:</label>
    <input type="color" id="color" value="#ff0000">
    <label for="price">Price:</label>
    <input type="number" id="price" min="0" placeholder="Set Price">
    <button id="apply-tool">Apply</button>
</div>

<div id="seat-grid"></div>
<button id="save-plan">Save Plan</button>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function () {
        // Generate initial seat grid
        const rows = 30;
        const cols = 20;
        const $seatGrid = $('#seat-grid');

        for (let i = 0; i < rows * cols; i++) {
            const $box = $('<div class="box"></div>');
            $seatGrid.append($box);
        }

        // Tool settings
        let selectedBoxes = [];
        let selectedColor = $('#color').val();
        let selectedPrice = $('#price').val();

        // Select box functionality
        $seatGrid.on('click', '.box', function () {
            $(this).toggleClass('selected');
        });

        // Apply toolbox settings
        $('#apply-tool').on('click', function () {
            selectedColor = $('#color').val();
            selectedPrice = $('#price').val();

            $('.box.selected').each(function () {
                $(this).css('background-color', selectedColor);
                if (selectedPrice) {
                    $(this).attr('data-price', selectedPrice);
                    const tooltipText = `Price: $${selectedPrice}`;
                    $(this).html(`<span class="tooltip">${tooltipText}</span>`);
                }
                $(this).removeClass('selected');
            });
        });

        $('#save-plan').on('click', function () {
            const selectedSeats = [];
            $('.box[data-price]').each(function () {
                const seatData = {
                    seat_id: $(this).index(), // Unique seat ID (row-col)
                    color: $(this).css('background-color'),
                    price: $(this).data('price')
                };
                selectedSeats.push(seatData);
            });

            $.ajax({
                url: 'save_plan.php',
                type: 'POST',
                data: { seats: selectedSeats },
                success: function (response) {
                    alert('Seat plan saved successfully!');
                },
                error: function (xhr, status, error) {
                    console.error('Error:', error);
                }
            });
        });

    });
</script>
</body>
</html>
