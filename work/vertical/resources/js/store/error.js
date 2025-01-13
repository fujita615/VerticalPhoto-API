//通信errorを監視するためにHTTPステータスを保存するstoreモジュール
export default {
	namespaced: true,
	state: {
		code: null
	},
	mutations: {
		setCode(state, apiCode) {
			state.code = apiCode;
		}
	}
};
