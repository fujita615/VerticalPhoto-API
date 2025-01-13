<template>
	<div class="p-modal">
		<div v-show="viewLoader" class="p-modal__message">
			<Loader />
		</div>
		<div v-show="!viewLoader" class="p-form p-modal__form">
			<h3 class="c-sub-heading c-sub-heading--article">Change Password</h3>
			<div class="p-form__label">
				<strong>New Password　</strong>
				<small> ※8文字以上50文字以内</small>
			</div>
			<div class="p-form__input">
				<i class="fa-solid fa-key p-form__input-icon"></i>
				<input
					v-model="passwordForm.password"
					type="password"
					placeholder="新パスワード"
					class="c-input c-input--form"
				/>
				<div class="p-form__error">
					<div v-if="registerErrors && registerErrors.password" class="p-form__error-message">
						<label v-for="msg in registerErrors.password" :key="msg">
							{{ msg }}
						</label>
					</div>
					<label v-show="validation.password" class="p-form__error-message">{{
						validation.password
					}}</label>
				</div>
			</div>
			<div class="p-form__label"><strong>New Password　</strong><small>※再入力</small></div>
			<div class="p-form__input">
				<i class="fa-solid fa-key p-form__input-icon"></i>
				<input
					v-model="passwordForm.password_confirmation"
					type="password"
					placeholder="新パスワード再入力"
					class="c-input c-input--form"
				/>
				<div class="p-form__error">
					<label v-show="validation.password_confirmation" class="p-form__error-message">{{
						validation.password_confirmation
					}}</label>
				</div>
			</div>
			<button
				v-show="passwordForm.password && passwordForm.password_confirmation"
				@click.prevent="editPassWord"
				class="c-button c-button--form p-form__button"
			>
				変更する
			</button>
			<button
				v-show="!passwordForm.password || !passwordForm.password_confirmation"
				class="c-button c-button--form p-form__button c-button--disabled"
			>
				Inputting...
			</button>
			<button @click.prevent="canselEdit" class="c-button c-button--edit">変更をキャンセル</button>
		</div>
	</div>
</template>
<script>
import Loader from './Loader.vue';

export default {
	components: {
		Loader
	},
	data() {
		return {
			viewLoader: false,
			passwordForm: {
				password: '',
				password_confirmation: ''
			},
			validation: {
				password: '',
				password_confirmation: ''
			}
		};
	},
	//mypage(親)コンポーネントに編集中止を伝える
	emits: ['canselEdit'],
	computed: {
		//APIサーバーから返却されたエラーメッセージを参照
		registerErrors() {
			return this.$store.state.auth.registerErrorMessage;
		},
		//APIサーバーから返却された通信の成功(true)/失敗(false)を表すflg
		apiStatusFlg() {
			return this.$store.state.auth.apiStatus;
		}
	},
	methods: {
		//パスワード編集作業を中止してalertメッセージとフォームを閉じるflg(true)を親コンポーネントに渡すメソッド
		canselEdit() {
			this.reset();
			this.$store.commit('message/setAlert', { message: '変更を中止しました', timeout: 3000 });
			this.$emit('canselEdit', true);
		},
		//フォームバリデーションとAPIからのエラーメッセージを空にするメソッド
		reset() {
			this.$store.dispatch('auth/allErrorMessageClear');
			this.validation.password = '';
			this.validation.password_confirmation = '';
		},
		//パスワードフォームでバリデーションをして、パスワード変更メソッドを呼び出すメソッド
		async editPassWord() {
			//まず残っているエラーメッセージを空にする
			this.reset();
			//バリデーション
			if (this.passwordForm.password.length < 8 || this.passwordForm.password.length > 50) {
				this.validation.password = '登録可能文字数は8〜50文字です';
			} else {
				this.validation.password = '';
			}
			if (this.passwordForm.password !== this.passwordForm.password_confirmation) {
				this.validation.password_confirmation = 'パスワードが再入力と一致していません';
			} else {
				this.validation.password_confirmation = '';
			}
			//バリデーションメッセージがある場合は送信処理中止
			if (this.validation.password || this.validation.password_confirmation) {
				return false;
			}
			this.viewLoader = true;
			await this.$store.dispatch('auth/editPassWord', this.passwordForm);
			this.viewLoader = false;
			//APIサーバーから通信成功flgが通知されたら
			if (this.apiStatusFlg) {
				// エラーメッセージを空にしてsuccessメッセージを出してTopページへリダイレクト
				this.reset();
				this.$store.commit('message/setSuccess', {
					message: 'パスワードを変更しました！',
					timeout: 3000
				});
				this.$router.push('/');
			}
		}
	}
};
</script>
