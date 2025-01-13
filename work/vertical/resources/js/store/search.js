import axios from 'axios';
import { OK } from '../util';

export default {
	namespaced: true,
	state: {
		allPhotos: [], //全写真
		Words: '', //検索ワード
		flg: 'init' //検索の状況
	},
	getters: {
		allPhotos: (state) => state.allPhotos,
		Words: (state) => state.Words,
		flg: (state) => state.flg
	},
	mutations: {
		setAllPhotos(state, photos) {
			state.allPhotos = photos;
		},
		setWords(state, input) {
			state.Words = input;
		},
		setFlg(state, result) {
			state.flg = result;
		}
	},
	actions: {
		//ロゴがクリックされたら検索ワードを空にして検索フラグをinitにするメソッド
		searchReset(context) {
			context.commit('setWords', '');
			context.commit('setFlg', 'init');
		},
		//DBから全写真データを取得するメソッド
		async fetchAllPhotos(context) {
			const response = await axios.get(`/api/photos`);
			//接続失敗の場合はエラーコードを取得・表示
			if (response.status !== OK) {
				context.commit('error/setCode', response.status);
				return false;
			}
			context.commit('setAllPhotos', response.data);
		}
	}
};
