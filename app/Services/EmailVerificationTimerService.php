<?php

namespace App\Services;

use App\Models\User;
use Carbon\Carbon;

class EmailVerificationTimerService
{
    /**
     * Durée du cooldown en minutes (par défaut 5 minutes)
     */
    private const COOLDOWN_MINUTES = 5;

    /**
     * Vérifie si l'utilisateur peut envoyer un nouvel email de vérification
     */
    public static function canSendVerificationEmail(User $user): bool
    {
        if (is_null($user->last_verification_email_sent_at)) {
            return true;
        }

        $lastSentAt = Carbon::parse($user->last_verification_email_sent_at);
        $cooldownEndsAt = $lastSentAt->addMinutes(self::COOLDOWN_MINUTES);

        return Carbon::now()->isAfter($cooldownEndsAt);
    }

    /**
     * Retourne le temps restant avant de pouvoir renvoyer un email (en secondes)
     */
    public static function getRemainingCooldownSeconds(User $user): int
    {
        if (is_null($user->last_verification_email_sent_at)) {
            return 0;
        }

        $lastSentAt = Carbon::parse($user->last_verification_email_sent_at);
        $cooldownEndsAt = $lastSentAt->addMinutes(self::COOLDOWN_MINUTES);
        $now = Carbon::now();

        if ($now->isAfter($cooldownEndsAt)) {
            return 0;
        }

        return $now->diffInSeconds($cooldownEndsAt);
    }

    /**
     * Retourne un message formaté du temps restant
     */
    public static function getRemainingCooldownMessage(User $user): string
    {
        $remainingSeconds = self::getRemainingCooldownSeconds($user);
        
        if ($remainingSeconds <= 0) {
            return '';
        }

        $minutes = intval($remainingSeconds / 60);
        $seconds = $remainingSeconds % 60;

        if ($minutes > 0) {
            return "Veuillez attendre {$minutes} minute(s) et {$seconds} seconde(s) avant de renvoyer un email.";
        }

        return "Veuillez attendre {$seconds} seconde(s) avant de renvoyer un email.";
    }

    /**
     * Met à jour le timestamp du dernier envoi d'email
     */
    public static function recordEmailSent(User $user): void
    {
        $user->update(['last_verification_email_sent_at' => Carbon::now()]);
    }

    /**
     * Retourne la durée du cooldown en minutes
     */
    public static function getCooldownMinutes(): int
    {
        return self::COOLDOWN_MINUTES;
    }
}
