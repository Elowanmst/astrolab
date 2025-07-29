<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms\Form;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use App\Models\HomePageSetting;

class HomePageSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static ?string $navigationLabel = 'Page d\'accueil';
    protected static ?string $title = 'Gestion de la page d\'accueil';
    protected static ?string $navigationGroup = 'Page d\'accueil';
    protected static ?int $navigationSort = 1;

    protected static string $view = 'filament.pages.home-page-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'hero_image' => HomePageSetting::get('hero_image'),
            'promotion_image' => HomePageSetting::get('promotion_image'),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('🎯 Image Hero')
                    ->description('Image principale en bannière')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                FileUpload::make('hero_image')
                                    ->label('Image Hero')
                                    ->image()
                                    ->directory('banners')
                                    ->helperText('Ratio recommandé: 16:9 ou 21:9 | Max: 10MB')
                                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                    ->maxSize(10240)
                                    ->nullable()
                                    ->columnSpan(1),
                            ]),
                    ]),
                    
                Section::make('📸 Image Promotion')
                    ->description('Image pour les sections promotionnelles')
                    ->schema([
                        FileUpload::make('promotion_image')
                            ->label('Image Promotion')
                            ->image()
                            ->directory('banners')
                            ->helperText('Formats flexibles | Max: 10MB')
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->maxSize(10240)
                            ->nullable(),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();
        
        // Traitement sécurisé des données avec vérification de type
        $processValue = function($value) {
            if (is_array($value)) {
                return !empty($value) ? $value[0] : null;
            }
            return $value;
        };
        
        // Sauvegarder chaque paramètre
        HomePageSetting::set('hero_image', $processValue($data['hero_image']));
        HomePageSetting::set('promotion_image', $processValue($data['promotion_image']));
        
        Notification::make()
            ->title('Modifications sauvegardées')
            ->body('Les paramètres de la page d\'accueil ont été mis à jour avec succès.')
            ->success()
            ->send();
    }
}
