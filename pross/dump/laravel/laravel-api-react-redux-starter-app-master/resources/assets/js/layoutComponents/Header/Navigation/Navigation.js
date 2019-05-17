import React, { Component } from 'react';
import { NavLink } from 'react-router-dom';
import { connect } from 'react-redux';
import { logout } from '../../../authComponents/authActions';
import { setModal } from '../../../sharedComponents/modalActions';
import CSSModules from 'react-css-modules';
import PropTypes from 'prop-types';
import styles from './Navigation.scss';

class Navigation extends Component {

	logout(e) {
		e.preventDefault();
		this.props.setModal('SIGN_OUT');
	}

	openLogin(e) {
		e.preventDefault();
		this.props.setModal('SIGN_IN');
	}

	openSignUp(e) {
		e.preventDefault();
		this.props.setModal('SIGN_UP');
	}

	render() {
		const { isAuthenticated } = this.props.auth;
		const userLinks = (
			<nav styleName="navLinks">
				<li><NavLink to="/" activeClassName="active">Home</NavLink></li>
				<li><NavLink to="/Page2" activeClassName="active">Page2</NavLink></li>
				<li><NavLink to="/Account" activeClassName="active">Account</NavLink></li>
				<li><a href="#" onClick={this.logout.bind(this)}>Log Out</a></li>
			</nav>
		);
		const guestLinks = (
			<nav styleName="navLinks">
				<li><NavLink to="/" activeClassName="active">Home</NavLink></li>
				<li><NavLink to="/Page2" activeClassName="active">Page2</NavLink></li>
				<li><a href="#" onClick={this.openLogin.bind(this)}>Log In</a></li>
				<li><a href="#" onClick={this.openSignUp.bind(this)}>Sign Up</a></li>
			</nav>
		);
		return (isAuthenticated) ? userLinks : guestLinks;
	}
}

Navigation.PropTypes = {
	auth: PropTypes.object.isRequired,
	logout: PropTypes.func.isRequired,
	openLogin: PropTypes.func
}

function mapStateToProps(state) {
	return {auth: state.auth}
}

export default connect(mapStateToProps, { logout, setModal} )(CSSModules(Navigation, styles));
