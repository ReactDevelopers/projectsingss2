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
import DownloadBtn from '../layout/DownloadSampleBtn';
import sampleFiles from '../../SampleFiles';
import * as columns from '../layout/columns';

class List extends React.Component <Props> {
    
    private table: null | TableArchitecture;

    constructor(props: Props) {
        super(props);
        this.actionBtn = this.actionBtn.bind(this);
        this.refreshTable = this.refreshTable.bind(this);
        this.confirmChange =  this.confirmChange.bind(this);
    }
     /**
     *  Listing the column name, which will display on the web page.
     */
    getColumns(filterData: {[key: string]: any}): TableColumnProps[] {
        
        const dataAlign: DataAlignType = 'center';
        const auth = this.props.helper.deepFind(this.props.rootState,'server.auth_user.response.data');

        var dataColumns: TableColumnProps[]  = [

            {
              columnTitle: true,
              dataField: 'id',
              dataAlign: dataAlign,
              hidden: true,
              isKey: true,
              title: 'ID'
            },
            columns.default.personnelNumberCell(filterData, this.props),
            columns.default.userNameCell(filterData, this.props),
            columns.default.userEmailCell(filterData, this.props),
            columns.default.userDepartmentCell(filterData, this.props),
            columns.default.userDesignationCell(filterData, this.props),
            columns.default.userDivisionCell(filterData, this.props),
            columns.default.userRoleNameCell(filterData, this.props),
            columns.default.userSupervisporNameCell(filterData, this.props)            
        ];

        if(auth.role_id === 1) {

            dataColumns.push({
                columnTitle: 'Action',
                dataField: undefined,
                dataAlign: dataAlign,
                export: false,
                dataSort: false,              
                title: 'Change Role',
                width: "130px",  
                dataFormat: this.actionBtn 
            });
        }
        
        return dataColumns;
    }

    actionBtn(cell: any, row: any, formatExtraData: any, rowIndex: number): string | React.ReactElement<any> {
        
        const roles: Array<{[key: string]: any}> | null = this.props.helper.deepFind(this.props.rootState.server,'options.response.data.roles', null);
        
        return (
            <div>
                <select 
                    name="chane_role" 
                    className="form-control" 
                    id="change_role" 
                    onChange={(e: any) => {this.confirmChange(e, row)}}
                    defaultValue={row.role_id}>
                    {roles && roles.map(v => {
                        return <option selected={v.id === row.role_id} value={v.id}>{v.title}</option>
                    })}
                    <option value="" selected={row.role_id === null}>N/A</option>
                </select>
            </div>
           );
    }

    /**
     * To delete the Event
     * @param e 
     * @param row 
     */
    confirmChange(e: any, row: {[key: string]: any} ) {

        const target = e.target;
        const role  = target.value;

        this.props.swal.confirm('Are you sure you want to change role? ', () => {
            
                this.props.swal.wait('Deleting...');
                let endPoint = {...API.USER_CHANGE_ROLE};
                endPoint.url += '/'+row.id;

                this.props.callApi(endPoint, {role_id: role })
                    .then(() => {

                        this.props.swal.success('Role has been changed.');
                        this.refreshTable();

                    }).catch((resposne: ServerResponse) => {

                        this.props.swal.error(resposne.message? resposne.message : 'Server Error.' );
                    }) 

            },{
                onCancel: () => {
                    
                    $(target).find('option[value="'+row.role_id+'"]').prop('selected', true);
                    this.props.swal.close();
                }
            }
        )        
    }

    shouldComponentUpdate(nextProps: Props) {

        return this.props.helper.shouldUpdate(nextProps, this.props, ['users','options']);
    }

    /**
     * TO Refresh The Table
     */
    refreshTable() {

        this.props.callApi(API.USER_LIST);
    }
    render() {
        
        const defaultPaginationPos: PaginationPostion = 'bottom';
        const breadcrumbs = [
            {title: 'Staff Database', url: '/user'},   
        ];
        
        const auth = this.props.helper.deepFind(this.props.rootState,'server.auth_user.response.data');

        const defaultFilters = {
            sortName: 'name',
            sortOrder: 'asc',
        };

        const customFilters: {[key: string]: any}  =  this.props.helper.deepFind(this.props.rootState, 'server.users.requestData.customFilters');


        return (
            <Template {...this.props} breadcrumb={breadcrumbs} 
            RightSideButton={<DownloadBtn filename={[sampleFiles.supervisor]} />}
            >
                {auth.role_id == 1 ?
                <Upload {...this.props} endPoint={API.SUPERVISOR_RELATION_UPLOAD} 
                    afterUploadSuccess={this.refreshTable} 
                    message={<UploderMessage message="drop the supervisors files here to Upload"/>}
                    /> : null }
                <Table {...this.props} 
                batchDeleteBtn={false}
                columns={this.getColumns(customFilters)} 
                exportFileName="staff.xlsx"
                ref={(table) => { this.table = table }}
                endPoint={API.USER_LIST} 
                defaultData={defaultFilters}
                search={false}
                paginationPosition={defaultPaginationPos} />            		

            </Template>
        )
    }
}

export default connect(mapStateToProps, mapDispatchToProps)(List)