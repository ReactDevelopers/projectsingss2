import * as React from 'react';
import {Props, mapDispatchToProps, mapStateToProps, ServerResponse} from '../../features/root-props';
import Template from '../layout/Template';
import UploderMessage from '../layout/UploderMessage';
import Table , {TableColumnProps, TableArchitecture, getSelectFilter} from '../../plugins/Table';
import  {connect } from 'react-redux';
import * as bs from 'react-bootstrap';
import API from '../../aep';
import { DataAlignType, PaginationPostion } from 'react-bootstrap-table';
import { Button } from 'react-bootstrap';
var FAS = require('react-fontawesome');
import moment from 'moment';
import Upload from '../../plugins/Upload';
import { Link } from 'react-bootstrap/lib/Navbar';
import DownloadBtn from '../layout/DownloadSampleBtn';
import * as columns from '../layout/columns';
import sampleFiles from '../../SampleFiles';
import {actions as rootActions} from '../../features/root-action';

interface CourseListProps extends Props {
    // showAction?: boolean;
    // showUploader?: boolean;
    // showExportBtn?: boolean;
    // showSelectedRow?: boolean;
}

class List extends React.Component <CourseListProps> {
    
    private table: null | TableArchitecture;

    constructor(props: CourseListProps) {
        super(props);
        this.actionBtn = this.actionBtn.bind(this);
        this.refreshTable = this.refreshTable.bind(this);
    }
     /**
     *  Listing the column name, which will display on the web page.
     */
    getColumns(filterData: {[key: string]: any}): TableColumnProps[] {
        
        const dataAlign: DataAlignType = 'center';

        return [
            {
              columnTitle: true,
              dataField: 'id',
              dataAlign: dataAlign,
              hidden: true,
              isKey: true,
              title: 'ID'
            },
            columns.default.programmeTypeCell(filterData, this.props),
            columns.default.courseCodeCell(filterData, this.props),
            //columns.default.categoryCell(filterData, this.props),            
            columns.default.courseTitleCell(filterData, this.props, 'course_title'),
            columns.default.courseDuration(filterData, this.props),
            
            {
                columnTitle: 'Action',
                dataField: 'id',
                dataAlign: dataAlign,
                export: false,
                dataSort: false,              
                title: 'Action',
                width: "40px", 
                dataFormat: this.actionBtn 
            },
        ]
    }

    actionBtn(cell: any, row: any, formatExtraData: any, rowIndex: number): string | React.ReactElement<any> {
        
        const auth = this.props.helper.deepFind(this.props.rootState,'server.auth_user.response.data');

        return (
            <div>

              <Button onClick={(e) => { 
                  this.props.history.push('/course/'+row.id);
  
               }} bsStyle="info" title="Course Detail" >
                <FAS name="eye" />
              </Button>
              {auth.role_id == 1 ?
              <Button bsStyle="warning" title="Delete Course" onClick={(e) => { this.deleteConfirm(e, row); }} >
                <FAS name="trash" />
              </Button> : null }
            </div>
           );
    }

    /**
     * To delete the Event
     * @param e 
     * @param row 
     */
    deleteConfirm(e: any, row: {[key: string]: any} ) {

        this.props.swal.confirm('Are you sure you want to delete the course ?', () => {
            this.props.swal.wait('Deleting...');
            let endPoint = {...API.COURSE_ACTION};
            endPoint.method = 'DELETE';
            endPoint.url += '/'+row.id;
            this.props.callApi(endPoint)
                .then(() => {

                    this.props.dispatch(rootActions.fetch.removeSelectedRow(API.COURSE_LIST, [row.id]));
                    this.props.swal.success('Course has been deleted successfully.');
                    this.refreshTable();

                }).catch((resposne: ServerResponse) => {

                    this.props.swal.error(resposne.message? resposne.message : 'Server Error.' );
                }) 
        })
    }

    shouldComponentUpdate(nextProps: Props) {

        return this.props.helper.shouldUpdate(nextProps, this.props, ['course','options']);
    }

    /**
     * TO Refresh The Table
     */
    refreshTable() {

        this.props.callApi(API.COURSE_LIST);
    }
    render() {
        
        const defaultPaginationPos: PaginationPostion = 'bottom';
        const breadcrumbs = [
            {title: 'Maintain Course Directory', url: '/course'},   
        ];
        
        const auth = this.props.helper.deepFind(this.props.rootState,'server.auth_user.response.data');

        const defaultFilters = {
            
            sortName: 'course_title',
            sortOrder: 'asc',
        };

        const customFilters: {[key: string]: any}  =  this.props.helper.deepFind(this.props.rootState, 'server.course.requestData.customFilters');
        
        
        return (
            <Template {...this.props} 
            RightSideButton={<DownloadBtn filename={[sampleFiles.course]} />}
            breadcrumb={breadcrumbs} >

                {auth.role_id == 1 ?
                <Upload {...this.props} endPoint={API.COURSE_UPLOAD} 
                    afterUploadSuccess={this.refreshTable} 
                    message={<UploderMessage message="Drop the Course files here to Upload"/>}
                    /> : null}

                <Table {...this.props} 
                batchDeleteBtn={true}
                columns={this.getColumns(customFilters)} 
                exportFileName="courses.xlsx"
                endPoint={API.COURSE_LIST} 
                defaultData={defaultFilters}
                batchDeleteEndPoint={API.COURSE_BATCH_DELETE}
                search={false}
                showSelectColumn={true}
                paginationPosition={defaultPaginationPos} />            		

            </Template>
        )
    }
}

export default connect(mapStateToProps, mapDispatchToProps)(List)