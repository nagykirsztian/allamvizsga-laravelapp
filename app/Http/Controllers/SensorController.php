<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use App\Http\Controllers\Controller;

class SensorController extends Controller
{
    public function saveAlertData(Request $request)
{
    try {
        // Verify CSRF token
        if (!$request->header('X-CSRF-TOKEN')) {
            throw new \Exception("CSRF token is missing");
        }

        // Extract data from the request
        $sensorId = $request->input('sensorId');
        $timestamp = $request->input('timestamp');
        $value = $request->input('value');
        $thresholdType = $request->input('thresholdType');

        // Create alert data array
        $alertData = [
            'sensorId' => $sensorId,
            'timestamp' => $timestamp,
            'value' => $value,
            'thresholdType' => $thresholdType,
        ];

        // Convert alert data to JSON format
        $alertJson = json_encode($alertData);

        // Define path to the JSON file
        $filePath = storage_path('app/alerts.json');

        // Append alert data to the JSON file
        if (File::append($filePath, $alertJson . PHP_EOL) === false) {
            throw new \Exception("Failed to append data to the JSON file");
        }

        return response()->json(['message' => 'Alert data saved successfully']);
    } catch (\Exception $e) {
        // Log the exception
        \Log::error('Error in saveAlertData: ' . $e->getMessage());

        // Return an error response
        return response()->json(['error' => 'Internal Server Error'], 500);
    }
}

       


}

