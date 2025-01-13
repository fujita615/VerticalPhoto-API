<template>
	<div class="p-detail__tag-container">
		<ul v-if="photo.tags.length > 0" class="p-tag">
			<li v-for="tag in photo.tags" :key="tag.name" class="p-tag__item">
				<a
					v-show="!editTagFlg"
					tite="tag"
					:href="`/photolist/${tag.name}`"
					class="c-button c-button--tag"
				>
					<span class="c-button__text"><i class="fa-solid fa-tags"></i>{{ tag.name }}</span>
					<span class="c-button__hover-text c-button__hover-text--form">
						<i class="fa-solid fa-link"></i>
					</span>
				</a>
				<button v-show="editTagFlg" @click.prevent="deleteTag(tag.name)" class="c-button c-button--tag">
					<span class="c-button__text"><i class="fa-solid fa-trash-can"></i>{{ tag.name }}</span>
					<span class="c-button__hover-text c-button__hover-text--form">
						<i class="fa-solid fa-trash-can"></i>
					</span>
				</button>
			</li>
		</ul>
		<div v-if="photo.posted_by_user" class="p-form__error p-form__error--fulltag">
			<div v-if="editTagFlg && validationOldPieces" class="p-form__error-message">
				{{ validationOldPieces }}
			</div>
		</div>
		<div v-show="editTagFlg && !validationOldPieces" class="p-form">
			<div class="c-input__label">
				<strong>Tag </strong><br />
				<small
					>複数入力時はスペースで区切ってください<br />1タグ10文字まで、合計5タグ登録可能です</small
				>
			</div>
			<div class="p-form__input">
				<i class="fa-solid fa-tags p-form__input-icon"></i>
				<input
					v-model="newTags"
					type="text"
					title="最大５個まで登録可能"
					placeholder="タグ ※合計５個まで"
					class="c-input c-input--form"
				/>
			</div>
			<button
				v-show="
					!newTags ||
					validationDuplication ||
					validationNewPieces ||
					validationWordCount ||
					!checkTagValidationFlg
				"
				class="c-button c-button--form c-button--disabled"
				title="編集中..."
			>
				while editing...
			</button>
			<button
				v-show="
					!(
						!newTags ||
						validationDuplication ||
						validationNewPieces ||
						validationWordCount ||
						!checkTagValidationFlg
					)
				"
				@click.self="addTags"
				type="submit"
				class="c-button c-button--form"
				title="タグを追加"
			>
				put tags on
			</button>
			<div class="p-form__error p-form__error--tags p-form__error-message">
				<span v-show="validationDuplication">{{ validationDuplication }}</span>
				<span v-show="validationNewPieces">{{ validationNewPieces }}<br /></span>
				<span v-show="validationWordCount">{{ validationWordCount }}</span>
				<div v-if="tagErrors && tagErrors.name" class="p-form__error-message">
					<label v-for="msg in tagErrors.name" :key="msg">
						{{ msg }}
					</label>
				</div>
			</div>
		</div>
		<button
			v-show="photo.posted_by_user && !editTagFlg"
			@click.self="editTagFlg = !editTagFlg"
			title="edit Tag"
			class="c-button c-button--edit"
		>
			<i class="fa-solid fa-pencil"></i> タグを編集する
		</button>
		<button
			v-show="photo.posted_by_user && editTagFlg"
			@click.self="editTagFlg = !editTagFlg"
			title="Finish editing"
			class="c-button c-button--edit"
		>
			<i class="fa-solid fa-ban"></i> タグ編集を終了する
		</button>
	</div>
</template>
<script>
import axios from 'axios';
import { OK, CREATED, UNPROCESSABLE_ENTITY } from '../util';

