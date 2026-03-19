<?php

namespace Systha\tenantpanel\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class TenantPanelDevCommand extends Command
{
    protected $signature = 'tenantpanel:dev {--host=127.0.0.1 : Dev server host} {--port=5174 : Dev server port}';

    protected $description = 'Run tenantpanel frontend in Vite dev mode.';

    public function handle(): int
    {
        $packageRoot = dirname(__DIR__, 2);
        $host = (string) $this->option('host');
        $port = (string) $this->option('port');

        $this->info("Starting tenant panel dev server at http://{$host}:{$port}");

        $command = ['npm', 'run', 'dev', '--', '--host=' . $host, '--port=' . $port];
        $process = new Process($command, $packageRoot);
        $process->setTimeout(null);
        $process->run(function ($type, $buffer) {
            $this->output->write($buffer);
        });

        return $process->isSuccessful() ? self::SUCCESS : self::FAILURE;
    }
}
