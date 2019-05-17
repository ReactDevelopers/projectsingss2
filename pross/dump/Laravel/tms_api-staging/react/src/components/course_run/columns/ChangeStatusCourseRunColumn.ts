import * as columns from '../../layout/columns';
import {Props} from '../../../features/root-props';
import {ApiEndPointI} from '../../../aep';

export default (filterData: {[key: string]: any}, props: Props, refreshTable: Function, endPoint?: ApiEndPointI ) => {
    
    const auth = props.helper.deepFind(props.rootState,'server.auth_user.response.data', {});

    return [
        
        {
            columnTitle: true,
            dataField: 'id',
            hidden: true,
            isKey: true,
            title: 'Course Run ID'
        },
        columns.default.primaryCell(filterData, props, 'Course Run Id'),
        columns.default.programmeTypeCell(filterData, props),
        columns.default.courseCodeCell(filterData, props),
        columns.default.courseTitleCell(filterData, props),
        columns.default.courseDuration(filterData, props),
        columns.default.datesCell(filterData, props),
        columns.default.courseAssessmentDateCell(filterData, props),
        columns.default.noOfTraineeCell(filterData, props),        
        columns.default.enrolledCell(filterData, props),
        columns.default.noOfAbsenteeCell(filterData, props),
        columns.default.creatorCell(filterData, props, auth.id),
        columns.default.changeCourseRunStatusCell(filterData, props, refreshTable),
        //columns.default.courseRunActionCell(filterData, props, refreshTable)
    ]
}