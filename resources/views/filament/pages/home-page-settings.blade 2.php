<x-filament-panels::page>
    <div class="space-y-6">
        <div class="bg-white dark:bg-gray-900 rounded-lg shadow p-6">
            <div class="mb-4">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                    🎨 Gestion des images de la page d'accueil
                </h2>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Gérez les images principales de votre site web
                </p>
            </div>
            
            <form wire:submit.prevent="save">
                {{ $this->form }}
                
                <div class="mt-6 flex justify-end">
                    <x-filament::button type="submit" color="success">
                        Enregistrer les modifications
                    </x-filament::button>
                </div>
            </form>
        </div>
        
        <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800 dark:text-blue-200">
                        Conseils pour les images
                    </h3>
                    <div class="mt-2 text-sm text-blue-700 dark:text-blue-300">
                        <ul class="list-disc pl-5 space-y-1">
                            <li><strong>Image Hero :</strong> Utilisez un format large (16:9 ou 21:9) pour un rendu optimal</li>
                            <li><strong>Image Promotion :</strong> Format plus flexible, idéal pour les sections de contenu</li>
                            <li><strong>Qualité :</strong> Privilégiez des images de haute qualité (max 10MB)</li>
                            <li><strong>Formats :</strong> JPG, PNG ou WebP recommandés</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
