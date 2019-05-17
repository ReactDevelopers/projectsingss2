import { getType, getReturnOfExpression } from 'typesafe-actions';
import * as  fetchAction from '../actions/fetch-action';
import {ServerResponse, ServerResponseListData, ListRequest} from '../features/root-props';


const returnsOfActions = Object.values(fetchAction).map(getReturnOfExpression);
export type Action = typeof returnsOfActions[number];

export interface ServerDataI<Response = any,> {
    
    [key: string]: FetchDataI<ServerResponse<Response>>;
}

export interface FetchDataI<Response = any, FilterDataType =string> {

    isFetching: boolean;
    response: Response;
    lastUpdated: Number;
    shouldUpdate: Boolean;    
    requestData: ListRequest<FilterDataType>
}
export const InitialListRequest: ListRequest = {

    page: 0,
    sizePerPage: 25,
    searchdata: undefined,
    customFilters: null,
    sortName: undefined,
    selected: [],
    sortOrder: undefined,
}

export const  InitialResponse  ={

    isFetching: false,
    response: null,
    lastUpdated: Date.now(),
    shouldUpdate: false,
    requestData: InitialListRequest
}

/**
 * Fetch Reducer
 */

export const reducer = (state: ServerDataI, action: Action) => {        

    const key = action.payload && action.payload.END_POINT ? action.payload.END_POINT.sectionName : '';
    switch (action.type) {
        case getType(fetchAction.requestingToApi):
        case getType(fetchAction.receivedApiResponse):
        case getType(fetchAction.receivedApiException):
        case getType(fetchAction.deleteResponse):
        case getType(fetchAction.makeAsChange):
           
            return Object.assign({}, state, {
                [key] : serverData(state[key] , action)
            })
        case getType(fetchAction.storeSelectRows):
            // return Object.assign({}, state, {
            //     [key] : serverData(state[key] , action)
            // })
            state[key].requestData.selected = action.payload.data;
            return state;
            
        case getType(fetchAction.removeSelectedRow):
            console.log('dssdddddddddddddddd');
            console.log(key);
            const selectedRows = state[key].requestData.selected;
            action.payload.data && action.payload.data.map(function(id){
                const index = selectedRows.indexOf(id);
                selectedRows.splice(index,1);
            });

            state[key].requestData.selected = selectedRows;

            return state;

      default:
        return state ? state :{};
    }
}

/**
 * Saving the Request data into the list.
 * @param state 
 * @param action 
 */
function saveListRequest(state: FetchDataI = InitialResponse, data: any) {
   
   return {...state.requestData, ...(data ? data  : InitialListRequest) } ;    
}

/**
 * Store Server data in the individual data key
 */

function serverData (state: FetchDataI = InitialResponse, action: Action) : FetchDataI<any> {

    switch (action.type) {
       case getType(fetchAction.requestingToApi): 
            
            var shouldResponseStore = action.payload.END_POINT.shouldResponseStore;
            shouldResponseStore = shouldResponseStore !== undefined ? shouldResponseStore : true;
            
            if(shouldResponseStore) {
                return Object.assign({}, state, {
                    isFetching: true,
                    shouldUpdate: !state.shouldUpdate,
                    requestData: action.payload.END_POINT.saveRequest ? saveListRequest(state, action.payload.data ) :{}
                });
            }
            else {

                 return state;   
            }

        case getType(fetchAction.receivedApiResponse): 
            var shouldResponseStore = action.payload.END_POINT.shouldResponseStore;
            shouldResponseStore = shouldResponseStore !== undefined ? shouldResponseStore : true;
            
            if(shouldResponseStore) {

                return Object.assign({}, state, {
                    isFetching: false,
                    shouldUpdate: !state.shouldUpdate,
                    response: action.payload.data
                });
            }
            else {

                return state;
            }
        case getType(fetchAction.receivedApiException): 
            return Object.assign({}, state, {
                isFetching: false,
                shouldUpdate: !state.shouldUpdate
            });
        case getType(fetchAction.deleteResponse):  
            return Object.assign({}, state, {
                isFetching: false,
                shouldUpdate: !state.shouldUpdate,
                response : null      
            })
        case getType(fetchAction.makeAsChange): 
            return Object.assign({}, state, {
                isFetching: false,
                lastUpdated: Date.now(),
                shouldUpdate: !state.shouldUpdate,
            })
       default: 
            return state;
    }
}
  