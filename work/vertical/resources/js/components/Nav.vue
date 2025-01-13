<template>
	<header class="l-header">
		<div class="l-header__container">
			<div @click="resetSearchPhoto" class="l-header__icon">
				<router-link to="/" class="c-icon c-icon--header">
					<img src="../../../public/assets/images/logo.png" alt="logo" />
				</router-link>
			</div>
			<nav class="l-header__nav">
				<ul v-show="isLogin" class="c-nav">
					<li class="c-nav__item">
						<button @click="changeShowPhotoForm" class="c-nav__link c-nav__link--header">
							<i class="fa-solid fa-cloud-arrow-up"></i>
						</button>
					</li>
					<li class="c-nav__item">
						<router-link to="/mypage" class="c-nav__link c-nav__link--header"
							><i class="fa-solid fa-user-check"></i
						></router-link>
					</li>
				</ul>

				<ul v-show="!isLogin" class="c-nav">
					<li class="c-nav__item">
						<router-link to="/login" class="c-nav__link c-nav__link--header"
							><i class="fa-solid fa-right-to-bracket"></i
						></router-link>
					</li>
				</ul>
			</nav>
		</div>
		<PhotoForm v-show="showPhotoForm" />
	</header>
</template>
<script>
import PhotoForm from './PhotoForm.vue';
export default {
	components: {
		PhotoForm
	},
	computed: {
		//ログイン中(true)か否(false)かを表す
		isLogin() {
			return this.$store.getters['auth/check'];
		},
		//写真投稿フォームを表示するか(true)か否(false)かを表す
		showPhotoForm() {
			return this.$store.state.formTab.showPhotoForm;
		}
	},
	methods: {
		//写真投稿フォームを表示すFlgをtrueにするメソッド
		changeShowPhotoForm() {
			this.$store.commit('formTab/setShowPhotoForm');
		},
		//検索窓の入力値を空にするメソッド
		resetSearchPhoto() {
			this.$store.dispatch('search/searchReset');
		}
	}
};
</script>
