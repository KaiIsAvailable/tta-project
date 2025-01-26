import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    base: '/build/assets/',  // Set the correct base path for assets
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/css/form.css',
                'resources/css/profile.css',
            ],
            refresh: true,
        }),
    ],
});
