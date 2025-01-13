import axios from 'axios';
import {
	OK,
	CREATED,
	LOGOUT,
	UNPROCESSABLE_ENTITY, //APIサーバーで受信したけどバリデーションエラーで処理中止となったリクエスト
	INTERNAL_SERVER_ERROR
} from '../util';

export default {
	namespaced: true,
	state: {
		user: '', //ログイン中のユーザーの登録情報
		apiStatus: '', //API処理の成功か失敗かを表すflg
		loginErrorMessage: '',
		registerErrorMessage: ''
	},
	getters: {
		check: (state) => !!state.user, //ログインしているか否かを表すflg
		username: (state) => (state.user ? state.user.nickname : ''), //ログイン中のユーザーのnickname
		user: (state) => state.user //ログイン中のユーザーの登録情報
	},
	mutations: {
		setUser(state, user) {
			state.user = user;
		},
		setApiStatus(state, apiStatus) {
			state.apiStatus = apiStatus;
		},
		setLoginErrorMessage(state, message) {
			state.loginErrorMessage = message;
		},
		setRegisterErrorMessage(state, message) {
			state.registerErrorMessage = message;
		}
	},
	actions: {
		//ログイン状態を確認してstate.userを更新するメソッド
		async currentUser(context) {
			//setApiStatusを初期化
			context.commit('setApiStatus', '');
			const response = await axios.get('/api/user');
			//response（サーバーが保持しているユーザー情報）を変数apiUserに詰める（ログインしてなければ''を詰める）
			const apiUser = response.data || '';

			//通信(HTTPステータス)が成功なら
			if (response.status === OK) {
				//API通信結果を示すsetApiStatusをtrueに
				context.commit('setApiStatus', true);
				//ユーザー情報を更新
				context.commit('setUser', apiUser);
				return false;
			}
			//API通信(HTTPステータス)が成功以外なら setApiStatusをfalseに
			context.commit('setApiStatus', false);
			//errorストアにHTTPステータスを渡す
			context.commit('error/setCode', response.status, {
				root: true
			});
		},
		//ユーザー新規登録メソッド
		async register(context, formUserData) {
			context.commit('setApiStatus', '');
			//registerAPIを呼び出してFormに入力された登録データを渡し、そのレスポンス（userデータ）を受け取る
			await axios.get('/api/refresh-token');
			const response = await axios.post('/api/register', formUserData);
			if (response.status === CREATED) {
				context.commit('setApiStatus', true);
				context.commit('setUser', response.data);
				return false;
			}
			context.commit('setApiStatus', false);
			if (response.status === UNPROCESSABLE_ENTITY) {
				context.commit('setRegisterErrorMessage', response.data.errors);
			} else {
				context.commit('error/setCode', response.status, {
					root: true
				});
			}
		},
		//ログインメソッド
		async login(context, formUserData) {
			context.commit('setApiStatus', '');
			await axios.get('/api/refresh-token');
			const response = await axios.post('/api/login', formUserData);
			if (response.status === OK) {
				context.commit('setApiStatus', true);
				context.commit('setUser', response.data);
				return false;
			}
			context.commit('setApiStatus', false);
			if (response.status === UNPROCESSABLE_ENTITY) {
				context.commit('setLoginErrorMessage', response.data.errors);
			} else {
				context.commit('error/setCode', response.status, {
					root: true
				});
			}
		},
		//ログアウトメソッド
		async logout(context) {
			context.commit('setApiStatus', '');
			const response = await axios.post('/api/logout');
			if (response.status === LOGOUT) {
				context.commit('setApiStatus', true);
				context.commit('setUser', '');
				await axios.get('/api/refresh-token');
				return false;
			}
			context.commit('setApiStatus', false);
			context.commit('error/setCode', response.status, { root: true });
		},
		//ユーザー情報編集メソッド
		async editUser(context, formUserData) {
			context.commit('setApiStatus', '');
			const response = await axios.patch('/api/register', formUserData);
			if (response.status === OK) {
				context.commit('setApiStatus', true);
				context.commit('setUser', response.data);
				return false;
			}
			context.commit('setApiStatus', false);
			if (response.status === UNPROCESSABLE_ENTITY) {
				context.commit('setRegisterErrorMessage', response.data.errors);
			} else {
				context.commit('error/setCode', response.status, {
					root: true
				});
			}
		},
		//パスワード変更メソッド
		async editPassWord(context, formUserData) {
			context.commit('setApiStatus', '');
			const response = await axios.put('/api/register', formUserData);
			if (response.status === OK) {
				context.commit('setApiStatus', true);
				context.commit('setUser', response.data);
				return false;
			}
			context.commit('setApiStatus', false);
			if (response.status === UNPROCESSABLE_ENTITY) {
				context.commit('setRegisterErrorMessage', response.data.errors);
			} else {
				context.commit('error/setCode', response.status, {
					root: true
				});
			}
		},
		//ユーザー登録削除メソッド
		async deleteUser(context) {
			context.commit('setApiStatus', '');
			const response = await axios.delete('/api/register');
			if (response.status === OK) {
				context.commit('setApiStatus', true);
				context.commit('setUser', '');
				return false;
			}
			context.commit('setApiStatus', false);
			context.commit('error/setCode', response.status, {
				root: true
			});
		},
		//エラーメッセージをクリアするメソッド
		allErrorMessageClear(context) {
			context.commit('setRegisterErrorMessage', '');
			context.commit('setLoginErrorMessage', '');
		}
	}
};
