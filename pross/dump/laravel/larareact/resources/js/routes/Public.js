import React from 'react'
import PropTypes from 'prop-types'
import { Route, Redirect, Link, withRouter } from 'react-router-dom'
import { connect } from 'react-redux'
import { LOGIN_URL, REGISTER_URL, HOME_URL } from '../constants/url'
import { authCheck } from '../modules/auth/store/actions'
import { fetchUser } from '../modules/auth/service'

const PublicRoutes = ({ component: Component, isAuthenticated, backToHome, ...other }) => {
	let authAndLogging = (isAuthenticated && other.path == LOGIN_URL.url) || (isAuthenticated && location.pathname == REGISTER_URL.url)
	if (authAndLogging) {
		console.log('checking auth')
		//backToHome();
	}
	return <Route {...other} render={props => (
		authAndLogging
			? <Redirect to={{
				pathname: HOME_URL.url,
				state: { from: props.location },
			}} />
			: <Component {...props} />
	)} />
}

PublicRoutes.propTypes = {
	component: PropTypes.func.isRequired,
	location: PropTypes.object,
	isAuthenticated: PropTypes.bool.isRequired,
};

// Retrieve data from store as props
function mapStateToProps(store) {
	return {
		//isAuthenticated: store.auth.isAuthenticated,
		isAuthenticated: !!localStorage.getItem('access_token'),
	}
}

const mapDispatchToProps = (dispatch) => {
	return {
		backToHome: () => {
			dispatch(authCheck())
			dispatch(fetchUser())
		}
	}
}

export default withRouter(connect(mapStateToProps, mapDispatchToProps)(PublicRoutes))
