import * as React from 'react';
import {Props, ServerResponse } from '../../../features/root-props';
import courseRun from '../../../aep/courseRun';
import API, {ApiEndPointI} from '../../../aep';
import Switch from "react-switch";

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

    changeStatusAction( row: any) {

        const should_check_deconflict =  row.should_check_deconflict === 'Yes' ? 'No' : 'Yes';
        //const should_check_deconflict =  row.should_check_deconflict;

        let endPoint = {...API.COURSE_RUN_DECONFLICT_STATUS_ACTION};
        endPoint.url +='/'+row.id;
        
        this.props.swal.confirm('Are you sure to change the conflict status?', () => {
            
            this.props.swal.wait('Updating status...');
            this.props.callApi(endPoint, {status:should_check_deconflict })
                .then((res: ServerResponse) => {
                    this.props.swal.success(res.message ? res.message : 'Status has been changed.');
                    this.props.refreshTable();

                }).catch((res: ServerResponse) => {

                    this.props.swal.error('Internal Server Error');
                })
        });
    }

    render() {

        const {cell, formatExtraData, row, rowIndex} = this.props;
        return (

           <label htmlFor={`event_display_change_${row.id}`}>
            <Switch
                onChange={() => {this.changeStatusAction(row)} }
                checked={row.should_check_deconflict  === 'Yes' ? true : false }
                
                uncheckedIcon={<span className="chk">No</span>}
                onColor="#78de78"
                offColor="#cfcfcf"
                checkedIcon={<span className="chk">Yes</span>}
                id={`event_display_change_${row.id}`}
            />
            </label>
        )
    }
}