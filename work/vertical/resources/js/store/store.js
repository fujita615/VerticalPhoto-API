import { createStore } from 'vuex';
import auth from './auth';
import error from './error';
import formTab from './formTab';
import message from './message';
import search from './search';

export const store = createStore({
	modules: {
		auth,
		error,
		formTab,
		message,
		search
	}
});
