import React from 'react'
import PropTypes from 'prop-types'
import { Route, Redirect, withRouter } from 'react-router-dom'
import { connect } from 'react-redux'
import { LOGIN_URL, REGISTER_URL, HOME_URL } from '../constants/url'
import { authCheck, authLogout } from '../modules/auth/store/actions'
// import Main from '../Main'

const PrivateRoute = ({ component: Component, isAuthenticated, logInFirst, ...rest }) => {
	if (!isAuthenticated) {
		//logInFirst();
	}
	return <Route {...rest} render={props => (
		isAuthenticated
			? <Component {...props} />
			: <Redirect to={{
				pathname: LOGIN_URL.url,
				state: { from: props.location },
			}} />
	)} />

}

PrivateRoute.propTypes = {
	component: PropTypes.func.isRequired,
	location: PropTypes.object,
	isAuthenticated: PropTypes.bool.isRequired,
}

// Retrieve data from store as props
function mapStateToProps(store) {
	return {
		//isAuthenticated: store.auth.isAuthenticated,
		isAuthenticated: !!localStorage.getItem('access_token'),
	}
}

const mapDispatchToProps = (dispatch) => {
	return {
		logInFirst: () => {
			dispatch(authLogout())
		}
	}
}
export default withRouter(connect(mapStateToProps, mapDispatchToProps)(PrivateRoute))
