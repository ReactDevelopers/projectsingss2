// RootActions
console.log('Jell..........');
import { RouterAction, LocationChangeAction } from 'react-router-redux';
import { getReturnOfExpression } from 'utility-types';

import ReduxFormAction  from 'redux-form/lib/actions';
import * as fetchAction from '../actions/fetch-action';
import * as authAction from '../actions/auth-action';
import * as loadBarAction from '../actions/loader-bar-action';
import * as swalAction from '../actions/swal-action';
import formAction from 'redux-form/lib/actions';

export const actions = {
  fetch: fetchAction,
  auth: authAction,
  loadBar: loadBarAction,
  swal: swalAction,
  formAction: formAction,
};


const returnsOfActions = [
  ...Object.values(fetchAction),
  ...Object.values(authAction),
  ...Object.values(loadBarAction),
  ...Object.values(swalAction),
  ...Object.values(formAction)
].map(getReturnOfExpression);

export type AppAction = typeof returnsOfActions[number];
type ReactRouterAction = RouterAction | LocationChangeAction;

export type RootAction =
  | AppAction
  | ReactRouterAction;