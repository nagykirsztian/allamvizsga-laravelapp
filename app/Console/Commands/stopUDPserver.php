<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class StopUDPServer extends Command
{
    protected $signature = 'udp:stop';
    protected $description = 'Stop the UDP server gracefully';

    public function handle()
    {
        $this->info('Sending termination signal to UDP server...');

        // Find the process ID (PID) of the UDP server command
        $process = new Process(['pgrep', '-f', 'udp:listen']);
        $process->run();

        if ($process->isSuccessful()) {
            $pid = trim($process->getOutput());
            // Send the SIGTERM signal to gracefully stop the UDP server
            posix_kill($pid, SIGTERM);
            $this->info('Termination signal sent to UDP server.');
        } else {
            $this->error('Failed to find the PID of the UDP server process.');
        }
    }
}
