import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import vue from "@vitejs/plugin-vue";
import tailwindcss from "@tailwindcss/vite";
import { VitePWA } from "vite-plugin-pwa";
import path from "path";

export default defineConfig({
    plugins: [
        laravel({
            input: "resources/js/app.js",
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
        tailwindcss(),
        VitePWA({
            registerType: "autoUpdate",
            injectRegister: "auto",
            outDir: "public",
            buildBase: "/",
            manifestFilename: "manifest.webmanifest",
            devOptions: {
                enabled: true, // Wajib aktif agar tidak 404 di local
            },
            includeAssets: ["favicon.ico", "icon_rsmd.png", "Maskot.jpg"],
            manifest: {
                id: "/",
                name: "RSMD Mobile - Pelayanan Pasien",
                short_name: "RSMD Mobile",
                description:
                    "Sistem Informasi dan Pelayanan Rumah Sakit Terpadu RSMD",
                theme_color: "#0d9488",
                background_color: "#ffffff",
                display: "standalone",
                orientation: "portrait",
                scope: "/",
                start_url: "/",
                icons: [
                    {
                        src: "/pwa-192.png",
                        sizes: "192x192",
                        type: "image/png",
                        purpose: "any",
                    },
                    {
                        src: "/icon_rsmd.png",
                        sizes: "512x512",
                        type: "image/png",
                        purpose: "any",
                    },
                    {
                        src: "/icon_rsmd.png",
                        sizes: "512x512",
                        type: "image/png",
                        purpose: "maskable",
                    },
                ],
            },
            workbox: {
                globPatterns: ["**/*.{js,css,html,ico,png,svg,jpg,woff2}"],
                navigateFallback: null,
                runtimeCaching: [
                    {
                        urlPattern: /^https:\/\/192\.168\.1\.190\/api\/.*/i,
                        handler: "NetworkFirst",
                        options: {
                            cacheName: "rsmd-api-cache",
                            expiration: {
                                maxEntries: 50,
                                maxAgeSeconds: 60 * 60 * 24,
                            },
                            cacheableResponse: {
                                statuses: [0, 200],
                            },
                        },
                    },
                ],
            },
        }),
    ],
    resolve: {
        alias: {
            "@": path.resolve(__dirname, "./resources/js"),
        },
    },
});
