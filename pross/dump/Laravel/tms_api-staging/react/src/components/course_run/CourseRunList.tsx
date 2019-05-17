import * as React from 'react';
import PropTypes from 'prop-types';
import {Props, mapDispatchToProps, mapStateToProps, ServerResponse, ListRequest} from '../../features/root-props';
import Template from '../layout/Template';
import Table , {TableColumnProps, TableArchitecture, getSelectFilter, getDateRangeFilter, TableProps} from '../../plugins/Table';
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
import createCourseRunCols from './columns/CreateCourseRunColumn';
import sampleFiles from '../../SampleFiles';

interface RunCourseListProps extends Props {
    
    whoViewed?: 'Admin' | 'Viewer'
}

export interface AfterPageDropDownProps  {
    requestData: ListRequest;
    rootProps: TableProps;
}

class AfterPageDropDown extends React.Component<AfterPageDropDownProps> {
    
    render () {

        return (
            <bs.Button onClick={() => {
                this.props.rootProps.history.push('/maintain-course-run');
            }} className="btn-primary export-btn">Upload Participants</bs.Button>
        )
    }
}

//export enum PageType  {'Create','Update','Status'};

class List extends React.Component <RunCourseListProps> {
    
    private table: null | TableArchitecture;
    private EndPoint: ApiEndPointI;

    constructor(props: RunCourseListProps) {
        
        super(props);
        this.refreshTable = this.refreshTable.bind(this);
        this.EndPoint = {...API.COURSE_RUN_LIST}
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
                title: 'Create/Update Course Run',
                url: '/course-run'
            }
        ];
        
        const auth = this.props.helper.deepFind(this.props.rootState,'server.auth_user.response.data');

        const defaultFilters = {
            customFilters: {
                created_by: {
                    value: auth ? auth.id : '',
                    comparator: '='
                }
            },
            sortName: 'course_title',
            sortOrder: 'asc',
        };

        const customFilters: {[key: string]: any} =  this.props.helper.deepFind(this.props.rootState, 'server.'+this.EndPoint.sectionName+'.requestData.customFilters');
        

        return (
            <Template {...this.props} 
                breadcrumb={breadcrumbs} 
                RightSideButton={<DownloadBtn filename={[sampleFiles.create_course_run,sampleFiles.update_course_run]} />}
            >   
                <div className="row">
                    <div className="col-md-6">
                        <Upload {...this.props} 
                            endPoint={API.COURSE_RUN_CREATE} 
                            afterUploadSuccess={this.refreshTable}
                            message={<UploderMessage message="Drop Create Course Run file here"/>}
                            /> 
                    </div>

                    <div className="col-md-6">
                        <Upload {...this.props} 
                            endPoint={API.COURSE_RUN_UPDATE} 
                            afterUploadSuccess={this.refreshTable}
                            message={<UploderMessage message="Drop Update Course Run file here"/>}
                            /> 
                    </div>
                </div>       

                <Table {...this.props} 
                batchDeleteBtn={true}
                afterPageDropDown={[AfterPageDropDown]}
                columns={createCourseRunCols(customFilters, this.props, this.refreshTable, this.EndPoint)} 
                exportFileName="course_runs.xlsx"
                endPoint={this.EndPoint}
                batchDeleteEndPoint={API.COURSE_RUN_BATCH_DELETE}
                defaultData={defaultFilters}
                search={false}
                showSelectColumn={true}
                paginationPosition={defaultPaginationPos} />            		

            </Template>
        )
    }
}

export default connect(mapStateToProps, mapDispatchToProps)(List)