import * as columns from '../../layout/columns';
import {Props} from '../../../features/root-props';
import { DataAlignType } from 'react-bootstrap-table';
const dataAlign: DataAlignType = 'center';
import {ApiEndPointI} from '../../../aep';

export default (filterData: {[key: string]: any}, props: Props, refreshTable: Function, endPoint?: ApiEndPointI) => {
    const auth = props.helper.deepFind(props.rootState,'server.auth_user.response.data');

    return [
        
        {
            columnTitle: true,
            dataField: 'id',
            hidden: true,
            isKey: true,
            title: 'id'
        },
        columns.default.primaryCell(filterData, props, 'Course Run Id'),
        columns.default.courseCodeCell(filterData, props),
        columns.default.categoryCell(filterData, props),
        columns.default.programmeTypeCell(filterData, props),
        columns.default.datesCell(filterData, props),
        {
            columnTitle: 'Month',
            dataField: 'month_year',
            dataAlign: dataAlign,
            export: false,
            dataSort: false,              
            title: 'Month',
            width: '120px',
            dataFormat: (cell: any, row: any, formatExtraData: any, rowIndex: number) => {
    
                return props.helper.displayDate(row['start_date'], 'MM/YYYY');
            }
        },
        columns.default.noOfTraineeCell(filterData, props),
        columns.default.noOfAbsenteeCell(filterData, props),
        columns.default.noOfFailureCell(filterData, props),
        columns.default.overallCell(filterData, props),
        columns.default.trainerDeliveryCell(filterData, props),
        columns.default.contentRelevanceCell(filterData, props),
        columns.default.siteVisitsCell(filterData, props),
        columns.default.facilitiesCell(filterData, props),
        columns.default.adminCell(filterData, props),
    ]
}