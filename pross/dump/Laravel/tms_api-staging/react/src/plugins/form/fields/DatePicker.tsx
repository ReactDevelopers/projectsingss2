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
import Datepicker, {ReactDatePickerProps} from 'react-datepicker';
import moment, { Moment } from 'moment';
import {FieldGeneralProps} from '../FormInit';

export type WrappedDateFieldProps = {
    // label?: string,
    // className?: string,
    // elementclassName?: string,
    type: string,
    placeholder?: string,
    serverError?: string | undefined,
    maxDate?: Moment,
    minDate?: Moment,
    isClearable?: boolean,
    onChange?: (date: Moment | null) => void,
   // onChange (date: moment.Moment | null, event: React.SyntheticEvent<any> | undefined): any;
} & WrappedFieldProps &  FieldGeneralProps;


/**
 * HTML Text Element With the Error Message
 * @param param0 
 */
class  DateInput extends React.Component<WrappedDateFieldProps>{

    constructor(props: WrappedDateFieldProps) {

        super(props);
        this.onChange = this.onChange.bind(this);
    }

    onChange(date: Moment){
        const inputDate = date? date.format('YYYY-MM-DD') : '';
        this.props.input.onChange(inputDate);
        if(this.props.onChange){
            this.props.onChange(date);
        }
    }
    render() {
        const {input, minDate,  onChange, isClearable, maxDate, placeholder, serverError, isFetching, disabled, meta: { touched , error, submitting}  } = this.props;
        const hasError = !!error && !!touched || !!serverError;
        // console.log('serverError');
        // console.log(input);
        // console.log(selected);
        // console.log(minDate);
        return (
        <>
            <Datepicker 
            onChange={ this.onChange} 
            selected={input.value ? moment(input.value): null}
            minDate={minDate}
            maxDate={maxDate}
            placeholderText={isFetching ? 'Loading...': placeholder}
            dateFormat="DD/MM/YYYY"
            className="form-control"
            isClearable={isClearable ===undefined ? true : isClearable }
            disabled={disabled || isFetching}
            showYearDropdown={true}
            showMonthDropdown={true}
            />
            {hasError && <span className="help-block">{serverError? serverError : error }</span>}
        </>
        );
    }

}
export default DateInput; 