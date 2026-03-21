<?php

declare(strict_types=1);

namespace Workbench\App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Foundation\Auth\User as Authenticatable;

final class User extends Authenticatable implements FilamentUser
{
    protected $fillable = ['name', 'email', 'password'];

    protected $hidden = ['password'];

    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }
}
