import {ApiEndPointI} from './index';

const PLACEMENT_LIST : ApiEndPointI = {

	method: 'GET',
	url: process.env.API_URL+'placement',
	sectionName: 'placement',
	mode: 'cors',	
	auth: true,
	saveRequest: true
}
const MY_PLACEMENT_LIST : ApiEndPointI = {

	method: 'GET',
	url: process.env.API_URL+'viewer/placement',
	sectionName: 'my_placement',
	mode: 'cors',	
	auth: true,
	saveRequest: true
}
const PLACEMENT_LIST_OF_A_COURSE_RUN : ApiEndPointI = {

	method: 'GET',
	url: process.env.API_URL+'placement/maintain-list',
	sectionName: 'placement_list_of_course_run',
	mode: 'cors',	
	auth: true,
	saveRequest: true
}
const MAINTAIN_COURSE_RUN_LIST : ApiEndPointI = {

	method: 'GET',
	//url: process.env.API_URL+'placement/maintain-list',
	url: process.env.API_URL+'course-run',
	sectionName: 'maintain_course_run',
	mode: 'cors',	
	auth: true,
	saveRequest: true
}
const MY_SUBORDINATE_PLACEMENT_LIST : ApiEndPointI = {

	method: 'GET',
	url: process.env.API_URL+'viewer/subordinate-placement',
	sectionName: 'subordinate_placement',
	mode: 'cors',	
	auth: true,
	saveRequest: true
}

const POST_COURSE_RUN_LIST : ApiEndPointI = {

	method: 'GET',
	url: process.env.API_URL+'placement/post-course-list',
	sectionName: 'post_course_run_list',
	mode: 'cors',	
	auth: true,
	saveRequest: true
}

const PLACEMENT_REPORT_LIST : ApiEndPointI = {

	method: 'GET',
	url: process.env.API_URL+'placement/report-list',
	sectionName: 'placement_report_list',
	mode: 'cors',	
	auth: true,
	saveRequest: true
}

const PLACEMENT_UPLOAD : ApiEndPointI = {

	method: 'POST',
	url: process.env.API_URL+'placement/upload',
	sectionName: 'placement_action',
	mode: 'cors',
	auth: true,	
	file: true,
}
const PLACEMENT_RESULT_UPLOAD : ApiEndPointI = {

	method: 'POST',
	url: process.env.API_URL+'placement/upload-result',
	sectionName: 'placement_action',
	mode: 'cors',
	auth: true,	
	file: true,
}
const PLACEMENT_ACTION : ApiEndPointI = {

	method: 'POST',
	url: process.env.API_URL+'placement',
	sectionName: 'placement_action',
	mode: 'cors',
	shouldMergeRequest: false,
	saveRequest: false,
	auth: true
}

const PLACEMENT_BATCH_DELETE : ApiEndPointI = {

	method: 'DELETE',
	url: process.env.API_URL+'placement',
	sectionName: 'placement_action',
	mode: 'cors',
	shouldMergeRequest: false,
	saveRequest: false,
	auth: true
}
const PLACEMENT_RESULT_BATCH_DELETE : ApiEndPointI = {

	method: 'DELETE',
	url: process.env.API_URL+'placement/result',
	sectionName: 'placement_action',
	mode: 'cors',
	shouldMergeRequest: false,
	saveRequest: false,
	auth: true
}

const PLACEMENT_EMAIL : ApiEndPointI = {

	method: 'POST',
	url: process.env.API_URL+'get-email-template',
	sectionName: 'placement_action',
	mode: 'cors',
	shouldMergeRequest: false,
	saveRequest: false,
	auth: true
}

const PLACEMENT_CONFLICT : ApiEndPointI = {

	method: 'POST',
	url: process.env.API_URL+'placement/check-conflict',
	sectionName: 'placement_action',
	mode: 'cors',
	shouldMergeRequest: false,
	saveRequest: false,
	auth: true
}

const PLACEMENT_CH_STUS_SEND_EMAIL : ApiEndPointI = {

	method: 'POST',
	url: process.env.API_URL+'send-email-and-change-status',
	sectionName: 'placement_action',
	mode: 'cors',
	shouldMergeRequest: false,
	saveRequest: false,
	auth: true,
	file: true
}

export default {PLACEMENT_LIST,
	PLACEMENT_BATCH_DELETE,
	PLACEMENT_REPORT_LIST, 
	PLACEMENT_RESULT_BATCH_DELETE, 
	POST_COURSE_RUN_LIST, 
	PLACEMENT_UPLOAD, 
	PLACEMENT_ACTION, 
	PLACEMENT_RESULT_UPLOAD, 
	PLACEMENT_EMAIL, 
	PLACEMENT_CH_STUS_SEND_EMAIL,
	MY_PLACEMENT_LIST,
	MY_SUBORDINATE_PLACEMENT_LIST,
	PLACEMENT_CONFLICT,
	PLACEMENT_LIST_OF_A_COURSE_RUN,
	MAINTAIN_COURSE_RUN_LIST
}