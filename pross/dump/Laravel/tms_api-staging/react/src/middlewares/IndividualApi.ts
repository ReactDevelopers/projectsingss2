import {RootAction, actions} from '../features/root-action';
import {RootState} from '../features/root-reducer';

import { fetchApi } from '../fetch';
import { getType, getReturnOfExpression } from 'typesafe-actions';
import { Store } from 'redux';
import {AuthI} from '../models/Auth';
import Cookie from 'js-cookie';
import {browserHistory} from '../store';
import * as  authAction from '../actions/auth-action';

const returnsOfActions = Object.values(authAction).map(getReturnOfExpression);

export type Action = typeof returnsOfActions[number];

export default (store: Store<RootState>) => (next: Function) => (action: Action) => {
    
    const data : object = action && action.payload && action.payload.data ?  action.payload.data : {};
   
    switch (action.type) {        
        /**
         * When User logOut Seccess.
         */
        case getType(authAction.logoutSuccess):

            Cookie.remove('access_token');
            store.dispatch(actions.fetch.deleteResponse(action.payload.END_POINT));
            browserHistory.push('/');
            return next(action);            
        case getType(authAction.logoutError):

            Cookie.remove('access_token');
            store.dispatch(actions.fetch.deleteResponse(action.payload.END_POINT));
            browserHistory.push('/');
            return next(action);            

        default: 

        return next(action);
    }
}