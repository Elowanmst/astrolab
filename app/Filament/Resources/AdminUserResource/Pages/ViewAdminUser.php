<?php

namespace App\Filament\Resources\AdminUserResource\Pages;

use App\Filament\Resources\AdminUserResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class ViewAdminUser extends ViewRecord
{
    protected static string $resource = AdminUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Informations d\'administration')
                    ->schema([
                        Infolists\Components\TextEntry::make('name')
                            ->label('Nom complet'),
                        Infolists\Components\TextEntry::make('email')
                            ->label('Email'),
                        Infolists\Components\TextEntry::make('phone')
                            ->label('Téléphone'),
                        Infolists\Components\IconEntry::make('is_admin')
                            ->label('Accès administrateur')
                            ->boolean(),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('Statut du compte')
                    ->schema([
                        Infolists\Components\TextEntry::make('email_verified_at')
                            ->label('Email vérifié le')
                            ->dateTime('d/m/Y à H:i')
                            ->placeholder('Non vérifié'),
                        Infolists\Components\TextEntry::make('created_at')
                            ->label('Compte créé le')
                            ->dateTime('d/m/Y à H:i'),
                        Infolists\Components\TextEntry::make('updated_at')
                            ->label('Dernière modification')
                            ->dateTime('d/m/Y à H:i'),
                    ])
                    ->columns(3),
            ]);
    }
}
