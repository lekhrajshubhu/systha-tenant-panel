<?php

namespace Systha\tenantpanel\Commands;

use Illuminate\Console\Command;

class SysthaTenantBuildCommand extends Command
{
    protected $signature = 'systha:tenant:build';

    protected $description = 'Canonical command alias for tenantpanel:setup --skip-install.';

    public function handle(): int
    {
        return (int) $this->call('tenantpanel:setup', ['--skip-install' => true]);
    }
}
