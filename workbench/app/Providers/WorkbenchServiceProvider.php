<?php

declare(strict_types=1);

namespace Workbench\App\Providers;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Workbench\App\Filament\Pages\TestRichEditor;
use Workbench\App\Models\User;

final class WorkbenchServiceProvider extends PanelProvider
{
    public function register(): void
    {
        parent::register();

        $this->app['config']->set('auth.providers.users.model', User::class);
    }

    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->authGuard('web')
            ->pages([
                TestRichEditor::class,
            ])
            ->middleware([
                'web',
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
