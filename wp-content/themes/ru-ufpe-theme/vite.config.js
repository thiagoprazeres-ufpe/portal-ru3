import path from 'node:path';
import { fileURLToPath } from 'node:url';
import tailwindcss from '@tailwindcss/vite';
import { defineConfig } from 'vite';

const rootDir = path.dirname(fileURLToPath(import.meta.url));

export default defineConfig(({ mode }) => ({
	plugins: [tailwindcss()],
	build: {
		copyPublicDir: false,
		emptyOutDir: true,
		outDir: path.resolve(rootDir, 'assets/dist'),
		assetsDir: '',
		sourcemap: mode === 'development',
		minify: mode !== 'development',
		rollupOptions: {
			input: path.resolve(rootDir, 'assets/src/js/app.js'),
			output: {
				entryFileNames: 'app.js',
				assetFileNames: (assetInfo) => {
					if (assetInfo.name === 'app.css') {
						return 'app.css';
					}

					return '[name][extname]';
				},
			},
		},
	},
}));
