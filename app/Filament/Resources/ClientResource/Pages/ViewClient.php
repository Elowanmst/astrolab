<?php

namespace App\Filament\Resources\ClientResource\Pages;

use App\Filament\Resources\ClientResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class ViewClient extends ViewRecord
{
    protected static string $resource = ClientResource::class;

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
                Infolists\Components\Section::make('Informations personnelles')
                    ->schema([
                        Infolists\Components\TextEntry::make('name')
                            ->label('Nom complet'),
                        Infolists\Components\TextEntry::make('email')
                            ->label('Email'),
                        Infolists\Components\TextEntry::make('phone')
                            ->label('Téléphone'),
                        Infolists\Components\TextEntry::make('email_verified_at')
                            ->label('Email vérifié le')
                            ->dateTime('d/m/Y à H:i'),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('Adresses')
                    ->schema([
                        Infolists\Components\Group::make([
                            Infolists\Components\TextEntry::make('billing_address')
                                ->label('Adresse de facturation'),
                            Infolists\Components\TextEntry::make('billing_city')
                                ->label('Ville'),
                            Infolists\Components\TextEntry::make('billing_postal_code')
                                ->label('Code postal'),
                            Infolists\Components\TextEntry::make('billing_country')
                                ->label('Pays'),
                        ])
                        ->columnSpan(1),

                        Infolists\Components\Group::make([
                            Infolists\Components\TextEntry::make('shipping_address')
                                ->label('Adresse de livraison'),
                            Infolists\Components\TextEntry::make('shipping_city')
                                ->label('Ville'),
                            Infolists\Components\TextEntry::make('shipping_postal_code')
                                ->label('Code postal'),
                            Infolists\Components\TextEntry::make('shipping_country')
                                ->label('Pays'),
                        ])
                        ->columnSpan(1),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('Statistiques')
                    ->schema([
                        Infolists\Components\TextEntry::make('orders_count')
                            ->label('Nombre de commandes')
                            ->state(function ($record) {
                                return $record->orders()->count();
                            }),
                        Infolists\Components\TextEntry::make('total_spent')
                            ->label('Total dépensé')
                            ->state(function ($record) {
                                return number_format($record->orders()->sum('total_amount'), 2) . ' €';
                            }),
                        Infolists\Components\IconEntry::make('newsletter_subscribed')
                            ->label('Abonné newsletter')
                            ->boolean(),
                        Infolists\Components\TextEntry::make('created_at')
                            ->label('Date d\'inscription')
                            ->dateTime('d/m/Y à H:i'),
                    ])
                    ->columns(4),

                Infolists\Components\Section::make('Commandes récentes')
                    ->schema([
                        Infolists\Components\RepeatableEntry::make('orders')
                            ->label('')
                            ->schema([
                                Infolists\Components\TextEntry::make('order_number')
                                    ->label('Numéro'),
                                Infolists\Components\TextEntry::make('status')
                                    ->label('Statut')
                                    ->badge(),
                                Infolists\Components\TextEntry::make('total_amount')
                                    ->label('Montant')
                                    ->money('EUR'),
                                Infolists\Components\TextEntry::make('created_at')
                                    ->label('Date')
                                    ->dateTime('d/m/Y'),
                            ])
                            ->columns(4)
                    ])
                    ->visible(fn ($record) => $record->orders()->exists()),
            ]);
    }
}
