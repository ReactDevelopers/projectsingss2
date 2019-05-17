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
import { _printTextField, _printSelectOptionsField,_printPercentageField, _courseDates,_printDateRangeField , _printSelectField , _printNumberField} from '../../../plugins/TableCell';

import ActionCell from '../../course_run/columns/ActionCell';
import ActionChangeStatus from '../../course_run/columns/ActionChangeStatus';
import ActionDecoflict from '../../course_run/columns/ActionDecoflict';
import {ApiEndPointI} from '../../../aep';

const dataAlign: DataAlignType = 'left';

/**
 * TO Print the Course Run Id Cell
 * @param filterData 
 */
export function courseRunIdCell(filterData: {[key: string]: any}, props: Props ) : TableColumnProps {    
    
    return _printTextField('Course Run Id','course_run_id', filterData, '80px');
}

/**
 * TO Print the Course Run Id Cell
 * @param filterData 
 */
export function primaryCell(filterData: {[key: string]: any}, props: Props, title: string ) : TableColumnProps {    
    
    return _printTextField(title,'id', filterData, '80px');
}

/**
 * To Print the Date Cell
 * @param filterData 
 * @param props 
 * @param defaultValue 
 * @param row 
 */
export function datesCell(filterData: {[key: string]: any}, props: Props, defaultValue?: any) : TableColumnProps {

    return _courseDates(props, filterData, '120px', defaultValue);
}

/**
 * Print Status Column
 */
export function courseRunStatusCell(filterData: {[key: string]: any}, props: Props, defaultValue?: any): TableColumnProps {
    
    return _printSelectField(props, {'Draft': 'Draft','Confirmed':'Confirmed','Closed':'Closed','Complete': 'Complete'}, 'Course Run Status', 'current_status', filterData, '120px');
}

/**
 * To Print the AsseCell
 * @param filterData 
 * @param props 
 * @param defaultValue 
 * @param row 
 */
export function courseAssessmentDateCell(filterData: {[key: string]: any}, props: Props,  defaultValue?: any ): TableColumnProps {

    return _printDateRangeField(props, 'Assessment Start/End Date', 'test_date_range', ['assessment_start_date','assessment_end_date'], filterData, '120px', defaultValue);
}

/**
 * To Print the duration_in_days
 * @param filterData 
 * @param props 
 * @param defaultValue 
 */
export function classSizeCell(filterData: {[key: string]: any}, props: Props, defaultValue?: any) : TableColumnProps {

    return _printNumberField(props, 'Class Size', 'class_size', filterData);
}

/**
 * To Print the available_slot
 * @param filterData 
 * @param props 
 * @param defaultValue 
 */
export function availableSlotCell(filterData: {[key: string]: any}, props: Props, defaultValue?: any) : TableColumnProps {

    return _printNumberField(props, 'Available', 'available_slot', filterData);
}

/**
 * To Print the Enrolled Number
 * @param filterData 
 * @param props 
 * @param defaultValue 
 */
export function enrolledCell(filterData: {[key: string]: any}, props: Props, defaultValue?: any) : TableColumnProps {

    return _printNumberField(props, 'Number Of Enrolled', 'enrolled', filterData);
}

//

/**
 * To Print the Overall
 * @param filterData 
 * @param props 
 * @param defaultValue 
 */
export function overallCell(filterData: {[key: string]: any}, props: Props, defaultValue?: any) : TableColumnProps {

    return _printPercentageField(props, 'Overall (%)', 'overall', filterData);
}


/**
 * To Print the trainer_delivery
 * @param filterData 
 * @param props 
 * @param defaultValue 
 */
export function trainerDeliveryCell(filterData: {[key: string]: any}, props: Props, defaultValue?: any) : TableColumnProps {

    return _printPercentageField(props, 'Trainer Delivery (%)', 'trainer_delivery', filterData);
}

/**
 * To Print the site_visits
 * @param filterData 
 * @param props 
 * @param defaultValue 
 */
export function siteVisitsCell(filterData: {[key: string]: any}, props: Props, defaultValue?: any) : TableColumnProps {

    return _printPercentageField(props, 'Site Visits (%)', 'site_visits', filterData);
}

/**
 * To Print the facilities
 * @param filterData 
 * @param props 
 * @param defaultValue 
 */
export function facilitiesCell(filterData: {[key: string]: any}, props: Props, defaultValue?: any) : TableColumnProps {

    return _printPercentageField(props, 'Facilities (%)', 'facilities', filterData);
}

/**
 * To Print the response_rate
 * @param filterData 
 * @param props 
 * @param defaultValue 
 */
export function responseRateCell(filterData: {[key: string]: any}, props: Props, defaultValue?: any) : TableColumnProps {

    return _printPercentageField(props, 'Response Rate (%)', 'response_rate', filterData);
}

/**
 * To Print the facilities
 * @param filterData 
 * @param props 
 * @param defaultValue 
 */
