import React, {Component} from 'react';
import { connect } from 'react-redux';
import { register } from './authActions';
import { setModal } from '../sharedComponents/modalActions';
import PropTypes from 'prop-types';
import CSSModules from 'react-css-modules';

import TextFieldGroup from './Common/TextFieldGroup';

import styles from './styles/formStyles.scss';

class SignUpForm extends Component {
	constructor() {
		super();
		this.state = {
            name: '',
			email : '',
			password : '',
			errors : '',
			isLoading : false,
			user: null
		};

		this.onSubmit = this.onSubmit.bind(this);
		this.onChange = this.onChange.bind(this);
	}

	isValid() {
		return true;
	}

	register() {
		this.props.register(this.state).then(
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
			this.register();
		} else {
			//display errors
		}
	}

	onChange(e) {
		this.setState({ [e.target.name] : e.target.value})
	}

	render() {
		const { name, errors, email, password, isLoading } = this.state;

		return (
			<form styleName="modalForm" onSubmit={this.onSubmit}>
				<TextFieldGroup
					field="name"
					placeholder="Name"
					value={name}
					error={errors.name}
					onChange={this.onChange}
					/>
                
                <TextFieldGroup
					field="email"
					placeholder="email"
					type="email"
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
					<button disabled={isLoading}>Sign Up</button>
				</div>
			</form>
		)
	}
}

SignUpForm.PropTypes = {
	register: PropTypes.func.IsRequired
}

SignUpForm.contextTypes = {
	router: PropTypes.object.isRequired
}


export default connect(null, { register, setModal})(CSSModules(SignUpForm, styles, {'allowMultiple' : true}));
