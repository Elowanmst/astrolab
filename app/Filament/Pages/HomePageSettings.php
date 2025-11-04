<?php
// filepath: app/Filament/Pages/HomePageSettings.php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms\Form;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use App\Models\HomePageSetting;
use App\Models\CountdownSetting;

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
        $countdown = CountdownSetting::first();
        
        $this->form->fill([
            'hero_image' => HomePageSetting::get('hero_image'),
            'promotion_image' => HomePageSetting::get('promotion_image'),
            // Données du countdown
            'countdown_title' => $countdown->title ?? null,
            'countdown_end_date' => $countdown->end_date ?? now()->addDays(7),
            'countdown_is_active' => $countdown->is_active ?? true,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('🎯 Images de la page d\'accueil')
                    ->description('Gestion des images principales')
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
                                    
                                FileUpload::make('promotion_image')
                                    ->label('Image Promotion')
                                    ->image()
                                    ->directory('banners')
                                    ->helperText('Formats flexibles | Max: 10MB')
                                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                    ->maxSize(10240)
                                    ->nullable()
                                    ->columnSpan(1),
                            ]),
                    ]),

                Section::make('⏰ Countdown / Chronomètre')
                    ->description('Configuration du compte à rebours sur la page d\'accueil')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Toggle::make('countdown_is_active')
                                    ->label('Activer le countdown')
                                    ->helperText('Afficher le compte à rebours sur la page d\'accueil')
                                    ->columnSpanFull(),

                                TextInput::make('countdown_title')
                                    ->label('Titre du countdown (optionnel)')
                                    ->placeholder('Ex: RESTE DU TEMPS')
                                    ->maxLength(100)
                                    ->nullable()
                                    ->helperText('Laissez vide si vous ne voulez pas de titre')
                                    ->columnSpan(1),

                                DateTimePicker::make('countdown_end_date')
                                    ->label('Date et heure de fin')
                                    ->helperText('Le countdown disparaîtra automatiquement à cette date')
                                    ->required()
                                    ->columnSpan(1),
                            ]),
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
        
        // Sauvegarder les images
        HomePageSetting::set('hero_image', $processValue($data['hero_image']));
        HomePageSetting::set('promotion_image', $processValue($data['promotion_image']));
        
        // Sauvegarder les paramètres du countdown
        CountdownSetting::truncate(); // Supprimer les anciens paramètres
        CountdownSetting::create([
            'title' => $data['countdown_title'], // Peut être null maintenant
            'end_date' => $data['countdown_end_date'],
            'is_active' => $data['countdown_is_active'],
        ]);
        
        Notification::make()
            ->title('Modifications sauvegardées')
            ->body('Les paramètres de la page d\'accueil ont été mis à jour avec succès.')
            ->success()
            ->send();
    }
}