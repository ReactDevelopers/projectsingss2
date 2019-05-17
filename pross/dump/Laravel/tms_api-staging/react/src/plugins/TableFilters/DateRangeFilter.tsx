import  * as React from 'react';
import DateRangePicker from 'react-bootstrap-daterangepicker';
import 'bootstrap-daterangepicker/daterangepicker.css';

import { CustomFilter, CustomFilterParameters } from 'react-bootstrap-table';
import moment, { Moment } from 'moment';

export interface ParamValueProps {
    start_date: string;
    end_date: string;
}

export interface DateRangeFilterProps  {

    filterHandler: Function;
    param: {
        comparator?: string;
        value?: ParamValueProps;
    };
    callback: Function;
}


class DateRangeFilter extends React.Component<DateRangeFilterProps> {
 

    constructor(props: DateRangeFilterProps) {

        super(props)
        this.change = this.change.bind(this);
        this.cancel = this.cancel.bind(this);
    }

    change(event:any, picker: any, param: {[key: string]: any}) {
 
        var newParam = {
            value: {
                start_date: picker.startDate.format('YYYY-MM-DD'),
                end_date: picker.endDate.format('YYYY-MM-DD')
            },
            comparator: 'date-range'
        };
        
        this.props.filterHandler({
            callback: this.props.callback,
            callbackParameters: newParam
        });
    }
    cancel(event:any, picker: any, param: {[key: string]: any}) {

        var newParam = {
            value: undefined,
            comparator: 'date-range'
        };

        this.props.filterHandler({
            callback: this.props.callback,
            callbackParameters: newParam
        });
    }
    shouldComponentUpdate(nextProps: DateRangeFilterProps) {

        
        if(nextProps.param.value === undefined && this.props.param.value !== undefined) {

            return true;
        }
        else if(nextProps.param.value !== undefined && this.props.param.value === undefined) {

            return true;
        }
        else if(nextProps.param.value && this.props.param.value && nextProps.param.value.start_date !==  this.props.param.value.start_date) {

            return true;
        }
        else if(nextProps.param.value && this.props.param.value && nextProps.param.value.end_date !==  this.props.param.value.end_date ) {

            return true;
        }
        else if(nextProps.param.comparator !== this.props.param.comparator) {

            return true;
        }
        return false;
    }

    callback() {
        return (this.props.param.comparator) ? true : false;
    }
    render() {
        
        const {param, param: {value, comparator}} = this.props;
        console.log('gggggggggggggggggggggggg');
        console.log(this.props);

        return (
                <div className="filter date-range-filter">
                   
                   <DateRangePicker 
                        startDate={value ? moment(value.start_date) : moment()} 
                        endDate={value ? moment(value.end_date) : moment()}
                        opens='center'
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
                        onCancel={(event: any, picker: any) => { this.cancel(event, picker, param)} }
                        onApply={(event: any, picker: any) => { this.change(event, picker, param)} }>
                        <button> {value === undefined || !value ? 'Select Dates' : moment(value.start_date).format('DD/MM/YYYY') + '-' + moment(value.end_date).format('DD/MM/YYYY') } </button>
                    </DateRangePicker>
                </div>                        
        );
    }    
}

export default DateRangeFilter;