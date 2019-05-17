import React, { Component } from 'react';
import ReactDOM from 'react-dom';

import ModalWrapper from '../sharedComponents/ModalWrapper';
import SignUpForm from './SignUpForm';

const SignInModal = props => {
    return (
		<ModalWrapper
			{...props}
			title={"Sign Up"}
			width={400}
			showOk={false}
		>
			<SignUpForm />
		</ModalWrapper>
	);
}

export default SignInModal;
