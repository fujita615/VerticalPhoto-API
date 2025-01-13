<template>
	<div class="p-modal">
		<div v-show="viewLoader" class="p-modal__message">
			<Loader>Sending your photo...</Loader>
		</div>
		<div v-show="!viewLoader" class="p-form p-form--photo p-modal__form">
			<h3 class="c-sub-heading c-sub-heading--article">Upload a photo</h3>
			<div class="p-form__label">
				<strong>Photo </strong>
				<small> 　 縦長の写真をUPしてください <br />※最大2MBまで ファイル形式:jpeg,png,gif</small>
			</div>
			<div class="p-form__input p-form__input--photo">
				<img
					v-show="!preview"
					src="../../../public/assets/images/photoform.png"
					class="p-form__photo"
				/>
				<input @change="onFileChange" type="file" class="c-input c-input--photo" />
				<output v-show="preview" class="p-form__output">
					<img :src="preview" class="p-form__output-image" alt="" />
				</output>
			</div>
			<div class="p-form__label">
				<strong>Tag </strong>
				<small>
					1タグ10文字まで、合計5タグ登録可能です<br />※複数入力時はスペースで区切ってください</small
				>
			</div>
			<div class="p-form__input">
				<i class="fa-solid fa-tags p-form__input-icon"></i>
				<input
					v-model="tags"
					type="text"
					title="最大５個まで登録可能"
					placeholder="タグ ※合計５個まで"
					class="c-input c-input--form"
				/>
			</div>
			<Note />
			<button
				v-show="photoValidationFlg && !validationNewPieces && !validationWordCount"
				@click.prevent="submitPhoto"
				class="c-button c-button--form p-form__button"
			>
				upload
			</button>
			<div
				v-show="!photoValidationFlg || validationNewPieces || validationWordCount"
				class="p-form__error p-form__error--photo"
			>
				<div v-if="photoErrors && photoErrors.photo" class="p-form__error-message">
					<label v-for="msg in photoErrors.photo" :key="msg">
						{{ msg }}
					</label>
				</div>
				<div v-if="photoErrors && photoErrors.tags" class="p-form__error-message">
					<label v-for="msg in photoErrors.tags" :key="msg">
						{{ msg }}
					</label>
				</div>
				<div v-show="preview" class="p-form__error-message">
					<span v-show="validationWordCount">{{ validationWordCount }}<br /></span>
					<span v-show="validationNewPieces">{{ validationNewPieces }}</span>
				</div>
				<div v-show="photoValidation" class="p-form__error-message">{{ photoValidation }}</div>
			</div>
			<button @click.prevent="changeShowPhotoForm" class="c-button c-button--edit">
				投稿をキャンセル
			</button>
		</div>
	</div>
</template>
<script>
import { CREATED, UNPROCESSABLE_ENTITY } from '../util';
import Loader from './Loader.vue';
import Note from './Note.vue';

