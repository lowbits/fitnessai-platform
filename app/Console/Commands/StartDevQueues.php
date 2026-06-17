<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class StartDevQueues extends Command
{
    protected $signature = 'dev:queues';

    protected $description = 'Start all development queue workers and scheduler';

    private array $processes = [];

    public function handle(): int
    {
        $this->info('🚀 Starting development queues...');

        $this->startProcesses();
        $this->monitorProcesses();

        return Command::SUCCESS;
    }

    private function startProcesses(): void
    {
        $commands = [
            ['php', 'artisan', 'schedule:work'],
            ['php', 'artisan', 'queue:work', '--queue=workouts', '--timeout=3000'],
            ['php', 'artisan', 'queue:work', '--queue=nutrition', '--timeout=3000'],
            ['php', 'artisan', 'queue:work', '--queue=images', '--timeout=3000'],
            ['php', 'artisan', 'queue:work', '--queue=default'],
        ];

        foreach ($commands as $command) {
            $process = new Process($command);
            $process->setTimeout(null);
            $process->start();

            $this->processes[] = $process;
            $this->info('✓ Started: '.implode(' ', $command));
        }

        $this->newLine();
        $this->info('✅ All queues running! Press Ctrl+C to stop.');
        $this->newLine();
    }

    private function monitorProcesses(): void
    {
        pcntl_signal(SIGTERM, [$this, 'handleShutdown']);
        pcntl_signal(SIGINT, [$this, 'handleShutdown']);

        while (true) {
            pcntl_signal_dispatch();

            foreach ($this->processes as $process) {
                // Output any new output
                echo $process->getIncrementalOutput();
                echo $process->getIncrementalErrorOutput();

                // Restart if died
                if (! $process->isRunning()) {
                    $this->warn('⚠ Process died, restarting: '.$process->getCommandLine());
                    $process->restart();
                }
            }

            usleep(100000); // 0.1 second
        }
    }

    public function handleShutdown(): void
    {
        $this->newLine();
        $this->info('🛑 Stopping all processes...');

        foreach ($this->processes as $process) {
            if ($process->isRunning()) {
                $process->stop();
            }
        }

        $this->info('✓ All processes stopped');
        exit(0);
    }
}
