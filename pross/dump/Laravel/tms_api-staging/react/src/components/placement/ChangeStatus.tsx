import * as React from 'react';
import {Props, ServerResponse } from '../../features/root-props';
import courseRun from '../../aep/courseRun';
import API from '../../aep';
import {prepareConflictMessage, isWarning } from './MakeBatchConfirm';

export interface ChangeStatusProps extends Props {
    cell: any;
    formatExtraData: any;
    row: any;
    rowIndex: any;
    onPage: 'CourseRunDetail' | 'PlacemnetList'
    courseRunId?: string;
    whereFrom?: 'maintain-list'; 
    refreshTable?: Function;
}

export default class ChangeStatus extends  React.Component<ChangeStatusProps> {
    
    constructor(props: ChangeStatusProps) {
        super(props);
        this.changeStatusAction  = this.changeStatusAction.bind(this);
    }

    /**
     * To Check the conflict
     * @param placement_id 
     */
    checkConflict(placement_id: string, isEmailPreview: boolean, target?: any, current_status?: string) {
        
        const {swal, callApi, dispatch } = this.props;
        swal.wait('Wait Checking Conflicts');
                    
        callApi(API.PLACEMENT_CONFLICT, {placement_id: [placement_id]})
            .then((res: ServerResponse) => {
                swal.close(); 
                target ?  $(target).find('option[value="Confirmed"]').prop('selected', true): null;

                isEmailPreview ? this.redirectToEmailPreview(placement_id, 'Confirmed') : this.makeStatusConfirm(placement_id);
            })
            .catch( (res: ServerResponse<any, {[key:string]: Array<{[key:string]: string}> }>)=>{
                // Have Conflict
                var msg = prepareConflictMessage(res, this.props);

                if(isWarning(res.errors) && res.error_code  !== 'CSO') {

                     swal.confirm('System got the conflict in the following record(s).'+ msg, () => {
              
                          isEmailPreview ? this.redirectToEmailPreview(placement_id, 'Confirmed') : this.makeStatusConfirm(placement_id);
                          swal.close();

                      },{
                          confirmBtnText :'Make Confirm',
                          cancelBtnText: 'Cancel',
                          customClass: 'conflict-sweet-alert'
                      })  

                } else {

                    swal.error(msg, undefined ,{
                        customClass: 'single-conflict-sweet-alert'
                    });

                    target ?  $(target).find('option[value="'+current_status+'"]').prop('selected', true): null;
                }
            })
    }
    /**
     * To Make the status as Confirmed
     * @param placement_id 
     */
    makeStatusConfirm(placement_id: string) {

        const {swal, callApi, dispatch } = this.props;
        swal.wait('Updating Status...');
        let statusEndPoint = { ... API.PLACEMENT_ACTION};
        statusEndPoint.method = 'PUT';
        statusEndPoint.url += '/make-status-confirmed';
        callApi(statusEndPoint, {placement_id: [placement_id]})
            .then((res: ServerResponse)=> {
                
                swal.success('Placement status has been changed as confirmed.');
                this.props.refreshTable ? this.props.refreshTable() : null;
            })
    }

    /**
     * To make the status as Cancelled | Draft
     */
    changeStatusToCanOrDar(placement_id: string, status: 'Draft' | 'Cancelled', target?: any){
        
        let statusEndPoint = { ... API.PLACEMENT_ACTION};
        statusEndPoint.method = 'POST';
        statusEndPoint.url += '/update-status/'+placement_id;
        
        const {swal, callApi } = this.props;
        swal.wait('Updating Status...');

        callApi(statusEndPoint, {status: status})
            .then((res: ServerResponse)=> {

                target ?  $(target).find('option[value="'+status+'"]').prop('selected', true): null;
                swal.success('Placement status has been changed successfully.');
                this.props.refreshTable ? this.props.refreshTable() : null;
            })

    }

    /**
     * Redirect to the Email Preview Page
     * @param placement_id 
     * @param status 
     */
    redirectToEmailPreview(placement_id: string, status: string) {
        
        var queryStrObject: {[key: string]: any} = { fromPage : this.props.onPage};

        
        if(this.props.courseRunId){
            queryStrObject['courseRunId'] = this.props.courseRunId;
        }
        if(this.props.whereFrom) {
             queryStrObject['whereFrom'] = this.props.whereFrom;   
        }
        const queryStr = this.props.helper.queryString(queryStrObject);

        this.props.history.push('/placement/'+placement_id+'/change-status/'+status+'?'+ queryStr);
    }

    /**
     * Handle the User action for change the status
     * @param e 
     * @param row 
     */
    changeStatusAction(e: any, row: any) {

        const target = e.target;
        const status  = target.value;
        
        const {swal, callApi, dispatch } = this.props;
        
        const message = (status === 'Reminder') ? 'Are you sure to send the reminder ?' : 'Are you sure to change the status?';

        swal.confirm(message, () => {
            
                //swal.close();
                if(status === 'Confirmed') {

                    this.checkConflict(row.id, false, target, row.current_status);
                    
                }
                else if(status === 'Confirmed-Email') {
                    
                    this.checkConflict(row.id, true);
                }
                else if(status === 'Cancelled-Email') {
                    swal.close();
                    this.redirectToEmailPreview(row.id, 'Cancelled');
                }
                else if(status === 'Cancelled'){

                    this.changeStatusToCanOrDar(row.id, 'Cancelled');
                }
                else if(status === 'Reminder'){ 
                    
                    if(row.current_status === 'Confirmed') {
                        swal.close();
                        this.redirectToEmailPreview(row.id, 'Reminder');

                    } else {

                        swal.error('Reminder can only send to the confirm placement.');
                    }

                }
                else {
                    
                    this.changeStatusToCanOrDar(row.id, 'Draft');
                }

            },{
                onCancel: () => {
                    
                    $(target).find('option[value="'+row.current_status+'"]').prop('selected', true);
                    swal.close();
                }
            }
        )
    }

    render() {

        const {cell, formatExtraData, row, rowIndex} = this.props;

        return (

            <select defaultValue={row.current_status} value={row.current_status} className="form-control" onChange={(e: any) => {this.changeStatusAction(e, row)}}>
                <option value="Draft"  disabled>Draft</option>
                <option value="Confirmed" >Confirmed</option>
                <option value="Confirmed-Email" >Confirmed & Email</option>
                <option value="Cancelled" >Cancelled</option>
                <option value="Cancelled-Email" >Cancelled-Email</option>
                <option value="Reminder" >Send Reminders</option>
            </select>
        )
    }
}