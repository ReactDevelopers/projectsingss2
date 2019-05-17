import * as React from 'react';
import {Props, ServerResponse } from '../../../features/root-props';
import courseRun from '../../../aep/courseRun';
import API, {ApiEndPointI} from '../../../aep';

export interface ChangeStatusProps extends Props {
    cell: any;
    formatExtraData: any;
    row: any;
    rowIndex: any;
    refreshTable: Function;
}

export default class ChangeStatus extends  React.Component<ChangeStatusProps> {
    
    constructor(props: ChangeStatusProps) {
        super(props);
        this.changeStatusAction  = this.changeStatusAction.bind(this);
    }

    changeStatusAction(e: any, row: any) {

        const target = e.target;

        const status  = target.value;
        this.props.swal.confirm('Are you sure you want the status? ', () => {
            this.props.swal.wait('Updating...');
            let endPoint = {...API.COURSE_RUN_ACTION};
            endPoint.method = 'PUT';
            endPoint.url += '/change-status/'+row.id;

            this.props.callApi(endPoint, {status: status})
                .then(() => {

                    this.props.swal.success('Course Run\'status has been changed Successfully.');
                    this.props.refreshTable();

                }).catch((resposne: ServerResponse) => {

                    this.props.swal.error(resposne.message? resposne.message : 'Server Error.' );
                    $(target).find('option[value="'+row.current_status+'"]').prop('selected', true);
                }) 
        },{
            onCancel: () => {
                
                $(target).find('option[value="'+row.current_status+'"]').prop('selected', true);
                this.props.swal.close();
            }
        })
    }

    render() {

        const {cell, formatExtraData, row, rowIndex} = this.props;

        return (

            <select className="form-control" defaultValue={row.current_status} value={row.current_status} onChange={(e: any) => {this.changeStatusAction(e, row)}}>
                <option value="Draft" disabled >Draft</option>
                <option value="Confirmed" >Confirmed</option>
                <option value="Completed" >Completed</option>
                <option value="Closed">Closed</option>
            </select>
        )
    }
}