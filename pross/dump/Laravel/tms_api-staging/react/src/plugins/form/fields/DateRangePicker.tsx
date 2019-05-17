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
//import Datepicker, {ReactDatePickerProps} from 'react-datepicker';
import moment, { Moment } from 'moment';
import {FieldGeneralProps} from '../FormInit';
import DateRangePicker from 'react-bootstrap-daterangepicker';
import 'bootstrap-daterangepicker/daterangepicker.css';
import Input from './TextAreaInput';

export type WrappedDateRangeFieldProps = {
 
    placeholder?: string,
    serverError?: string | undefined,
    maxDate?: Moment,
    minDate?: Moment,
    opens?: 'left' | 'right' | 'center';
    //onChange?: (date: Moment | null) => void,
} & WrappedFieldProps &  FieldGeneralProps;


/**
 * HTML Text Element With the Error Message
 * @param param0 
 */
class  DateRangeInput extends React.Component<WrappedDateRangeFieldProps>{

    constructor(props: WrappedDateRangeFieldProps) {

        super(props);
        this.onChange = this.onChange.bind(this);
        this.onCancel = this.onCancel.bind(this);
    }

    onChange(event:any, picker: any) {

        this.props.input.onChange([
            picker.startDate.format('YYYY-MM-DD'), 
            picker.endDate.format('YYYY-MM-DD') 
        ]);
    }
    onCancel(event:any, picker: any){
        
        this.props.input.onChange('');
    }
    render() {
        const {input, opens, minDate,  maxDate, placeholder, serverError, isFetching, disabled, meta: { touched , error, submitting}  } = this.props;
        const hasError = !!error && !!touched || !!serverError;
        console.log('Input....');
        return (
        <>
            <DateRangePicker 
                startDate={input.value ? moment(input.value[0]) : moment()} 
                endDate={input.value ? moment(input.value[1]) : moment()}
                opens={opens}
                locale={
                    {format: "DD/MM/YYYY", cancelLabel: 'Clear Filter'}
                }
                ranges={
                    {
                        'Today': [moment(), moment()],
                        'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                        'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                        'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                        'This Month': [moment().startOf('month'), moment().endOf('month')],
                        'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                    }
                }
                onCancel={(event: any, picker: any) => { this.onCancel(event, picker)} }
                onApply={(event: any, picker: any) => { this.onChange(event, picker)} }>
                <input type="text" readOnly={true} 
                value={input.value ? moment(input.value[0]).format('DD/MM/YYYY') + ' - ' + moment(input.value[1]).format('DD/MM/YYYY') : ''}
                className="form-control"
                placeholder={placeholder}
                />
            </DateRangePicker>
            {hasError && <span className="help-block">{serverError? serverError : error }</span>}
        </>
        );
    }

}
export default DateRangeInput; 