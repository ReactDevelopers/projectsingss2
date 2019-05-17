const SWAL_DISPLAY = 'SWAL_DISPLAY';
const SWAL_DISPLAY_CONFIRM = 'SWAL_DISPLAY_CONFIRM';
const SWAL_DISPLAY_SUCCESS = 'SWAL_DISPLAY_SUCCESS';
const SWAL_DISPLAY_ERROR = 'SWAL_DISPLAY_ERROR';
const SWAL_DISPLAY_WAIT = 'SWAL_DISPLAY_WAIT';
const SWAL_HIDE = 'SWAL_HIDE';
import {  ReactElement } from 'react';
import {SweetAlertProps} from 'react-bootstrap-sweetalert';
import {InitialProps} from '../reducers/swal-reducer';
import { createAction } from 'typesafe-actions';

/**
 * Display the Message in Popup
 */
export const   show  = createAction(SWAL_DISPLAY, (message: ReactElement<any> | string, callBack?: Function, options?: SweetAlertProps) => {

    options = options ? options : InitialProps;
    options.show = true;
    options.displayType = 1;
    options.onSuccess = callBack;
    options.message = message;  

    return {
        type: SWAL_DISPLAY,
        payload: options,
    }
})

/**
 * Close the Poup
 */
export const   close  =createAction(SWAL_HIDE, () => {
        
    return {
        type: SWAL_HIDE,
        payload: {...InitialProps , show: false}
    }
})

/**
 * Display the confirmation popup
 */
export const confirm = createAction(SWAL_DISPLAY_CONFIRM, (message: ReactElement<any> | string, callBack?: Function, options?: SweetAlertProps) => {

    options = options? options : InitialProps;
    options.show = true; 
    options.displayType = 2;
    options.onConfirm = callBack;
    options.message = message;
    return {
        type: SWAL_DISPLAY_CONFIRM,
        payload: options,
    }
})

/**
 * Display the Success Message
 */
export const success = createAction(SWAL_DISPLAY_SUCCESS, (message: ReactElement<any> | string, callBack?: Function, options?: SweetAlertProps) => {

    options = options? options : InitialProps;
    options.show = true; 
    options.displayType = 4;
    options.onSuccess = callBack;
    options.message = message;
    console.log('333333333333333333333333333');
    console.log(options);
    
    return {
        type: SWAL_DISPLAY_SUCCESS,
        payload: options
    }
})

/**
 * Display Error Message 
 */

export const  error = createAction(SWAL_DISPLAY_ERROR, (message: ReactElement<any> | string, callBack?: Function, options?: SweetAlertProps) => {

    options = options? options : InitialProps;
    options.show = true; 
    options.displayType = 5;
    options.onError = callBack;
    options.message = message;

    return {
        type: SWAL_DISPLAY_ERROR,
        payload: options
    }
})

/**
 * Display Waiting Popup
 */
export const wait = createAction(SWAL_DISPLAY_ERROR, (message: ReactElement<any> | string, callBack?: Function, options?: SweetAlertProps) => {
    return {
        type: SWAL_DISPLAY_ERROR,
        payload: {
            displayType: 3, 
            show: true, 
            message: message, 
            title: ''
        }
    }
})