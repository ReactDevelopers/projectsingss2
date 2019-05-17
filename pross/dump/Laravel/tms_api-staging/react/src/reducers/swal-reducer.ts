
import {SweetAlertProps} from 'react-bootstrap-sweetalert';
import { getType, getReturnOfExpression } from 'typesafe-actions';
import * as  swalAction from '../actions/swal-action';
const returnsOfActions = Object.values(swalAction).map(getReturnOfExpression);
export type Action = typeof returnsOfActions[number];

export const InitialProps: SweetAlertProps = {
    show: false,
    displayType: 1,
    message: '',
    title: ''
}

export const reducer = (state: SweetAlertProps, action: Action) => {

    switch(action.type) {

        case getType(swalAction.show) :
        case getType(swalAction.success):
        case getType(swalAction.wait):
        case getType(swalAction.close):
        case getType(swalAction.confirm):
        case getType(swalAction.error):
            
            return {...action.payload}
        default: 
            return state ? state : InitialProps
    }
}