import {defineConfig}from "vite"

export default defineConfig({
  server: {
    origin: 'http://localhost:5173'
  },
  build: {
    // generate .vite/manifest.json in outDir
    manifest: true,
    rollupOptions: {
      // overwrite default .html entry
      input: './src/js/main.js',
    },
  },
})
