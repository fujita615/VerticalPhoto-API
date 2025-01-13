<template>
	<li>
		<figure class="p-photolist__figure">
			<router-link
				:to="`/photos/${item.id}`"
				:title="`View the photo by ${item.owner.nickname}`"
				class="c-figure c-figure--zoom-mask"
			>
				<span class="c-figure__mask">
					<img
						:src="item.url"
						:alt="`Photo By ${item.owner.nickname}`"
						class="c-figure__image c-figure__image--photolist c-figure__image--zoom-mask"
					/>
					<span class="c-figure__mask-area">
						<!-- ダウンロードボタン -->
						<a
							v-show="isLogin"
							@click.stop
							title="Download photo"
							:href="`/photos/${item.id}/download`"
							class="c-button c-button--photo c-button--photo-list"
						>
							<span class="c-button__text"><i class="fa-regular fa-circle-down"></i></span>
							<span class="c-button__hover-text c-button__hover-text--form">
								<i class="fa-solid fa-circle-down"></i>
							</span>
						</a>
						<!-- いいねボタン -->
						<button
							@click.prevent="like"
							title="Like photo"
							:class="{ 'is-liked': item.liked_by_user }"
							class="c-button c-button--photo c-button--photo-list"
						>
							<span class="c-button__text"
								><i class="fa-regular fa-thumbs-up"></i> {{ item.likes_count }}</span
							>
							<span class="c-button__hover-text c-button__hover-text--form">
								<i class="fa-solid fa-thumbs-up"></i>
							</span>
						</button>
						<div v-show="item.posted_by_user" class="c-figure__photographer c-figure__photographer--mask">
							MY PHOTO
						</div>
						<div
							v-show="!item.posted_by_user"
							class="c-figure__photographer c-figure__photographer--mask"
						>
							{{ item.owner.nickname }}
						</div>
					</span>
				</span>
			</router-link>
		</figure>
	</li>
</template>

<script>
export default {
	//親コンポーネントから渡ってくる写真（１枚）データ
	props: {
		item: {
			type: Object,
			required: true
		}
	},
	computed: {
		//ログイン中か否か
		isLogin() {
			return this.$store.getters['auth/check'];
		}
	},
	methods: {
		//クリックされた写真のIDといいね済みかと投稿者かを親コンポーネントに知らせるメソッド
		like() {
			this.$emit('like', {
				id: this.item.id,
				liked: this.item.liked_by_user,
				self: this.item.posted_by_user
			});
		}
	}
};
</script>
