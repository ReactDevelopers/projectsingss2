import React, { Component } from 'react';
import ReactDOM from 'react-dom';

import ModalWrapper from '../sharedComponents/ModalWrapper';
import LoginForm from './LoginForm';

const SignInModal = props => {
    return (
		<ModalWrapper
			{...props}
			title={"Sign in"}
			width={400}
			showOk={false}
		>
			<LoginForm />
		</ModalWrapper>
	);
}

export default SignInModal;
