import { createRouter, createWebHistory } from 'vue-router';

//urlを検知して切り替えるコンポーネント達を読み込む
import PhotoList from './pages/PhotoList.vue';
import Login from './pages/Login.vue';
import Policy from './pages/Policy.vue';
import PhotoDetail from './pages/PhotoDetail.vue';
import { store } from './store/store';
import SystemError from './pages/errors/SystemError.vue';
import NotFound from './pages/errors/NotFound.vue';
import NoCookie from './pages/errors/NoCookie.vue';
import MyPage from './pages/MyPage.vue';

export const router = createRouter({
	history: createWebHistory(),
	routes: [
		{
			path: '/',
			name: 'index',
			component: PhotoList
			//pagination
			// props: route =>
			// {
			//     const page = route.query.page
			//   return { page: /^[1-9][0-9]*$/.test(page) ? page * 1 : 1 }
			// }
		},
		{
			path: '/photolist/:photoTag',
			name: 'photolist',
			component: PhotoList,
			props: true
		},
		{
			path: '/login',
			name: 'login',
			component: Login,
			async beforeEnter(to, from, next) {
				await axios.get('/api/refresh-token');
				if (store.getters['auth/check']) {
					next('/');
				}
				//もしcookieが無効ならcookieの有効化をお願いするページへ遷移
				else if (!document.cookie) {
					next('/nocookie');
				} else {
					next();
				}
			}
		},
		{
			path: '/photos/:id',
			component: PhotoDetail,
			props: true
		},
		{
			path: '/mypage',
			component: MyPage,
			name: 'mypage',
			beforeEnter(to, from, next) {
				if (!store.getters['auth/check']) {
					next('/login');
				} else {
					next();
				}
			}
		},
		{
			path: '/500',
			name: 'SystemError',
			component: SystemError
		},
		{
			path: '/nocookie',
			name: 'nocookie',
			component: NoCookie
		},
		{
			path: '/policy',
			name: 'policy',
			component: Policy
		},
		{
			path: '/:catchAll(.*)',
			name: 'notfound',
			component: NotFound
		}
	]
});
