//csrf対策
export function getCookieValue(searchKey) {
	//トークンがundefindだった場合は処理終了
	if (typeof searchKey === 'undefined') {
		return '';
	}
	let val = '';
	//cookieを全て取り出して;で区切り配列に代入
	document.cookie.split(';').forEach((cookie) => {
		//さらに要素を=で区切ってkeyとvalueに分け,
		//keyが引数searchKeyだった時のvalをreturnする処理を 要素数分　繰り返す
		const [key, value] = cookie.split('=');
		if (key === searchKey) {
			return (val = value);
		}
	});
	return val;
}
// httpステータスコード
export const OK = 200;
export const CREATED = 201;
export const LOGOUT = 204;
export const Un_authorized = 401;
export const NOT_FOUND = 404;
export const UNAUTHORIZED = 419; //認証失効（セッション切れ）
export const UNPROCESSABLE_ENTITY = 422; //バリデーションエラー
export const INTERNAL_SERVER_ERROR = 500; //システムエラー
