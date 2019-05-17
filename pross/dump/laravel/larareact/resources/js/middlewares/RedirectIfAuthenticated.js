import history from '../history/history';
import { push } from 'react-router-redux'
import { authCheck } from '../modules/auth/store/actions'
import HTTP from '../utils/Http';

const RedirectIfAuthenticated = store => next => action => {
    let isAuthenticated = !!localStorage.getItem('access_token');
    // if (isAuthenticated) {
    //     history.push('/laravel/larareact/public/');
    //     //store.dispatch(push("/laravel/larareact/public/"));
    // }
    return next(action);
}

export default RedirectIfAuthenticated