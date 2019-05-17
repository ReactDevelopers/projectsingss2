import { WrappedFieldProps } from 'redux-form';
import * as React from 'react';
import * as classNames from 'classnames';
import TextAreaInput, {WrappedTextAreaFieldProps} from '../fields/TextAreaInput';

/**
 * Bootstrap FormGroup Layout
 * @param props 
 */
const Layout: React.StatelessComponent<WrappedTextAreaFieldProps> = (props) => {
    
    const {label, serverError, className, labelPossition,
        meta: {  touched , error, submitting} 
    } = props;

    const hasError = !!error && !!touched || !!serverError;
    let classNameArray = ['form-group', [className], { 'has-error': hasError }];
    const classNamesResult = classNames(classNameArray);
    const lp = labelPossition ? labelPossition : 'Left';

    return (
        <div className={classNamesResult}>
            {label ? <label className={`${lp === 'Left' ? 'col-sm-3 control-label' :'p-b-10'} lable-posstion-${lp}`} 
                htmlFor="firstName">{label}
            </label> : ''}
            <div className={`${lp === 'Left'? 'col-sm-'+ (label? 9 : 12) : ''}`}>
                <TextAreaInput {...props} />
            </div>
        </div>
    )
}
export default Layout;