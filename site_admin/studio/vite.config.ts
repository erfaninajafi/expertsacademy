import { defineConfig } from 'vitest/config';
import react from '@vitejs/plugin-react';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
  plugins: [react(), tailwindcss()],
  base: './',
  build: {
    outDir: '../asset/page-studio',
    emptyOutDir: true,
    manifest: true,
    sourcemap: false,
    rollupOptions: { input: { main: 'src/main.tsx' } },
  },
  server: {
    host: '127.0.0.1',
    port: 5173,
    strictPort: true,
    cors: { origin: /^http:\/\/(localhost|127\.0\.0\.1)(:\d+)?$/ },
  },
  test: {
    environment: 'jsdom',
    include: ['src/**/*.test.{ts,tsx}'],
    setupFiles: ['./src/test-setup.ts'],
  },
});
