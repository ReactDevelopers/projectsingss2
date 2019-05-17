import { combineReducers } from 'redux';
import { routerReducer as router, RouterState} from 'react-router-redux';
import {reducer as formReducer,  FormState, FormAction} from 'redux-form';
import { RootAction } from './root-action';
import { ServerResponse } from '../features/root-props';
import { AuthI } from '../models/auth';
import { ServerDataI, reducer as fetchReducer } from '../reducers/fetch-reducer';
import {  reducer as loaderBarReducer, loadBarProps } from '../reducers/loaderBar-reducer';
import {  reducer as swalReducer } from '../reducers/swal-reducer';
import {SweetAlertProps} from 'react-bootstrap-sweetalert';

interface cFormState extends FormState {
  error?: string;
}
interface FormStateMap  {
  
  [formname: string] : cFormState
}

interface StoreEnhancerState { }

export interface RootState extends StoreEnhancerState {
  router: RouterState;
  form: FormStateMap;
  server: ServerDataI,
  loaderBar: loadBarProps,
  swal: SweetAlertProps,
}

export const rootReducer = combineReducers<RootState, RootAction>({  
  router,
  form: formReducer,
  server: fetchReducer,
  loaderBar: loaderBarReducer,
  swal: swalReducer
});
