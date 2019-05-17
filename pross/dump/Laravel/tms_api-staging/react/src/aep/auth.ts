
export const AUTH_KEY = 'auth_user';
import {ApiEndPointI} from './index';
import * as authAction from '../actions/auth-action';


const  AUTH_USER : ApiEndPointI = {

	method: 'POST',
	url: process.env.API_URL+'login-action',
	sectionName: AUTH_KEY,
	mode: 'cors',
	type:{
		 request: authAction.authReq, 
		 success: authAction.authSuccess,
		 fail: authAction.authError
	}	
}

const  AUTH_ME : ApiEndPointI = {

	method: 'GET',
	url: process.env.API_URL+'me',
	sectionName: AUTH_KEY,
	auth: false,
	mode: 'cors',
	type:{
		request: authAction.meReq, 
		 success: authAction.meSuccess,
		 fail: authAction.meError
	}	
}
const  LOGOUT : ApiEndPointI = {

	method: 'GET',
	url: process.env.API_URL+'logout',
	sectionName: AUTH_KEY,
	auth: true,
	mode: 'cors',
	type:{		 
		request: authAction.logoutReq, 
		success: authAction.logoutSuccess,
		fail: authAction.logoutError
	}	
}

const  GET_OPTIONS : ApiEndPointI = {

	method: 'GET',
	url: process.env.API_URL+'get-list',
	sectionName: 'options',
	auth: true,
	mode: 'cors',	
}

export default { AUTH_USER, AUTH_ME, LOGOUT, GET_OPTIONS }