export default {
	props: {
		//photodetail(親コンポーネント）から渡される写真(１枚)データ
		photoData: {
			type: Object,
			requierd: true
		}
	},
	data() {
		return {
			// tags:[],
			newTags: '', //フォームの入力値
			tagErrors: '',
			editTagFlg: false, //編集フォームのon・off
			checkTagValidationFlg: false, //バリデーションのon・off
			validationOldPieces: '',
			validationDuplication: '',
			validationNewPieces: '',
			validationWordCount: ''
		};
	},
	computed: {
		//ログイン中かを表すFlg
		isLogin() {
			return this.$store.getters['auth/check'];
		},
		//親コンポーネントから受け取る写真（１枚）データ
		photo() {
			return this.photoData;
		},
		//写真データに紐付け済みのTags
		fetchOldTags() {
			//紐付けされたTagがない場合は空文字を返却
			if (!this.photo.tags) {
				return '';
			}
			//紐付けされたTagを一つずつ string型に変換して配列を新たに生成
			return this.photo.tags.map((tag) => {
				return tag.name.toString();
			});
		}
	},
	methods: {
		//TagをDBに登録するメソッド
		async addTags() {
			const response = await axios.patch(`/api/photos/${this.photo.id}/tags`, { name: this.newTags });
			if (response.status === UNPROCESSABLE_ENTITY) {
				this.tagErrors = response.data.errors;
				return false;
			}
			if (response.status !== CREATED) {
				this.$store.commit('error/setCode', response.status);
				return false;
			}
			//登録成功したらサーバからresponsされるtagデータを表示用変数photo.tagに代入
			this.photo.tags = response.data.tags;
			//フォームを初期化して処理終了
			this.resetTagInput();
			this.editTagFlg = false;
		},
		//Tag数をバリデーションするメソッド
		countOldTags() {
			if (this.photo.tags.length === 5) {
				this.validationOldPieces = '登録できるタグは５個までです';
				//編集処理中止(フォームのバリデーション非活性)
				this.checkTagValidationFlg = false;
			} else {
				this.validationOldPieces = '';
				//フォームのバリデーション活性
				this.checkTagValidationFlg = true;
			}
		},
		//編集時に入力値inputと既に登録済みTagを連結させてバリデーションするメソッド
		tagValidation(input) {
			let oldTags = []; //登録済みのTag
			if (!this.photo.tags) {
				oldTags = [];
			} else {
				oldTags = this.photo.tags.map((tag) => {
					return tag.name.toString();
				});
			}
			//フォームの入力文字列inputをスペースで区切り、配列inputWordsに変換
			let inputWords = input.trim().split(/\s+/);
			//新規登録tagと登録済みtagを連結、要素から空白を取り除いて配列tagWordsにする
			let tagWords = oldTags.concat(inputWords).filter((word) => {
				return !(word === null || word === undefined || word === '');
			});
			//重複入力を取り除く
			tagWords = [...new Set(tagWords)];
			//バリデーション
			if (oldTags !== '' && tagWords.length === oldTags.length && input !== '') {
				this.validationDuplication = '既に登録済みのタグです';
			} else {
				this.validationDuplication = '';
			}
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
		},
		//Tagの紐付けを解除するメソッド
		async deleteTag(tagName) {
			const response = await axios.delete(`/api/photos/${this.photo.id}/tags/${tagName}`);
			if (response.status !== OK) {
				this.$store.commit('error/setCode', response.status);
				return false;
			}
			//APIから登録成功のresponseが届いたら、表示中のtagを格納しているphoto.tagsから消去した要素(tag名)を除去する
			this.photo.tags = this.photo.tags.filter((elem) => !(elem.name === tagName));
			this.validationOldPieces = '';
			this.tagValidation(this.newTags, this.photo.tags);
		},
		//編集終了（中止）時、入力値とバリデーションメッセージを空にする関数
		resetTagInput() {
			this.newTags = '';
			this.validationDuplication = '';
			this.validationNewPieces = '';
			this.validationWordCount = '';
		}
	},
	watch: {
		//編集するボタンが押されるかを監視
		editTagFlg(newValue) {
			if (newValue === true) {
				//登録済みTag数をバリデーションする
				this.countOldTags();
			} else {
				// 編集終了(中止）したら入力とバリデーションメッセージを空にする
				this.resetTagInput();
			}
		},
		//フォームに入力されるかを監視
		newTags(newValue) {
			//文字入力があった時はバリデーションを開始
			if (newValue.match(/\S/g) && newValue !== '') {
				this.checkTagValidationFlg = true;
				this.tagValidation(newValue, this.fetchOldTags);
			} else {
				this.checkTagValidationFlg = false;
				this.validationDuplication = '';
			}
		},
		//photoデータの変更を監視
		photo(newValue, oldValue) {
			//photo.IDが変わった（＝別の写真の詳細ページへ遷移した）ときはフォームを閉じる(input内容をリセットする)
			if (newValue.id !== oldValue.id) {
				this.editTagFlg = false;
			} else {
				//それ以外(=photo.tagを更新した時)はバリデーションの引数を入れ直してバリデーションを行う
				this.tagValidation(this.newTags, this.photo.tags);
			}
		}
	}
};
</script>
