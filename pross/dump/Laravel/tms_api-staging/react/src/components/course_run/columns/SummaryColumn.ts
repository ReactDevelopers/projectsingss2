import * as columns from '../../layout/columns';
import {Props} from '../../../features/root-props';
import {ApiEndPointI} from '../../../aep';

export default (filterData: {[key: string]: any}, props: Props, refreshTable: Function, endPoint?: ApiEndPointI) => {
    const auth = props.helper.deepFind(props.rootState,'server.auth_user.response.data', {});

    return [
        
        {
            columnTitle: true,
            dataField: 'id',
            hidden: true,
            isKey: true,
            title: 'id'
        },
        columns.default.primaryCell(filterData, props, 'Course Run Id'),
        columns.default.courseTitleCell(filterData, props),
        columns.default.overallCell(filterData, props),
        columns.default.trainerDeliveryCell(filterData, props),
        columns.default.contentRelevanceCell(filterData, props),
        columns.default.siteVisitsCell(filterData, props),
        columns.default.facilitiesCell(filterData, props),
        columns.default.adminCell(filterData, props),
        columns.default.responseRateCell(filterData, props),
        columns.default.courseRunStatusCell(filterData, props),
        columns.default.creatorCell(filterData, props, auth.id),
        columns.default.courseRunActionCell(filterData, props, refreshTable, 'COURSE_SUMMARY', endPoint)
    ]
}