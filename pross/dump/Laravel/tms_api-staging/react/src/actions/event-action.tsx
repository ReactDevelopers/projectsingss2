import { createAction } from 'typesafe-actions';
const EVENT_REQUEST = 'EVENT_REQUEST';
const EVENT_SUCCESS = 'EVENT_SUCCESS';
const EVENT_ERROR = 'EVENT_ERROR';

const EVENT_ACTION_REQUEST = 'EVENT_ACTION_REQUEST';
const EVENT_ACTION_SUCCESS = 'EVENT_ACTION_SUCCESS';
const EVENT_ACTION_ERROR = 'EVENT_ACTION_ERROR';

const EVENT_DETAIL_REQUEST = 'EVENT_DETAIL_REQUEST';
const EVENT_DETAIL_SUCCESS = 'EVENT_DETAIL_SUCCESS';
const EVENT_DETAIL_ERROR = 'EVENT_DETAIL_ERROR';

import {ServerResponse} from '../features/root-props';
import {ApiEndPointI} from '../aep';

export const  eventReq = createAction(EVENT_REQUEST, (END_POINT: ApiEndPointI) => {

    return  {
        type: EVENT_REQUEST,
        payload: {
            data: null,
            END_POINT: END_POINT
        }
     }
})

export const  eventSuccess = createAction(EVENT_SUCCESS, (END_POINT: ApiEndPointI, data: ServerResponse) => {

    return  {
        type: EVENT_SUCCESS,
        payload: {
            data: data,
            END_POINT: END_POINT
        }
     }
})

export const  eventError = createAction(EVENT_ERROR, (END_POINT: ApiEndPointI, data: ServerResponse) => {

    return  {
        type: EVENT_ERROR,
        payload: {
            data: data,
            END_POINT: END_POINT
        }
     }
})

export const  eventActionReq = createAction(EVENT_ACTION_REQUEST, (END_POINT: ApiEndPointI) => {

    return  {
        type: EVENT_ACTION_REQUEST,
        payload: {
            data: null,
            END_POINT: END_POINT
        }
     }
})

export const  eventActionSuccess = createAction(EVENT_ACTION_SUCCESS, (END_POINT: ApiEndPointI, data: ServerResponse) => {

    return  {
        type: EVENT_ACTION_SUCCESS,
        payload: {
            data: data,
            END_POINT: END_POINT
        }
     }
})

export const  eventActionError = createAction(EVENT_ACTION_ERROR, (END_POINT: ApiEndPointI, data: ServerResponse) => {

    return  {
        type: EVENT_ACTION_ERROR,
        payload: {
            data: data,
            END_POINT: END_POINT
        }
     }
})


export const  eventDetailReq = createAction(EVENT_DETAIL_REQUEST, (END_POINT: ApiEndPointI) => {

    return  {
        type: EVENT_DETAIL_REQUEST,
        payload: {
            data: null,
            END_POINT: END_POINT
        }
     }
})

export const  eventDetailSuccess = createAction(EVENT_DETAIL_SUCCESS, (END_POINT: ApiEndPointI, data: ServerResponse) => {

    return  {
        type: EVENT_DETAIL_SUCCESS,
        payload: {
            data: data,
            END_POINT: END_POINT
        }
     }
})

export const  eventDetailError = createAction(EVENT_DETAIL_ERROR, (END_POINT: ApiEndPointI, data: ServerResponse) => {

    return  {
        type: EVENT_DETAIL_ERROR,
        payload: {
            data: data,
            END_POINT: END_POINT
        }
     }
})