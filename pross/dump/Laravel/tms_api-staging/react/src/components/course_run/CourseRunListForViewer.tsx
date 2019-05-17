import * as React from 'react';
import PropTypes from 'prop-types';
import {Props, mapDispatchToProps, mapStateToProps, ServerResponse} from '../../features/root-props';
import Template from '../layout/Template';
import Table , {TableColumnProps, TableArchitecture, getSelectFilter, getDateRangeFilter} from '../../plugins/Table';
import DateFilter from '../../plugins/TableFilters/DateFilter';
import * as ReactDOM from 'react-dom';
import  {connect } from 'react-redux';
import * as bs from 'react-bootstrap';
import API from '../../aep';
import { DataAlignType, PaginationPostion } from 'react-bootstrap-table';
import { Button } from 'react-bootstrap';
var FAS = require('react-fontawesome');
import moment from 'moment';
import Upload from '../../plugins/Upload';
import {actions as RootAction} from '../../features/root-action';
import {ApiEndPointI} from '../../aep';
import UploderMessage from '../layout/UploderMessage';
import DownloadBtn from '../layout/DownloadSampleBtn';
import cols from './columns/CourseRunViewerColumn';

interface RunCourseListProps extends Props {
    
    whoViewed?: 'Admin' | 'Viewer'
}

//export enum PageType  {'Create','Update','Status'};

class List extends React.Component <RunCourseListProps> {
    
    private table: null | TableArchitecture;
    private EndPoint: ApiEndPointI;

    constructor(props: RunCourseListProps) {
        
        super(props);
        this.refreshTable = this.refreshTable.bind(this);
        this.EndPoint = {...API.COURSE_RUN_ACTIVE_LIST}
    }

    shouldComponentUpdate(nextProps: Props) {

        return this.props.helper.shouldUpdate(nextProps, this.props, [this.EndPoint.sectionName,'options']);
    }


    /**
     * TO Refresh The Table
     */
    refreshTable() {
        
        this.props.callApi(this.EndPoint);
    }
    render() {
        
        const defaultPaginationPos: PaginationPostion = 'bottom';
        const breadcrumbs = [
            {
                title: 'Show All Course Run',
                url: '/all-course-run'
            }
        ];
        
        const auth = this.props.helper.deepFind(this.props.rootState,'server.auth_user.response.data');

        const defaultFilters = {
            sortName: 'course_title',
            sortOrder: 'asc',
        };

        const customFilters: {[key: string]: any} =  this.props.helper.deepFind(this.props.rootState, 'server.'+this.EndPoint.sectionName+'.requestData.customFilters');
        

        return (
            <Template {...this.props} 
                breadcrumb={breadcrumbs} >         

                <Table {...this.props} 
                batchDeleteBtn={false}
                columns={cols(customFilters, this.props, this.refreshTable)} 
                exportFileName="course_runs.xlsx"
                endPoint={this.EndPoint}
                defaultData={defaultFilters}
                search={false}
                showSelectColumn={true}
                paginationPosition={defaultPaginationPos} />           		

            </Template>
        )
    }
}

export default connect(mapStateToProps, mapDispatchToProps)(List)