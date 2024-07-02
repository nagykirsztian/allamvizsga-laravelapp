<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alert Email</title>
</head>
<body>
    <h1>New Sensor Alert</h1>
    
    <p>A new sensor alert has been triggered:</p>
    
    <ul>
        <li><strong>Sensor ID:</strong> {{ $data['sensorId'] }}</li>
        <li><strong>Timestamp:</strong> {{ $data['timestamp'] }}</li>
        <li><strong>Value:</strong> {{ $data['value'] }}</li>
        <li><strong>Threshold Type:</strong> {{ $data['thresholdType'] }}</li>
    </ul>
    
   
</body>
</html>
