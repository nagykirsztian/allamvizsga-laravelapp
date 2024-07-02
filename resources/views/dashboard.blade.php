    <head>
        <title>Sensor Data</title>
        <meta name="csrf-token" content="BxKhgFVJfC38q90Ty6oR7G2fort01XmHXr7HxBQ4">
        <!-- Add any necessary styles or scripts -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
        var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        var alertsSent = {};
        var lastAlertTimestamps = {};
        var previousSensorData = {};
        var lastAlertTimestamp = {};

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
            // Check if 5 minutes have passed since the last alert
        // if (lastAlertTimestamp[sensorId] && Date.now() - lastAlertTimestamp[sensorId] < 5 * 60 * 1000) {
        //     // Skip sending the alert if less than 5 minutes have passed
        //     console.log('Skipping alert for sensor ' + sensorId + '. Less than 5 minutes have passed since the last alert.');
        //     return;
        // }

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
                // Update the last alert timestamp for this sensor
                lastAlertTimestamp[sensorId] = Date.now();
            },
            error: function(xhr, status, error) {
                console.error(error);
            }
        });
    }


    // Function to check for value thresholds and send alert data
    function checkThresholds(data) {
        // Get the current timestamp
        var currentTimestamp = new Date().getTime();

        // Print current timestamp for debugging
        console.log('Current timestamp:', currentTimestamp);

        // Check if alerts have been sent for this sensor
        if (!lastAlertTimestamps.hasOwnProperty(data.id)) {
            lastAlertTimestamps[data.id] = 0; // Initialize last alert timestamp
        }

        // Print last alert timestamp for debugging
        console.log('Last alert timestamp for sensor ID ' + data.id + ':', lastAlertTimestamps[data.id]);

        // Check if the value exceeds the maximum threshold
        if (data.value > data.max && (currentTimestamp - lastAlertTimestamps[data.id] >  60000)) {
            var timestamp = new Date().toISOString();
            sendAlertData(data.id, timestamp, data.value, 'max');
            lastAlertTimestamps[data.id] = currentTimestamp; // Update last alert timestamp

            // Print alert sent message for debugging
            console.log('Alert sent for exceeding maximum threshold.');
        } else if (data.value < data.min && (currentTimestamp - lastAlertTimestamps[data.id] >  60000)) {
            // Check if the value is below the minimum threshold
            var timestamp = new Date().toISOString();
            sendAlertData(data.id, timestamp, data.value, 'min');
            lastAlertTimestamps[data.id] = currentTimestamp; // Update last alert timestamp

            // Print alert sent message for debugging
            console.log('Alert sent for falling below minimum threshold.');
        } else {
            // Print message indicating that no alert is sent
            console.log('No alert sent. Either threshold not exceeded or last alert sent within last 5 minutes.');
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
                    var row = '<tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">';
                    row += '<td class="px-6 py-4">' + data.id + '</td>';
                    row += '<td class="px-6 py-4">' + data.value + '</td>';
                    row += '<td class="px-6 py-4">' + data.min + '</td>'; // Display min value
                    row += '<td class="px-6 py-4">' + data.max + '</td>'; // Display max value
                    // Create chart canvas element
                    var canvasId = 'chart_' + data.id;
                    row += '<td><canvas id="' + canvasId + '" width="800" height="200"></canvas></td>';
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
    <style>


            </style>
    </head>

    <x-app-layout>
        <x-slot name="header">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Dashboard') }}
            </h2>
        </x-slot>

        <div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8"> 
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">  
                

            <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
    <input type="hidden" id="csrf-token" value="{{ csrf_token() }}">
        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr> 
                
                    <th scope="col" class="px-6 py-3">
                        ID
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Value
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Min
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Max
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Chart
                    </th>
                </tr>
            </thead>
            <tbody id="sensorDataBody" >
                
            </tbody>
        </table>
    </div>


        
                    </div> 
                </div>
            </div>
        </div> 
    </x-app-layout>
