import React from 'react';
import {connect} from 'react-redux';

// import ExportDataModal from './ExportDataModal.jsx';
import * as actions from './modalActions.js';
import SignInModal from '../authComponents/SignInModal';
import SignOutModal from '../authComponents/SignOutModal';


const ModalConductor = props => {
	console.log('props.currentModal');
	console.log(props.currentModal);
  switch (props.currentModal) {
    case 'SIGN_IN':
    	return <SignInModal {...props}/>;
	case 'SIGN_OUT':
		return <SignOutModal {...props}/>;

	default:
      return null;
  }
};

function mapStateToProps(state) {
	return {currentModal: state.modal.currentModal}
}

export default connect(mapStateToProps, actions)(ModalConductor);
