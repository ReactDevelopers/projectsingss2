import * as React from 'react';
import { Field, reduxForm, getFormSubmitErrors, FormErrors } from 'redux-form';
import {connect} from 'react-redux';
import * as bs from 'react-bootstrap';
import {getFieldError, CustomFormProps} from '../../plugins/form/FormInit';
import TextInput from '../../plugins/form/layout/FormGroupText';
import validate from './loginValidation';


export interface LoginFormData {
    username?: string;
    password?: string;
}

export interface LoginFormProps extends CustomFormProps<LoginFormData> {}

const LoginForm: React.StatelessComponent<LoginFormProps> = ({ handleSubmit, 
    submitErrors, 
    submitting, 
    pristine, 
    valid,
    error, 
        }) => (
        <div className="container">
            <bs.Form horizontal={true} onSubmit={handleSubmit}>
                <Field 
                    name="username" 
                    component={TextInput} 
                    serverError={getFieldError(submitErrors,'username')} 
                    placeholder="Email/UserName" 
                    type="text" />
                <Field serverError={getFieldError(submitErrors,'password')} 
                    name="password"                     
                    component={TextInput} 
                    placeholder="Password" 
                    type="password" />
                <div className="loginBtn text-right">
                    <div className="row">
                        <div className="col-sm-12">
                            <button className="btn-round btn btn-default" disabled={submitting} type="submit">Submit</button>
                        </div>
                    </div>
                </div>    
                
            </bs.Form>
        </div>
);


const Lform =  reduxForm({
    form: 'login',  // a unique identifier for this form,
    validate: validate,
})(LoginForm);

export default connect(
    state => ({
        // values: getFormValues('myForm')(state),
        // syncErrors: getFormSyncErrors('myForm')(state),
        submitErrors: getFormSubmitErrors('login')(state),
        // dirty: isDirty('myForm')(state),
        // pristine: isPristine('myForm')(state),
        // valid: isValid('myForm')(state),
        // invalid: isInvalid('myForm')(state)
      })
)(Lform);