import {ApiEndPointI} from './index';

const USER_LIST : ApiEndPointI = {

	method: 'GET',
	url: process.env.API_URL+'user',
	sectionName: 'users',
	mode: 'cors',	
	auth: true,
	saveRequest: true
}

const USER_CHANGE_ROLE : ApiEndPointI = {

	method: 'PUT',
	url: process.env.API_URL+'user/change-role',
	sectionName: 'user_action',
	mode: 'cors',	
	auth: true,
}

const SUPERVISOR_RELATION_UPLOAD : ApiEndPointI = {

	method: 'POST',
	url: process.env.API_URL+'user/upload',
	sectionName: 'user_action',
	mode: 'cors',
	file: true,
	auth: true
}

export default  {USER_LIST, SUPERVISOR_RELATION_UPLOAD, USER_CHANGE_ROLE}