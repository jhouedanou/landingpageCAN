import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import os from 'os';

// Get local IP address for mobile testing
function getLocalIP() {
    const interfaces = os.networkInterfaces();
    for (const name of Object.keys(interfaces)) {
        for (const iface of interfaces[name]) {
            // Skip internal (loopback) and non-IPv4 addresses
            if (iface.family === 'IPv4' && !iface.internal) {
                return iface.address;
            }
        }
    }
    return 'localhost';
}

const host = getLocalIP();

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
            host: host, // Dynamic IP for mobile access
            protocol: 'ws',
            port: 5173,
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
