import { WrappedFieldProps } from 'redux-form';
import * as React from 'react';
import * as classNames from 'classnames';
import TextInput, {WrappedTextFieldProps} from '../fields/TextInput';

/**
 * Bootstrap FormGroup Layout
 * @param props 
 */
const Layout: React.StatelessComponent<WrappedTextFieldProps> = (props) => {
    
    const {label, serverError, className, labelPossition, 
        meta: {  touched , error, submitting} 
    } = props;

    const hasError = !!error && !!touched || !!serverError;
    let classNameArray = ['form-group', [className], { 'has-error': hasError }];
    const classNamesResult = classNames(classNameArray);
    let lp = labelPossition ? labelPossition : 'Left';
    // console.log('sdkjhekdekfe');
    // console.log(serverError);
    return (
        <div className={classNamesResult}>
            {label ? <label className={`control-label ${lp === 'Left' ? 'col-sm-3 ' :'p-b-10'} label-posstion-${lp}`} 
                htmlFor="firstName">{label}
            </label> : ''}

            <div className={`${lp === 'Left'? 'col-sm-'+ (label? 9 : 12) : ''}`}>
                <TextInput {...props} />
            </div>
        </div>
    )
}
export default Layout;