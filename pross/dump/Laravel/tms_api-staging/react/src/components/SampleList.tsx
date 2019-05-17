import * as React from 'react';
import {Props, mapDispatchToProps, mapStateToProps, ServerResponse} from '../../features/root-props';
import Template from '../layout/Template';
import Table , {TableColumnProps, TableArchitecture} from '../../plugins/Table';
import  {connect } from 'react-redux';
import * as bs from 'react-bootstrap';
import API from '../../aep';
import { DataAlignType, PaginationPostion } from 'react-bootstrap-table';
import { Button } from 'react-bootstrap';
var FAS = require('react-fontawesome');
import moment from 'moment';
import Upload from '../../plugins/Upload';

class CreateCourseList extends React.Component <Props> {
    
    private table: null | TableArchitecture;

    constructor(props: Props) {
        super(props);
        this.actionBtn = this.actionBtn.bind(this);
        this.refreshTable = this.refreshTable.bind(this);
    }
     /**
     *  Listing the column name, which will display on the web page.
     */
    getColumns(filterData: {[key: string]: any} | null): TableColumnProps[] {
        
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
            {
                columnTitle: 'Category',
                dataField: 'prog_category_name',
                dataAlign: dataAlign,
                filter: { type: 'TextFilter', delay: 1000, defaultValue: filterData &&  filterData.prog_category_name ? filterData.prog_category_name.value: null },
                export: false,
                dataSort: true,              
                title: 'Category',
                width: "80px",           
            },
            {
                columnTitle: 'Course Code',
                dataField: 'course_code',
                dataAlign: dataAlign,
                filter: { type: 'TextFilter', delay: 1000, defaultValue: filterData &&  filterData.course_code ? filterData.course_code.value: null },
                export: false,
                dataSort: true,              
                title: 'Course Code',
                width: "80px",           
            },
            {
                columnTitle: 'Course Title',
                dataField: 'course_title',
                dataAlign: dataAlign,
                filter: { type: 'TextFilter', delay: 1000, defaultValue: filterData &&  filterData.course_title ? filterData.course_title.value: null },
                export: false,
                dataSort: true,              
                title: 'Course Title',
                width: "130px",  
                dataFormat: (cell: any, row: any, formatExtraData: any, rowIndex: number): string | React.ReactElement<any> => {
                  return this.table ? this.table.displayLessMoreAction(cell, formatExtraData, 65) : cell;
               },            
            },
            {
                columnTitle: 'Duration',
                dataField: 'duration_in_days',
                dataAlign: dataAlign,
                filter: { 
                    type: 'NumberFilter', 
                    defaultValue: {
                        number: filterData &&  filterData.duration_in_days ? filterData.duration_in_days.value: undefined,
                        comparator: filterData &&  filterData.duration_in_days ? filterData.duration_in_days.comparator: undefined
                    }
                },                
                export: false,
                dataSort: true,              
                title: 'Duration',
                width: "130px",            
            },
            {
                columnTitle: 'Programme Type',
                dataField: 'prog_type_name',
                dataAlign: dataAlign,
                filter: { type: 'TextFilter', delay: 1000, defaultValue: filterData &&  filterData.prog_type_name ? filterData.prog_type_name.value: null },
                export: false,
                dataSort: true,              
                title: 'Programme Type',
                width: "80px",           
            },
            {
                columnTitle: 'Cost Per Pax',
                dataField: 'cost_per_pax',
                dataAlign:dataAlign,
                filter: { 
                    type: 'NumberFilter', 
                    defaultValue: {
                        number: filterData &&  filterData.cost_per_pax ? filterData.cost_per_pax.value: undefined,
                        comparator: filterData &&  filterData.cost_per_pax ? filterData.cost_per_pax.comparator: undefined
                    }
                },
                export: false,
                dataSort: true,              
                title: 'Cost Per Pax',
                width: "130px",           
            },
            {
                columnTitle: 'Action',
                dataField: 'course_title',
                dataAlign: dataAlign,
                filter: { type: 'TextFilter', delay: 1000, defaultValue: filterData &&  filterData.course_title ? filterData.course_title.value: null },
                export: false,
                dataSort: true,              
                title: 'Action',
                width: "130px",  
                dataFormat: this.actionBtn 
            },
        ]
    }

    actionBtn(cell: any, row: any, formatExtraData: any, rowIndex: number): string | React.ReactElement<any> {
        
        return (
            <div>

              <Button onClick={(e) => { 
                  this.props.history.push('/event/'+row.id);
  
               }} bsStyle="info" title="Course Detail" >
                <FAS name="eye" />
              </Button>
              <Button bsStyle="warning" title="Delete Event" onClick={(e) => { this.deleteConfirm(e, row); }} >
                <FAS name="trash" />
              </Button>
            </div>
           );
    }

    /**
     * To delete the Event
     * @param e 
     * @param row 
     */
    deleteConfirm(e: any, row: {[key: string]: any} ) {

        this.props.swal.confirm('Are you sure you wnat to delete the Coutrse ?', () => {
            this.props.swal.wait('Deleting...');
            let endPoint = {...API.COURSE_ACTION};
            endPoint.method = 'DELETE';
            endPoint.url += '/'+row.id;
            this.props.callApi(endPoint)
                .then(() => {

                    this.props.swal.success('Course has been Deleted Successfully.');
                    this.refreshTable();

                }).catch((resposne: ServerResponse) => {

                    this.props.swal.error(resposne.message? resposne.message : 'Server Error.' );
                }) 
        })
    }

    shouldComponentUpdate(nextProps: Props) {

        return this.props.helper.shouldUpdate(nextProps, this.props, 'course');
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
            {title: 'Course List', url: '/course'},   
        ];
        
        const auth = this.props.helper.deepFind(this.props.rootState,'server.auth_user.response.data');

        const defaultFilters = {
            // customFilters: {
            //     year: moment().format('YYYY')
            // },
            sortName: 'course_title',
            sortOrder: 'asc',
        };

        const customFilters: {[key: string]: any} | null =  this.props.helper.deepFind(this.props.rootState, 'server.course.requestData.customFilters');


        return (
            <Template {...this.props} breadcrumb={breadcrumbs} >
                <Upload {...this.props} endPoint={API.COURSE_ACTION} afterUploadSuccess={this.refreshTable} />
                <Table {...this.props} 
                columns={this.getColumns(customFilters)} 
                exportFileName="event.xlsx"
                ref={(table) => { this.table = table }}
                endPoint={API.COURSE_LIST} 
                defaultData={defaultFilters}
                search={false}
                paginationPosition={defaultPaginationPos} />            		

            </Template>
        )
    }
}

export default connect(mapStateToProps, mapDispatchToProps)(CreateCourseList)