// import libs
import React from 'react'
import { BrowserRouter as Router, Switch } from 'react-router-dom'
import createBrowserHistory from 'history/createBrowserHistory'
import {
	ConnectedRouter
} from 'react-router-redux'

// import components
import routes from './routes'
import PrivateRoute from './Private'
import PublicRoute from './Public'

import Layout from '../layout'

const history = createBrowserHistory()

const Routes = () => (
	<Router hisotry={history}>
		<Layout>
			<Switch>
				{routes.map((route, i) => {
					route.path = '/laravel/larareact/public' + route.path
					if (route.auth) {
						return <PrivateRoute key={i} {...route} />
					}
					return <PublicRoute key={i} {...route} />
				})}
			</Switch>
		</Layout>
	</Router>
)

export default Routes
