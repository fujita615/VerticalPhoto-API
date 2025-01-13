<template>
	<Dialog>
		<template #header>
			お問い合わせいただき<br class="c-br c-br--sp" />
			ありがとうございました
		</template>
		<template #body>
			取り急ぎ 自動返信メールをお送りしました。<br />
			<br />
			なお、自動返信は下記のような原因によりお客様が受け取れない場合がございます。<br />
			<br />
			<br />
			■自動返信が迷惑メールと判断されている<br />
			■お預かりしたメールアドレスが間違っている<br />
			<br />
			<br />
			しばらく経ってもメールが届かない場合は
			大変お手数ですがご確認・再度のお問い合わせをお願いいたします
		</template>
		<template #footer>
			<div @click="resetContactForm()">
				<router-link to="/"><strong> TOPページへ</strong></router-link>
			</div>
		</template>
	</Dialog>
</template>
<script>
import Dialog from './Dialog.vue';

export default {
	components: {
		Dialog
	},
	methods: {
		// お問い合わせフォームを初期化して閉じるメソッド
		resetContactForm() {
			this.$store.commit('formTab/setShowContactForm');
			this.$store.commit('formTab/setMailSituation', 'edit');
		}
	}
};
</script>
