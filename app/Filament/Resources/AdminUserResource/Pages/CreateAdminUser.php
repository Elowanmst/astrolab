<?php

namespace App\Filament\Resources\AdminUserResource\Pages;

use App\Filament\Resources\AdminUserResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateAdminUser extends CreateRecord
{
    protected static string $resource = AdminUserResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Administrateur créé')
            ->body('Le compte administrateur a été créé avec succès.');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // S'assurer que is_admin est toujours true pour cette ressource
        $data['is_admin'] = true;
        
        // Vérifier l'email par défaut pour les comptes admin
        if (!isset($data['email_verified_at'])) {
            $data['email_verified_at'] = now();
        }

        return $data;
    }
}
