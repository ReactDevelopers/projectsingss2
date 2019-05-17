import * as columns from '../../layout/columns';
import {Props} from '../../../features/root-props';
import {PageType} from '../PlacementTable';

export default (filterData: {[key: string]: any}, props: Props, refreshTable: Function) => {

    return [
        
        {
            columnTitle: true,
            dataField: 'id',
            hidden: true,
            isKey: true,
            title: 'ID'
        },
        columns.default.courseRunIdCell(filterData, props),
        columns.default.courseTitleCell(filterData, props),
        columns.default.personnelNumberCell(filterData, props),
        columns.default.participantNameCell(filterData, props),
        columns.default.courseDepartmentCell(filterData, props),
        columns.default.userDivisionCell(filterData, props),
        columns.default.userBranchCell(filterData, props),
        columns.default.datesCell(filterData, props),
        columns.default.attendanceCell(filterData, props),
        columns.default.resultCell(filterData, props),
        columns.default.absentReasonCell(filterData, props),
        columns.default.failureReasonCell(filterData, props),
    ]
}