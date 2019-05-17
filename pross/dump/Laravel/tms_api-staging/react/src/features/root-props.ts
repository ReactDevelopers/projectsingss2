import { History } from 'history';
import { Dispatch} from 'react-redux';
import {actions, RootAction} from './root-action';
import {RootState} from './root-reducer';
import {ApiEndPointI} from '../aep';
import { callApi } from '../actions/fetch-action';
import {FormErrors, FormStateMap } from 'redux-form';
import { Location as locationState} from 'history';
import { SortOrder} from 'react-bootstrap-table';
import {ReactElement} from 'react';
import {SweetAlertProps} from 'react-bootstrap-sweetalert';
import {HelperProps} from '../plugins/Helper';
/**
 * Component Root Properties
 */
export interface Props {

    rootState: RootState;
    history: History;
    dispatch: Dispatch<RootAction>;
    callApi: (END_POINT: ApiEndPointI, data?: object, forceUpdate?: boolean ) => Promise<any>;
    params: () => Array<string>;
    breadcrumb?: Array< { url: string, title: string }>;
    helper: HelperProps;
    swal: {
        show :( message: ReactElement<any> | string, callBack?: Function, options?: SweetAlertProps)=> void;
        success :( message: ReactElement<any> | string, callBack?: Function, options?: SweetAlertProps) => void;
        confirm :( message: ReactElement<any> | string, callBack?: Function, options?: SweetAlertProps) => void;
        wait :( message: ReactElement<any> | string, callBack?: Function, options?: SweetAlertProps) => void;
        error :( message: ReactElement<any> | string, callBack?: Function, options?: SweetAlertProps) => void;
        close :() => void;
      }
}
/**
 * Inject the state into to Props
 * @param state RootState
 * @param props Root Props
 */
export function mapStateToProps(state: RootState, props: Props): Props {
    
    return {
      ...props,
      rootState: state,
      params: () => { return params(state.router.location) }
    }
}
/**
 * Inject the action into props
 * @param dispatch 
 */
export function mapDispatchToProps(dispatch: Dispatch<RootAction>) { 
    return {
      dispatch,
      callApi: (END_POINT: ApiEndPointI, data: object, forceUpdate?: boolean ) =>  dispatch(actions.fetch.callApi(END_POINT, data, forceUpdate)),
      swal: {
        show :( message: ReactElement<any> | string, callBack?: Function, options?: SweetAlertProps) => dispatch(actions.swal.show(message, callBack, options)),
        success :( message: ReactElement<any> | string, callBack?: Function, options?: SweetAlertProps) => dispatch(actions.swal.success(message, callBack, options)),
        confirm :( message: ReactElement<any> | string, callBack?: Function, options?: SweetAlertProps) => dispatch(actions.swal.confirm(message, callBack, options)),
        wait :( message: ReactElement<any> | string, callBack?: Function, options?: SweetAlertProps) => dispatch(actions.swal.wait(message, callBack, options)),
        error :( message: ReactElement<any> | string, callBack?: Function, options?: SweetAlertProps) => dispatch(actions.swal.error(message, callBack, options)),
        close :() => dispatch(actions.swal.close())       
      }
    }
}
/**
 * Server Response Type
 */
export interface ServerResponse<data= {[key: string]: any}, formError=FormErrors> {

    data: data;
    error_code?: string | number;
    errors: formError;
    message: string | null;
    status: boolean;
}
/**
 * Split the Url from the slash
 * @param location 
 */
export const params = (location: locationState | null): Array<string> => {

    const pathName = location ? location.pathname : '';
    return pathName.split('/');
}

/**
 * Server Response When gets the List 
 */
export interface ServerResponseListData<DataModel= {[key: string]: any}, FilterDataType=string> {
    total: number;
    per_page: number;
    current_page : number;
    last_page : number;
    first_page_url : string | null;
    last_page_url: string | null;
    next_page_url: string | null;
    prev_page_url: string | null;
    path: string;
    from: number;
    to: number;
    data: Array<DataModel>;    
}

export interface ListRequest<FilterDataType = string> {
    page: number;
    sizePerPage: number;
    searchdata: string | undefined;
    sortName: string | undefined;
    sortOrder: SortOrder | undefined;
    selected: Array<number>;
    customFilters: { [key: string] : FilterDataType } | null
}