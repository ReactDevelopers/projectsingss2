import {ApiEndPointI} from './index';


const COURSE_LIST : ApiEndPointI = {

	method: 'GET',
	url: process.env.API_URL+'course',
	sectionName: 'course',
	mode: 'cors',	
	auth: true,
	saveRequest: true
}

const COURSE_LIST_VIEWER : ApiEndPointI = {

	method: 'GET',
	url: process.env.API_URL+'viewer/course',
	sectionName: 'course',
	mode: 'cors',	
	auth: true,
	saveRequest: true
}

const COURSE_ACTION : ApiEndPointI = {

	method: 'POST',
	url: process.env.API_URL+'course',
	sectionName: 'course_action',
	mode: 'cors',
	auth: true,	
}
const COURSE_UPLOAD : ApiEndPointI = {

	method: 'POST',
	url: process.env.API_URL+'course/upload',
	sectionName: 'course_action',
	mode: 'cors',
	file: true,
	auth: true
}

const COURSE_DETAIL : ApiEndPointI = {

	method: 'GET',
	url: process.env.API_URL+'course',
	sectionName: 'course_detail',
	mode: 'cors',
	auth: true
}

const COURSE_BATCH_DELETE : ApiEndPointI = {

	method: 'DELETE',
	url: process.env.API_URL+'course',
	sectionName: 'course_action',
	mode: 'cors',
	auth: true
}


export default {COURSE_LIST,COURSE_LIST_VIEWER,COURSE_BATCH_DELETE, COURSE_ACTION, COURSE_UPLOAD, COURSE_DETAIL}