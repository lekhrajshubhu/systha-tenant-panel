<?php

namespace Systha\tenantpanel\Commands;

use Illuminate\Console\Command;

class SysthaTenantRoutesCommand extends Command
{
    protected $signature = 'systha:tenant:routes';

    protected $description = 'Canonical command alias for tenantpanel:routes.';

    public function handle(): int
    {
        return (int) $this->call('tenantpanel:routes');
    }
}
