<x-filament::page>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <x-filament::card>
            <h2 class="text-xl font-bold">Produits</h2>
            <p>{{ \App\Models\Product::count() }} produits au total</p>
        </x-filament::card>

        <x-filament::card>
            <h2 class="text-xl font-bold">Catégories</h2>
            <p>{{ \App\Models\Category::count() }} catégories enregistrées</p>
        </x-filament::card>
    </div>
</x-filament::page>
