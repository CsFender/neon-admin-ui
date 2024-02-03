<?php

namespace Neon\Admin\Models;

use Neon\Models\Traits\Uuid;

use Filament\Models\Contracts\FilamentUser;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable implements FilamentUser
{
    use Notifiable;
    use SoftDeletes;
    use Uuid;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'data'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [

    ];

    
    public function canAccessFilament(): bool
    {
        return true; //- str_ends_with($this->email, '@yourdomain.com') && $this->hasVerifiedEmail();
    }

    public function canAccessPanel(\Filament\Panel $panel): bool
    {
        return $panel->getId() == 'neon-admin';
    }
}
