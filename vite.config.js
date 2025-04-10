// vite.config.js
import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';
import path from 'path';

export default defineConfig({
  plugins: [vue()],
  // Nota: cuando se va a usar como asset de Laravel, no es necesario un index.html de entrada
  root: 'resources/js',  // Solo apuntamos a donde está el código Vue, sin incluir HTML
  build: {
    outDir: path.resolve(__dirname, 'public/vendor/my-quotes-package'),
    emptyOutDir: true,
    rollupOptions: {
      input: {
        app: path.resolve(__dirname, 'resources/js/app.js')
      },
      output: {
        entryFileNames: 'app.js',
        chunkFileNames: '[name].js',
        assetFileNames: assetInfo => {
          if (assetInfo.name && assetInfo.name.endsWith('.css')) {
            return 'app.css';
          }
          return '[name].[ext]';
        }
      }
    }
  }
});
