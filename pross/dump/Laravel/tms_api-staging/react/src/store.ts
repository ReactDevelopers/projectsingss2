import { createStore, applyMiddleware, compose, StoreCreator, Store } from 'redux';
import { createEpicMiddleware } from 'redux-observable';
import { routerMiddleware as createRouterMiddleware, } from 'react-router-redux';
import { createBrowserHistory } from 'history';

import { rootReducer, RootState } from './features/root-reducer';
import { rootEpic } from './features/root-epic';
import thunkMiddleware from 'redux-thunk';
import logger from 'redux-logger';
import ApiMiddleware from './middlewares/ApiMiddleware';
import IndividualApi from './middlewares/IndividualApi';

const composeEnhancers = (
  process.env.NODE_ENV !== 'production' &&
  window && window.__REDUX_DEVTOOLS_EXTENSION_COMPOSE__
) || compose;

export const epicMiddleware = createEpicMiddleware(rootEpic);
export const browserHistory = createBrowserHistory({
  basename: process.env.DOMAIN_PATH
});
export const routerMiddleware = createRouterMiddleware(browserHistory);

function configureStore(initialState?: RootState): Store<RootState> {
  // configure middlewares
  const middlewares = [
    thunkMiddleware,    
    ApiMiddleware,
    IndividualApi,
    epicMiddleware,
    routerMiddleware,    
    //logger,
  ];
  if(process.env.NODE_ENV !== 'production'){
    middlewares.push(logger);
  }
  // compose enhancers
  const enhancer = composeEnhancers(
    applyMiddleware(...middlewares)
  );
  // create store
  return createStore(    
    rootReducer,
    initialState!,
    enhancer
  );
}

// pass an optional param to rehydrate state on app start
export const store = configureStore();

// export store singleton instance
export default store;
