<!DOCTYPE html>
<html>
<head>
    <title>Sensor Data</title>
    <meta name="csrf-token" content="BxKhgFVJfC38q90Ty6oR7G2fort01XmHXr7HxBQ4">
    <!-- Add any necessary styles or scripts -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
    var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

function drawChart(canvasId, values) {
    var ctx = document.getElementById(canvasId).getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: [...Array(20).keys()].map(num => num + 1),
            datasets: [{
                label: 'Sensor Data',
                data: values,
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            animation: {
                duration: 1000, // Duration of the animation in milliseconds
                easing: 'easeInOutQuad', // Easing function
                animateScale: true, // Animate scale changes (e.g., y-axis scaling)
                animateRotate: true // Animate rotation of the chart (for pie/doughnut charts)
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
}

// Function to send alert data to the server
function sendAlertData(sensorId, timestamp, value, thresholdType) {
    $.ajax({
        url: '/save-alert-data', // Route to save alert data
        type: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('#csrf-token').val() // Include CSRF token in headers
        },
        data: {
            sensorId: sensorId,
            timestamp: timestamp,
            value: value,
            thresholdType: thresholdType // 'min' or 'max'
        },
        success: function(response) {
            console.log('Alert data saved:', response);
        },
        error: function(xhr, status, error) {
            console.error(error);
        }
    });
}


// Function to check for value thresholds and send alert data
function checkThresholds(data) {
    if (data.value > data.max) {
        var timestamp = new Date().toISOString();
        sendAlertData(data.id, timestamp, data.value, 'max');
    } else if (data.value < data.min) {
        var timestamp = new Date().toISOString();
        sendAlertData(data.id, timestamp, data.value, 'min');
    }
}

// Function to fetch sensor data and update table
function updateSensorData() {

    // Store current scroll position
    var scrollPosition = $(window).scrollTop();

    $.ajax({
        url: '/sensor-data-json', // Route to fetch sensor data as JSON
        type: 'GET',
        success: function(response) {
            $('#sensorDataBody').empty();
        
            // Populate table with sensor data and create charts
            $.each(response, function(index, data) {
                var row = '<tr>';
                row += '<td>' + data.id + '</td>';
                row += '<td>' + data.value + '</td>';
                row += '<td>' + data.min + '</td>'; // Display min value
                row += '<td>' + data.max + '</td>'; // Display max value
                // Create chart canvas element
                
                var canvasId = 'chart_' + data.id;
                row += '<td><canvas id="' + canvasId + '" width="1000" height="200"></canvas></td>';

                row += '</tr>';
                $('#sensorDataBody').append(row);

                // Draw chart for the current sensor
                var values = data.values.slice(-20);
                drawChart(canvasId, values);

                 // Check thresholds for the current sensor data
                 checkThresholds(data);
            });

            // Restore scroll position
            $(window).scrollTop(scrollPosition);
        },
        error: function(xhr, status, error) {
            console.error(error);
        }
    });
}




// Fetch and update sensor data every second
$(document).ready(function() {
    updateSensorData(); // Initial call
    setInterval(updateSensorData, 5000); // Refresh every 1 second
});


    </script>
</head>
<body>
    <h1>Sensor Data</h1>
    <div id="class-container">
    <input type="hidden" id="csrf-token" value="{{ csrf_token() }}">
        <table border="1">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Value</th>
                    <th>MinValue</th>
                    <th>MaxValue</th>
                    <th>Chart</th>
                </tr>
            </thead>
            <tbody id="sensorDataBody">
                <!-- Table rows will be populated dynamically via JavaScript -->
            </tbody>
        </table>
    </div>   
</body>
</html>
