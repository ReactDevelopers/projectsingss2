import { createAction } from 'typesafe-actions';
const AUTH_REQUESTING = 'AUTH_REQUESTING';
const AUTH_SUCCESS = 'AUTH_SUCCESS';
const AUTH_ERROR = 'AUTH_ERROR';

const ME_REQUESTING = 'ME_REQUESTING';
const ME_SUCCESS = 'ME_SUCCESS';
const ME_ERROR = 'ME_ERROR';

const LOGOUT_REQUESTING = 'LOGOUT_REQUESTING';
const LOGOUT_SUCCESS = 'LOGOUT_SUCCESS';
const LOGOUT_ERROR = 'LOGOUT_ERROR';

//const STORE_ACCESS_TOKEN = 'STORE_ACCESS_TOKEN';

import {ServerResponse} from '../features/root-props';
import {ApiEndPointI} from '../aep';

export const  authReq = createAction(AUTH_REQUESTING, (END_POINT: ApiEndPointI) => {

    return  {
        type: AUTH_REQUESTING,
        payload: {
            data: null,
            END_POINT: END_POINT
        }
     }
})

export const  authSuccess = createAction(AUTH_SUCCESS, (END_POINT: ApiEndPointI, data: ServerResponse) => {

    return  {
        type: AUTH_SUCCESS,
        payload: {
            data: data,
            END_POINT: END_POINT
        }
     }
})

export const  authError = createAction(AUTH_ERROR, (END_POINT: ApiEndPointI, data: ServerResponse) => {

    return  {
        type: AUTH_ERROR,
        payload: {
            data: data,
            END_POINT: END_POINT
        }
     }
})



export const  meReq = createAction(ME_REQUESTING, (END_POINT: ApiEndPointI) => {

    return  {
        type: ME_REQUESTING,
        payload: {
            data: null,
            END_POINT: END_POINT
        }
     }
})

export const  meSuccess = createAction(ME_SUCCESS, (END_POINT: ApiEndPointI, data: ServerResponse) => {

    return  {
        type: ME_SUCCESS,
        payload: {
            data: data,
            END_POINT: END_POINT
        }
     }
})

export const  meError = createAction(ME_ERROR, (END_POINT: ApiEndPointI, data: ServerResponse) => {

    return  {
        type: ME_ERROR,
        payload: {
            data: data,
            END_POINT: END_POINT
        }
     }
})


export const  logoutReq = createAction(LOGOUT_REQUESTING, (END_POINT: ApiEndPointI) => {

    return  {
        type: LOGOUT_REQUESTING,
        payload: {
            data: null,
            END_POINT: END_POINT
        }
     }
})

export const  logoutSuccess = createAction(LOGOUT_SUCCESS, (END_POINT: ApiEndPointI, data?: ServerResponse) => {

    return  {
        type: LOGOUT_SUCCESS,
        payload: {
            data: data,
            END_POINT: END_POINT
        }
     }
})

export const  logoutError = createAction(LOGOUT_ERROR, (END_POINT: ApiEndPointI, data: ServerResponse) => {

    return  {
        type: LOGOUT_ERROR,
        payload: {
            data: data,
            END_POINT: END_POINT
        }
     }
})

