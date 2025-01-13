<template>
	<ul v-show="isLogin" class="c-nav">
		<li class="c-nav__item">
			<button @click="logout" class="c-nav__link c-nav__link--underline c-nav__link--footer">
				Logout
			</button>
		</li>
	</ul>
</template>
<script>
export default {
	computed: {
		//ログイン中か(true)否(false)かを表す
		isLogin() {
			return this.$store.getters['auth/check'];
		},
		//API通信が成功(true)か失敗(false)かを表すFlg
		apiStatusFlg() {
			return this.$store.state.auth.apiStatus;
		}
	},
	methods: {
		//ログアウトメソッドを呼びだすメソッド
		async logout() {
			await this.$store.dispatch('auth/logout');
			//Logout成功したらログインページへリダイレクト
			if (this.apiStatusFlg) {
				this.$router.push('/login');
			}
		}
	}
};
</script>
