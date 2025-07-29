<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClientResource\Pages;
use App\Filament\Resources\ClientResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ClientResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'Clients';

    protected static ?string $modelLabel = 'Client';

    protected static ?string $pluralModelLabel = 'Clients';

    protected static ?string $navigationGroup = 'Gestion des utilisateurs';

    protected static ?int $navigationSort = 1;

    // Filtrer uniquement les clients (non-admin)
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('is_admin', false);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informations personnelles')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nom complet')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        Forms\Components\TextInput::make('phone')
                            ->label('Téléphone')
                            ->tel()
                            ->maxLength(255),
                        Forms\Components\DateTimePicker::make('email_verified_at')
                            ->label('Email vérifié le')
                            ->nullable(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Adresse de facturation')
                    ->schema([
                        Forms\Components\TextInput::make('billing_address')
                            ->label('Adresse')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('billing_city')
                            ->label('Ville')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('billing_postal_code')
                            ->label('Code postal')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('billing_country')
                            ->label('Pays')
                            ->maxLength(255),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Adresse de livraison')
                    ->schema([
                        Forms\Components\TextInput::make('shipping_address')
                            ->label('Adresse')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('shipping_city')
                            ->label('Ville')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('shipping_postal_code')
                            ->label('Code postal')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('shipping_country')
                            ->label('Pays')
                            ->maxLength(255),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Préférences')
                    ->schema([
                        Forms\Components\Toggle::make('newsletter_subscribed')
                            ->label('Abonné à la newsletter')
                            ->default(false),
                        Forms\Components\DateTimePicker::make('created_at')
                            ->label('Date d\'inscription')
                            ->disabled(),
                        Forms\Components\DateTimePicker::make('updated_at')
                            ->label('Dernière modification')
                            ->disabled(),
                    ])
                    ->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nom')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('Téléphone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('billing_city')
                    ->label('Ville')
                    ->searchable(),
                Tables\Columns\IconColumn::make('newsletter_subscribed')
                    ->label('Newsletter')
                    ->boolean(),
                Tables\Columns\IconColumn::make('email_verified_at')
                    ->label('Email vérifié')
                    ->boolean()
                    ->getStateUsing(fn ($record) => !is_null($record->email_verified_at)),
                Tables\Columns\TextColumn::make('orders_count')
                    ->label('Nb commandes')
                    ->counts('orders')
                    ->sortable(),
                Tables\Columns\TextColumn::make('orders_sum_total_amount')
                    ->label('Total dépensé')
                    ->sum('orders', 'total_amount')
                    ->money('EUR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Inscrit le')
                    ->dateTime('d/m/Y')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\Filter::make('verified')
                    ->label('Email vérifié')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('email_verified_at')),
                Tables\Filters\Filter::make('newsletter')
                    ->label('Abonné newsletter')
                    ->query(fn (Builder $query): Builder => $query->where('newsletter_subscribed', true)),
                Tables\Filters\Filter::make('has_orders')
                    ->label('A passé des commandes')
                    ->query(fn (Builder $query): Builder => $query->has('orders')),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\OrdersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListClients::route('/'),
            'create' => Pages\CreateClient::route('/create'),
            'view' => Pages\ViewClient::route('/{record}'),
            'edit' => Pages\EditClient::route('/{record}/edit'),
        ];
    }

    // Empêcher l'accès en lecture seule
    public static function canCreate(): bool
    {
        return true;
    }

    public static function canEdit(Model $record): bool
    {
        return true;
    }

    public static function canDelete(Model $record): bool
    {
        return true;
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('is_admin', false)->count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'primary';
    }
}
