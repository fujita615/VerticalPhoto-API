import 'bootstrap';
import { getCookieValue } from './util';
import axios from 'axios';
window.axios = axios;

//カスタムheader(X-Requested-With)にajax通信を行えるXMLHttpRequestオブジェクトを代入
//フォームではなくheaderをチェックするようAPIサーバに伝える
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

//APIサーバへのリクエスト時に挟む処理
//APIサーバからcookieに預かっているトークンをリクエスト時のheaderにつめてCSRFトークンチェックをしてもらう
window.axios.interceptors.request.use((config) => {
	config.headers['X-XSRF-TOKEN'] = getCookieValue('XSRF-TOKEN');
	return config;
});

//APIサーバからのレスポンス時に挟む処理
//responseがあればresponseはそのまま、errorならerrorをresponseに詰める
window.axios.interceptors.response.use(
	(response) => response,
	(error) => error.response || error
);
