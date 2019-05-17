import {ApiEndPointI} from './index';

const EMAIL_TEMPLATE_ACTION : ApiEndPointI = {

	method: 'PUT',
	url: process.env.API_URL+'email-template',
	sectionName: 'email_template_action',
	mode: 'cors',
	auth: true
}

const EMAIL_TEMPLATE_DETAIL : ApiEndPointI = {

	method: 'GET',
	url: process.env.API_URL+'email-template',
	sectionName: 'email_template_detail',
	mode: 'cors',
	auth: true
}



export default {EMAIL_TEMPLATE_ACTION, EMAIL_TEMPLATE_DETAIL}