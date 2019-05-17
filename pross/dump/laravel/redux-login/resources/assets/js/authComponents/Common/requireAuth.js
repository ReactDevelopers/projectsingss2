import React, { Component } from 'react';
import { connect } from 'react-redux';
import PropTypes from 'prop-types';

import { setModal } from '../../sharedComponents/modalActions';



export default function(ComposedComponent) {
	class Authenticate extends Component {
		componentWillMount() {
			if (!this.props.isAuthenticated) {
				this.context.router.history.push('/');
				this.openLogin();
			}
		}

		componentWillUpdate(nextProps) {
			if (!nextProps.isAuthenticated) {
				this.context.router.history.push('/');
				this.openLogin();				
			}
		}

		openLogin(redirect = null) {
			this.props.setModal('SIGN_IN');
		}

		render() {
			return (
				<div>
				{ this.props.isAuthenticated === true
					? <ComposedComponent {...this.props} />
					: null
				}
				</div>
			);
		}
	}

	Authenticate.propTypes = {
		isAuthenticated: PropTypes.bool.isRequired
	}

	Authenticate.contextTypes = {
		router: PropTypes.object.isRequired
	}

	function mapStateToProps(state) {
		return {
			isAuthenticated: state.auth.isAuthenticated
		}
	}
	return connect(mapStateToProps, { setModal })(Authenticate);
}
