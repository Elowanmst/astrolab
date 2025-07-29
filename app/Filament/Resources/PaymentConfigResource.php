<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentConfigResource\Pages;
use App\Filament\Resources\PaymentConfigResource\RelationManagers;
use App\Models\PaymentConfig;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PaymentConfigResource extends Resource
{
    protected static ?string $model = PaymentConfig::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    
    protected static ?string $navigationLabel = 'Configuration Paiement';
    
    protected static ?string $modelLabel = 'configuration de paiement';
    
    protected static ?string $pluralModelLabel = 'configurations de paiement';
    
    protected static ?string $navigationGroup = 'Configuration';
    
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Processeur de paiement')
                    ->schema([
                        Forms\Components\Select::make('processor')
                            ->label('Processeur')
                            ->options([
                                'stripe' => 'Stripe',
                                'paypal' => 'PayPal',
                                'lyra' => 'Lyra/PayZen',
                            ])
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                $names = [
                                    'stripe' => 'Stripe',
                                    'paypal' => 'PayPal', 
                                    'lyra' => 'Lyra/PayZen',
                                ];
                                $set('name', $names[$state] ?? '');
                            }),
                            
                        Forms\Components\TextInput::make('name')
                            ->label('Nom d\'affichage')
                            ->required()
                            ->maxLength(255),
                            
                        Forms\Components\Textarea::make('description')
                            ->label('Description')
                            ->placeholder('Description de ce processeur de paiement')
                            ->rows(2),
                            
                        Forms\Components\Toggle::make('is_active')
                            ->label('Actif')
                            ->helperText('Un seul processeur peut être actif à la fois')
                            ->reactive(),
                            
                        Forms\Components\Toggle::make('is_test_mode')
                            ->label('Mode test')
                            ->default(true)
                            ->helperText('Utiliser les clés de test plutôt que les clés de production'),
                    ])->columns(2),
                    
                Forms\Components\Section::make('Configuration')
                    ->schema([
                        Forms\Components\KeyValue::make('config_data')
                            ->label('Paramètres de configuration')
                            ->keyLabel('Paramètre')
                            ->valueLabel('Valeur')
                            ->helperText('Configuration spécifique au processeur sélectionné')
                            ->addActionLabel('Ajouter un paramètre'),
                    ]),
                    
                Forms\Components\Section::make('Frais de transaction')
                    ->schema([
                        Forms\Components\TextInput::make('fee_percentage')
                            ->label('Commission (%)')
                            ->numeric()
                            ->step(0.01)
                            ->suffix('%')
                            ->default(0)
                            ->helperText('Pourcentage de commission sur chaque transaction'),
                            
                        Forms\Components\TextInput::make('fee_fixed')
                            ->label('Frais fixes')
                            ->numeric()
                            ->step(0.01)
                            ->prefix('€')
                            ->default(0)
                            ->helperText('Montant fixe ajouté à chaque transaction'),
                    ])->columns(2),
                    
                Forms\Components\Section::make('Guide de configuration')
                    ->schema([
                        Forms\Components\Placeholder::make('stripe_help')
                            ->label('')
                            ->content('
                                **Stripe - Paramètres requis :**
                                - `public_key` : Clé publique Stripe (pk_test_... ou pk_live_...)
                                - `secret_key` : Clé secrète Stripe (sk_test_... ou sk_live_...)
                                - `webhook_secret` : Secret webhook (whsec_...)
                            ')
                            ->visible(fn ($get) => $get('processor') === 'stripe'),
                            
                        Forms\Components\Placeholder::make('paypal_help')
                            ->label('')
                            ->content('
                                **PayPal - Paramètres requis :**
                                - `client_id` : Client ID PayPal
                                - `client_secret` : Client Secret PayPal
                                - `sandbox` : true pour test, false pour production
                            ')
                            ->visible(fn ($get) => $get('processor') === 'paypal'),
                            
                        Forms\Components\Placeholder::make('lyra_help')
                            ->label('')
                            ->content('
                                **Lyra/PayZen - Paramètres requis :**
                                - `shop_id` : Identifiant boutique
                                - `key_test` : Clé de test
                                - `key_prod` : Clé de production
                                - `endpoint` : URL de l\'API (ex: https://api.payzen.eu)
                            ')
                            ->visible(fn ($get) => $get('processor') === 'lyra'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('processor')
                    ->label('Processeur')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'stripe' => 'info',
                        'paypal' => 'warning',
                        'lyra' => 'success',
                        default => 'gray',
                    }),
                    
                Tables\Columns\TextColumn::make('name')
                    ->label('Nom')
                    ->searchable(),
                    
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Actif')
                    ->boolean()
                    ->sortable(),
                    
                Tables\Columns\IconColumn::make('is_test_mode')
                    ->label('Mode test')
                    ->boolean()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('fee_percentage')
                    ->label('Commission')
                    ->suffix('%')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('fee_fixed')
                    ->label('Frais fixes')
                    ->money('EUR')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Modifié le')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('processor')
                    ->label('Processeur')
                    ->options([
                        'stripe' => 'Stripe',
                        'paypal' => 'PayPal',
                        'lyra' => 'Lyra/PayZen',
                    ]),
                    
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Statut')
                    ->placeholder('Tous')
                    ->trueLabel('Actifs seulement')
                    ->falseLabel('Inactifs seulement'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('updated_at', 'desc');
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
            'index' => Pages\ListPaymentConfigs::route('/'),
            'create' => Pages\CreatePaymentConfig::route('/create'),
            'edit' => Pages\EditPaymentConfig::route('/{record}/edit'),
        ];
    }
}
