<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ShippingMethodResource\Pages;
use App\Filament\Resources\ShippingMethodResource\RelationManagers;
use App\Models\ShippingMethod;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ShippingMethodResource extends Resource
{
    protected static ?string $model = ShippingMethod::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';
    
    protected static ?string $navigationLabel = 'Méthodes de livraison';
    
    protected static ?string $modelLabel = 'méthode de livraison';
    
    protected static ?string $pluralModelLabel = 'méthodes de livraison';
    
    protected static ?string $navigationGroup = 'Configuration';
    
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informations générales')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nom')
                            ->required()
                            ->placeholder('Ex: Livraison à domicile')
                            ->maxLength(255),
                            
                        Forms\Components\TextInput::make('code')
                            ->label('Code')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->placeholder('Ex: home, pickup, express')
                            ->maxLength(50)
                            ->helperText('Code unique pour identifier cette méthode dans le système'),
                            
                        Forms\Components\Textarea::make('description')
                            ->label('Description')
                            ->placeholder('Description détaillée de cette méthode de livraison')
                            ->rows(3),
                            
                        Forms\Components\Toggle::make('is_active')
                            ->label('Actif')
                            ->default(true)
                            ->helperText('Désactiver pour masquer cette méthode aux clients'),
                    ])->columns(2),
                    
                Forms\Components\Section::make('Tarification')
                    ->schema([
                        Forms\Components\TextInput::make('price')
                            ->label('Prix')
                            ->numeric()
                            ->prefix('€')
                            ->step(0.01)
                            ->default(0)
                            ->required(),
                            
                        Forms\Components\TextInput::make('free_from_amount')
                            ->label('Gratuit à partir de')
                            ->numeric()
                            ->prefix('€')
                            ->step(0.01)
                            ->helperText('Montant du panier à partir duquel la livraison devient gratuite (optionnel)'),
                    ])->columns(2),
                    
                Forms\Components\Section::make('Délais de livraison')
                    ->schema([
                        Forms\Components\TextInput::make('estimated_days_min')
                            ->label('Délai minimum (jours)')
                            ->numeric()
                            ->minValue(0)
                            ->helperText('Nombre minimum de jours pour la livraison'),
                            
                        Forms\Components\TextInput::make('estimated_days_max')
                            ->label('Délai maximum (jours)')
                            ->numeric()
                            ->minValue(0)
                            ->helperText('Nombre maximum de jours pour la livraison'),
                            
                        Forms\Components\TextInput::make('sort_order')
                            ->label('Ordre d\'affichage')
                            ->numeric()
                            ->default(0)
                            ->helperText('Ordre d\'affichage dans la liste (0 = premier)'),
                    ])->columns(3),
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
                    
                Tables\Columns\TextColumn::make('code')
                    ->label('Code')
                    ->searchable()
                    ->badge(),
                    
                Tables\Columns\TextColumn::make('price')
                    ->label('Prix')
                    ->money('EUR')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('free_from_amount')
                    ->label('Gratuit dès')
                    ->money('EUR')
                    ->placeholder('Jamais')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('estimated_delivery')
                    ->label('Délai')
                    ->placeholder('Non défini'),
                    
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Actif')
                    ->boolean()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Ordre')
                    ->sortable(),
            ])
            ->filters([
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
            ->defaultSort('sort_order')
            ->reorderable('sort_order');
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
            'index' => Pages\ListShippingMethods::route('/'),
            'create' => Pages\CreateShippingMethod::route('/create'),
            'edit' => Pages\EditShippingMethod::route('/{record}/edit'),
        ];
    }
}
