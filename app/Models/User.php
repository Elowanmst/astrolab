<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;
// use Filament\Panel;

class User extends Authenticatable implements FilamentUser
{
    
    use HasFactory, Notifiable;
    
    /**
    * The attributes that are mass assignable.
    *
    * @var list<string>
    */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];
    
    //Décommenter cette fonction en prod pour s'assurer que seul un utilisateur avec un email @astrolab.com puisse accéder au panneau d'administration
    // public function canAccessPanel(Panel $panel): bool
    // {
    //     return str_ends_with($this->email, '@astrolab.com') && $this->hasVerifiedEmail();
    // }
    
    
    /**
    * The attributes that should be hidden for serialization.
    *
    * @var list<string>
    */
    protected $hidden = [
        'password',
        'remember_token',
    ];
    
    /**
    * Get the attributes that should be cast.
    *
    * @return array<string, string>
    */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
