import {defineConfig} from 'vite';
import laravel from 'laravel-vite-plugin';
import react from '@vitejs/plugin-react';
import tailwindcss from '@tailwindcss/vite'
import path from 'path';
import svgr from 'vite-plugin-svgr';
export default defineConfig({
    plugins: [

        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/admin/app.jsx',
            ],
            refresh: true,
        }),
        react(),
        tailwindcss(),
        svgr(),
    ],
    resolve: {
        extensions: ['.js', '.jsx', '.ts', '.tsx'], // Ensure .js and .jsx are resolved
        alias: {
            '@': path.resolve(__dirname, 'resources/js/admin'),
        },
    },
});
