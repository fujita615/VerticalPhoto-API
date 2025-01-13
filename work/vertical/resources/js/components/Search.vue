<template>
	<form @submit.prevent="searchPhotos" class="p-form">
		<div class="p-form__input">
			<i class="fa-solid fa-magnifying-glass p-form__input-icon"></i>
			<input
				v-model="searchWords"
				type="text"
				placeholder="タグで絞り込み"
				class="c-input c-input--search"
			/>
		</div>
	</form>
</template>
<script>
export default {
	//photolist（親コンポーネント）から全写真データを受け取る
	props: {
		allItems: {
			type: Object,
			required: true
		}
	},
	//検索結果データを親コンポーネントに返却
	emits: ['searchItems'],
	data() {
		return {
			searchItems: '' //検索結果データ
		};
	},
	computed: {
		// 検索ワード（vuexで管理しつつv-modelで双方向表示）
		searchWords: {
			get() {
				return this.$store.getters['search/Words'];
			},
			set(newValue) {
				this.$store.commit('search/setWords', newValue);
			}
		}
	},
	methods: {
		searchPhotos() {
			let inputWords = []; //入力ワード（複数）を格納する配列

			//親コンポーネントから受け取ったallItemsを検索対象のseartchItems配列に渡す
			this.searchItems = [...this.allItems];
			// 入力された検索ワードが空白だけではない場合は配列に変換する
			if (this.searchWords.match(/\S/g) && this.searchWords !== '') {
				inputWords = this.searchWords
					.trim()
					.split(/\s+/)
					.filter((word) => {
						return !(word === null || word === undefined || word === '');
					});
				//検索ワードの重複を削除
				inputWords = [...new Set(inputWords)];
			} else {
				//検索ワードがない場合は全データをそのまま検索結果として親コンポーネントに返却して終了
				this.$emit('searchItems', this.searchItems);
				return false;
			}
			//検索ワード（１つ）で写真データを絞り込み、絞り込んだデータを次のワードでさらに絞り込む処理をinputWordsの要素分繰り返す(forEach)
			inputWords.forEach((word) => {
				this.searchItems = this.searchItems.filter((photo) => {
					return photo.tags.some((tag) => {
						return tag.name.includes(word);
					});
				});
			});

			//photolist(親コンポーネント)に検索結果を返却
			this.$emit('searchItems', this.searchItems);
		}
	}
};
</script>
