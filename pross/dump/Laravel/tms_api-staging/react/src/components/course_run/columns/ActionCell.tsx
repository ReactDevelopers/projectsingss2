import * as React from 'react';
import {Props, ServerResponse } from '../../../features/root-props';
import { Button } from 'react-bootstrap';
var FAS = require('react-fontawesome');
import API, {ApiEndPointI} from '../../../aep';
import {actions as rootActions} from '../../../features/root-action';
import auth from '../../../aep/auth';

export interface DeleteProps extends Props {

    cell: any;
    formatExtraData: any;
    row: any;
    rowIndex: any;
    refreshTable: Function;
    deleteFor: 'COURSE_RUN' | 'COURSE_SUMMARY';
    atPage?: 'COURSE_RUN' | 'COURSE_SUMMARY' | 'MAINTAIN_COURSE';
    showDelete?: boolean;
    listEndPoint?: ApiEndPointI;
}

export default class Delete extends  React.Component<DeleteProps> {
    
    constructor(props: DeleteProps) {
        super(props);
        this.deleteConfirm  = this.deleteConfirm.bind(this);
    }

    /**
     * To delete the Placement
     * @param e 
     * @param row 
     */
    deleteConfirm(e: any, row: {[key: string]: any} ) {
        
        const {deleteFor, listEndPoint}  = this.props;

        this.props.swal.confirm('Are you sure you want to delete the Course Run ?', () => {
            this.props.swal.wait('Deleting...');
            let endPoint =  deleteFor === 'COURSE_RUN' ? {...API.COURSE_RUN_ACTION} : {...API.COURSE_RUN_SUMMARY_DELETE};

            endPoint.method = 'DELETE';
            endPoint.url += '/'+row.id;
            this.props.callApi(endPoint)
                .then(() => {

                    listEndPoint ? this.props.dispatch(rootActions.fetch.removeSelectedRow(listEndPoint, [row.id])) : null;

                    this.props.swal.success('Course Run has been deleted successfully.');
                    this.props.refreshTable();

                }).catch((resposne: ServerResponse) => {

                    this.props.swal.error(resposne.message? resposne.message : 'Server Error.' );
                }) 
        })
    }

    render() {

        const {cell, atPage, formatExtraData, row, rowIndex, deleteFor, showDelete } = this.props;
        const auth_user = this.props.helper.deepFind(this.props.rootState.server,'auth_user.response.data', {});
        
        
        return (
            <div>
              { deleteFor === 'COURSE_RUN'?   <Button onClick={(e) => {

                  if(atPage === 'MAINTAIN_COURSE') {
                      
                    this.props.history.push('/maintain-course-run/'+row.id);
                  }
                  else {
                    this.props.history.push('/course-run/'+row.id);
                  }
  
               }} bsStyle="info" title="Course Detail" >
                <FAS name="eye" />
              </Button> : null }

              { auth_user.view_as !== 'Viewer' && showDelete === true ? 
              <Button bsStyle="warning" title="Delete Course Run" onClick={(e) => { this.deleteConfirm(e, row); }} >
                <FAS name="trash" />
              </Button> : null }
            </div>
        );
    }
}