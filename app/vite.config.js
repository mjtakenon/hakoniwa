import {defineConfig, loadEnv} from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import { templateCompilerOptions } from '@tresjs/core'

export default ({mode}) => {
    const viteEnv = loadEnv(mode, process.cwd())

    const inputs = ['resources/css/app.scss']
    console.debug(process.env.NODE_ENV);
    if (process.env.NODE_ENV === "development") {
        inputs.push('resources/js/debug.ts');
    } else {
        inputs.push('resources/js/app.ts');
    }

    if (viteEnv.VITE_SERVER_HOST === undefined) viteEnv.VITE_SERVER_HOST = "localhost";
    if (viteEnv.VITE_SERVER_PORT === undefined) viteEnv.VITE_SERVER_PORT = "54373";

    return defineConfig({
        plugins: [
            laravel({
                input: inputs,
                refresh: true,
            }),
            vue({
                template: {
                    transformAssetUrls: {
                        includeAbsolute: false,
                    },
                    compilerOptions: {
                        isCustomElement: tag => tag.startsWith('Tres') && tag !== 'TresCanvas',
                    }
                },
                ...templateCompilerOptions
            })
        ],
        server: {
            port: viteEnv.VITE_SERVER_PORT,
            host: true,
            hmr: {
                host: viteEnv.VITE_SERVER_HOST,
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
}
