import * as React from 'react';
import {Props, mapDispatchToProps, mapStateToProps,ListRequest, ServerResponse} from '../../features/root-props';
import Template from '../layout/Template';
import Table , {TableColumnProps, TableProps, TableArchitecture, getSelectFilter, getDateFilter, getDateRangeFilter} from '../../plugins/Table';
import  {connect } from 'react-redux';
import * as bs from 'react-bootstrap';
import API, {ApiEndPointI} from '../../aep';
import { DataAlignType, PaginationPostion } from 'react-bootstrap-table';
import { Button } from 'react-bootstrap';
var FAS = require('react-fontawesome');
import moment from 'moment';
import Upload from '../../plugins/Upload';
import ChangeStatus from './ChangeStatus';
import Delete from './Delete';
import UploderMessage from '../layout/UploderMessage';
import DownloadBtn from '../layout/DownloadSampleBtn';
import { bool } from 'prop-types';
//import * as PlacementColumns from './Columns'
import postCourseRunCol from './columns/PostCourseRunColumns';
import placementCol from './columns/PlacementColumns';
import placementReportCol from './columns/PlacementDataReport';
import myPlacementCol from './columns/MyPlacementColumns';
import mySubPlacementCol from './columns/MySubordinatePlacementColumns';
import courseRunPlacementCol, {viewerColumn} from './columns/PlacementOfCourseRunColumns';

export interface PlacementTableProps extends Props {
    showBatchDelete?: boolean;
    showExportBtn?: boolean;
    showSelectedRow?: boolean;
    batchDeleteEndPoint?: ApiEndPointI;
    whoViewed?: 'Admin' | 'Viewer'
    initialFilters?: Array<'LOGIN_USER' | 'COURSE_RUN_ID' | 'RESULT_UPLOADED'>;
    displayForCourseRunId?: string;
    pageFor: PageType;
    whereFrom?: 'maintain-list'; 
    endPoint: ApiEndPointI;
    afterPageDropDown?: Array<React.ComponentType<{requestData: ListRequest, rootProps: TableProps }>>;
}
export enum PageType  {'PLACEMENT','PLACEMENT_REPORT', 'OF_COURSE_RUN_ID','POST_COURSE_RUN_DATA', 
'MY_PLACEMENT' ,'MY_SUBORDINATE_PLACEMENT'};
class PlacementTable extends React.Component <PlacementTableProps> {
    
    private EndPoint: ApiEndPointI;

    constructor(props: PlacementTableProps) {

        super(props);
        this.EndPoint = {...this.props.endPoint};
        this.refreshTable = this.refreshTable.bind(this);
    }
    
     /**
     *  Listing the column name, which will display on the web page.
     */
    getColumns(filterData: {[key: string]: any} ): TableColumnProps[] {

        const auth_user = this.props.helper.deepFind(this.props.rootState.server,'auth_user.response.data', {});
        
        if(this.props.pageFor === PageType.POST_COURSE_RUN_DATA) {

            return postCourseRunCol(filterData, this.props, this.refreshTable)
        }
        else if(this.props.pageFor  === PageType.PLACEMENT) {

            return placementCol(filterData, this.props, this.refreshTable)
        }
        else if(this.props.pageFor  === PageType.PLACEMENT_REPORT) {

            return placementReportCol(filterData, this.props, this.refreshTable)
        }
        else if(this.props.pageFor  === PageType.MY_PLACEMENT) {

            return myPlacementCol(filterData, this.props, this.refreshTable)
        }
        else if(this.props.pageFor  === PageType.MY_SUBORDINATE_PLACEMENT) {

            return mySubPlacementCol(filterData, this.props, this.refreshTable)
        }
        else if(this.props.pageFor  === PageType.OF_COURSE_RUN_ID) {

            if(auth_user.view_as !== 'Viewer') {
                return courseRunPlacementCol(filterData, this.props, this.refreshTable, this.props.displayForCourseRunId, this.props.whereFrom)
            }
            else {
                return viewerColumn(filterData, this.props, this.refreshTable, this.props.displayForCourseRunId)
            }
        }
        
        else {

            return []
        }
    }
    
    refreshTable() {
        
        this.props.callApi(this.EndPoint);
    }

    shouldComponentUpdate(nextProps: PlacementTableProps) {
        //console.log('this.EndPoint.sectionName');
        //console.log(this.EndPoint.sectionName);
        return this.props.helper.shouldUpdate(nextProps, this.props, [this.EndPoint.sectionName,'options']);
    }
    
    /**
     * Prepare the Initial Filters
     */
    getInitialFilters() {

        const {initialFilters, displayForCourseRunId} = this.props;
        const auth = this.props.helper.deepFind(this.props.rootState,'server.auth_user.response.data');

        var defaultFilters: {[key: string]:  any} = {
            customFilters: {},
            sortName: 'title',
            sortOrder: 'asc',
        };

        if( initialFilters && initialFilters.indexOf('LOGIN_USER') !== -1 ) {

            defaultFilters.customFilters.officer = {
                value: auth.id,
                comparator: '='
            }
        }

        if(initialFilters && initialFilters.indexOf('COURSE_RUN_ID') !== -1  && displayForCourseRunId){

            defaultFilters.customFilters.course_run_id = {
                value: displayForCourseRunId,
                comparator: '='
            }
        }
        if(initialFilters && initialFilters.indexOf('RESULT_UPLOADED') !== -1 ){

            defaultFilters.customFilters.result_uploaded = {
                value: 'Yes',
                comparator: '='
            }
        }

        return defaultFilters;
    }

    render() {

        const {showExportBtn, batchDeleteEndPoint, showBatchDelete, showSelectedRow, afterPageDropDown} = this.props;

        const defaultPaginationPos: PaginationPostion = 'bottom';       

        const customFilters: {[key: string]: any}  =  this.props.helper.deepFind(this.props.rootState, 'server.'+this.EndPoint.sectionName+'.requestData.customFilters');
        console.log('batchDeleteEndPoint');
        console.log(batchDeleteEndPoint);

        return (
           
                <Table {...this.props} 
                columns={this.getColumns(customFilters)} 
                batchDeleteBtn={showBatchDelete}
                exportFileName={showExportBtn === undefined || showExportBtn ? 'placements.xlsx': undefined}
                endPoint={this.EndPoint} 
                batchDeleteEndPoint={batchDeleteEndPoint}
                defaultData={this.getInitialFilters()}
                search={false}
                afterPageDropDown={afterPageDropDown}
                showSelectColumn={showSelectedRow}
                paginationPosition={defaultPaginationPos} />            		

        )
    }
}

export default connect(mapStateToProps, mapDispatchToProps)(PlacementTable)