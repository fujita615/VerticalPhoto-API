<template>
	<div class="p-modal">
		<div v-show="mailSituation === 'send'" class="p-modal__message">
			<Loader>
				<template #body>Sending your mail...</template>
			</Loader>
		</div>
		<div v-show="mailSituation === 'edit'" class="p-form p-form--contact p-modal__form">
			<h3 class="c-sub-heading c-sub-heading--article">お問い合わせフォーム</h3>
			<div class="p-form__label">
				<strong>Name</strong><small> 　※30文字以内で入力してください</small>
			</div>
			<div class="p-form__input p-form__input--contact">
				<i class="fa-regular fa-circle-user p-form__input-icon"></i>
				<input
					v-model="contactForm.name"
					type="text"
					placeholder="お名前"
					class="c-input c-input--form"
				/>
				<div class="p-form__error">
					<div v-if="contactErrors && contactErrors.name" class="p-form__error-message">
						<label v-for="msg in contactErrors.name" :key="msg">
							{{ msg }}
						</label>
					</div>
				</div>
			</div>
			<div class="p-form__label"><strong>Mail</strong></div>
			<div class="p-form__input p-form__input--contact">
				<i class="fa-regular fa-envelope p-form__input-icon"></i>
				<input
					v-model="contactForm.email"
					type="mail"
					placeholder="mail"
					class="c-input c-input--form"
				/>
				<div class="p-form__error">
					<div v-if="contactErrors && contactErrors.email" class="p-form__error-message">
						<label v-for="msg in contactErrors.email" :key="msg">
							{{ msg }}
						</label>
					</div>
					<label v-show="validation.email" class="p-form__error-message">{{ validation.email }}</label>
				</div>
			</div>
			<div class="p-form__label">
				<strong>Message</strong> <small> 　※500文字以内で入力してください</small>
			</div>
			<div class="p-form__textarea">
				<textarea
					v-model="contactForm.text"
					cols="30"
					rows="10"
					placeholder="お問い合わせ内容"
					maxlength="500"
					class="c-textarea c-textarea--contact"
				>
				</textarea>
				<div class="p-form__error">
					<div v-if="contactErrors && contactErrors.text" class="p-form__error-message">
						<label v-for="msg in contactErrors.text" :key="msg">
							{{ msg }}
						</label>
					</div>
					<label v-show="validation.text" class="p-form__error-message">{{ validation.text }}</label>
				</div>
			</div>
			<button
				v-show="contactForm.name && contactForm.email && contactForm.text"
				@click.prevent="submitFormMail"
				type="submit"
				class="c-button c-button--form p-form__button"
			>
				submit
			</button>
			<button
				v-show="!(contactForm.name && contactForm.email && contactForm.text)"
				class="c-button c-button--form c-button--disabled p-form__button"
			>
				全項目入力必須です
			</button>
			<button @click.prevent="closeContactForm" class="c-button c-button--edit">
				お問い合わせを中止
			</button>

			お預かりする個人情報についてはprivacy policyをご確認ください
		</div>
		<div class="p-modal__message" v-show="mailSituation === 'success'">
			<Thanks />
		</div>
	</div>
</template>
<script>
import { OK, UNPROCESSABLE_ENTITY } from '../util';
import Loader from './Loader.vue';
import Thanks from './Thanks.vue';

export default {
	data() {
		return {
			contactForm: {
				name: '',
				email: '',
				text: ''
			},
			contactErrors: '',
			validation: {
				email: '',
				text: ''
			}
		};
	},
	computed: {
		mailSituation() {
			return this.$store.state.formTab.mailSituation;
		}
	},
	components: {
		Loader,
		Thanks
	},
	methods: {
		//メール送信を中止してフォームを閉じるメソッド
		closeContactForm() {
			this.reset();
			this.$store.commit('formTab/setMailSituation', 'edit');
			this.$store.commit('formTab/setShowContactForm');
		},
		//フォームの入力内容とエラーメッセージを空にする
		reset() {
			this.contactForm.name = '';
			this.contactForm.email = '';
			this.contactForm.text = '';
			this.contactErrors = '';
			this.validation.email = '';
			this.validation.text = '';
		},
		//メールl送信
		async submitFormMail() {
			//send mail..の画面を表示
			this.$store.commit('formTab/setMailSituation', 'send');
			//送信前のバリデーション
			const mailPattern = /^[A-Za-z0-9]{1}[A-Za-z0-9_.-]*@{1}[A-Za-z0-9_.-]+.[A-Za-z0-9]+$/;
			if (!mailPattern.test(this.contactForm.email)) {
				this.validation.email = '有効なメールアドレスを入力してください';
			} else {
				this.validation.email = '';
			}
			if (!this.contactForm.text.match(/\S/g)) {
				this.validation.text = 'お問い合せ内容が未入力です';
			} else if (this.contactForm.text.trim().length > 500) {
				this.validation.text = '500文字以内で入力してください';
			} else {
				this.validation.text = '';
			}
			//バリデーションエラーがあったら送信処理を中止して編集画面に戻る
			if (this.validation.email || this.validation.text) {
				this.$store.commit('formTab/setMailSituation', 'edit');
				return false;
			}
			await axios.get('/api/refresh-token');

			const FormMailData = new FormData();
			FormMailData.append('name', this.contactForm.name);
			FormMailData.append('email', this.contactForm.email);
			FormMailData.append('text', this.contactForm.text);
			const response = await axios.post('/api/contact', FormMailData);
			if (response.status === UNPROCESSABLE_ENTITY) {
				this.contactErrors = response.data.errors;
				return false;
			}
			//通信成功以外ならエラーメッセージを表示するページへ移動して終了
			if (response.status !== OK) {
				this.closeContactForm();
				this.$store.commit('error/setCode', response.status);
				return false;
			}
			this.$store.commit('formTab/setMailSituation', 'success');
			this.reset();
		}
	},
	watch: {
		// フォームが空になった際はバリデーションメッセージを消す
		'contactForm.name': function (newValue) {
			if (!newValue) {
				if (this.contactErrors.name) this.contactErrors.name = '';
			}
		},
		'contactForm.email': function (newValue) {
			if (!newValue) {
				this.validation.email = '';
				if (this.contactErrors.email) this.contactErrors.email = '';
			}
		},
		'contactForm.text': function (newValue) {
			if (!newValue) {
				this.validation.text = '';
				if (this.contactErrors.text) this.contactErrors.text = '';
			}
		}
	}
};
</script>
