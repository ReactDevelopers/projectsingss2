import { createAction } from 'typesafe-actions';
import END_POINTS, { 
    ApiEndPointI, 
    REQUESTING_API, 
    RECEIVED_API_RESPONSE, 
    RECEIVED_APT_EXCEPTION,
    CALL_API
}
from '../aep';


const DELETE_RESPONSE = 'DELETE_RESPONSE';
const SELECT_ROW = 'SELECT_ROW';
const MAKE_AS_CHANGE_MANUALLY = 'MAKE_AS_CHANGE_MANUALLY';
const REMOVE_SELECTED_ROW = 'REMOVE_SELECTED_ROW';

import {ServerResponse, ListRequest} from '../features/root-props';

// console.log('endpoints');
// console.log(END_POINTS.AUTH_USER);

/**
 * This Action will dispatch everytime before call the api
 */
export const requestingToApi = createAction(REQUESTING_API, (END_POINT: ApiEndPointI, data?: any) => {

    return {
        type: REQUESTING_API,
        payload: {
            data: data,
            END_POINT: END_POINT
        }
    }
});

export const  receivedApiResponse = 

createAction(RECEIVED_API_RESPONSE, (END_POINT: ApiEndPointI, json: ServerResponse ) => {    
    
    if(typeof END_POINT.extendResponse === 'function') {

      let response = END_POINT.extendResponse(json);
      json = response ? response : json;
    }

    return {
        type: RECEIVED_API_RESPONSE,        
        payload: {
            END_POINT: END_POINT,
            data: json
        }
    }
})

export const  callApi = createAction(CALL_API, (END_POINT: ApiEndPointI, data?: object, forceUpdate?: boolean) => {

    return  {
        type: CALL_API,
        payload: {
            data: data,
            END_POINT: END_POINT,
            forceUpdate: forceUpdate !== undefined? forceUpdate : true,
        }
     }
})

export const  storeSelectRows = createAction(SELECT_ROW, (END_POINT: ApiEndPointI, selectRows: Array<number>) => {

    return  {
        type: SELECT_ROW,
        payload: {
            data: selectRows,
            END_POINT: END_POINT
        }
     }
})

export const  removeSelectedRow = createAction(REMOVE_SELECTED_ROW, (END_POINT: ApiEndPointI, selectRows: Array<number>) => {

    return  {
        type: REMOVE_SELECTED_ROW,
        payload: {
            data: selectRows,
            END_POINT: END_POINT
        }
     }
})

export const receivedApiException = createAction(RECEIVED_APT_EXCEPTION, (END_POINT: ApiEndPointI, data: ServerResponse ) => {
    return  {
        type: RECEIVED_APT_EXCEPTION,
        payload: {
            data: data,
            END_POINT: END_POINT
        }
     }
})

export const deleteResponse = createAction(DELETE_RESPONSE, (END_POINT: ApiEndPointI) => {
    return  {
        type: DELETE_RESPONSE,
        payload: {
            data: null,
            END_POINT: END_POINT
        }
     }
})

export const makeAsChange = createAction(MAKE_AS_CHANGE_MANUALLY, (END_POINT: ApiEndPointI) => {
    return  {
        type: MAKE_AS_CHANGE_MANUALLY,
        payload: {
            data: null,
            END_POINT: END_POINT
        }
     }
})