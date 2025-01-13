<template>
	<div class="p-standard">
		<div class="p-standard__container">
			<h2 class="c-medium-heading c-medium-heading--section">当サイトについて</h2>
			<!-- 注意書きコンポーネント -->
			<Note />
			<br />
			<!-- クッキーポリシーコンポーネント -->
			<CookiePolicy />
			<h3 class="c-sub-heading c-sub-heading--article">個人情報保護方針</h3>
			<p class="c-paragraph c-paragraph--article">サンプル</p>
		</div>
	</div>
</template>
<script>
import CookiePolicy from '../components/CookiePolicy.vue';
import Note from '../components/Note.vue';

export default {
	components: {
		Note,
		CookiePolicy
	}
};
</script>