export function adminCell(filterData: {[key: string]: any}, props: Props, defaultValue?: any) : TableColumnProps {

    return _printPercentageField(props, 'Admin (%)', 'admin', filterData);
}

/**
 * To Print the trainer_delivery
 * @param filterData 
 * @param props 
 * @param defaultValue 
 */
export function contentRelevanceCell(filterData: {[key: string]: any}, props: Props, defaultValue?: any) : TableColumnProps {

    return _printPercentageField(props, 'Content Relevance (%)', 'content_relevance', filterData);
}


/**
 * To Print the No of Trainee
 * @param filterData 
 * @param props 
 * @param defaultValue 
 */
export function noOfTraineeCell(filterData: {[key: string]: any}, props: Props, defaultValue?: any) : TableColumnProps {

    return _printNumberField(props, 'Class Size', 'no_of_trainee', filterData);
}

/**
 * To Print the No of Absentee
 * @param filterData 
 * @param props 
 * @param defaultValue 
 */
export function noOfAbsenteeCell(filterData: {[key: string]: any}, props: Props, defaultValue?: any) : TableColumnProps {

    return _printNumberField(props, 'Number of Absentee(s)', 'no_of_absantee', filterData);
}

/**
 * To Print the No of Absentee
 * @param filterData 
 * @param props 
 * @param defaultValue 
 */
export function noOfFailureCell(filterData: {[key: string]: any}, props: Props, defaultValue?: any) : TableColumnProps {

    return _printNumberField(props, 'No of Failure(s)', 'no_of_failure', filterData);
}

/**
 * Change Course Run Status
 * @param filterData 
 * @param props 
 * @param refreshTable 
 */
export function changeCourseRunStatusCell(filterData: {[key: string]: any}, props: Props, refreshTable: Function): TableColumnProps {
    
    return {
        columnTitle: 'Change Status',
        dataField: 'id',
        dataAlign: dataAlign,
        export: false,
        dataSort: false,              
        title: 'Status',
        width: "120px",         
        dataFormat: (cell: any, row: any, formatExtraData: any, rowIndex: number) => {

            return <ActionChangeStatus 
            cell={cell} row={row} 
            formatExtraData={formatExtraData} 
            rowIndex={rowIndex} 
            refreshTable={refreshTable}
            {...props} />
        }   
    }
}
/**
 * Change Course Run Status
 * @param filterData 
 * @param props 
 * @param refreshTable 
 */
export function changeDeconflictActionCell(filterData: {[key: string]: any}, props: Props, refreshTable: Function): TableColumnProps {
    
    return {
        columnTitle: 'Deconflict',
        dataField: 'id',
        dataAlign: dataAlign,
        export: false,
        dataSort: false,              
        title: 'Deconflict',
        width: "80px",         
        dataFormat: (cell: any, row: any, formatExtraData: any, rowIndex: number) => {

            return <ActionDecoflict 
            cell={cell} row={row} 
            formatExtraData={formatExtraData} 
            rowIndex={rowIndex} 
            refreshTable={refreshTable}
            {...props} />
        }   
    }
}
//
/**
 * Course Run Action Button view and Delete
 * @param filterData 
 * @param props 
 * @param refreshTable 
 * @param pageType 
 */
export function courseRunActionCell(filterData: {[key: string]: any}, props: Props, refreshTable: Function, deleteFor: 'COURSE_RUN' | 'COURSE_SUMMARY', endPoint?: ApiEndPointI, showDelete?: boolean, atPage?: 'COURSE_RUN' | 'COURSE_SUMMARY' | 'MAINTAIN_COURSE' ): TableColumnProps {
    
    return {
        columnTitle: 'Action',
        dataField: 'id',
        dataAlign: dataAlign,
        export: false,
        dataSort: false,              
        title: 'Action',
        width: deleteFor === 'COURSE_RUN' ? "85px" : '60px',       
        dataFormat: (cell: any, row: any, formatExtraData: any, rowIndex: number) => {

            return <ActionCell 
            cell={cell} row={row} 
            atPage={atPage}
            showDelete={showDelete === undefined ? true :  showDelete}
            deleteFor={deleteFor}
            formatExtraData={formatExtraData} 
            rowIndex={rowIndex}
            listEndPoint={endPoint}
            refreshTable={refreshTable}
            {...props} />
        }
    }
}


/**
 * To Print the Course run remark
 */
export function courseRunRemark(filterData: {[key: string]: any}, props: Props, dataField?: string ) : TableColumnProps {
    var dataFieldName:string = dataField ? dataField : 'remarks';
    return _printTextField('Remarks',dataFieldName, filterData, '150px', true, undefined, {
        dataFormat: (cell: any, row: any, formatExtraData: any, rowIndex: number) => {
            return <ReadMore key={`title_in_row${row.id}`} data={row[dataFieldName] ? row[dataFieldName].toString() : '' } minChar={65} />
        },
        dataSort: false
    });
}