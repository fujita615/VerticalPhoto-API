<template>
	<h3 v-if="getSearchFlg === 'none'">no matching photos</h3>
	<div v-if="getSearchFlg === 'hit'" class="p-photolist__moreview-container">
		<button
			v-if="hasNextFlg"
			@click="load"
			class="c-button c-button-photo p-photolist__moreview-next"
		>
			もっと見る
		</button>
		<button @click="scrollTop" class="c-button c-button-photo p-photolist__moreview-preview">
			<i class="fa-solid fa-circle-arrow-up faa-float animated"></i>
		</button>
	</div>
</template>
<script>
export default {
	name: 'MoreView',
	//photoListコンポーネント（親）から渡される値
	props: {
		//一覧表示する写真全データ
		allItems: {
			type: Object,
			required: true
		},
		//一度に表示する数
		loadNum: {
			type: Number,
			required: true
		}
	},
	//親コンポーネントに返却するデータ
	emits: ['viewItems'],
	data() {
		return {
			hasNextFlg: true, //まだ表示できていない写真が残っているか(true)否か
			viewItems: [] //表示するデータ
		};
	},
	computed: {
		//検索結果を表すFlg(検索前init、検索結果ありhit、検索結果なしnone)
		getSearchFlg() {
			return this.$store.getters['search/flg'];
		}
	},
	methods: {
		//１クリックごとに全写真を指定個数ずつ表示用配列に格納するメソッド
		load() {
			this.viewItems = this.viewItems.concat(
				this.allItems.slice(this.viewItems.length, this.viewItems.length + this.loadNum)
			);
			if (this.viewItems.length >= this.allItems.length) {
				this.hasNextFlg = false;
			}
			if (this.viewItems.length === 0) {
				this.$store.commit('search/setFlg', 'none');
			} else {
				this.$store.commit('search/setFlg', 'hit');
			}
			// 格納した表示用写真を親コンポーネントに返却する
			this.$emit('viewItems', this.viewItems);
		},
		//クリックしたら画面TOPに戻るメソッド
		scrollTop() {
			window.scrollTo({
				top: 0,
				behavior: 'smooth'
			});
		},
		//表示データを初期化するメソッド(※親コンポーネントから利用)
		reset() {
			this.viewItems = [];
			this.hasNextFlg = true;
		}
	}
};
</script>
