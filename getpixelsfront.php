<!DOCTYPE html>
<html>
<head>
    <title>Virtual Try-On for Neck Jewelry</title>
    <script src="/vton/js/code.jquery.com_jquery-3.6.0.min.js"></script>
</head>
<body>
    <img id="necklaceImage" src="/vton/necklace/gchain.jpeg" alt="Necklace" style="height:500px;">
    <img id="humanimage" src="/vton/necklace/default-face-image.png" alt="human" style="height:500px;display:none"/>
    <canvas id="necklaceCanvas"></canvas>
    <div id="pointInfo" style="font-weight: bold;"></div>
    <button id="sendButton">Send Coordinates</button> <!-- New button for sending coordinates -->
    <button id="clearButton">Clear Points</button> <!-- Clear button -->

    <script>
        const image = document.getElementById('necklaceImage');
        const canvas = document.getElementById('necklaceCanvas');
        const ctx = canvas.getContext('2d');
        const topPoint = { x: 0, y: 0 };
        const bottomPoint = { x: 0, y: 0 };
        const pointInfo = document.getElementById('pointInfo');

        // Function to draw a marker on the canvas
        function drawMarker(x, y, color) {
            ctx.fillStyle = color;
            ctx.beginPath();
            ctx.arc(x, y, 5, 0, Math.PI * 2);
            ctx.fill();
        }

        // Function to draw a reference line between two points
        function drawReferenceLine(point1, point2, color) {
            ctx.strokeStyle = color;
            ctx.beginPath();
            ctx.moveTo(point1.x, point1.y);
            ctx.lineTo(point2.x, point2.y);
            ctx.stroke();
        }

        // Function to update the point information and draw reference lines
        function updatePointInfo() {
            pointInfo.innerHTML = `Top Point: x=${topPoint.x}, y=${topPoint.y} <br> Bottom Point: x=${bottomPoint.x}, y=${bottomPoint.y}`;
            
            // Clear the canvas and redraw the image
            canvas.width = image.width;  // Set the canvas width to match the image width
            canvas.height = image.height; // Set the canvas height to match the image height
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            ctx.drawImage(image, 0, 0, image.width, image.height);
            
            // Draw reference lines if both top and bottom points are set
            if (topPoint.x && topPoint.y && bottomPoint.x && bottomPoint.y) {
                drawReferenceLine(topPoint, bottomPoint, 'green');
            }
            
            // Draw markers on top of the reference lines
            if (topPoint.x && topPoint.y) {
                drawMarker(topPoint.x, topPoint.y, 'red');
            }
            if (bottomPoint.x && bottomPoint.y) {
                drawMarker(bottomPoint.x, bottomPoint.y, 'blue');
            }
        }

        // Ensure the image is loaded before adjusting canvas size
        image.onload = function () {
            updatePointInfo();
        };

        // Event listener to mark top and bottom points
        canvas.addEventListener('click', function (e) {
            const rect = canvas.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;

            if (!topPoint.x && !topPoint.y) {
                topPoint.x = x;
                topPoint.y = y;
                updatePointInfo();
            } else if (!bottomPoint.x && !bottomPoint.y) {
                bottomPoint.x = x;
                bottomPoint.y = y;
                updatePointInfo();
            }
        });

        // Event listener to clear the points
        const clearButton = document.getElementById('clearButton');
        clearButton.addEventListener('click', function () {
            topPoint.x = 0;
            topPoint.y = 0;
            bottomPoint.x = 0;
            bottomPoint.y = 0;
            updatePointInfo();
        });
        
        const sendCoordinates = document.getElementById('sendButton');
        
        // Get a reference to the necklace image element in your HTML
        var imageElement = document.getElementById('necklaceImage');

        // Get a reference to the human image element in your HTML
        var human_imageElement = document.getElementById('humanimage');

        // Create a new FileReader
        var reader = new FileReader();
        var base64Image = reader.result;
        var human_reader = new FileReader();
        var human_image = human_reader.result;

        //ajax request to send coordinates and images
        sendCoordinates.addEventListener('click',function () {
            //alert();
            points = {};
            points['thorax_top_x'] = 245;
            points['thorax_top_y'] = 145;
            points['thorax_bottom_x'] = 245;
            points['thorax_bottom_y'] = 482;
            //canvas.width = image.width;
            //canvas.height = image.height;
            //ctx.drawImage(image, 0, 0, image.width, image.height);
            const base64Image = canvas.toDataURL('image/png');
            const human_image = canvas.toDataURL('image/png');
            jQuery.ajax({
                url: '/overlayimage',
                type: "POST",
                data: {
                    points: points,
                    jewellery_image: base64Image,
                    human_image: human_image                        
                    //requestData: points
                },
                dataType: "json",
                success: function (data) {
                    //console.log(data);
                },
                error: function (error) {
                    //console.log(`Error ${error}`);
                    console.log(error);
                    console.log("points : ")
                    console.log(points);
                }
            });
        });
    </script>
</body>
</html>