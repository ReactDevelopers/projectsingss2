import React from 'react';
import ReactDOM from 'react-dom';
import { Provider,} from 'react-redux';
import { loginSuccess,
		 setAuthorizationToken,
	  	 refreshAuthorizationToken,
         getRefreshToken } from './authComponents/authActions';
import jwtDecode from 'jwt-decode';
import Routes from './routes';

// import axios from 'axios';
import { store } from './bootstrap'

const root = document.querySelector('#index');

require('./bootstrap');

if (localStorage.jwtToken) {
	const token = jwtDecode(localStorage.jwtToken);
	const tokenExp = token.exp < Date.now() / 1000;
	tokenExp && console.log('token expired');

	tokenExp ?
		refreshAuthorizationToken(localStorage.jwtToken, store)
		: setAuthorizationToken(localStorage.jwtToken);

	store.dispatch(loginSuccess(jwtDecode(localStorage.jwtToken)));
}

ReactDOM.render(
	<Provider store={store}>
		<Routes />
	</Provider>
	, root
);