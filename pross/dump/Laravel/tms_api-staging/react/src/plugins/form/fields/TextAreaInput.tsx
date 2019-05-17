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
    
import Textarea from "react-textarea-autosize";

import * as React from 'react';
import {FieldGeneralProps} from '../FormInit';

export type WrappedTextAreaFieldProps = {
    // label?: string,
    // className?: string,
    // elementclassName?: string,
    //type: string,
    placeholder?: string,
    serverError?: string | undefined,
} & WrappedFieldProps & FieldGeneralProps;

/**
 * HTML Text Element With the Error Message
 * @param param0 
 */
const Input: React.StatelessComponent<WrappedTextAreaFieldProps> =  (

    {input,  placeholder, serverError, isFetching, disabled,
        meta: { touched , error, submitting} 
    }) => {

    const hasError = !!error && !!touched || !!serverError;

    return (
       <>
            <Textarea {...input}             
            placeholder={isFetching ? 'Loading...': placeholder} 
            className="form-control" 
            disabled={isFetching || disabled} />
            {hasError && <span className="help-block">{serverError? serverError : error }</span>}
        </>
    )
}
export default Input; 