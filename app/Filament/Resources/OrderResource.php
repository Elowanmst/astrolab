<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $navigationLabel = 'Commandes';

    protected static ?string $modelLabel = 'Commande';

    protected static ?string $pluralModelLabel = 'Commandes';

    protected static ?string $navigationGroup = 'E-commerce';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informations de commande')
                    ->schema([
                        Forms\Components\TextInput::make('order_number')
                            ->label('Numéro de commande')
                            ->disabled(),
                        Forms\Components\Select::make('user_id')
                            ->label('Client')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('status')
                            ->label('Statut')
                            ->options([
                                'pending' => 'En attente',
                                'processing' => 'En traitement',
                                'shipped' => 'Expédiée',
                                'delivered' => 'Livrée',
                                'cancelled' => 'Annulée',
                            ])
                            ->required(),
                        Forms\Components\Select::make('payment_status')
                            ->label('Statut de paiement')
                            ->options([
                                'pending' => 'En attente',
                                'completed' => 'Payé',
                                'failed' => 'Échoué',
                                'refunded' => 'Remboursé',
                            ])
                            ->required(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Détails financiers')
                    ->schema([
                        Forms\Components\TextInput::make('total_amount')
                            ->label('Montant total')
                            ->numeric()
                            ->prefix('€')
                            ->required(),
                        Forms\Components\TextInput::make('transaction_id')
                            ->label('ID de transaction')
                            ->disabled(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Informations de livraison')
                    ->schema([
                        Forms\Components\Textarea::make('shipping_address')
                            ->label('Adresse de livraison')
                            ->rows(3),
                        Forms\Components\DateTimePicker::make('shipped_at')
                            ->label('Date d\'expédition'),
                        Forms\Components\DateTimePicker::make('delivered_at')
                            ->label('Date de livraison'),
                    ])
                    ->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order_number')
                    ->label('Numéro')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Client')
                    ->searchable()
                    ->sortable()
                    ->default('Invité'),
                Tables\Columns\TextColumn::make('user.email')
                    ->label('Email')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Statut')
                    ->colors([
                        'warning' => 'pending',
                        'primary' => 'processing',
                        'info' => 'shipped',
                        'success' => 'delivered',
                        'danger' => 'cancelled',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'En attente',
                        'processing' => 'En traitement',
                        'shipped' => 'Expédiée',
                        'delivered' => 'Livrée',
                        'cancelled' => 'Annulée',
                        default => $state,
                    }),
                Tables\Columns\BadgeColumn::make('payment_status')
                    ->label('Paiement')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'completed',
                        'danger' => 'failed',
                        'info' => 'refunded',
                    ])
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'pending' => 'En attente',
                        'completed' => 'Payé',
                        'failed' => 'Échoué',
                        'refunded' => 'Remboursé',
                        default => 'Non défini',
                    }),
                Tables\Columns\TextColumn::make('items_count')
                    ->label('Articles')
                    ->counts('items')
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_amount')
                    ->label('Montant')
                    ->money('EUR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('payment_transactions_count')
                    ->label('Transactions')
                    ->counts('paymentTransactions')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Créée le')
                    ->dateTime('d/m/Y à H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('shipped_at')
                    ->label('Expédiée le')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Statut')
                    ->options([
                        'pending' => 'En attente',
                        'processing' => 'En traitement',
                        'shipped' => 'Expédiée',
                        'delivered' => 'Livrée',
                        'cancelled' => 'Annulée',
                    ]),
                Tables\Filters\SelectFilter::make('payment_status')
                    ->label('Statut de paiement')
                    ->options([
                        'pending' => 'En attente',
                        'completed' => 'Payé',
                        'failed' => 'Échoué',
                        'refunded' => 'Remboursé',
                    ]),
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('Du'),
                        Forms\Components\DatePicker::make('created_until')
                            ->label('Au'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('mark_as_shipped')
                        ->label('Marquer comme expédiées')
                        ->icon('heroicon-o-truck')
                        ->requiresConfirmation()
                        ->action(fn (Collection $records) => $records->each(fn ($record) => $record->update(['status' => 'shipped', 'shipped_at' => now()])))
                        ->deselectRecordsAfterCompletion(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            // RelationManagers seront ajoutés plus tard
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'pending')->count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'warning';
    }

    // Empêcher la création manuelle de commandes (elles viennent du site)
    public static function canCreate(): bool
    {
        return false;
    }
}
