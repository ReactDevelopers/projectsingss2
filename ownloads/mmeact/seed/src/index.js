import React from 'react';
import ReactDOM from 'react-dom';
import './index.css';
import App from './App';
//import * as serviceWorker from './serviceWorker';

//import , { browserHistory  } from './store';
import { createBrowserHistory } from 'history';
export const browserHistory = createBrowserHistory({
    basename: process.env.DOMAIN_PATH
});

const render = Component =>
  ReactDOM.render(

      <Component history={browserHistory} />,
      
    document.getElementById('root')
);

render(App);

// Webpack Hot Module Replacement API
//if (module.hot) module.hot.accept('./app', () => render(App));