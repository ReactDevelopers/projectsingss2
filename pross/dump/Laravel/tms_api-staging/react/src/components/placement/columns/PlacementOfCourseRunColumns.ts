import * as columns from '../../layout/columns';
import {Props} from '../../../features/root-props';
import {PageType} from '../PlacementTable';

export default (filterData: {[key: string]: any}, props: Props, refreshTable: Function, courseRunId?: string, whereFrom?:  'maintain-list') => {

    return [
        
        {
            columnTitle: true,
            dataField: 'id',
            hidden: true,
            isKey: true,
            title: 'ID'
        },
        columns.default.changeStatusCell(filterData, props, 'CourseRunDetail', courseRunId, refreshTable, whereFrom),
        columns.default.courseCodeCell(filterData, props),
        columns.default.courseTitleCell(filterData, props),
        columns.default.personnelNumberCell(filterData, props),
        columns.default.participantNameCell(filterData, props),
        columns.default.datesCell(filterData, props),
        columns.default.courseAssessmentDateCell(filterData, props),
        columns.default.resultCell(filterData, props),
        columns.default.userDepartmentCell(filterData, props),
        columns.default.userSupervisporNameCell(filterData, props),
        columns.default.placementActionCell(filterData, props, refreshTable, PageType.OF_COURSE_RUN_ID )
    ]
}

export function viewerColumn (filterData: {[key: string]: any}, props: Props, refreshTable: Function, courseRunId?: string) {

    return [
        
        // {
        //     columnTitle: true,
        //     dataField: 'id',
        //     hidden: true,
        //     isKey: true,
        //     title: 'ID'
        // },
        // columns.default.participantNameCell(filterData, props),
        // columns.default.placementStatusCell(filterData, props)
        {
            columnTitle: true,
            dataField: 'id',
            hidden: true,
            isKey: true,
            title: 'ID'
        },
        columns.default.courseCodeCell(filterData, props),
        columns.default.courseTitleCell(filterData, props),
        columns.default.personnelNumberCell(filterData, props),
        columns.default.participantNameCell(filterData, props),
        columns.default.datesCell(filterData, props),
        columns.default.courseAssessmentDateCell(filterData, props),
        columns.default.placementStatusCell(filterData, props),
        columns.default.resultCell(filterData, props),
        columns.default.userDepartmentCell(filterData, props),
        columns.default.userSupervisporNameCell(filterData, props),
    ]
}