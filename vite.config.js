import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: [
                // Rechargement automatique pour les fichiers Blade
                'resources/views/**/*.blade.php',
                'resources/views/**/**/*.blade.php',
                'app/Http/Controllers/**/*.php',
                'routes/**/*.php',
            ],
        }),
    ],
    server: {
        host: '0.0.0.0',
        port: 5173,
        strictPort: false,
        hmr: {
            host: 'localhost',
            protocol: 'ws',
        },
        watch: {
            usePolling: true,
            interval: 100,
        },
        // Ouvrir automatiquement le navigateur
        open: false,
    },
    // Améliorer le build pour le développement
    build: {
        sourcemap: true,
        rollupOptions: {
            output: {
                manualChunks: undefined,
            },
        },
    },
});
