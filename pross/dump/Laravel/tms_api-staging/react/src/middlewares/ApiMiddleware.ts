import {RootAction, actions} from '../features/root-action';
import {RootState} from '../features/root-reducer';
import {ListRequest} from '../features/root-props';
import {InitialListRequest} from '../reducers/fetch-reducer';


import { fetchApi } from '../fetch';
import { getType, getReturnOfExpression } from 'typesafe-actions';
import { Store } from 'redux';

import * as  fetchAction from '../actions/fetch-action';
const returnsOfActions = Object.values(fetchAction).map(getReturnOfExpression);

import {SubmissionError} from 'redux-form';


export type Action = typeof returnsOfActions[number];

export default (store: Store<RootState>) => (next: Function) => (action: Action) => {


    /**
     * When Requesting to server
     */
    if( action.type === getType(fetchAction.callApi) ) {

        const END_POINT = action.payload.END_POINT;
        const data = action.payload.data;
        const forceUpdate = action.payload.forceUpdate;
        return fetchApi(END_POINT, data, store.dispatch, store.getState(), forceUpdate);
    }   

    /**
     * When requesting to server
     */
    if(action.type === getType(fetchAction.requestingToApi)) {
        store.dispatch(actions.loadBar.show());
        //throw new SubmissionError({...action.payload.data.errors, _error: action.payload.data.message}); 
    }

    /**
     * When get any success during the Request
     */
    if(action.type === getType(fetchAction.receivedApiResponse)) {
        store.dispatch(actions.loadBar.hide());
        //throw new SubmissionError({...action.payload.data.errors, _error: action.payload.data.message}); 
    }

    /**
     * When get any exception during the Request
     */
    if(action.type === getType(fetchAction.receivedApiException)) {
        store.dispatch(actions.loadBar.hide());
        // const errors = action.payload.data.errors ? action.payload.data.errors : {};
        // const message = action.payload.data.message ? action.payload.data.message : '';
        // throw new SubmissionError({...errors, _error: message}); 
    }

    //if(action.type === AUTH_SUCCESS) 

    return next(action);
}