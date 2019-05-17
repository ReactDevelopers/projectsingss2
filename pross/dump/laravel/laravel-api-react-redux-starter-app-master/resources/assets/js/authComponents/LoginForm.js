import React, {Component} from 'react';
import { connect } from 'react-redux';
import { login } from './authActions';
import { setModal } from '../sharedComponents/modalActions';
import PropTypes from 'prop-types';
import CSSModules from 'react-css-modules';

import TextFieldGroup from './Common/TextFieldGroup';

import styles from './styles/formStyles.scss';

class LoginForm extends Component {
	constructor() {
		super();
		this.state = {
			email : '',
			password : '',
			errors : '',
			isLoading : false,
			provider : null,
			user: null
		};

		this.onSubmit = this.onSubmit.bind(this);
		this.onChange = this.onChange.bind(this);
	}

	isValid() {
		return true;
	}

	signIn() {
		this.props.login(this.state).then(
			(res) => {
				this.props.setModal('');
				this.context.router.history.push('/Account')
			},
			(err) => this.setState({
				//  errors: err.response.data.errors,
				 isLoading: false
			 })
		);
	}

	onSubmit(e) {
		e.preventDefault();
		if (this.isValid()) {
			this.setState({errors: {}, isLoading: true});
			this.signIn();
		} else {
			//display errors
		}
	}

	onChange(e) {
		this.setState({ [e.target.name] : e.target.value})
	}

	transferToSignUp(e) {
		e.preventDefault();
		return false;
	}

	render() {
		const { errors, email, password, isLoading } = this.state;

		return (
			<form styleName="modalForm" onSubmit={this.onSubmit}>
				<TextFieldGroup
					field="email"
					type="email"
					placeholder="email"
					value={email}
					error={errors.email}
					onChange={this.onChange}
					/>

				<TextFieldGroup
					field="password"
					placeholder="password"
					value={password}
					error={errors.password}
					onChange={this.onChange}
					type="password"
					/>

				<div>
					<button disabled={isLoading}>Login</button>
				</div>
			</form>
		)
	}
}

LoginForm.PropTypes = {
	login: PropTypes.func.IsRequired
}

LoginForm.contextTypes = {
	router: PropTypes.object.isRequired
}


export default connect(null, { login, setModal})(CSSModules(LoginForm, styles, {'allowMultiple' : true}));
