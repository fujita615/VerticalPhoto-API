<template>
	<div class="p-modal">
		<div v-show="viewLoader" class="p-modal__message">
			<Loader />
		</div>
		<div v-show="!viewLoader" class="p-form p-modal__form">
			<h3 class="c-sub-heading c-sub-heading--article">Change user registration</h3>
			<div class="p-form__label"><strong>Name </strong> <small>　※30文字以内</small></div>
			<div class="p-form__input">
				<i class="fa-regular fa-circle-user p-form__input-icon"></i>
				<input
					v-model="registerForm.name"
					type="text"
					placeholder="お名前"
					class="c-input c-input--form"
				/>
				<div class="p-form__error">
					<div v-if="registerErrors && registerErrors.name" class="p-form__error-message">
						<label v-for="msg in registerErrors.name" :key="msg">
							{{ msg }}
						</label>
					</div>
					<label v-show="validation.name" class="p-form__error-message">{{ validation.name }}</label>
				</div>
			</div>
			<div class="p-form__label">
				<strong>Username </strong><br />
				<small
					>写真やコメント投稿時に公開される名前です <br class="c-br c-br--sp-tab" />※15文字以内</small
				>
			</div>
			<div class="p-form__input">
				<i class="fa-solid fa-user-tag p-form__input-icon"></i>
				<input
					v-model="registerForm.nickname"
					type="text"
					placeholder="ユーザーネーム"
					class="c-input c-input--form"
				/>
				<div class="p-form__error">
					<div class="p-form__error-message">
						<label
							v-if="registerErrors && registerErrors.nickname"
							v-for="msg in registerErrors.nickname"
							:key="msg"
						>
							{{ msg }}　
						</label>
						<br v-if="registerErrors && registerErrors.nickname" class="c-br c-br--sp-tab" />
						<label v-if="registerErrors && registerErrors.nickname && candidateName">
							ユーザーネームのご提案：{{ candidateName }}
						</label>
					</div>
					<label v-show="validation.nickname" class="p-form__error-message">
						{{ validation.nickname }}
					</label>
				</div>
			</div>
			<div class="p-form__label"><strong>Mail </strong></div>
			<div class="p-form__input">
				<i class="fa-regular fa-envelope p-form__input-icon"></i>
				<input
					v-model="registerForm.email"
					type="mail"
					placeholder="メール"
					class="c-input c-input--form"
				/>
				<div class="p-form__error">
					<div v-if="registerErrors && registerErrors.email" class="p-form__error-message">
						<label v-for="msg in registerErrors.email" :key="msg">
							{{ msg }}
						</label>
					</div>
					<label v-show="validation.email" class="p-form__error-message">{{ validation.email }}</label>
				</div>
			</div>
			<button
				v-show="registerForm.name && registerForm.nickname && registerForm.email"
				@click.prevent="editUser"
				class="c-button c-button--form p-form__button"
			>
				変更する
			</button>
			<button
				v-show="!registerForm.name || !registerForm.nickname || !registerForm.email"
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
	props: {
		//mypage（親コンポーネント）から渡されるuser登録情報
		registerForm: {
			type: Object,
			required: true
		}
	},
	emits: ['canselEdit'],
	data() {
		return {
			viewLoader: false,
			validation: {
				name: '',
				nickname: '',
				email: ''
			},
			//ユーザー名候補として表示する値
			candidateName: ''
		};
	},
	computed: {
		registerErrors() {
			return this.$store.state.auth.registerErrorMessage;
		},
		//処理成功か否かを表すflg
		apiStatus() {
			return this.$store.state.auth.apiStatus;
		}
	},
	methods: {
		//ユーザー情報の編集を終了（中止）してフォームを閉じるメソッド
		canselEdit() {
			// エラーメッセージを空にしてDBに登録しているユーザー情報を再取得する
			this.clearError();
			this.$store.dispatch('auth/currentUser');
			//親コンポーネントに編集中止を伝える（＝フォームを閉じる）
			this.$emit('canselEdit', true);
			this.$store.commit('message/setAlert', { message: '変更を中止しました', timeout: 3000 });
		},
		// エラーメッセージを空にするメソッド
		clearError() {
			//フォームの入力内容とエラーメッセージを初期化(nullに)する
			this.$store.dispatch('auth/allErrorMessageClear');
			this.validation.name = '';
			this.validation.nickname = '';
			this.validation.email = '';
		},
		//フォームの入力内容をバリデーションしてDBに変更登録するメソッド
		async editUser() {
			const mailPattern = /^[A-Za-z0-9]{1}[A-Za-z0-9_.-]*@{1}[A-Za-z0-9_.-]+.[A-Za-z0-9]+$/;
			if (this.registerForm.name.length > 30) {
				this.validation.name = '登録文字数上限を超えています';
			} else {
				this.validation.name = '';
			}
			if (this.registerForm.nickname.length > 15) {
				this.validation.nickname = '登録文字数上限を超えています';
			} else {
				this.validation.nickname = '';
			}
			if (!mailPattern.test(this.registerForm.email)) {
				this.validation.email = '有効なメールアドレスを入力してください';
			} else {
				this.validation.email = '';
			}
			if (this.validation.name || this.validation.nickname || this.validation.email) {
				return false;
			}
			//全バリデーション成功ならeditUserメソッドを呼び出してユーザーデータを更新する
			this.viewLoader = true;
			await this.$store.dispatch('auth/editUser', this.registerForm);
			this.viewLoader = false;
			//通信成功ならsuccessメッセージをだして更新したユーザー情報を取得、フォームを閉じる
			if (this.apiStatus) {
				this.$store.commit('message/setSuccess', {
					message: '登録情報を変更しました！',
					timeout: 3000
				});
				this.clearError();
				this.$store.dispatch('auth/currentUser');
				this.$emit('canselEdit', true);
			}
		},
		//登録不可だったusernameの頭文字から始まる一意な名前候補を生成するメソッド
		createdNickname() {
			if (this.registerForm.nickname.length < 3) {
				this.candidateName = this.registerForm.nickname.concat(new Date().getTime().toString());
			} else {
				this.candidateName = this.registerForm.nickname
					.slice(0, 2)
					.concat(new Date().getTime().toString());
			}
		}
	},
	watch: {
		//ユーザー名登録時に重複エラーが発生しないか監視
		'registerErrors.nickname': function (newValue) {
			if (newValue !== '') {
				this.createdNickname();
			}
		}
	}
};
</script>
