<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentTransactionResource\Pages;
use App\Models\PaymentTransaction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PaymentTransactionResource extends Resource
{
    protected static ?string $model = PaymentTransaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    protected static ?string $navigationLabel = 'Transactions de paiement';

    protected static ?string $modelLabel = 'Transaction';

    protected static ?string $pluralModelLabel = 'Transactions de paiement';

    protected static ?string $navigationGroup = 'Configuration';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informations de transaction')
                    ->schema([
                        Forms\Components\TextInput::make('transaction_id')
                            ->label('ID Transaction')
                            ->disabled(),
                        Forms\Components\Select::make('processor')
                            ->label('Processeur')
                            ->options([
                                'stripe' => 'Stripe',
                                'paypal' => 'PayPal',
                                'lyra' => 'Lyra/PayZen',
                                'simulation' => 'Simulation',
                            ])
                            ->disabled(),
                        Forms\Components\Select::make('status')
                            ->label('Statut')
                            ->options([
                                'pending' => 'En attente',
                                'completed' => 'Complété',
                                'failed' => 'Échoué',
                                'cancelled' => 'Annulé',
                                'refunded' => 'Remboursé',
                            ])
                            ->required(),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Montants')
                    ->schema([
                        Forms\Components\TextInput::make('amount')
                            ->label('Montant')
                            ->numeric()
                            ->prefix('€')
                            ->disabled(),
                        Forms\Components\TextInput::make('fees')
                            ->label('Frais')
                            ->numeric()
                            ->prefix('€')
                            ->disabled(),
                        Forms\Components\TextInput::make('refunded_amount')
                            ->label('Montant remboursé')
                            ->numeric()
                            ->prefix('€'),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Informations de paiement')
                    ->schema([
                        Forms\Components\TextInput::make('payment_method')
                            ->label('Méthode de paiement')
                            ->disabled(),
                        Forms\Components\TextInput::make('card_last_4')
                            ->label('4 derniers chiffres')
                            ->disabled(),
                        Forms\Components\TextInput::make('card_brand')
                            ->label('Marque de carte')
                            ->disabled(),
                    ])
                    ->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('transaction_id')
                    ->label('ID Transaction')
                    ->searchable()
                    ->limit(20),
                Tables\Columns\TextColumn::make('order.order_number')
                    ->label('Commande')
                    ->searchable()
                    ->url(fn ($record) => $record->order ? route('filament.admin.resources.orders.edit', $record->order) : null)
                    ->description(fn ($record) => $record->order ? "Client: " . ($record->order->user->name ?? 'Invité') : null),
                Tables\Columns\BadgeColumn::make('processor')
                    ->label('Processeur')
                    ->colors([
                        'primary' => 'stripe',
                        'warning' => 'paypal',
                        'success' => 'lyra',
                        'secondary' => 'simulation',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'stripe' => 'Stripe',
                        'paypal' => 'PayPal',
                        'lyra' => 'Lyra',
                        'simulation' => 'Simulation',
                        default => $state,
                    }),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Statut')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'completed',
                        'danger' => 'failed',
                        'secondary' => 'cancelled',
                        'info' => 'refunded',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'En attente',
                        'completed' => 'Complété',
                        'failed' => 'Échoué',
                        'cancelled' => 'Annulé',
                        'refunded' => 'Remboursé',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('amount')
                    ->label('Montant')
                    ->money('EUR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('fees')
                    ->label('Frais')
                    ->money('EUR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('net_amount')
                    ->label('Net')
                    ->money('EUR')
                    ->getStateUsing(fn ($record) => $record->amount - $record->fees)
                    ->sortable(),
                Tables\Columns\TextColumn::make('payment_method')
                    ->label('Méthode')
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'card' => 'Carte',
                        'bank_transfer' => 'Virement',
                        'paypal' => 'PayPal',
                        default => $state ?? 'Non défini',
                    }),
                Tables\Columns\TextColumn::make('card_last_4')
                    ->label('Carte')
                    ->formatStateUsing(fn (?string $state, $record): string => 
                        $state ? "****{$state} ({$record->card_brand})" : 'N/A'
                    ),
                Tables\Columns\TextColumn::make('processed_at')
                    ->label('Traité le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('processor')
                    ->label('Processeur')
                    ->options([
                        'stripe' => 'Stripe',
                        'paypal' => 'PayPal',
                        'lyra' => 'Lyra/PayZen',
                        'simulation' => 'Simulation',
                    ]),
                Tables\Filters\SelectFilter::make('status')
                    ->label('Statut')
                    ->options([
                        'pending' => 'En attente',
                        'completed' => 'Complété',
                        'failed' => 'Échoué',
                        'cancelled' => 'Annulé',
                        'refunded' => 'Remboursé',
                    ]),
                Tables\Filters\Filter::make('amount')
                    ->form([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('amount_from')
                                    ->label('Montant minimum')
                                    ->numeric(),
                                Forms\Components\TextInput::make('amount_to')
                                    ->label('Montant maximum')
                                    ->numeric(),
                            ]),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['amount_from'],
                                fn (Builder $query, $amount): Builder => $query->where('amount', '>=', $amount),
                            )
                            ->when(
                                $data['amount_to'],
                                fn (Builder $query, $amount): Builder => $query->where('amount', '<=', $amount),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Pas de suppression en masse pour les transactions
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPaymentTransactions::route('/'),
            'create' => Pages\CreatePaymentTransaction::route('/create'),
            'view' => Pages\ViewPaymentTransaction::route('/{record}'),
            'edit' => Pages\EditPaymentTransaction::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'completed')->whereDate('created_at', today())->count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'success';
    }

    // Empêcher la création manuelle de transactions
    public static function canCreate(): bool
    {
        return false;
    }
}
