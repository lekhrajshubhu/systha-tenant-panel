<?php

namespace Systha\tenantpanel;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class TenantpanelServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/tenantpanel.php', 'tenantpanel');

        $this->app['router']->aliasMiddleware('systha.tenant.auth', \Systha\Core\Http\Middleware\TenantJwtAuth::class);
        $this->app['router']->aliasMiddleware('systha.tenant.context', \Systha\Core\Http\Middleware\TenantContextFromToken::class);

        // Legacy aliases during compatibility phase.
        $this->app['router']->aliasMiddleware('tenant.auth', \Systha\Core\Http\Middleware\TenantJwtAuth::class);
        $this->app['router']->aliasMiddleware('tenant.context', \Systha\Core\Http\Middleware\TenantContextFromToken::class);
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../config/tenantpanel.php' => config_path('tenantpanel.php'),
        ], 'tenantpanel-config');

        $this->loadRoutesFrom(__DIR__ . '/../routes/api.php');
        $this->registerTenantSpaFallbackRoute();
        $this->registerLegacyTenantSpaRedirect();

        if ($this->app->runningInConsole()) {
            $this->commands($this->discoverCommands());
        }
    }

    private function discoverCommands(): array
    {
        $commands = [];
        $namespace = __NAMESPACE__ . '\\Commands\\';

        foreach (glob(__DIR__ . '/Commands/*Command.php') as $file) {
            $class = $namespace . pathinfo($file, PATHINFO_FILENAME);

            if (class_exists($class)) {
                $commands[] = $class;
            }
        }

        return $commands;
    }

    private function registerTenantSpaFallbackRoute(): void
    {
        $prefix = trim((string) config('tenantpanel.public_path', 'tenant-panel'), '/');
        $indexPath = public_path($prefix . '/index.html');

        Route::middleware('web')
            ->get($prefix . '/{any?}', function (Request $request) use ($indexPath, $prefix) {
                if ($request->path() === $prefix) {
                    return redirect('/' . $prefix . '/dashboard');
                }

                $relativePath = ltrim(str_replace($prefix, '', $request->path()), '/');
                $requestedFile = public_path($prefix . ($relativePath !== '' ? '/' . $relativePath : ''));

                if ($relativePath !== '' && is_file($requestedFile)) {
                    return response()->file($requestedFile);
                }

                if (!is_file($indexPath)) {
                    abort(404, 'Tenant panel build not found. Run: php artisan tenantpanel:setup');
                }

                return response()->file($indexPath);
            })
            ->where('any', '.*');
    }

    private function registerLegacyTenantSpaRedirect(): void
    {
        $prefix = trim((string) config('tenantpanel.public_path', 'tenant-panel'), '/');
        $legacyPrefix = 'tenants';

        if ($prefix === $legacyPrefix) {
            return;
        }

        Route::middleware('web')
            ->get($legacyPrefix . '/{any?}', function (Request $request) use ($prefix, $legacyPrefix) {
                $relativePath = ltrim(str_replace($legacyPrefix, '', $request->path()), '/');
                $target = '/' . $prefix . ($relativePath !== '' ? '/' . $relativePath : '');

                return redirect($target);
            })
            ->where('any', '.*');
    }
}
