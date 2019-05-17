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
        columns.default.categoryCell(filterData, props),
        columns.default.courseTitleCell(filterData, props),
        columns.default.participantNameCell(filterData, props),
        columns.default.datesCell(filterData, props),
        columns.default.placementStatusCell(filterData, props),
        columns.default.changeStatusCell(filterData, props, 'PlacemnetList'),
        columns.default.placementActionCell(filterData, props, refreshTable, PageType.PLACEMENT )
    ]
}