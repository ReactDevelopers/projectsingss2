import * as React from 'react';
import { DataAlignType, SelectFilterOptionsType, TableHeaderColumnProps} from 'react-bootstrap-table';
import {Props} from '../../features/root-props';
const dataAlign: DataAlignType = 'left';
import {
    TableColumnProps, 
    getSelectFilter, 
    getDateFilter, 
    getDateRangeFilter
} 
from '../../plugins/Table';


/**
 * TO Print the Text Field
 */

export function _printTextField(title: string, dataField: string, filterData: {[key: string]: any}, width?: string,  show?: boolean, defaultText?: any, moreOptions?: TableHeaderColumnProps ): TableColumnProps {
    
    const defaultValue = filterData &&  filterData[dataField] ? filterData[dataField].value: defaultText;

    return {
        columnTitle: title,
        dataField: dataField,
        dataAlign: dataAlign,
        filter: { 
            type: 'TextFilter', 
            delay: 1000, 
            defaultValue: defaultValue,
            placeholder: 'Search' 
        },
        export: false,
        dataSort: true,              
        title: title,
        hidden: (show !== undefined) ? !show : false,
        width: width? width: '150px',
        ...moreOptions         
    }
}

/**
 * To Print Select Field
 */
export function _printSelectOptionsField(props: Props, optionKey: string, valueKey: string, title: string, dataField: string, filterData: {[key: string]: any}, width?: string, show?: boolean, defaultValue?: any ): TableColumnProps {
    return {
        columnTitle: title,
        dataField: dataField,
        dataAlign: dataAlign,
        filter: getSelectFilter(
            props.helper.deepFind(props.rootState.server,'options.response.data.'+optionKey,[]),
            props.helper.deepFind(filterData, dataField + '.value', defaultValue),
            props.rootState.server['options'] ? props.rootState.server['options'].isFetching : false,
            'id',
            valueKey,
        ),
        export: false,
        dataSort: true,              
        title: title,
        width: width? width: '120px',
        hidden: (show !== undefined) ? !show : false, 
        dataFormat: (cell: any, row: any, formatExtraData: any, rowIndex: number) => {
            return cell ? cell : 'NA';
        }       
    }
}

//props.helper.makeSelectListArray(
/**
 * To Print Select Field
 */
export function _printSelectField(props: Props, options: SelectFilterOptionsType , title: string, dataField: string, filterData: {[key: string]: any}, width?: string, show?: boolean, defaultValue?: any ): TableColumnProps {
    return {
        columnTitle: title,
        dataField: dataField,
        dataAlign: dataAlign,
        filter: getSelectFilter(
            options,
            props.helper.deepFind(filterData, dataField + '.value', defaultValue),
            false
        ),
        export: false,
        dataSort: true,              
        title: title,
        width: width? width: '120px',
        hidden: (show !== undefined) ? !show : false,  
        dataFormat: (cell: any, row: any, formatExtraData: any, rowIndex: number) => {
            return cell ? cell : 'NA';
        }      
    }
}

/**
 * To Print Number Field
 */
export function _printNumberField(props: Props , title: string, dataField: string, filterData: {[key: string]: any}, width?: string, show?: boolean, defaultText?: any ): TableColumnProps {
    
    const defaultValue = filterData &&  filterData[dataField] ? filterData[dataField].value: defaultText;
    const comparator = filterData &&  filterData[dataField] ? filterData[dataField].comparator: undefined;

    return {
        columnTitle: title,
        dataField: dataField,
        dataAlign: dataAlign,
        filter: { 
            type: 'NumberFilter', 
            defaultValue: {
                number: defaultValue,
                comparator: comparator,
            },
            placeholder: 'ALL'
        },                
        export: false,
        dataSort: true,              
        title: title,
        width: width? width: '120px',   
        dataFormat: (cell: any, row: any, formatExtraData: any, rowIndex: number) => {
            return cell ? cell : 'NA';
        }        
    }
}

/**
 * To Print Percentage Field
 */
export function _printPercentageField(props: Props , title: string, dataField: string, filterData: {[key: string]: any}, width?: string, show?: boolean, defaultText?: any ): TableColumnProps {
    
    const defaultValue = filterData &&  filterData[dataField] ? filterData[dataField].value: defaultText;
    const comparator = filterData &&  filterData[dataField] ? filterData[dataField].comparator: undefined;

    return {
        columnTitle: title,
        dataField: dataField,
        dataAlign: dataAlign,
        filter: { 
            type: 'NumberFilter', 
            defaultValue: {
                number: defaultValue,
                comparator: comparator,
            },
            placeholder: 'ALL'
        },                
        export: false,
        dataSort: true,              
        title: title,
        dataFormat: (cell: any, row: any, formatExtraData: any, rowIndex: number) => {
            return cell ? (props.helper.isFloat(cell) ? cell : Math.round( cell)) +'%' : 'NA';
        },
        width: width? width: '120px',           
    }
}

/**
 * To Print the Date Rnage Field
 */
export function _printDateRangeField(props: Props,  title: string, dataField: string, dates: [string, string], filterData: {[key: string]: any}, width?: string, defaultValue?: any ): TableColumnProps {
    
    return {
        columnTitle: title,
        dataField: dataField,
        dataAlign: dataAlign,
        // hidden: (show !== undefined) ? !show : false,
        filter: getDateRangeFilter(
            props.helper.deepFind(filterData, dataField+ '.value', defaultValue)
        ),
        export: false,
        dataSort: true,              
        title: title,
        width: width,
        dataFormat: (cell: any, row: any, formatExtraData: any, rowIndex: number) => {
            return (
                <p>
                    {props.helper.displayDate(row[dates[0]])}
                    <br/>
                    {props.helper.displayDate(row[dates[1]])}
                </p>  
            )
        }
    }  
}

export function _courseDates(props: Props, filterData: {[key: string]: any}, width?: string, defaultValue?: any ): TableColumnProps {
    
    return _printDateRangeField(props, 'Course Start/End Date', 'date_range', ['start_date','end_date'], filterData, width, defaultValue);
}

