import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import react from '@vitejs/plugin-react';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css', // <-- add your CSS here
                'resources/js/app.js',   // your main JS
              
            ],
            refresh: true,
        }),
        react(),
    ],
});
