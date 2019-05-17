import React, { Component } from 'react';
import ReactDOM from 'react-dom';
import { connect } from 'react-redux';
import { logout } from './authActions';
import { setModal } from '../sharedComponents/modalActions';
import PropTypes from 'prop-types';
import CSSModules from 'react-css-modules';


import ModalWrapper from '../sharedComponents/ModalWrapper';

import styles from './styles/formStyles';

const SignOutModal = (props, context) => {
	
	const signOut = () => {
		props.logout();
		context.router.history.push('/');
	}

    return (
		<ModalWrapper
			{...props}
			title={"LogOut"}
			width={400}
			showOk={true}
			onOk={signOut}
		>
			<div styleName="modalForm">
				<h2>Are you sure you wish to log out?</h2>
			</div>
		</ModalWrapper>
	);
}

SignOutModal.contextTypes = {
	router: PropTypes.object.isRequired
}

export default connect(null, { logout, setModal })(CSSModules(SignOutModal, styles));
