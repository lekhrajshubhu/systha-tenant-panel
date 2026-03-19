<?php

namespace Systha\tenantpanel\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;

class TenantPanelRoutesCommand extends Command
{
    protected $signature = 'tenantpanel:routes';

    protected $description = 'List tenantpanel API routes only (api/tenant*).';

    public function handle(): int
    {
        $rows = $this->collectRows('api/tenant');

        if ($rows->isEmpty()) {
            $this->warn('No tenantpanel API routes found for prefix: api/tenant');
            return self::SUCCESS;
        }

        $this->table(['METHOD', 'URI', 'NAME', 'ACTION', 'MIDDLEWARE'], $rows->all());

        return self::SUCCESS;
    }

    /**
     * @return Collection<int, array<int, string>>
     */
    private function collectRows(string $prefix): Collection
    {
        return collect(Route::getRoutes()->getRoutes())
            ->filter(function ($route) use ($prefix): bool {
                return str_starts_with($route->uri(), $prefix);
            })
            ->map(function ($route): array {
                $methods = implode('|', array_values(array_diff($route->methods(), ['HEAD'])));
                $middleware = implode(', ', $route->middleware());

                return [
                    $methods,
                    $route->uri(),
                    (string) ($route->getName() ?? ''),
                    $route->getActionName(),
                    $middleware,
                ];
            })
            ->sortBy(fn (array $row): string => $row[1] . '|' . $row[0])
            ->values();
    }
}
