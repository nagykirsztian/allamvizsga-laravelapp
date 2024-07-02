 <?php

$multicastAddress = '192.168.1.12'; // Multicast address
$port = 8002; // UDP port

function sendMulticastUDPPacket($address, $port) {
    // Generate random data within the specified range
    $data = '[07;' . number_format(rand(100, 150) / 100, 2) . 'E-04;S0]';

    $socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);

    if ($socket === false) {
        echo "socket_create() failed: reason: " . socket_strerror(socket_last_error()) . "\n";
    } else {
        // Set socket option for multicast
        if (!socket_set_option($socket, IPPROTO_IP, MCAST_JOIN_GROUP, array('group' => $address, 'interface' => 0))) {
            echo "socket_set_option() failed: reason: " . socket_strerror(socket_last_error($socket)) . "\n";
            socket_close($socket);
            return;
        }

        if (!socket_sendto($socket, $data, strlen($data), 0, $address, $port)) {
            echo "socket_sendto() failed: reason: " . socket_strerror(socket_last_error($socket)) . "\n";
        } else {
            echo "Multicast UDP packet sent to $address:$port - Message: $data\n";
        }
        socket_close($socket);
    }
}

// Loop to send multicast UDP packets every 4 seconds
while (true) {
    sendMulticastUDPPacket($multicastAddress, $port);
    sleep(4); // Wait for 4 seconds before sending the next packet
} 


