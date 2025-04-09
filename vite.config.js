import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';
import path from 'path';

export default defineConfig({
  plugins: [vue()],
  root: 'resources/js', // Directorio raíz de tus archivos Vue
  build: {
    outDir: path.resolve(__dirname, 'resources/dist'),
    emptyOutDir: true,
    rollupOptions: {
      input: {
        app: path.resolve(__dirname, 'resources/js/app.js')
      }
    }
  }
});
