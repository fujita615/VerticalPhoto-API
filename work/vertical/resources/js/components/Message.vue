<template>
	<div
		v-show="message"
		class="p-message"
		:class="{
			'p-message--alert': flg === 'alert',
			'p-message--success': flg === 'success'
		}"
	>
		<div
			class="p-message__container"
			:class="{
				'p-message__container--alert': flg === 'alert',
				'p-message__container--success': flg === 'success'
			}"
		>
			<div v-show="flg === 'success'" class="p-message__mark p-message__mark--success">
				<i class="fa-solid fa-circle-check"></i>
			</div>
			<div v-show="flg === 'alert'" class="p-message__mark p-message__mark--alert">
				<i class="fa-solid fa-circle-exclamation"></i>
			</div>
			<div class="p-message__message">
				{{ message }}
			</div>
		</div>
	</div>
</template>

<script>
export default {
	data() {
		return {
			message: '',
			flg: ''
		};
	},
	computed: {
		//storeの値が更新されたらflgでaleartかsuccessかをFlgに代入しつつ、内容を返すメソッド
		messageCheck() {
			if (this.$store.state.message.alert) {
				this.flg = 'alert';
				return this.$store.state.message.alert;
			} else {
				this.flg = 'success';
				return this.$store.state.message.success;
			}
		}
	},
	watch: {
		//messageCheck(storeに格納されている値)が変更されたらdataの値を更新する
		messageCheck() {
			this.message = this.messageCheck;
		}
	}
};
</script>
