<?php

namespace Systha\tenantpanel\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class TenantPanelUninstallCommand extends Command
{
    protected $signature = 'tenantpanel:uninstall';

    protected $description = 'Remove published tenant panel assets from public/tenant-panel';

    public function handle(): int
    {
        $targetPath = public_path(config('tenantpanel.public_path', 'tenant-panel'));

        if (!is_dir($targetPath)) {
            $this->warn("Nothing to remove. Path not found: {$targetPath}");
            return self::SUCCESS;
        }

        File::deleteDirectory($targetPath);

        $this->info("Removed tenant panel assets: {$targetPath}");

        return self::SUCCESS;
    }
}
