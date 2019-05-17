import * as React from 'react';
import {InjectedFormProps, FormErrors, FormProps} from 'redux-form';
import { Dispatch } from 'redux';

/**
 * Default Form Props
 */
export type CustomFormProps <FormData> = {
    className?: string,
    dispatch?: Dispatch<{}>,
    submitErrors?: { [key: string] : Array<string> },
    syncErrors: {[key: string]: string},
    isFetching?: boolean; 
    values?:{ [key: string] : Array<string> };
    onSubmit: (values:FormData , dispatch: Dispatch<{}>, props: FormProps<FormData,{}>) => void | FormErrors<FormData> | Promise<any>

} & InjectedFormProps;


export type FieldGeneralProps = {
    
    label?: string,
    className?: string,
    elementclassName?: string,
    disabled?: boolean,
    isFetching?: boolean,
    labelPossition?: 'Left' | 'Right' | 'Top' | 'Bottom',
}


/**
 * Find the error of the given element
 * @param errors 
 * @param key 
 */
export function getFieldError(errors: { [key: string] : Array<string> | string } | undefined, key: string) : string | undefined {
    return (errors && errors[key] ? (typeof errors[key] === 'object' ? errors[key].join(', ') : errors[key]) : undefined);
}