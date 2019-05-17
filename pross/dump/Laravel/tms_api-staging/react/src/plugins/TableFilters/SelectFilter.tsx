import  * as React from 'react';
import { CustomFilter, CustomFilterParameters, SelectFilterOptionsType } from 'react-bootstrap-table';
import Helper from '../../plugins/Helper';
const helper = new Helper();

interface SelectFilterProps  {

    filterHandler: Function;
    param: {
        comparator?: string;
        value?: string;
    };
    valueKey?: string;
    labelKey?: string;
    options: SelectFilterOptionsType | Array<{[key: string]: string | number}>;
    callback: Function;
    placeHolder?: string;
    shouldUpdate: boolean;
}


class SelectFilter extends React.Component<SelectFilterProps> {
 

    constructor(props: SelectFilterProps) {

        super(props)
        this.onChange = this.onChange.bind(this);       
    }    

    onChange(e: any, param: {[key: string]: any}) {
       
        var newParam = {
            value: e.target.value,
            comparator: '='
        };

        this.props.filterHandler({
            callback: this.props.callback,
            callbackParameters: newParam
        });
    }
    shouldComponentUpdate(nextProps: SelectFilterProps) {

        if(nextProps.param.value !== this.props.param.value) {

            return true;
        }
        else if(nextProps.param.comparator !== this.props.param.comparator) {

            return true;
        }
        else if(nextProps.shouldUpdate !==  this.props.shouldUpdate){

            return true
        }

        return false;
    }

    
    render() {
        
        const {param, placeHolder, options, param: {value, comparator}, valueKey, labelKey } = this.props;
        const optKeys = Object.keys(options);

        return (
                <div className="filter select-filte-warpper">
                    <select  className="select-filter form-control" value={value} onChange={ (e) => {this.onChange(e, this.props.param)}}>
                        <option value="">{placeHolder ? placeHolder : 'ALL'}</option>
                       
                        {
                           helper.isObject(options) ? optKeys.map(v => {
                            return <option key={`selected_option_${v}`} value={v}  >{options[v]} </option>
                        }):  options.map(v1 => {
                            return <option key={`selected_option_${v1[valueKey]}`} value={v1[valueKey]}  >{v1[labelKey]} </option>
                            
                        }) }
                    </select>

                </div>                        
        );
    }    
}

export default SelectFilter;