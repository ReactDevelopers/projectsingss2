import React from 'react';
import {BrowserRouter as Router,
		Route,
		Link } from 'react-router-dom';
import createBrowserHistory from 'history/createBrowserHistory'

//public pages
import Layout from './layoutComponents/Layout';
import Home from './pageComponents/Home';
import Page2 from './pageComponents/Page2';
import Account from './pageComponents/Account';


//auth pages
import requireAuth from './authComponents/Common/requireAuth';

// misc
import NoMatch from './components/NoMatch';

const routerHistory  = createBrowserHistory()

const Routes = () => {
	return (
		<Router history={routerHistory}>
			<Layout>
				<Route exact path="/" component={Home}/>
				<Route path="/Page2" component={Page2}/>
				
				<Route path="/Account" component={requireAuth(Account)} />
			</Layout>
		</Router>
	)
}

export default Routes
