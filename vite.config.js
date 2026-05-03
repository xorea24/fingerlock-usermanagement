import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

import { cloudflare } from "@cloudflare/vite-plugin";

export default defineConfig({
    plugins: [laravel({
        input: [
            'resources/sass/app.scss',
            'resources/js/app.js',
        ],
        refresh: true,
    }), cloudflare()],
});