export default {
	namespaced: true,
	state: {
		alert: '', //注意メッセージ
		success: '' //処理成功メッセージ
	},
	mutations: {
		//messageをtimeout秒だけstateに代入するメソッド
		setAlert(state, { message, timeout }) {
			state.alert = message;
			//デフォルトは3秒
			if (typeof timeout === 'undefined') {
				timeout = 3000;
			}
			setTimeout(() => (state.alert = ''), timeout);
		},
		setSuccess(state, { message, timeout }) {
			state.success = message;
			if (typeof timeout === 'undefined') {
				timeout = 3000;
			}
			setTimeout(() => (state.success = ''), timeout);
		}
	}
};
