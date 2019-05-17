import { combineReducers } from 'redux';
import auth from './authReducer';
import modal from './modalReducer';

const rootReducer = combineReducers({
	auth,
	modal
})

export default rootReducer;
