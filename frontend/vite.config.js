import { defineConfig, loadEnv } from 'vite'
import laravel from 'laravel-vite-plugin'
import vue from '@vitejs/plugin-vue'
import { templateCompilerOptions } from '@tresjs/core'

export default defineConfig(({ command, mode }) => {
  const env = loadEnv(mode, process.cwd())

  const inputs = ['src/css/app.scss']
  if (mode === 'development') {
    inputs.push('src/js/debug.ts')
  } else {
    inputs.push('src/js/app.ts')
  }

  if (env.VITE_SERVER_HOST === undefined) env.VITE_SERVER_HOST = 'localhost'
  if (env.VITE_SERVER_PORT === undefined) env.VITE_SERVER_PORT = '54373'

  return {
    plugins: [
      laravel({
        input: inputs,
        refresh: true
      }),
      vue({
        template: {
          transformAssetUrls: {
            includeAbsolute: false
          },
          compilerOptions: {
            isCustomElement: (tag) => tag.startsWith('Tres') && tag !== 'TresCanvas'
          }
        },
        ...templateCompilerOptions
      })
    ],
    server: {
      port: env.VITE_SERVER_PORT,
      host: true,
      hmr: {
        host: env.VITE_SERVER_HOST
      },
      watch: {
        usePolling: true
      }
    },
    build: {
      outDir: 'public/build/' // ビルド成果物の生成先
    },
    resolve: {
      alias: [
        { find: '$css/', replacement: `${__dirname}/src/css/` },
        { find: '$js/', replacement: `${__dirname}/src/js/` },
        { find: '$store/', replacement: `${__dirname}/src/js/store/` },
        { find: '$vue/', replacement: `${__dirname}/src/vue/` }
      ]
    }
  }
})
