//各form/Dialogの状態を保存
export default {
	namespaced: true,
	state: {
		showPhotoForm: false,
		showContactForm: false,
		mailSituation: 'edit', //contactフォームのメール送信状況
		welcomeFlg: true
	},
	mutations: {
		setShowPhotoForm(state) {
			state.showPhotoForm = !state.showPhotoForm;
		},
		setShowContactForm(state) {
			state.showContactForm = !state.showContactForm;
		},
		setMailSituation(state, situation) {
			state.mailSituation = situation;
		},
		setWecomeFlg(state) {
			state.welcomeFlg = false;
		}
	}
};
