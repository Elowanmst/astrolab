import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css', 
                'resources/css/product-detail.css',
                'resources/js/app.js',
                'resources/js/modal.js',
                'resources/js/size.js',
                'resources/js/product-detail.js'
            ],
            refresh: true,
        }),
    ],
});
