<template>
	<Dialog>
		<template #header> 当サイトはポートフォリオサイトです<br class="c-br c-br--sp" /> </template>
		<template #body>
			<p class="c-paragraph">
				サンプル
				<br />
				<br />
			</p>
		</template>
		<template #footer>
			<div @click="closeDialog()">
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
		// ダイアログを閉じるメソッド
		closeDialog() {
			this.$store.commit('formTab/setWecomeFlg');
		}
	}
};
</script>
