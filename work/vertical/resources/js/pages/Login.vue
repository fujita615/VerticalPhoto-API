<template>
	<div class="p-auth">
		<div class="p-auth__container">
			<div class="p-auth__tab">
				<label
					@click="chengeLoginForm"
					class="c-label c-label--tab"
					:class="{ 'is-active': formFlg === 'login' }"
					>ログイン
				</label>
				<label
					@click="chengeRegisterForm"
					class="c-label c-label--tab"
					:class="{ 'is-active': formFlg === 'register' }"
					>会員登録
				</label>
			</div>

			<!-- ログインフォーム -->
			<form @submit.prevent v-show="formFlg === 'login'" class="p-form p-form--user">
				<div class="p-form__label"><strong>Mail</strong></div>
				<div class="p-form__input">
					<i class="fa-regular fa-envelope p-form__input-icon"></i>
					<input
						v-model="loginForm.email"
						type="mail"
						placeholder="メール"
						class="c-input c-input--form"
					/>
					<div class="p-form__error">
						<label v-show="loginValidation.email" class="p-form__error-message">{{
							loginValidation.email
						}}</label>
					</div>
				</div>
				<div class="p-form__label"><strong>Password </strong></div>
				<div class="p-form__input">
					<i class="fa-solid fa-key p-form__input-icon"></i>
					<input
						v-model="loginForm.password"
						type="password"
						autocomplete="off"
						placeholder="パスワード"
						class="c-input c-input--form"
					/>
					<div class="p-form__error">
						<div v-if="loginErrors && loginErrors.email" class="p-form__error-message">
							<label v-for="msg in loginErrors.email" :key="msg">
								{{ msg }}
							</label>
						</div>
						<div v-if="loginErrors && loginErrors.password" class="p-form__error-message">
							<label v-for="msg in loginErrors.password" :key="msg">
								{{ msg }}
							</label>
						</div>
						<label v-show="loginValidation.password" class="p-form__error-message">{{
							loginValidation.password
						}}</label>
					</div>
				</div>
				<button
					v-show="loginForm.email && loginForm.password"
					@click.prevent="login"
					title="ログイン"
					class="p-form__button c-button c-button--form"
				>
					Login
				</button>
				<button
					v-show="!loginForm.email || !loginForm.password"
					class="p-form__button c-button c-button--form c-button--disabled"
				>
					Inputting...
				</button>
			</form>
			<!-- 会員登録フォーム -->
			<form @submit.prevent class="p-form p-form--user" v-show="formFlg === 'register'">
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
						<label v-show="registerValidation.name" class="p-form__error-message">{{
							registerValidation.name
						}}</label>
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
								v-for="msg in registerErrors.nickname"
								:key="msg"
								v-if="registerErrors && registerErrors.nickname"
							>
								{{ msg }} 　
							</label>
							<br class="c-br c-br--sp-tab" v-if="registerErrors && registerErrors.nickname" />
							<label v-if="registerErrors && registerErrors.nickname && candidateName">
								ユーザーネームのご提案：{{ candidateName }}
							</label>
						</div>
						<label v-show="registerValidation.nickname" class="p-form__error-message">{{
							registerValidation.nickname
						}}</label>
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
						<label v-show="registerValidation.email" class="p-form__error-message">{{
							registerValidation.email
						}}</label>
					</div>
				</div>
				<div class="p-form__label">
					<strong>Password　</strong><small> ※8文字以上50文字以内 </small>
				</div>
				<div class="p-form__input">
					<i class="fa-solid fa-key p-form__input-icon"></i>
					<input
						v-model="registerForm.password"
						autocomplete="off"
						type="password"
						placeholder="パスワード"
						class="c-input c-input--form"
					/>
					<div class="p-form__error">
						<div v-if="registerErrors && registerErrors.password" class="p-form__error-message">
							<label v-for="msg in registerErrors.password" :key="msg">
								{{ msg }}
							</label>
						</div>
						<label v-show="registerValidation.password" class="p-form__error-message">{{
							registerValidation.password
						}}</label>
					</div>
				</div>
				<div class="p-form__label"><strong> Password　</strong><small>※再入力</small></div>
				<div class="p-form__input">
					<i class="fa-solid fa-key p-form__input-icon"></i>
					<input
						v-model="registerForm.password_confirmation"
						type="password"
						autocomplete="off"
						placeholder="パスワード再入力"
						class="c-input c-input--form"
					/>
					<div class="p-form__error">
						<label v-show="registerValidation.password_confirmation" class="p-form__error-message">{{
							registerValidation.password_confirmation
						}}</label>
					</div>
				</div>
				<!-- サンプルサイトとしての注意文 -->
				<Note />
				<button
					v-show="
						registerForm.name &&
						registerForm.nickname &&
						registerForm.email &&
						registerForm.password &&
						registerForm.password_confirmation
					"
					@click.prevent="register"
					class="c-button c-button--form p-form__button"
				>
					登録する
				</button>
				<button
					v-show="
						!registerForm.name ||
						!registerForm.nickname ||
						!registerForm.email ||
						!registerForm.password ||
						!registerForm.password_confirmation
					"
					class="p-form__button c-button c-button--form c-button--disabled"
				>
					Inputting...
				</button>
			</form>
		</div>
	</div>
