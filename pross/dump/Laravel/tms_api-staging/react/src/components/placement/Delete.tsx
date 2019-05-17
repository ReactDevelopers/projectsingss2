import * as React from 'react';
import {Props, ServerResponse } from '../../features/root-props';
import { Button } from 'react-bootstrap';
var FAS = require('react-fontawesome');
import {PageType} from './PlacementTable';
import API, {ApiEndPointI} from '../../aep';
import {actions as rootActions} from '../../features/root-action';

export interface DeleteProps extends Props {

    cell: any;
    formatExtraData: any;
    row: any;
    rowIndex: any;
    pageFor: PageType;
    refreshTable: Function;
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

        this.props.swal.confirm('Are you sure you want to delete the Placement ?', () => {
            this.props.swal.wait('Deleting...');
            let endPoint = {...API.PLACEMENT_ACTION};
            
            endPoint.method = 'DELETE';
            endPoint.url +=  (this.props.pageFor === PageType.PLACEMENT || this.props.pageFor ===  PageType.OF_COURSE_RUN_ID ? '' : '/result');

            this.props.callApi(endPoint, {ids: [row.id]})
                .then(() => {
                    
                    var endPoint: ApiEndPointI | null = null;

                    if(this.props.pageFor === PageType.PLACEMENT) {

                        endPoint = API.MAINTAIN_COURSE_RUN_LIST;
                    }
                    else if(this.props.pageFor === PageType.OF_COURSE_RUN_ID) {

                        endPoint = {...API.PLACEMENT_LIST_OF_A_COURSE_RUN};
                        endPoint.sectionName += '.'+row.course_run_id;
                    }
                    else if(this.props.pageFor === PageType.POST_COURSE_RUN_DATA) {
                        endPoint = API.POST_COURSE_RUN_LIST;
                    }
                    
                    endPoint ? this.props.dispatch(rootActions.fetch.removeSelectedRow(endPoint, [row.id])) : null;

                    this.props.swal.success('Placement has been Deleted Successfully.');
                    this.props.refreshTable();

                }).catch((resposne: ServerResponse) => {

                    this.props.swal.error(resposne.message? resposne.message : 'Server Error.' );
                }) 
        })
    }

    render() {

        const {cell, formatExtraData, row, rowIndex} = this.props;

        return (
            <div>              
              <Button bsStyle="warning" title="Delete Placement" onClick={(e) => { this.deleteConfirm(e, row); }} >
                <FAS name="trash" />
              </Button>
            </div>
        );
    }
}