import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
});

// import { defineConfig } from 'vite';
// import laravel from 'laravel-vite-plugin';

// export default defineConfig({
//     server: {
//         host: "0.0.0.0",
//         port: 5173,
//         strictPort: true,
//         hmr: {
//             host: ' https://6888fb8653a0.ngrok-free.app',
//             protocol: 'wss',
//             clientPort: 443,
//         },
//     },

//     plugins: [
//         laravel({
//             input: ['resources/css/app.css', 'resources/js/app.js'],
//             refresh: true,
//         }),
//     ],
// });
