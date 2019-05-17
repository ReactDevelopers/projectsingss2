import axios from 'axios';
import {LOG_IN_SUCCESS, LOG_OUT_SUCCESS} from './actionTypes';
import jwtDecode from 'jwt-decode';


export function loginSuccess(user) {
	return {
		type: LOG_IN_SUCCESS,
		user
	};
}

export function logoutSuccess() {
	return {
		type: LOG_OUT_SUCCESS,
		user: null
	}
}

export function logout() {
	return (dispatch) => {
		return axios.post('api/logout').then(res => {
				localStorage.removeItem('jwtToken');
				//delete access token from axios defaults
				setAuthorizationToken(false);
				//remove user from store
				dispatch(logoutSuccess());
			}
		)
		.catch(err => {
			throw(err);
		})
	}
}

export function login(data) {
	return (dispatch) => {
		return axios.post('api/authenticate', data).then(res => {
				const token = res.data.token;
				localStorage.setItem('jwtToken', token);
				setAuthorizationToken(token);
				dispatch(loginSuccess(jwtDecode(token)));
			}
		)
		.catch(err => {
			throw(err);
			// let w = window.open();
			// w.document.html(err.response.data);
		});
	}
}

export function register(data) {
	return (dispatch) => {
		return axios.post('api/register', data).then(res => {
				const token = res.data.token;
				localStorage.setItem('jwtToken', token);
				setAuthorizationToken(token);
				dispatch(loginSuccess(jwtDecode(token)));
			}
		)
		.catch(err => {
			throw(err);
		});
	}
}

export function refreshAuthorizationToken(token, store) {
	return axios({
		url : 'api/token',
		method: 'get',
		headers: {'Authorization' : `Bearer ${token}`}
	}).then(res => {
		console.log(res);
		const token = res.data.token;
		localStorage.setItem('jwtToken', token);
		setAuthorizationToken(token, true);
		store.dispatch(loginSuccess(jwtDecode(token)));
	})
	.catch(err => {
		throw(err);
	});
}

function removeAxiosDefaultToken() {
	delete axios.defaults.headers.common['Authorization'];
}

export function setAuthorizationToken(token, refresh = false) {
	if (token) {
		refresh && removeAxiosDefaultToken();
		axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
	} else {
		removeAxiosDefaultToken();
	}
}
