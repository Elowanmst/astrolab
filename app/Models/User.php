<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;

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
        'phone',
        'address',
        'city',
        'postal_code',
        'country',
        'newsletter',
        'birth_date',
    ];
    
    // Seuls les utilisateurs avec un email @astrolab.com peuvent accéder au panneau d'administration Filament
    public function canAccessPanel(Panel $panel): bool
    {
        return str_ends_with($this->email, '@astrolab.com');
    }
    
    
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
            'birth_date' => 'datetime',
        ];
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
