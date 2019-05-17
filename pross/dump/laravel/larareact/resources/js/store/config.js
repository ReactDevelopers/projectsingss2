/**
 * Main store function
 */
import { createStore, applyMiddleware, compose } from 'redux'
import thunk from 'redux-thunk'
//import { createLogger } from 'redux-logger'
import rootReducer from './reducers'
import logger from '../middlewares/logger'
import crashReporter from '../middlewares/crashReporter'
import RedirectIfAuthenticated from '../middlewares/RedirectIfAuthenticated'
import monitorReducerEnhancer from '../enhancers/monitorReducer'
// import * as allMiddleWares from '../middlewares/onePlaceForAll'
import { timeoutScheduler } from '../middlewares/onePlaceForAll'

//var browserHistory = require("history").createBrowserHistory
//import createHistory from "history/createBrowserHistory"
//import { createBrowserHistory as browserHistory } from 'history';
//import browserHistory from '../history/history'
import { createBrowserHistory } from "history";

import {
	ConnectedRouter,
	syncHistoryWithStore,
	routerMiddleware
} from 'react-router-redux'

// Build the middleware for intercepting and dispatching navigation actions
// const routeMiddleware = routerMiddleware(browserHistory);
const routeMiddleware = routerMiddleware(createBrowserHistory());

//const loggerMiddleware = createLogger();
const middlewares = [
	thunk,
	routeMiddleware,
	logger,
	crashReporter,
	timeoutScheduler,
	RedirectIfAuthenticated
];
//middlewares.push(loggerMiddleware);
//const storeConfig = (initialState = {}) => {
export default function (history, initialState = {}) {
	// Middleware and store enhancers
	const enhancers = [
		applyMiddleware(...middlewares)
	]

	if (process.env.NODE_ENV !== 'production') {
		//enhancers.push(applyMiddleware(loggerMiddleware))
		enhancers.push(monitorReducerEnhancer)
		window.devToolsExtension && enhancers.push(window.devToolsExtension())
	}

	const store = createStore(rootReducer, initialState, compose(...enhancers))

	// For hot reloading reducers
	if (module.hot) {
		// Enable Webpack hot module replacement for reducers
		module.hot.accept('./reducers', () => {
			const nextReducer = require('./reducers').default // eslint-disable-line global-require
			store.replaceReducer(nextReducer)
		})
	}
	return store
}

//export default storeConfig