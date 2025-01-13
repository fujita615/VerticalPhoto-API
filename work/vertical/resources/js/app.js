import './bootstrap';
import { createApp } from 'vue';
import { router } from './router';
import { store } from './store/store';
import App from './App.vue';

//画面遷移（生成）時はまずログイン状態かを確認してuserデータを取得する
async function generateVue() {
	await store.dispatch('auth/currentUser');

	const app = createApp(App);
	app.use(router).use(store);
	app.mount('#app');
}

generateVue();
