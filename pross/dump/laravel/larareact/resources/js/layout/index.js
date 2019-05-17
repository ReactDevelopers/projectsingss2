//import libs
import React, { Component } from 'react'
import PropTypes from 'prop-types'
import { connect } from 'react-redux'
import { withRouter } from 'react-router-dom'
import User from '../modules/user/User'
// import services actions
import { fetchUser } from '../modules/auth/service'

// import components
import PrivateLayout from './Private'
import PublicLayout from './Public'

class Layout extends Component {
	static displayName = 'Layout'
	static propTypes = {
		isAuthenticated: PropTypes.bool.isRequired,
		user: PropTypes.object.isRequired,
		children: PropTypes.node.isRequired,
		dispatch: PropTypes.func.isRequired,
	}
	componentWillMount() {
		console.log('mounting layout');
		this.unlisten = this.props.history.listen((location, action) => {
			console.log(`The current URL is ${location.pathname}${location.search}${location.hash}`)
			console.log(`The last navigation action was ${action}`)
		});
		const { isAuthenticated, user } = this.props
		//Cheking if looged in and store has user value
		if (isAuthenticated && !user.id) {
			this.props.dispatch(fetchUser())
		}
	}

	componentWillUnmount() {
		this.unlisten();
	}

	render() {
		const { children, ...props } = this.props
		if (this.props.isAuthenticated) {
			return <PrivateLayout {...props}>{children}</PrivateLayout>
		}
		return <PublicLayout {...props}>{children}</PublicLayout>
	}
}

const mapStateToProps = state => {
	let isAuthenticated = !!localStorage.getItem('access_token')
	return {
		//isAuthenticated: state.auth.isAuthenticated,
		isAuthenticated: isAuthenticated,
		user: isAuthenticated ? state.user : new User({}),
	}
}

export default withRouter(connect(mapStateToProps)(Layout))
