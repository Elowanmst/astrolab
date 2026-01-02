<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Notifications\VerifyEmailNotification;
use App\Services\EmailVerificationTimerService;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;

class User extends Authenticatable implements FilamentUser, MustVerifyEmail 
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
        // 'newsletter_subscribed',
        'birth_date',
        'is_admin',
        'billing_address',
        'billing_city',
        'billing_postal_code',
        'billing_country',
        'shipping_address',
        'shipping_city',
        'shipping_postal_code',
        'shipping_country',
        'last_verification_email_sent_at',
    ];
    
    // Les utilisateurs admin ou avec un email @astrolab.com peuvent accéder au panneau d'administration Filament
    public function canAccessPanel(Panel $panel): bool
    {
        return $this->is_admin;
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
            'is_admin' => 'boolean',
            'last_verification_email_sent_at' => 'datetime',
            // 'newsletter_subscribed' => 'boolean',
        ];
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Send the email verification notification.
     * Implémente un système de timer pour éviter le spam.
     */
    public function sendEmailVerificationNotification()
    {
        // Vérifier si l'utilisateur peut envoyer un nouvel email
        if (!EmailVerificationTimerService::canSendVerificationEmail($this)) {
            $remainingMessage = EmailVerificationTimerService::getRemainingCooldownMessage($this);
            throw new \Exception("Email de vérification déjà envoyé récemment. {$remainingMessage}");
        }

        // Envoyer la notification
        $this->notify(new VerifyEmailNotification);
        
        // Enregistrer l'heure d'envoi
        EmailVerificationTimerService::recordEmailSent($this);
    }

    /**
     * Vérifie si l'utilisateur peut envoyer un email de vérification
     */
    public function canSendVerificationEmail(): bool
    {
        return EmailVerificationTimerService::canSendVerificationEmail($this);
    }

    /**
     * Retourne le temps restant avant de pouvoir renvoyer un email
     */
    public function getVerificationEmailCooldownSeconds(): int
    {
        return EmailVerificationTimerService::getRemainingCooldownSeconds($this);
    }

    /**
     * Retourne un message formaté du temps restant
     */
    public function getVerificationEmailCooldownMessage(): string
    {
        return EmailVerificationTimerService::getRemainingCooldownMessage($this);
    }
}
