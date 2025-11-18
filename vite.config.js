import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css', 
                'resources/js/app.js',
                'resources/css/product-detail.css',
                'resources/css/checkout.css',
                'resources/js/modal.js',
                'resources/js/size.js',
                'resources/js/product-detail.js'
            ],
            refresh: true,
        }),
    ],
});
