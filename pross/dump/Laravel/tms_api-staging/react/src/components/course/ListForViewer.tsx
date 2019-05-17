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

class List extends React.Component <Props> {
    
    private table: null | TableArchitecture;

    constructor(props: Props) {
        super(props);
        this.refreshTable = this.refreshTable.bind(this);
        this.actionBtn = this.actionBtn.bind(this);
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
        
        return (
            <div>

              <Button onClick={(e) => { 
                  this.props.history.push('/course/'+row.id);
  
               }} bsStyle="info" title="Course Detail" >
                <FAS name="eye" />
              </Button>
            </div>
           );
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
            {title: 'Show All Courses', url: '/all-course'},   
        ];
        
        const auth = this.props.helper.deepFind(this.props.rootState,'server.auth_user.response.data');

        const defaultFilters = {
            
            sortName: 'course_title',
            sortOrder: 'asc',
        };

        const customFilters: {[key: string]: any}  =  this.props.helper.deepFind(this.props.rootState, 'server.course.requestData.customFilters');
        
        
        return (
            <Template {...this.props} 
            breadcrumb={breadcrumbs} >

                <Table {...this.props} 
                batchDeleteBtn={false}
                columns={this.getColumns(customFilters)} 
                exportFileName="courses.xlsx"
                endPoint={API.COURSE_LIST_VIEWER} 
                defaultData={defaultFilters}
                search={false}
                showSelectColumn={true}
                paginationPosition={defaultPaginationPos} />            		

            </Template>
        )
    }
}

export default connect(mapStateToProps, mapDispatchToProps)(List)