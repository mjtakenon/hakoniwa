import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.ts'],
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    includeAbsolute: false,
                }
            }
        })
    ],
    server: {
      port: 54373,
      host: true,
      hmr: {
        host: 'localhost',
      },
      watch: {
        usePolling: true,
      },
    },
    build: {
        outDir: 'public/build/', // ビルド成果物の生成先
        manifest: true
    },
});
