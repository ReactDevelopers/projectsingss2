import { getType, getReturnOfExpression } from 'typesafe-actions';
import * as  loadbarAction from '../actions/loader-bar-action';
const returnsOfActions = Object.values(loadbarAction).map(getReturnOfExpression);
export type Action = typeof returnsOfActions[number];

export interface loadBarProps  {
    show: boolean;
}
export const reducer = (state: loadBarProps, action: Action) => {

    switch(action.type) {

        case getType(loadbarAction.show) :
        case getType(loadbarAction.hide) :
            return {show: action.payload.show}
        default: 
            
            return state ? state : {show: false}
    }
}