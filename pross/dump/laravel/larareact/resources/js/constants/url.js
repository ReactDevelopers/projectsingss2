export const LOGIN_URL = {
	url:'/laravel/larareact/public/login',
	auth:false
};
export const REGISTER_URL = {
	url:'/laravel/larareact/public/register',
	auth:false
};
export const HOME_URL = {
	url:'/laravel/larareact/public/',
	auth:false
}; 
export const ARTICLE_LIST_URL = {
	url:'/laravel/larareact/public/articles',
	auth:true
}; 
export const ARTICLE_CREATE_URL = {
	url:'/laravel/larareact/public/articles/create',
	auth:true
}; 

export default {
	LOGIN_URL,
	REGISTER_URL,
	HOME_URL,
	ARTICLE_LIST_URL,
	ARTICLE_CREATE_URL
}