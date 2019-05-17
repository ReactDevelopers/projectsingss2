import { DataAlignType, PaginationPostion } from 'react-bootstrap-table';
import * as React from 'react';
import {
    TableColumnProps, 
    getSelectFilter, 
    getDateFilter, 
    getDateRangeFilter
} 
from '../../../plugins/Table';
import {Props} from '../../../features/root-props';
import { _printTextField, _printSelectOptionsField, _courseDates , _printSelectField } from '../../../plugins/TableCell';

import {PageType} from '../../placement/PlacementTable';
import ChangeStatus from '../../placement/ChangeStatus';
import Delete from '../../placement/Delete';

const dataAlign: DataAlignType = 'left';


/**
 * Print Status Column
 */
export function placementStatusCell(filterData: {[key: string]: any}, props: Props, defaultValue?: any): TableColumnProps {
    
    return _printSelectField(props, {'Draft': 'Draft','Confirmed':'Confirmed','Cancelled':'Cancelled'}, 'Status', 'current_status', filterData, '120px');
}

export function placementActionTextCell(filterData: {[key: string]: any}, props: Props, defaultValue?: any): TableColumnProps {
    
    return _printSelectField(props, {'Penalty': 'Penalty','Waived':'Waived'}, 'Action', 'action', filterData, '120px');
}

/**
 * Print attendance Column
 */
export function attendanceCell(filterData: {[key: string]: any}, props: Props, defaultValue?: any): TableColumnProps {
    
    return _printSelectField(props, {'Present': 'Present','Absent':'Absent'}, 'Attendance', 'attendance', filterData, '100px');
}


/**
 * Print assessment_results Column
 */
export function resultCell(filterData: {[key: string]: any}, props: Props, defaultValue?: any): TableColumnProps {
    
    return _printSelectField(props, {'Pass': 'Pass','Fail':'Fail'}, 'Assessment Results', 'assessment_results', filterData, '100px');
}

/**
 * Print percipient_name Column
 */
export function participantNameCell(filterData: {[key: string]: any}, props: Props, defaultValue?: any) : TableColumnProps {

    return _printSelectOptionsField(props, 'users', 'name', 'Participants', 'percipient_name', filterData, '120px');
}

/**
 * Print failure_reason Column
 */
export function failureReasonCell(filterData: {[key: string]: any}, props: Props, defaultValue?: any) : TableColumnProps {

    return _printSelectOptionsField(props, 'failure_reason', 'failure_reason', 'Failure Reason', 'failure_reason', filterData, '100px');
}

/**
 * Print absent_reason Column
 */
export function absentReasonCell(filterData: {[key: string]: any}, props: Props, defaultValue?: any) : TableColumnProps {

    return _printSelectOptionsField(props, 'absent_reason', 'absent_reason', 'Absent Reason', 'absent_reason', filterData, '120px');
}

//percipient_name
/**
 * Print Status action Column
 */
export function changeStatusCell(filterData: {[key: string]: any}, props: Props, page: 'CourseRunDetail' | 'PlacemnetList', courseRunId?: string, refreshTable?: Function,  whereFrom?:  'maintain-list'): TableColumnProps {
    
    return {
        columnTitle: 'Change Status',
        dataField: 'current_status',
        dataAlign: dataAlign,
        export: false,
        dataSort: false,              
        title: 'Change Status',
        width: "120px",         
        dataFormat: (cell: any, row: any, formatExtraData: any, rowIndex: number) => {

            return <ChangeStatus 
            cell={cell} row={row} 
            formatExtraData={formatExtraData} 
            rowIndex={rowIndex}
            whereFrom={whereFrom}
            courseRunId={courseRunId} 
            onPage={page}
            refreshTable={refreshTable}
            {...props} />
        }   
    }
}

/**
 * Print Status action Column
 */
export function placementActionCell(filterData: {[key: string]: any}, props: Props, refreshTable: Function, pageType: PageType ): TableColumnProps {
    
    return {
        columnTitle: 'Action',
        dataField: 'course_title',
        dataAlign: dataAlign,
        export: false,
        dataSort: false,              
        title: 'Action',
        width: "45px",       
        dataFormat: (cell: any, row: any, formatExtraData: any, rowIndex: number) => {

            return <Delete 
            cell={cell} row={row} 
            formatExtraData={formatExtraData} 
            rowIndex={rowIndex}
            pageFor={pageType}
            refreshTable={refreshTable}
            {...props} />
        }
    }
}
