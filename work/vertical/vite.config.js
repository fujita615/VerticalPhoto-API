import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
	plugins: [
		vue(),
		laravel({
			input: ['resources/sass/style.scss', 'resources/js/app.js'],

			refresh: true
		})
	],
	server: {
		host: true,
		hmr: {
			host: 'localhost'
		},
		watch: {
			usePolling: true
		}
	}
});
