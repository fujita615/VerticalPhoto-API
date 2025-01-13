<template>
	<h3 class="c-sub-heading c-sub-heading--article">comment</h3>
	<label v-show="photo.comments.length > 0" class="p-detail__comment-count">
		{{ photo.comments.length }}件</label
	>
	<ul v-if="photo.comments.length > 0" class="p-detail__comment-container">
		<li
			v-for="comment in photo.comments"
			:key="comment.content"
			class="p-detail__comment"
			:class="{ 'p-detail__comment--by-author': comment.commented_by_author }"
		>
			<p class="c-paragraph c-paragraph--comment">{{ comment.content }}</p>
			<p class="c-paragraph__commenter">{{ comment.author.nickname }}</p>
			<button
				v-show="comment.commented_by_author"
				@click="showEditForm"
				class="c-button c-button--photo"
				title="edit comment"
			>
				<span class="c-button__text">編集</span>
				<span class="c-button__hover-text c-button__hover-text--form">
					<i class="fa-solid fa-pencil"></i>
				</span>
			</button>
			<button
				v-show="comment.commented_by_author"
				@click="deleteComment"
				class="c-button c-button--photo"
				title="Delete comment"
			>
				<span class="c-button__text">削除</span>
				<span class="c-button__hover-text c-button__hover-text--form">
					<i class="fa-solid fa-trash-can"></i>
				</span>
			</button>
		</li>
	</ul>
	<p v-else-if="isLogin && !photo.posted_by_user">最初のコメントをどうぞ！</p>
	<p v-else>現在コメントはありません</p>

	<!-- コメント投稿フォーム -->
	<div v-show="isLogin && !photo.posted_by_user && !photo.commented_by_user" class="p-form">
		<div class="p-form__textarea">
			<textarea
				v-model="comment"
				cols="30"
				rows="10"
				placeholder="コメントする！"
				class="c-textarea c-textarea--comment"
			>
			</textarea>
		</div>
		<button v-show="!comment || validation" class="c-button c-button--form c-button--disabled">
			Please comment
		</button>
		<button
			v-show="comment && !validation"
			@click.prevent="addComment"
			class="c-button c-button--form"
		>
			submit comment
		</button>
	</div>
	<div v-show="showEditFormFlg" class="p-form">
		<div class="p-form__textarea">
			<textarea
				v-model="newComment.content"
				cols="30"
				rows="10"
				placeholder="コメントする！"
				class="c-textarea c-textarea--comment"
			>
			</textarea>
		</div>
		<button
			v-show="!newComment.content || validation"
			class="c-button c-button--form c-button--disabled"
		>
			Please comment
		</button>
		<button
			v-show="newComment.content && !validation"
			@click.prevent="editComment"
			class="c-button c-button--form"
		>
			update comment
		</button>
	</div>
	<div class="p-form__error">
		<div v-if="commentErrors && commentErrors.content" class="p-form__error-message">
			<label v-for="msg in commentErrors.content" :key="msg">
				{{ msg }}
			</label>
		</div>
		<div v-show="validation" class="p-form__error-message">
			{{ validation }}
		</div>
	</div>
</template>

<script>
import axios from 'axios';
import { OK, CREATED, UNPROCESSABLE_ENTITY } from '../util';

export default {
	props: {
		photoData: {
			type: Object,
			required: true
		}
	},
	data() {
		return {
			comment: '',
			commentErrors: null,
			validation: '',
			showEditFormFlg: false,
			newComment: ''
		};
	},
	computed: {
		isLogin() {
			return this.$store.getters['auth/check'];
		},
		photo() {
			return this.photoData;
		}
	},
	methods: {
		//コメントを新規登録するメソッド
		async addComment() {
			const response = await axios.post(`/api/photos/${this.photo.id}/comments`, {
				content: this.comment
			});
			//APIサーバでバリデーションエラーがあった際はエラーメッセージを表示して処理を途中終了
			if (response.status === UNPROCESSABLE_ENTITY) {
				this.commentErrors = response.data.errors;
				return false;
			}
			this.comment = '';
			this.commentErrors = '';
			//APIサーバでバリデーションエラー以外のエラーがあった際はエラーページへリダイレクト（処理を途中終了）
			if (response.status !== CREATED) {
				this.$store.commit('error/setCode', response.status);
				return false;
			}
			//既に表示してあるthis.photo.commentsの先頭列に今APIサーバに送信して返却されたコメント(respons.data)を追加して表示
			(this.photo.comments = [response.data, ...this.photo.comments]),
				(this.photo.commented_by_user = true);
		},
		//投稿済みコメントを編集するためのフォームを表示するメソッド
		showEditForm() {
			this.showEditFormFlg = !this.showEditFormFlg;
			this.newComment = this.photo.comments.find((comment) => comment.commented_by_author == true);
			this.photo.comments = this.photo.comments.filter(
				(comment) => !(comment.commented_by_author == true)
			);
		},
		//コメント編集・再投稿メソッド
		async editComment() {
			const response = await axios.put(`/api/photos/${this.photo.id}/comments`, {
				content: this.newComment.content
			});
			if (response.status === UNPROCESSABLE_ENTITY) {
				this.commentErrors = response.data.errors;
				return false;
			}
			this.newComment = '';
			this.commentErrors = '';
			if (response.status !== CREATED) {
				this.$store.commit('error/setCode', response.status);
				return false;
			}
			(this.photo.comments = [response.data, ...this.photo.comments]),
				(this.showEditFormFlg = !this.showEditFormFlg);
		},
		//投稿コメントを消去するメソッド
		async deleteComment() {
			const response = await axios.delete(`/api/photos/${this.photo.id}/comments`);
			if (response.status !== OK) {
				this.$store.commit('error/setCode', response.status);
				return false;
			}
			//表示中のコメント(photo.comments)から自分のコメントを消す
			this.photo.comments = this.photo.comments.filter((elem) => !(elem.commented_by_author === true));
			//コメントフォームを表示させる
			this.photo.commented_by_user = false;
			this.commentErrors = '';
		}
	},
	watch: {
		//コメント新規投稿時のバリデーション
		comment(newValue) {
			if (newValue === '' || !newValue.match(/\S/g)) {
				this.validation = '　　';
			} else if (newValue.length > 500) {
				this.validation = '500文字以内で入力してください';
			} else {
				this.validation = '';
			}
		},
		//コメント編集時のバリデーション
		'newComment.content': function (newValue) {
			if (newValue === '' || !newValue.match(/\S/g)) {
				this.validation = '　　';
			} else if (newValue.length > 500) {
				this.validation = '500文字以内で入力してください';
			} else {
				this.validation = '';
			}
		}
	}
};
</script>
