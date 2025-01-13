<template>
	<Search ref="searchPhotos" :all-items="allPhotos" @search-items="fetchSearchPhoto" />
	<h3 v-if="searchTag()" class="c-sub-heading c-sub-heading--article">
		<i class="fa-solid fa-tag"></i>{{ searchTag() }}
	</h3>
	<label v-show="photos.length > 0" class="p-mypage__photo-count"> {{ photos.length }} photo</label>
	<div class="p-photolist">
		<ul class="p-photolist__container">
			<Photo
				v-for="photo in viewPhotos"
				:key="photo.id"
				:item="photo"
				@like="onLikeClick"
				class="p-photolist__card"
			/>
		</ul>
	</div>
	<MoreView ref="MoreView" :all-items="photos" :load-num="loadNum" @view-items="loadMoreView" />
	<!-- <PageLink :current-page="currentPage" :last-page="lastPage"></PageLink> -->
</template>
<script>
import { OK } from '../util';
import Photo from '../components/Photo.vue';
import MoreView from '../components/MoreView.vue';
import Search from '../components/Search.vue';

export default {
	components: {
		Photo,
		MoreView,
		Search
		// PageLink,
	},
	props: {
		// page: {
		//     type: Number,
		//     required: false,
		//     default:1,
		// },
		photoTag: {
			//router.js経由で渡される検索タグ名
			type: String,
			required: false,
			default: ''
		}
	},
	data() {
		return {
			photos: [], //moreview(分割加工コンポーネント)に渡すためのデータ
			viewPhotos: [], //表示するために分割加工したデータ
			loadNum: 12 //一度に表示する写真の個数
			//  currentPage: 0,※pagination用
			// lastPage: 0,※pagination用
		};
	},
	computed: {
		//絞り込み検索の結果を表すフラグ
		searchFlg: {
			get() {
				return this.$store.getters['search/flg'];
			},
			set(newValue) {
				return this.$store.commit('search/setFlg', newValue);
			}
		},
		//一覧表示する写真データ
		allPhotos: {
			get() {
				return this.$store.getters['search/allPhotos'];
			},
			set(newValue) {
				return this.$store.commit('search/setAllPhotos', newValue);
			}
		}
	},
	methods: {
		// タグ指定があった場合、全写真から絞り込み検索をするメソッド
		async fetchTagPhotos() {
			if (this.photoTag !== '') {
				await this.$store.commit(
					'search/setAllPhotos',
					this.allPhotos.filter((photo) => {
						return photo.tags.some((tag) => {
							return tag.name === this.photoTag;
						});
					})
				);
			}
			// 絞り込み検索の結果,データがなかった場合（=urlの直接入力時）検索結果flgをnoneにして、TOP画面へリダイレクト
			if (this.allPhotos.length === 0) {
				this.$store.commit('search/setFlg', 'none');
				this.$router.push('/');
			}
		},
		//searchコンポーネント（子コンポーネント）から返却された写真データを取得するメソッド
		async fetchSearchPhoto(searchItems) {
			this.photos = searchItems;
			//写真撮影者が退会している写真は除外する
			this.photos = this.photos.filter((photo) => {
				return photo.owner !== null;
			});
			//分割データを初期化して,あらためて取得しデータを分割し直す
			await this.$refs.MoreView.reset();
			this.$refs.MoreView.load();
		},
		//写真一覧データを分割するコンポーネントから返却された分割データを取得するメソッド
		loadMoreView(viewItems) {
			this.viewPhotos = viewItems;
		},
		//fetchTagPhotosの後、写真データが存在する時だけtag名を表示する（=urlの直接入力対策）
		searchTag() {
			if (this.searchFlg === 'hit') {
				return this.photoTag;
			}
		},
		//photo（子コンポーネント）から渡ってくる値からLikeをつけるのか外すのか判定するメソッド
		onLikeClick({ id, liked, self }) {
			if (!this.$store.getters['auth/check']) {
				this.$store.commit('message/setAlert', {
					message: 'いいね機能を使うにはログインしてください',
					timeout: 3000
				});
				return false;
			}
			if (self) {
				this.$store.commit('message/setAlert', {
					message: 'ご自身の写真にいいね機能は使えません',
					timeout: 3000
				});
				return false;
			}
			if (liked) {
				this.unlike(id);
			} else {
				this.like(id);
			}
		},
		//Likeをつけるメソッド
		async like(id) {
			//DBにlikeを登録
			const response = await axios.put(`/api/photos/${id}/like`);
			if (response.status !== OK) {
				this.$store.commit('error/setCode', response.status);
				return false;
			}
			//photo全てに対してidをkeyにmap(繰り返し更新）処理
			this.photos = this.photos.map((photo) => {
				if (photo.id === response.data.photo_id) {
					//responseで返ってきた該当photo(id)だった場合にlikesカウントをプラス１
					photo.likes_count += 1;
					photo.liked_by_user = true;
				}
				return photo;
			});
		},
		//likeを外すメソッド
		async unlike(id) {
			const response = await axios.delete(`/api/photos/${id}/like`);
			if (response.status !== OK) {
				this.$store.commit('error/setCode', response.status);
				return false;
			}
			this.photos = this.photos.map((photo) => {
				if (photo.id === response.data.photo_id) {
					photo.likes_count -= 1;
					photo.liked_by_user = false;
				}
				return photo;
			});
		}
	},
	watch: {
		//$routeオブジェクトを監視して、変化を察知したら(=遷移やリロード)
		$route: {
			async handler() {
				//DBから写真一覧データを取得
				await this.$store.dispatch('search/fetchAllPhotos');
				//tagの入力があった場合は取得した写真を一覧をフロントエンドでさらに絞り込む
				await this.fetchTagPhotos();
				//searchコンポーネント（子コンポーネント）の関数を呼び出して検索ワードで絞り込む
				await this.$refs.searchPhotos.searchPhotos();
				//moreviewコンポーネント（子コンポーネント）の関数を呼び出して一度に表示する個数に写真データを分割する
				await this.$refs.MoreView.load();
			},
			immediate: true
		},
		//検索flgがinitになった際は(=ロゴクリックされた際は)
		async searchFlg(newValue) {
			if (newValue === 'init') {
				// DBからデータ取得して全データを表示
				await this.$store.dispatch('search/fetchAllPhotos');
				await this.$refs.searchPhotos.searchPhotos();
				this.$refs.MoreView.reset();
				this.$refs.MoreView.load();
			}
		}
	}
};
</script>
