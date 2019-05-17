import { WrappedFieldProps } from 'redux-form';
import * as React from 'react';
import * as classNames from 'classnames';
import RichTextInput, {WrappedRichTextFieldProps} from '../fields/RichTextInput';

/**
 * Bootstrap FormGroup Layout
 * @param props 
 */
const Layout: React.StatelessComponent<WrappedRichTextFieldProps> = (props) => {
    
    const {label, serverError, className, labelPossition,
        meta: {  touched , error, submitting} 
    } = props;

    const hasError = !!error && !!touched || !!serverError;
    let classNameArray = ['form-group', [className], { 'has-error': hasError }];
    const classNamesResult = classNames(classNameArray);
    const lp = labelPossition ? labelPossition : 'Left';

    return (
        <div className={classNamesResult}>            
            {label ? <label className={`control-label ${lp === 'Left' ? 'col-sm-3' :'p-b-10'} label-posstion-${lp}`} 
                htmlFor="firstName">{label}
            </label> : ''}

            <div className={`${lp === 'Left'? 'col-sm-'+ (label? 9 : 12) : ''}`}>
                <RichTextInput {...props} />
            </div>
        </div>
    )
}
export default Layout;