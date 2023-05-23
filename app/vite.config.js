import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

const inputs = ['resources/css/app.scss']
if (process.env.NODE_ENV === "development") {
    inputs.push('resources/js/debug.ts');
} else {
    inputs.push('resources/js/app.ts');
}

export default defineConfig({
    plugins: [
        laravel({
            input: inputs,
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