export default {
	components: {
		Loader,
		Note
	},
	data() {
		return {
			preview: null, //プレビュー機能用にファイルの画像データを格納する変数
			photo: null, //選択した写真ファイル自体を格納する変数
			photoErrors: null,
			viewLoader: false,
			photoValidation: '',
			tags: '', //tagを格納する文字列変数
			validationNewPieces: '',
			validationWordCount: '',
			photoValidationFlg: false
		};
	},
	computed: {
		//フォトフォームを表示するか否かのFlg
		showPhotoForm() {
			return this.$store.state.formTab.showPhotoForm;
		}
	},
	methods: {
		//inputタグでファイルが選択されたらバリデーションしてpreview表示するメソッド
		onFileChange(event) {
			this.preview = null;
			this.photoErrors = null;
			this.photoValidation = '';
			this.photoValidationFlg = false;
			//ファイルが空だったら処理中止
			if (event.target.files.length === 0) {
				this.reset();
				return false;
			}
			//ファイルが画像形式でなかったら処理中止
			if (!event.target.files[0].type.match('image.*')) {
				this.photoValidation = '画像形式のみ保存可能です';
				return false;
			}
			//ファイルが画像形式でなかったら処理中止
			if (event.target.files[0].size >= 2000000) {
				this.photoValidation = 'ファイルの上限サイズ2MBを超えています';
				return false;
			}
			//FileRenderのインスタンスを生成
			const reader = new FileReader();
			const image = new Image();
			reader.onload = (e) => {
				//縦横のサイズ確認用変数checkImgに画像データを格納
				const checkImg = e.target.result;
				image.src = checkImg;
				image.onload = () => {
					// 画像が縦長でなかったら処理終了
					if (image.naturalWidth >= image.naturalHeight) {
						this.photoValidation = '縦長の画像のみ保存可能です';
						return false;
					} else {
						//縦長画像なら表示用変数previewにデータを格納してバリデーション終了
						this.preview = e.target.result;
						this.photoValidationFlg = true;
					}
				};
			};
			//ファイルのデータをdataURLに変換
			reader.readAsDataURL(event.target.files[0]);
			//サーバに送信する変数photoに格納
			this.photo = event.target.files[0];
		},

		//タグ新規登録時に入力値inputをバリデーションするメソッド
		tagValidation(input) {
			//inputにスペース以外の入力があったら
			if (input.match(/\S/g)) {
				//入力文字列inputをスペースで区切り、配列inputWordsに変換
				let inputWords = input.split(/\s+/);

				//新規登録tagと登録済みtagを連結、要素から空白を取り除いて配列tagWordsに格納
				let tagWords = inputWords.filter((word) => {
					return !(word === null || word === undefined || word === '' || !word.match(/\S/g));
				});
				//重複入力を取り除く
				tagWords = [...new Set(tagWords)];
				//バリデーション
				if (tagWords.length > 5) {
					this.validationNewPieces = '登録できるタグは５個までです';
				} else {
					this.validationNewPieces = '';
				}
				if (
					tagWords.find((word) => {
						return word.length > 10;
					})
				) {
					this.validationWordCount = '1タグの最大文字数は10文字です';
				} else {
					this.validationWordCount = '';
				}
			}
		},
		//フォームの入力内容とエラーメッセージを空にするメソッド
		reset() {
			(this.preview = ''),
				(this.photo = null),
				(this.$el.querySelector('input[type="file"]').value = null);
			this.photoErrors = '';
			this.photoValidation = '';
			this.validationNewPieces = '';
			this.validationWordCount = '';
		},
		//写真をサーバーに送信するメソッド
		async submitPhoto() {
			this.viewLoader = true;
			const PhotoData = new FormData();
			PhotoData.append('photo', this.photo);
			//もしtag入力があったら一緒にformdataにする
			if (this.tags !== '' && this.tags.match(/\S/g)) {
				PhotoData.append('tags', this.tags);
			}
			//APIサーバーからの返信を変数responseに格納
			const response = await axios.post('/api/photos', PhotoData);
			this.viewLoader = false;
			//返信がバリデーションエラーメッセージならメッセージを表示して処理中止
			if (response.status === UNPROCESSABLE_ENTITY) {
				this.photoErrors = response.data.errors;
				return false;
			}
			//フォームの内容やerrorメッセージを空にしてフォームを閉じる
			this.reset();
			this.tags = '';
			this.$store.commit('formTab/setShowPhotoForm');
			//通信成功以外ならエラーメッセージを表示するページへ移動して終了
			if (response.status !== CREATED) {
				this.$store.commit('error/setCode', response.status);
				return false;
			}
			//通信成功ならsuccessメッセージを表示して投稿した写真の詳細ページへリダイレクト
			this.$store.commit('message/setSuccess', { message: '写真を投稿しました！', timeout: 6000 });
			this.$router.push(`/photos/${response.data.id}`);
		},
		//フォームを閉じるメソッド
		changeShowPhotoForm() {
			this.$store.commit('formTab/setShowPhotoForm');
		}
	},
	watch: {
		//tagが入力されたら即時バリデーション
		tags(newValue) {
			if (newValue) {
				this.tagValidation(newValue);
			} else if (newValue === '') {
				this.validationNewPieces = '';
				this.validationWordCount = '';
			}
		},
		//preview画像が差し代わったらinputタグのtagsをバリデーションチェックする
		preview() {
			this.tagValidation(this.tags);
		},

		// 写真フォームが閉じられたら入力内容を初期化（入力項目とエラーメッセージを空にする）
		showPhotoForm(newValue) {
			if (newValue === false) {
				this.reset();
				this.tags = '';
			}
		}
	}
};
</script>
