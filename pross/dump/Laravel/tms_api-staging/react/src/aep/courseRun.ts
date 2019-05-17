import {ApiEndPointI} from './index';
import TextInput from '../plugins/form/fields/TextInput';


const COURSE_RUN_LIST : ApiEndPointI = {

	method: 'GET',
	url: process.env.API_URL+'course-run',
	sectionName: 'course_run',
	mode: 'cors',	
	auth: true,
	saveRequest: true
}
const COURSE_RUN_STATUS_LIST : ApiEndPointI = {

	method: 'GET',
	url: process.env.API_URL+'course-run/edit-status-list',
	sectionName: 'course_run_status',
	mode: 'cors',	
	auth: true,
	saveRequest: true
}
const COURSE_RUN_ACTIVE_LIST : ApiEndPointI = {

	method: 'GET',
	url: process.env.API_URL+'viewer/course-run',
	sectionName: 'course_run_active',
	mode: 'cors',	
	auth: true,
	saveRequest: true
}

const COURSE_RUN_REPORT_LIST : ApiEndPointI = {

	method: 'GET',
	url: process.env.API_URL+'course-run/report-list',
	sectionName: 'course_run_report',
	mode: 'cors',	
	auth: true,
	saveRequest: true
}

const COURSE_RUN_ACTION : ApiEndPointI = {

	method: 'POST',
	url: process.env.API_URL+'course-run',
	sectionName: 'course_run_action',
	mode: 'cors',
	auth: true,	
	saveRequest: false,
}

const COURSE_RUN_DETAIL : ApiEndPointI = {

	method: 'GET',
	url: process.env.API_URL+'course-run',
	sectionName: 'course_run_detail',
	mode: 'cors',
	auth: true,	
	saveRequest: false,
}

const COURSE_RUN_CREATE : ApiEndPointI = {

	method: 'POST',
	url: process.env.API_URL+'course-run/upload-new',
	sectionName: 'course_run_action',
	mode: 'cors',
	file:true,
	auth: true,	
	saveRequest: false,
}

const COURSE_RUN_UPDATE : ApiEndPointI = {

	method: 'POST',
	url: process.env.API_URL+'course-run/upload-existed',
	sectionName: 'course_run_action',
	mode: 'cors',
	file:true,
	auth: true,	
	saveRequest: false,
}

const COURSE_RUN_SUMMARY_UPLOAD : ApiEndPointI = {

	method: 'POST',
	url: process.env.API_URL+'course-run/upload-summary',
	sectionName: 'course_run_action',
	mode: 'cors',
	file:true,
	auth: true,	
	saveRequest: false,
}

const COURSE_RUN_SUMMARY_LIST : ApiEndPointI = {

	method: 'GET',
	url: process.env.API_URL+'course-run/post-summary-list',
	sectionName: 'course_run_summary',
	mode: 'cors',
	auth: true,	
	saveRequest: true
}

const COURSE_RUN_SUMMARY_DELETE : ApiEndPointI = {

	method: 'DELETE',
	url: process.env.API_URL+'course-run/summary',
	sectionName: 'course_run_action',
	mode: 'cors',
	auth: true,	
	saveRequest: false,
}

const COURSE_RUN_BATCH_DELETE : ApiEndPointI = {

	method: 'DELETE',
	url: process.env.API_URL+'course-run',
	sectionName: 'course_run_action',
	mode: 'cors',
	auth: true,	
	saveRequest: false,
}


const COURSE_RUN_DECONFLICT_STATUS_ACTION : ApiEndPointI = {

	method: 'PUT',
	url: process.env.API_URL+'course-run/change-de-conflict-status',
	sectionName: 'course_run_action',
	mode: 'cors',
	auth: true,	
	saveRequest: false,
}

export default {
	COURSE_RUN_LIST, 
	COURSE_RUN_STATUS_LIST,
	COURSE_RUN_ACTION, 
	COURSE_RUN_CREATE, 
	COURSE_RUN_UPDATE,
	COURSE_RUN_SUMMARY_UPLOAD,
	COURSE_RUN_SUMMARY_LIST,
	COURSE_RUN_SUMMARY_DELETE,
	COURSE_RUN_ACTIVE_LIST,
	COURSE_RUN_REPORT_LIST,
	COURSE_RUN_DETAIL,
	COURSE_RUN_BATCH_DELETE,
	COURSE_RUN_DECONFLICT_STATUS_ACTION
}