import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
    ],
    // 👇 ADD THIS SERVER BLOCK
    server: {
        host: '0.0.0.0', // Listen on all network addresses
        hmr: {
            host: '192.168.100.32' // 👈 YOUR LAPTOP'S IP ADDRESS (From your screenshot)
        },
    },
});