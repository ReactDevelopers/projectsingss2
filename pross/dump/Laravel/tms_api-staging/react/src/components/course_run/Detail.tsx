import * as React from 'react';
import {Props, mapDispatchToProps, mapStateToProps, ServerResponse} from '../../features/root-props';
import Template from '../layout/Template';
import API from '../../aep';
import  {connect } from 'react-redux';
import {actions as RootActions} from '../../features/root-action';
import ChangeStatus from '../placement/ChangeStatus';
import Delete from '../placement/Delete';
// import {PageType} from '../placement/List';

import PlacementTable , {PlacementTableProps, PageType}  from '../placement/PlacementTable';
import MakeBatchConfirm from '../placement/MakeBatchConfirm';

import { 
    BootstrapTable, 
    SelectRowMode,
    ButtonGroupProps, 
    TableHeaderColumn, 
    ColumnDescription,
    CustomFilter,
    SortOrder,
    FilterData,
    SelectFilterOptionsType,
    SearchFieldProps,
    TableHeaderColumnProps, 
    PaginationPostion } from 'react-bootstrap-table';

interface DetailProps extends Props {
    whereFrom?: 'maintain-list';
}
class Detail extends React.Component<DetailProps> {
    
    private CourseId: string;

    constructor(props: DetailProps) {
        super(props);

        const params = this.props.params();
        this.CourseId = params[params.length-1];
        this.getData  = this.getData.bind(this);
    }

    shouldComponentUpdate(nextProps: DetailProps) {

        return this.props.helper.shouldUpdate(nextProps, this.props, [API.COURSE_RUN_DETAIL.sectionName]);
    }

    componentWillMount(){
        this.getData();
    }
    componentWillUnmount() {

        this.props.dispatch(RootActions.fetch.deleteResponse(API.COURSE_RUN_DETAIL));
    }
    /**
     * To Get The detail from Server
     */
    getData() {

        let endPoint = {...API.COURSE_RUN_DETAIL};
        endPoint.url +='/'+this.CourseId;
        this.props.callApi(endPoint)
            .then((response: ServerResponse) => {
        })
    }

