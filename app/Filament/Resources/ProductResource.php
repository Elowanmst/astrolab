<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use App\Enums\ProductSize;


class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';
    
    protected static ?string $navigationGroup = 'Boutique';
    
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name'),
                Forms\Components\TextInput::make('description')
                    ->maxLength(255),
                Forms\Components\TextInput::make('price')
                    ->numeric()
                    ->required()
                    ->minValue(0)
                    ->maxValue(1000000),
                Forms\Components\Select::make('category_id')
                    ->relationship('category', 'name')
                    ->required(),
                SpatieMediaLibraryFileUpload::make('image')
                    ->collection('products')
                    ->image()
                    ->required()
                    ->maxSize(1024 * 10) // 10 MB
                    ->acceptedFileTypes(['image/*'])
                    ->enableOpen()
                    ->multiple()
                    ->reorderable(),
                Forms\Components\Select::make('color')
                    ->relationship('colors', 'name')
                    ->multiple()
                    ->preload()
                    ->required()
                    ->createOptionForm([
                        Forms\Components\TextInput::make('name')
                            ->label('Nom de la couleur')
                            ->required(),
                        Forms\Components\ColorPicker::make('hex')
                            ->label('Code couleur (hex)')
                            ->required()
                            ->helperText('Sélectionnez la couleur ou entrez un code hexadécimal (ex: #FF0000)'),
                    ]), // Active la création rapide d'une couleur
                    
                // Nouveau : Gestion des stocks par taille
                Forms\Components\Section::make('Stocks par taille')
                    ->description('Gérer le stock pour chaque taille disponible')
                    ->schema([
                        Forms\Components\Repeater::make('sizeStocks')
                            ->relationship('sizeStocks')
                            ->schema([
                                Forms\Components\Select::make('size')
                                    ->options(\App\Enums\ProductSize::getOptions())
                                    ->required()
                                    ->distinct()
                                    ->label('Taille'),
                                Forms\Components\TextInput::make('stock')
                                    ->numeric()
                                    ->required()
                                    ->minValue(0)
                                    ->default(0)
                                    ->label('Stock'),
                            ])
                            ->columns(2)
                            ->addActionLabel('Ajouter une taille')
                            ->collapsible()
                            ->reorderableWithButtons()
                            ->deleteAction(
                                fn ($action) => $action->requiresConfirmation()
                            ),
                    ])
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('description')
                    ->limit(50)
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('price')
                    ->sortable()
                    ->money('EUR'),
                Tables\Columns\TextColumn::make('total_stock')
                    ->label('Stock total')
                    ->getStateUsing(fn ($record) => $record->getTotalStock())
                    ->sortable()
                    ->badge()
                    ->color(fn ($state) => match (true) {
                        $state === 0 => 'danger',
                        $state <= 10 => 'warning', 
                        default => 'success'
                    }),
                Tables\Columns\TextColumn::make('available_sizes')
                    ->label('Tailles disponibles')
                    ->getStateUsing(fn ($record) => implode(', ', $record->getAvailableSizes()))
                    ->limit(30)
                    ->tooltip(fn ($record) => implode(', ', $record->getAvailableSizes())),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Category')
                    ->sortable()
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['category', 'sizeStocks']);
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
