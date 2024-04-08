<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Post;
use App\Config;


// class UDPServer extends Command
// {
//     protected $signature = 'udp:listen';
//     protected $description = 'Starts a UDP server to listen for incoming packets';

//     public function handle()
//     {
//         // Multicast configuration
//         $multicastAddress = '228.0.1.1'; // Multicast address
//         $multicastPort = 50076; // Multicast port

//         // Device configuration
//         $serverAddress = '192.168.0.101'; // Your device's IP address
//         $serverPort = 8000; // Port for clients to connect

//         // Create a UDP socket for multicast communication
//         $multicastSocket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
//         if (!$multicastSocket) {
//             $this->error("Failed to create multicast socket: " . socket_strerror(socket_last_error()));
//             return;
//         }

//         // Set socket options for multicast
//         socket_set_option($multicastSocket, IPPROTO_IP, MCAST_JOIN_GROUP, array('group' => $multicastAddress, 'interface' => 0));

//         // Bind the multicast socket to the device's IP address and port
//         if (!socket_bind($multicastSocket, $serverAddress, $multicastPort)) {
//             $this->error("Failed to bind multicast socket: " . socket_strerror(socket_last_error($multicastSocket)));
//             socket_close($multicastSocket);
//             return;
//         }

//         $this->info("Multicast UDP server started. Listening on $serverAddress:$multicastPort");

//         // Main loop
//         while (true) {
//             // Receive data from the multicast socket
//             $multicastData = '';
//             $multicastClientAddress = '';
//             $multicastClientPort = 0;
//             if (socket_recvfrom($multicastSocket, $multicastData, 1024, 0, $multicastClientAddress, $multicastClientPort) !== false) {
//                 $this->info("Received multicast data from $multicastClientAddress:$multicastClientPort: $multicastData");
//                 $this->info("Received multicast data: $multicastData");
//                 // Process the received data here
//                 $multicastData = trim($multicastData);
//                 $this->info("Received multicast data: $multicastData");
//                 // Check if the received data matches the expected format
//                 if (preg_match('/^\[(\d{2});(\d{1,2}\.\d{2}E-06);S0\]$/', $multicastData, $matches)) {
//                     $id = $matches[1];
//                     $value = $matches[2];

//                     // Store or update data in MongoDB using the Post model
//                     $post = Post::where('id', $id)->first(); // Check if data with the ID exists

//                     if ($post) {
//                         // If data with the ID exists, update the value
//                         $values = $post->values ?: [];
//                         $values[] = $value;
//                         $post->values = $values;
//                         $post->value = $value;
//                         $post->save();
//                     } else {
//                         // If data with the ID doesn't exist, create a new entry
//                         Post::create([
//                             'id' => $id,
//                             'value' => $value,
//                             'values' => [$value],
//                             'min' => 0,
//                             'max' => 100,
//                         ]);
//                     }
//                 } else {
//                     $this->error("Received data does not match the expected format: $multicastData");
//                 }
//             }

//             // Add your data processing logic here

//             // Sleep for a short while to avoid high CPU usage
//             usleep(100000); // 100 milliseconds
//         }

//         // Close the multicast socket
//         socket_close($multicastSocket);
//     }


// }

class UDPServer extends Command
{
    protected $signature = 'udp:listen';
    protected $description = 'Starts a UDP server to listen for incoming packets';
    protected $stopServer = false;

    public function handle()
{

    // pcntl_signal(SIGINT, [$this, 'handleSignal']);
    // pcntl_signal(SIGTERM, [$this, 'handleSignal']);
    // Server configuration
    $serverAddress = '192.168.1.10'; // Your server's IP address
    $serverPorts = config('udp.server_ports'); // Ports for each sensor

    // Create and bind sockets for each sensor
    $sockets = [];
    foreach ($serverPorts as $port) {
        $socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
        if (!$socket) {
            $this->error("socket_create() failed: reason: " . socket_strerror(socket_last_error()));
            return;
        }

        if (!socket_bind($socket, $serverAddress, $port)) {
            $this->error("socket_bind() failed for port $port: reason: " . socket_strerror(socket_last_error($socket)));
            return;
        }

        $sockets[$port] = $socket;
    }

    $this->info("UDP server started. Waiting for incoming packets...");

    while (true) {
        // Loop through each socket and check for incoming data
        foreach ($sockets as $port => $socket) {
            $data = '';
            $clientIP = '';
            $clientPort = 0;

            // Receive data from the socket
            if (socket_recvfrom($socket, $data, 1024, 0, $clientIP, $clientPort) !== false) {
                $this->info("Got data on Port $port");
                $data = trim($data);

                // Check if the received data matches the expected format
                if (preg_match('/^\[(\d{2});(\d{1,2}\.\d{2}E-04);S0\]$/', $data, $matches)) {
                                       $id = $matches[1];
                                        $value = $matches[2];
                    // Store or update data in MongoDB using the Post model
                    $post = Post::where('id', $id)->first(); // Check if data with the ID exists

                    if ($post) {
                        // If data with the ID exists, update the value
                        $values = $post->values ?: [];
                        $values[] = $value;
                        $post->values = $values;

                        // Update the current value
                        $post->value = $value;
                        $post->location = 0; // Set the location

                        $post->save();
                    } else {
                        // If data with the ID doesn't exist, create a new entry
                        Post::create([
                            'id' => $id,
                            'value' => $value,
                            'values' => [$value],
                            'min' => 0,
                            'max' => 100,
                            'location' => 0, // Set the location
                        ]);
                    }
                } else {
                    $this->error("Received data does not match the expected format: $data");
                }
            }
        }
    }

    // Close all sockets
    foreach ($sockets as $socket) {
        socket_close($socket);
    }
}

// public function handleSignal($signal)
//     {
//         $this->info('Received signal to stop server.');
//         $this->stopServer = true;
//     }

 }