    render() {
        const auth_user = this.props.helper.deepFind(this.props.rootState.server,'auth_user.response.data', {});
        var breadcrumbs = [];
        if(this.props.whereFrom === 'maintain-list') { 
            
            breadcrumbs = [
                {title: 'Maintain Course Run', url: '/maintain-course-run'},
                {title: 'Course Run Detail', url: '/maintain-course-run/'+ this.CourseId},   
            ];
            
        } else {

            breadcrumbs = [
                {title: auth_user.view_as === 'Viewer' ? 'Show All Course Run' : 'Create/Update Course Run', url: auth_user.view_as === 'Viewer' ? '/all-course-run':  '/course-run'},
                {title: 'Course Run Detail', url: '/course-run/'+ this.CourseId},   
            ];
        }

        const {helper } = this.props;

        const data: {[key:string]: any} =  helper.deepFind(this.props.rootState.server, API.COURSE_RUN_DETAIL.sectionName+'.response.data',{});

        var endPoint = {...API.PLACEMENT_LIST_OF_A_COURSE_RUN};
        endPoint.url += '/'+this.CourseId;
        endPoint.sectionName += '.'+this.CourseId;

        let placementDeleteEndPoint = {...API.PLACEMENT_ACTION};
        placementDeleteEndPoint.method = 'DELETE';

        const isFetching =  helper.deepFind(this.props.rootState.server,API.COURSE_RUN_DETAIL.sectionName+'.isFetching',true);

        return(
            <Template {...this.props} breadcrumb={breadcrumbs} >
                <div className="head-section light-blue-border border-12 dark-blue-bg">
                    <h5>{data.course_code? data.course_code : 'loading...' }</h5>
                    <h4>{data.course_title ? data.course_title : 'loading...'}</h4>
                </div>
                <div className="light-blue-border border-12 white-bg p-20">
                    <table className="table table-structure table-td-50">   
                        <tbody> 
                        <tr><td><label>Course Run Id: </label></td><td>{!isFetching ? data.id: 'loading..'}</td></tr>
                        <tr><td><label>Category: </label></td><td>{!isFetching ? data.prog_category_name: 'loading..'}</td></tr>
                        <tr><td><label>Course Code: </label></td><td>{!isFetching ? data.course_code : 'loading..'}</td></tr>
                        <tr><td><label>Course Title: </label></td><td>{!isFetching ? data.course_title: 'loading...'}</td></tr>
                        {/* <tr><td><label>Duration (No of Days)</label></td><td>{!isFetching ? data.duration_in_days :'loading...'}</td></tr>
                        <tr><td><label>Programme Type</label></td><td>{!isFetching ? data.prog_type_name: 'loading...'}</td></tr>
                        <tr><td><label>Dept/ Competency Level (if applicable)</label></td> <td>{!isFetching ? data.dept_name: 'loading...'}</td></tr>
                        <tr><td><label>Assessment Type</label></td><td>{!isFetching ? data.assessment_type_name : 'loading...'}</td></tr>
                        <tr><td><label>Mandatory Y/N</label></td><td>{!isFetching ? data.mandatory : 'loading...'}</td></tr>
                        <tr><td><label>Delivery Method</label></td><td>{!isFetching ? data.delivery_method : 'loading...'}</td></tr>
                        <tr><td><label>Training Location</label></td><td>{!isFetching ? data.training_location_name : 'loading...'}</td></tr>
                        <tr><td><label>Course Provider</label></td><td>{!isFetching ? data.course_provider_name : 'loading...'}</td></tr>
                        <tr><td><label>Cost Per Pax: </label></td><td>{!isFetching ? data.cost_per_pax : 'loading...'}</td></tr>
                        <tr><td><label>Grant/Susbsidy (Yes/No): </label></td><td>{!isFetching ? data.subsidy: 'loading...'}</td></tr>
                        <tr><td><label>If yes, provide value ($): </label></td><td>{!isFetching ? data.subsidy_value: 'loading...'}</td></tr>
                        <tr><td><label>Vendor Contact/Email Account: </label></td><td>{!isFetching ? data.vendor_email : 'loading...'}</td></tr> */}
                        <tr><td><label>Start date: </label></td><td>{!isFetching ? helper.displayDate(data.start_date) : 'loading...'}</td></tr>
                        <tr><td><label>End date: </label></td><td>{!isFetching ? helper.displayDate(data.end_date) : 'loading...'}</td></tr>
                        <tr><td><label>Assessment Start date: </label></td><td>{!isFetching ? helper.displayDate(data.assessment_start_date) : 'loading...'}</td></tr>
                        <tr><td><label>Assessment End date: </label></td><td>{!isFetching ? helper.displayDate(data.assessment_end_date) : 'loading...'}</td></tr>
                        <tr><td><label>Class Size: </label></td><td>{!isFetching ? data.no_of_trainee : 'loading...'}</td></tr>
                        <tr><td><label>Enrolled Number: </label></td><td>{!isFetching ? data.enrolled : 'loading...'}</td></tr>
                        <tr><td><label>Number of Absentees: </label></td><td>{!isFetching ? data.no_of_absentees : 'loading...'}</td></tr>
                        <tr><td><label>Created By: </label></td><td>{!isFetching ? data.created_by : 'loading...'}</td></tr>
                        {auth_user.view_as !== 'Viewer' ? 
                            <tr><td><label>Exclude From Deconflict: </label></td><td>{!isFetching ? (data.should_check_deconflict === 'Yes' ? 'No' : 'Yes') : 'loading...'}</td></tr>
                        : null}
                        <tr><td><label>Remarks: </label></td><td>{!isFetching ? data.remarks : 'loading...'}</td></tr>
                        
                        </tbody>
                    </table>
                </div>
                
                <PlacementTable 
                    {...this.props} 
                    endPoint={endPoint} 
                    afterPageDropDown={auth_user.view_as === 'Viewer'? undefined : [MakeBatchConfirm]}
                    showExportBtn={false}
                    showBatchDelete={true}
                    batchDeleteEndPoint={placementDeleteEndPoint}                    
                    whereFrom={this.props.whereFrom}
                    displayForCourseRunId={this.CourseId}
                    showSelectedRow={(auth_user.view_as !== 'Viewer')}
                    pageFor={PageType.OF_COURSE_RUN_ID}
                />:
               


            </Template>
        );
    }
}

export default connect(mapStateToProps, mapDispatchToProps)(Detail)