import {LoginFormData, LoginFormProps} from './LoginForm';
import { FormErrors} from 'redux-form';

/**
 * Validate login Form
 * @param values 
 * @param props 
 */
const validate = (values: LoginFormData, props: LoginFormProps): FormErrors<LoginFormData> => {
    const { username, password } = values;
    const errors: FormErrors<LoginFormData> = {};

    if (!username) {
        errors.username = 'Required';
    }
    if (!password) {
        errors.password = 'Required';
    }
    return errors;
};

export default validate;