</template>

<script>
import Note from '../components/Note.vue';
export default {
	components: {
		Note
	},
	data() {
		return {
			formFlg: 'user',
			loginForm: {
				email: '',
				password: ''
			},
			registerForm: {
				name: '',
				nickname: '',
				email: '',
				password: '',
				password_confirmation: ''
			},
			loginValidation: {
				email: '',
				password: ''
			},
			registerValidation: {
				name: '',
				nickname: '',
				email: '',
				password: '',
				password_confirmation: ''
			},
			candidateName: ''
		};
	},
	computed: {
		apiStatus() {
			return this.$store.state.auth.apiStatus;
		},
		loginErrors() {
			return this.$store.state.auth.loginErrorMessage;
		},
		registerErrors() {
			return this.$store.state.auth.registerErrorMessage;
		}
	},
	methods: {
		chengeLoginForm() {
			this.formFlg = 'login';
		},
		chengeRegisterForm() {
			this.formFlg = 'register';
		},
		//ログインメソッド
		async login() {
			//エラーメッセージを空にする
			this.clearError();
			//バリデーション
			const mailPattern = /^[A-Za-z0-9]{1}[A-Za-z0-9_.-]*@{1}[A-Za-z0-9_.-]+.[A-Za-z0-9]+$/;
			if (!mailPattern.test(this.loginForm.email)) {
				this.loginValidation.email = '有効なメールアドレスを入力してください。';
				return false;
			} else {
				this.loginValidation.email = '';
			}
			if (this.loginForm.password.length < 8 || this.loginForm.password.length > 50) {
				this.loginValidation.password = 'ログイン情報が存在しません。';
				return false;
			} else {
				this.loginValidation.password = '';
			}
			//バリデーションチェックを通過したらloginメソッドを呼び出す
			await this.$store.dispatch('auth/login', this.loginForm);
			//loginメソッドが成功したらTOPページへリダイレクト
			if (this.apiStatus) {
				this.$router.push('/');
			}
		},
		//新規登録メソッド
		async register() {
			//エラーメッセージを空にする
			this.clearError();
			//バリデーション
			const mailPattern = /^[A-Za-z0-9]{1}[A-Za-z0-9_.-]*@{1}[A-Za-z0-9_.-]+.[A-Za-z0-9]+$/;
			if (this.registerForm.name.length > 30) {
				this.registerValidation.name = '登録文字数上限を超えています。';
			} else {
				this.registerValidation.name = '';
			}

			if (this.registerForm.nickname.length > 15) {
				this.registerValidation.nickname = '登録文字数上限を超えています。';
			} else {
				this.registerValidation.nickname = '';
			}

			if (!mailPattern.test(this.registerForm.email)) {
				this.registerValidation.email = '有効なメールアドレスを入力してください。';
			} else {
				this.registerValidation.email = '';
			}
			if (this.registerForm.password.length < 8 || this.registerForm.password.length > 50) {
				this.registerValidation.password = '登録可能文字数は8〜50文字です。';
			} else {
				this.registerValidation.password = '';
			}

			if (this.registerForm.password !== this.registerForm.password_confirmation) {
				this.registerValidation.password_confirmation = 'パスワードが再入力と一致していません。';
			} else {
				this.registerValidation.password_confirmation = '';
			}
			if (
				this.registerValidation.name ||
				this.registerValidation.nickname ||
				this.registerValidation.email ||
				this.registerValidation.password ||
				this.registerValidation.password_confirmation
			) {
				return false;
			}

			//バリデーションチェックを通過したらregisterメソッドを呼び出す
			await this.$store.dispatch('auth/register', this.registerForm);
			// 通信成功したらTOPページへリダイレクト
			if (this.apiStatus) {
				this.$router.push('/');
			}
		},
		//エラーメッセージを空にするメソッド
		clearError() {
			this.loginValidation.email = '';
			this.loginValidation.password = '';
			this.registerValidation.name = '';
			this.registerValidation.nickname = '';
			this.registerValidation.email = '';
			this.registerValidation.password = '';
			this.registerValidation.password_confirmation = '';
			this.$store.dispatch('auth/allErrorMessageClear');
			this.candidateName = '';
		},
		//ユーザー名の候補を生成するメソッド
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
	created() {
		//ページアクセス時はloginフォームを表示
		this.formFlg = 'login';
		this.clearError();
	},
	watch: {
		//ユーザー名の重複エラー発生時は候補名を表示する
		'registerErrors.nickname': function (newValue) {
			if (newValue !== '') {
				this.createdNickname();
			}
		},
		//login・registerフォームを切り替えた時は入力値・エラーメッセージを初期化する
		formFlg() {
			this.clearError();
			this.loginForm.email = '';
			this.loginForm.password = '';
			this.registerForm.name = '';
			this.registerForm.nickname = '';
			this.registerForm.email = '';
			this.registerForm.password = '';
			this.registerForm.password_confirmation = '';
		}
	}
};
</script>
