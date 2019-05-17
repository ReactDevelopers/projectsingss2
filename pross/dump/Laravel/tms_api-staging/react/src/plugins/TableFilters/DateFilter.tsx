import  * as React from 'react';
import DatePicker from 'react-datepicker';

import { CustomFilter, CustomFilterParameters } from 'react-bootstrap-table';
import moment, { Moment } from 'moment';

interface DateFilterProps  {

    filterHandler: Function;
    param: {
        comparator?: string;
        value?: string;
    };
    callback: Function;
    minDate?: Moment;
    maxDate?: Moment;
}


class DateFilter extends React.Component<DateFilterProps> {
 

    constructor(props: DateFilterProps) {

        super(props)
        this.change = this.change.bind(this);
        //this.callback= this.callback.bind(this);
        this.changeOperator = this.changeOperator.bind(this);
       
    }

    change(v: Moment | null, param: {[key: string]: any}) {

        var newParam = {
            value: v ? v.format('YYYY-MM-DD'): '',
            comparator: param.comparator
        };
        
        this.props.filterHandler({
            callback: this.props.callback,
            callbackParameters: newParam
        });
    }

    changeOperator(e: any, param: {[key: string]: any}) {
       
        var newParam = {
            value: param.value,
            comparator: e.target.value
        };

        this.props.filterHandler({
            callback: this.props.callback,
            callbackParameters: newParam
        });
    }
    shouldComponentUpdate(nextProps: DateFilterProps) {

        if(nextProps.param.value !== this.props.param.value) {

            return true;
        }
        else if(nextProps.param.comparator !== this.props.param.comparator) {

            return true;
        }
        else if(nextProps.maxDate !== this.props.maxDate) {

            return true;
        }
        else if(nextProps.minDate !== this.props.minDate) {

            return true;
        }

        return false;
    }

    callback() {
        return (this.props.param.comparator) ? true : false;
    }
    render() {
        
        const {minDate, maxDate, param: {value, comparator}} = this.props;

        return (
            <div>
                <div className="filter date-filter">
                    <select className="date-filter-comparator form-control" onChange={ (e) => {this.changeOperator(e, this.props.param)}}>
                        {/* <option></option> */}
                        <option value="=" selected={comparator === "=" ? true : false}>=</option>
                        <option value=">" selected={comparator === ">" ? true : false}>&gt;</option>
                        <option value=">=" selected={comparator === ">=" ? true : false}>&gt;=</option>
                        <option value="<" selected={comparator === "<" ? true : false}>&lt;</option>
                        <option value="<=" selected={comparator === "<=" ? true : false}>&lt;=</option>
                        <option value="!=" selected={comparator === "!=" ? true : false}>!=</option>
                    </select>
                    <DatePicker minDate={minDate} maxDate={maxDate} 
                        dateFormat="DD/MM/YYYY" 
                        isClearable={true}
                        onChange={(e: any) => this.change(e, this.props.param)} className="form-control" 
                        selected={value ? moment(value): null}  />

                </div>                        
            </div>
        );
    }    
}

export default DateFilter;