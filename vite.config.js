import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',       // Cambiado de .scss a .css
                'resources/js/app.js',
                'resources/css/sia-style.css', // Tu estilo institucional UPDS
            ],
            refresh: true,
        }),
    ],
});