import { DataAlignType, PaginationPostion } from 'react-bootstrap-table';
import * as React from 'react';
import {
    TableColumnProps, 
    getSelectFilter, 
    getDateFilter, 
    getDateRangeFilter
} 
from '../../../plugins/Table';
import ReadMore from '../../../plugins/TableCell/ReadMore';
import {Props} from '../../../features/root-props';
import { _printTextField, _printSelectOptionsField, _courseDates , _printSelectField, _printNumberField } from '../../../plugins/TableCell';
const dataAlign: DataAlignType = 'left';

/**
 * TO Print the Category Cell
 * @param filterData 
 */
export function categoryCell(filterData: {[key: string]: any}, props: Props ) : TableColumnProps {
    
    return _printSelectOptionsField(props, 'programe_category', 'prog_category_name', 'Category', 'prog_category_name', filterData, '120px');    
}

/**
 * TO Print the Category Cell
 * @param filterData 
 */
export function programmeTypeCell(filterData: {[key: string]: any}, props: Props ) : TableColumnProps {
    
    return _printSelectOptionsField(props, 'programme_type', 'prog_type_name', 'Programme Type', 'prog_type_name', filterData, '100px');    
}

/**
 * TO Print the Department Cell
 * @param filterData 
 */
export function courseDepartmentCell(filterData: {[key: string]: any}, props: Props ) : TableColumnProps {
    
    return _printSelectOptionsField(props, 'departments', 'dept_name', 'Department', 'dept_name', filterData, '120px');    
}

/**
 * To Print the Course Code
 */
export function courseCodeCell(filterData: {[key: string]: any}, props: Props ) : TableColumnProps {
    
    return _printTextField('Course Code','course_code', filterData, '80px');
}
/**
 * To Print the Course Title
 */
export function courseTitleCell(filterData: {[key: string]: any}, props: Props, dataField?: string ) : TableColumnProps {
    dataField = dataField ? dataField : 'title';
    return _printTextField('Course Title',dataField, filterData, '150px', true, undefined, {
        dataFormat: (cell: any, row: any, formatExtraData: any, rowIndex: number) => {
            return <ReadMore key={`title_in_row${row.id}`} data={row[dataField] ? row[dataField].toString() : '' } minChar={65} />
        }
    });
}

/**
 * To Print the Creator Data
 * @param filterData 
 * @param props 
 * @param defaultValue 
 */
export function creatorCell(filterData: {[key: string]: any}, props: Props, defaultValue?: any) : TableColumnProps {

    return _printSelectOptionsField(props, 'users', 'name', 'Created By', 'created_by', filterData, '100px',true, defaultValue);
}

/**
 * To Print the duration_in_days
 * @param filterData 
 * @param props 
 * @param defaultValue 
 */
export function courseDuration(filterData: {[key: string]: any}, props: Props, defaultValue?: any) : TableColumnProps {

    return _printNumberField(props, 'Duration (No of days)', 'duration_in_days', filterData, '120px');
}
