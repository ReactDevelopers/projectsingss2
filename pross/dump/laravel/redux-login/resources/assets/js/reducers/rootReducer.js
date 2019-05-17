import { combineReducers } from 'redux';
import auth from './authReducer';
import modal from './modalReducer';
// localStorage.removeItem('jwtToken');
console.log('rootter');
const rootReducer = combineReducers({
	auth,
	modal
})

export default rootReducer;
