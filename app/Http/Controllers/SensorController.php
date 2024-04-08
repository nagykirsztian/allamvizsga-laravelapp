<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\MailController;

class SensorController extends Controller
{


    public function saveAlertData(Request $request)
{
    try {
        // Verify CSRF token
        if (!$request->header('X-CSRF-TOKEN')) {
            throw new \Exception("CSRF token is missing");
        }

        // Define path to the JSON file
        $filePath = storage_path('app/alerts.json');

        $existingData = [];
        if (file_exists($filePath)) {
            $existingData = json_decode(file_get_contents($filePath), true);
        }
        // Extract data from the request
        $sensorId = $request->input('sensorId');
        $timestamp = $request->input('timestamp');
        $value = $request->input('value');
        $thresholdType = $request->input('thresholdType');

       
        // Create alert data array
        $newData = [
            'sensorId' => $sensorId,
            'timestamp' => $timestamp,
            'value' => $value,
            'thresholdType' => $thresholdType,
        ];

        $existingData[] = $newData;

        // Convert alert data to JSON format
        $jsonData = json_encode($existingData, JSON_PRETTY_PRINT);

        // Append alert data to the JSON file
        if (file_put_contents($filePath, $jsonData) === false) {
            throw new \Exception("Failed to append data to the JSON file");
        }
        
        // Send email notifications
        $mailController = new MailController();
        $mailController->sendEmailWithData($newData); // Call the function from MailController

        return response()->json(['message' => 'Alert data saved successfully']);
    } catch (\Exception $e) {
        // Log the exception
        \Log::error('Error in saveAlertData: ' . $e->getMessage());

        // Return an error response
        return response()->json(['error' => 'Internal Server Error'], 500);
    }
}


public function store(Request $request)
    {
        //Artisan::call('udp:stop');
        $action = $request->input('action');

        if ($action === 'addsensor') {
            // Logic for adding a sensor
            // Validate the incoming request data
        $validatedData = $request->validate([
            'id' => 'required',
            'port' => 'required|numeric', // Ensure that port is an integer
        ]);

        // Create a new sensor entry
        $sensor = Post::create([
            'id' => $validatedData['id'],
            'value' => 0, // Base value of 0
            'values' => [], // Empty array
            'min' => 0,
            'max' => 100,
            'location' => 0, // Set the location
            'port' => $validatedData['port'], // Save port as int32
            // Add other fields as needed
        ]);

        $newPort = intval($validatedData['port']);
        $serverPorts = config('udp.server_ports');
        if (!in_array($newPort, $serverPorts)) {
            $serverPorts[] = $newPort;
            Config::set('udp.server_ports', $serverPorts);
            
        }

        // Update the configuration with the new server ports array
        $newConfig = [
            'server_ports' => $serverPorts,
        ];

        // Convert the configuration array to a PHP representation
        $configPHP = '<?php return ' . var_export($newConfig, true) . ';';

        // Save the configuration to the file
        file_put_contents(config_path('udp.php'), $configPHP);

        // Artisan::call('udp:listen');

        // if (Artisan::output()) {
        //     Log::info('UDP server restarted successfully after adding a new sensor.');
        // } else {
        //     Log::error('Failed to restart UDP server after adding a new sensor.');
        // }


        } elseif ($action === 'destroy') {
            // Logic for deleting a sensor
            // Validate the incoming request data
        $request->validate([
            'delete_id' => 'required',
        ]);
    
        // Find the sensor by ID and delete it
        $sensor = Post::where('id', $request->delete_id)->first();
        if ($sensor) {
            // Delete the sensor from the database
            $sensor->delete();
    
            // Get the port of the deleted sensor
            $deletedPort = $sensor->port;
    
            // Get the current server ports from the configuration
            $serverPorts = config('udp.server_ports');
    
            // Find and remove the deleted port from the server ports array
            $index = array_search($deletedPort, $serverPorts);
            if ($index !== false) {
                unset($serverPorts[$index]);
                $serverPorts = array_values($serverPorts); // Reset array keys
            }
    
            // Update the configuration with the modified server ports array
            $newConfig = [
                'server_ports' => $serverPorts,
            ];
    
            // Convert the configuration array to a PHP representation
            $configPHP = '<?php return ' . var_export($newConfig, true) . ';';
    
            // Save the configuration to the file
            file_put_contents(config_path('udp.php'), $configPHP);
        } 
        } elseif ($action === 'thresholds') {
            // Logic for setting thresholds
            // Validate the incoming request data
        $request->validate([
            'id' => 'required',
            'min' => 'required|numeric',
            'max' => 'required|numeric',
        ]);

        // Find the sensor by ID and update the minimum and maximum values
        $sensor = Post::where('id', $request->id)->first();
        if ($sensor) {
            $sensor->min = $request->min;
            $sensor->max = $request->max;
            $sensor->save();
        } 

        }

        return redirect()->route('editsensors')->with('success', 'Sensor added successfully');

        
    }


}

