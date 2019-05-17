import { DOMAIN_PATH } from '../constant/config'

export default function appUrl(url) {

	if( url.startsWith('/') ){
        url = url.substr(1);
    }

	else if( url.endsWith('/') ){
		url = url.slice(0,-1);
    }

    let baseUrl = DOMAIN_PATH;
    return baseUrl + url;

}