import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { resolve } from 'path';
import fs from 'fs-extra';

// Copy TinyMCE assets during build
async function copyTinyMCEAssets() {
    const sourceDir = 'node_modules/tinymce';
    const targetDir = 'public/tinymce';
    
    try {
        await fs.copy(resolve(sourceDir, 'icons'), resolve(targetDir, 'icons'));
        await fs.copy(resolve(sourceDir, 'skins'), resolve(targetDir, 'skins'));
        await fs.copy(resolve(sourceDir, 'themes'), resolve(targetDir, 'themes'));
        console.log('TinyMCE assets copied successfully');
    } catch (err) {
        console.error('Error copying TinyMCE assets:', err);
    }
}

export default defineConfig({
    build: {
        outDir: 'public/build',
        emptyOutDir: true,
        rollupOptions: {
            output: {
                manualChunks(id) {
                    if (id.includes('node_modules/tinymce')) {
                        return 'tinymce';
                    }
                }
            }
        }
    },
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        {
            name: 'copy-tinymce-assets',
            async buildStart() {
                await copyTinyMCEAssets();
            }
        }
    ]
});
