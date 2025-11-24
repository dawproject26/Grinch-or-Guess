import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            //He añadido la ruta de mi archivo css, para poder usar vite en mi view
            input: ['resources/css/wheelfireclub.css', 'resources/js/postit.js', 'resources/css/app.css', 'resources/js/app.js', 'resources/css/wheelfireclub.css'],
            refresh: true,
        }),
    ],
});
