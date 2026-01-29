import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';
import legacy from '@vitejs/plugin-legacy';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),
        legacy({
            targets: ['chrome >= 64', 'android >= 8'],
            additionalLegacyPolyfills: ['regenerator-runtime/runtime']
        }),
    ],
});
