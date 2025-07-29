<?php

namespace App\Filament\Resources\PaymentTransactionResource\Pages;

use App\Filament\Resources\PaymentTransactionResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class ViewPaymentTransaction extends ViewRecord
{
    protected static string $resource = PaymentTransactionResource::class;

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
                Infolists\Components\Section::make('Informations de transaction')
                    ->schema([
                        Infolists\Components\TextEntry::make('transaction_id')
                            ->label('ID Transaction')
                            ->copyable(),
                        Infolists\Components\TextEntry::make('processor')
                            ->label('Processeur')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'stripe' => 'primary',
                                'paypal' => 'warning',
                                'lyra' => 'success',
                                'simulation' => 'secondary',
                                default => 'gray'
                            }),
                        Infolists\Components\TextEntry::make('status')
                            ->label('Statut')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'pending' => 'warning',
                                'completed' => 'success',
                                'failed' => 'danger',
                                'cancelled' => 'secondary',
                                'refunded' => 'info',
                                default => 'gray'
                            }),
                        Infolists\Components\TextEntry::make('order.order_number')
                            ->label('Numéro de commande')
                            ->copyable(),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('Montants')
                    ->schema([
                        Infolists\Components\TextEntry::make('amount')
                            ->label('Montant')
                            ->money('EUR'),
                        Infolists\Components\TextEntry::make('fees')
                            ->label('Frais')
                            ->money('EUR'),
                        Infolists\Components\TextEntry::make('net_amount')
                            ->label('Montant net')
                            ->money('EUR'),
                        Infolists\Components\TextEntry::make('refunded_amount')
                            ->label('Montant remboursé')
                            ->money('EUR')
                            ->visible(fn ($record) => $record->refunded_amount > 0),
                    ])
                    ->columns(4),

                Infolists\Components\Section::make('Détails de paiement')
                    ->schema([
                        Infolists\Components\TextEntry::make('payment_method')
                            ->label('Méthode de paiement'),
                        Infolists\Components\TextEntry::make('card_last_4')
                            ->label('4 derniers chiffres'),
                        Infolists\Components\TextEntry::make('card_brand')
                            ->label('Marque de carte'),
                        Infolists\Components\TextEntry::make('currency')
                            ->label('Devise'),
                    ])
                    ->columns(4),

                Infolists\Components\Section::make('Dates')
                    ->schema([
                        Infolists\Components\TextEntry::make('created_at')
                            ->label('Créée le')
                            ->dateTime('d/m/Y à H:i'),
                        Infolists\Components\TextEntry::make('processed_at')
                            ->label('Traitée le')
                            ->dateTime('d/m/Y à H:i'),
                        Infolists\Components\TextEntry::make('refunded_at')
                            ->label('Remboursée le')
                            ->dateTime('d/m/Y à H:i')
                            ->visible(fn ($record) => $record->refunded_at),
                    ])
                    ->columns(3),

                Infolists\Components\Section::make('Réponse du processeur')
                    ->schema([
                        Infolists\Components\TextEntry::make('failure_reason')
                            ->label('Raison de l\'échec')
                            ->visible(fn ($record) => $record->failure_reason),
                        Infolists\Components\TextEntry::make('processor_response')
                            ->label('Réponse complète')
                            ->formatStateUsing(fn ($state) => $state ? json_encode($state, JSON_PRETTY_PRINT) : 'Aucune')
                            ->extraAttributes(['class' => 'font-mono text-xs'])
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),
            ]);
    }
}
