
import React from 'react';
import {connect } from 'react-redux';
import PropTypes from 'prop-types';
import CSSModules from 'react-css-modules'
import styles from './styles/ModalWrapper.scss';

import CSSTransitionGroup from 'react-transition-group/CSSTransitionGroup'

import { setModal } from './modalActions';

const ModalWrapper = props => {
  const handleBackgroundClick = e => {
    if (e.target === e.currentTarget) closeModal();
  };

  const onOk = () => {
    props.onOk();
    closeModal();
  };

  const okButton = props.showOk
    ? (
      <button styleName="ok-btn"
        onClick={onOk}
        disabled={props.okDisabled}
      >
        {props.okText}
      </button>
    ) : null;

	const closeModal = () => {
		props.setModal();
	}

	const signUpModal = (
		<div styleName="signUpModal">
			<div styleName="closeButton" onClick={closeModal}><span /></div>
				{props.children}
			</div>
	)

	const normalModal = (
		<CSSTransitionGroup
			transitionName={{
			enter: styles.enter,
			enterActive: styles.enterActive,
			leave: styles.leave,
			leaveActive: styles.leaveActive,
			appear: styles.appear,
			appearActive: styles.appearActive,
		}}
			transitionAppear={true}
			transitionAppearTimeout={1000}
			transitionEnterTimeout={1000}
			transitionLeaveTimeout={1000}>
			<div styleName="modalWrapper" onClick={handleBackgroundClick}>
				<div styleName="modalBox">
					<header styleName="title">
						<h1>{props.title}</h1>
						<div styleName="closeButton" onClick={closeModal}><span /></div>
					</header>
					{props.children}
					{okButton}
				</div>
			</div>
		</CSSTransitionGroup>
	)
  return (props.signUp) ? signUpModal : normalModal;
};

ModalWrapper.propTypes = {
  // props
  title: PropTypes.string,
  showOk: PropTypes.bool,
  okText: PropTypes.string,
  okDisabled: PropTypes.bool,
  width: PropTypes.number,
  style: PropTypes.object,
	signIp: PropTypes.bool,

  // methods
  closeModal: PropTypes.func,
  onOk: PropTypes.func,
};

ModalWrapper.defaultProps = {
  title: '',
  showOk: true,
  okText: 'OK',
  okDisabled: false,
  width: 400,
  onOk: () => {},
	signUp: false
};

export default connect(null, { setModal})(CSSModules(ModalWrapper, styles, {'allowMultiple' : true}));
