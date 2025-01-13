<template>
	<div class="l-wrapper">
		<Nav />
		<main class="l-main">
			<div class="p-container">
				<Message />
				<Welcome v-show="showWelcomeDialog" />
				<router-view />
			</div>
		</main>
		<footer class="l-footer">
			<div class="p-container">
				<div class="l-footer__nav">
					<div class="l-footer__nav-container">
						<FooterNav />
					</div>
					<div class="l-footer__link">
						<router-link class="c-icon c-icon--footer" to="/">
							<img @click="resetSearchPhoto" src="../../public/assets/images/logo.png" alt="logo" />
						</router-link>
						<ul class="c-nav c-nav--sns">
							<li class="c-nav__item">
								<a href=" " target="_blank" rel="noopener noreferrer" class="c-nav__link c-nav__link--sns">
									<i class="fab fa-github faa-shake animated-hover"></i>
								</a>
							</li>
						</ul>
					</div>
				</div>
				<div class="l-footer__info">
					<div class="l-footer__colophon">
						<button @click="setContactForm" class="c-link c-link--footer c-link--no-line">contact</button>
						<ContactForm v-show="showContactForm" />
					</div>
					<div class="l-footer__dot"><small>・</small></div>
					<div class="l-footer__colophon">
						<router-link to="policy" class="c-link c-link--footer c-link--no-line"
							>privacy policy
						</router-link>
					</div>
					<div class="l-footer__dot"><small>・</small></div>
					<div class="l-footer__copyright">&copy;2024 Vertical Photo</div>
				</div>
			</div>
		</footer>
	</div>
</template>

<script>
import Nav from './components/Nav.vue';
import FooterNav from './components/FooterNav.vue';
import Message from './components/Message.vue';
import ContactForm from './components/ContactForm.vue';
import Welcome from './components/Welcome.vue';

import { INTERNAL_SERVER_ERROR, UNAUTHORIZED, NOT_FOUND, Un_authorized } from './util.js';

export default {
	components: {
		Nav,
		FooterNav,
		Message,
		ContactForm,
		Welcome
	},
	computed: {
		//errorストアのcode値(HTTPステータス)を参照するメソッド
		errorCode() {
			return this.$store.state.error.code;
		},
		showPhotoForm() {
			return this.$store.state.formTab.showPhotoForm;
		},
		showContactForm() {
			return this.$store.state.formTab.showContactForm;
		},
		showWelcomeDialog() {
			return this.$store.state.formTab.welcomeFlg;
		}
	},
	methods: {
		//logoをクリックしたら絞り込み検索表示を解除するメソッドを呼び出す
		resetSearchPhoto() {
			this.$store.dispatch('search/searchReset');
		},
		async setContactForm() {
			await axios.get('/api/refresh-token');
			if (!document.cookie) {
				this.$router.push('/nocookie');
				return false;
			}
			this.$store.commit('formTab/setShowContactForm');
		}
	},
	watch: {
		// errorCodeを監視してステータスによってリダイレクト先を指定
		errorCode: {
			async handler(status) {
				//500エラー
				if (status === INTERNAL_SERVER_ERROR) {
					this.$router.push('/500');

					// 認証切れ
				} else if (status === UNAUTHORIZED || status === Un_authorized) {
					await axios.get('/api/refresh-token');
					if (this.$store.getters['auth/check']) {
						this.$store.commit('auth/setUser', '');
						await this.$store.commit('message/setAlert', {
							message: 'ログイン時間超過のため処理を中止しました',
							timeout: 6000
						});
						await this.$router.push('/login');
					}

					//404エラー
				} else if (status === NOT_FOUND) {
					this.$router.push('/notfound');
				}
			},
			immediate: true
		},
		//クエリパラメータの変更を察知したらエラーコードをリセットする
		//（URLは同じでもクエリパラメータが変わればページ遷移とみなす）
		$route() {
			this.$store.commit('error/setCode', null);
		}
	}
};
</script>
