import { 
    Field, 
    Fields, 
    reduxForm, 
    ValidateCallback, 
    ConfigProps,  
    getFormSubmitErrors, 
    InjectedFormProps, 
    FormErrors, 
    FormAction, 
    FieldsProps, 
    WrappedFieldProps } from 'redux-form';

import * as React from 'react';
import {FieldGeneralProps} from '../FormInit';

export type WrappedTextFieldProps = {
    // label?: string,
    // className?: string,
    // elementclassName?: string,
    type: string,
    placeholder?: string,
    serverError?: string | undefined,
} & WrappedFieldProps & FieldGeneralProps;

/**
 * HTML Text Element With the Error Message
 * @param param0 
 */
const TextInput: React.StatelessComponent<WrappedTextFieldProps> =  (

    {input, type, placeholder, serverError, disabled, isFetching,
        meta: { touched , error, submitting} 
    }) => {
        // console.log('ssdisabled');
        // console.log(disabled);
    const hasError = !!error && !!touched || !!serverError;

    return (
       <>
            <input {...input} type={type} disabled={isFetching || disabled}
            placeholder={isFetching ? 'Loading...': placeholder} className="form-control" />
            {hasError && <span className="help-block">{serverError? serverError : error }</span>}
        </>
    )
}
export default TextInput; 