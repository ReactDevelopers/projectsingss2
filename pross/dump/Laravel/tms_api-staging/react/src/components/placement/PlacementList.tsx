import * as React from 'react';
import {Props, mapDispatchToProps, mapStateToProps, ServerResponse} from '../../features/root-props';
import Template from '../layout/Template';
import  {connect } from 'react-redux';
import API, {ApiEndPointI} from '../../aep';
import { PaginationPostion } from 'react-bootstrap-table';
import Upload from '../../plugins/Upload';
import UploderMessage from '../layout/UploderMessage';
import DownloadBtn from '../layout/DownloadSampleBtn';
import PlacementTable , {PlacementTableProps, PageType}  from './PlacementTable';
import sampleFiles from '../../SampleFiles';

class List extends React.Component <Props> {
    
    private EndPoint: ApiEndPointI;

    constructor(props: Props) {

        super(props);
        this.refreshTable = this.refreshTable.bind(this);      
        this.EndPoint = {...API.PLACEMENT_LIST};
    }
       

    shouldComponentUpdate(nextProps: Props) {

        return this.props.helper.shouldUpdate(nextProps, this.props, [this.EndPoint.sectionName,'options']);
    }

    /**
     * TO Refresh The Table
     */
    refreshTable() {
        
        this.props.callApi(this.EndPoint);
    }
    currentBreadcrumb(){


        return {
            title: 'Submit/Upload Placement Data',
            url: 'placement'
        }        
    }

   
    render() {

        const defaultPaginationPos: PaginationPostion = 'bottom';
        const breadcrumbs = [this.currentBreadcrumb()];
        

        const customFilters: {[key: string]: any}  =  this.props.helper.deepFind(this.props.rootState, 'server.'+this.EndPoint.sectionName+'.requestData.customFilters');


        return (
            <Template {...this.props} 
            breadcrumb={breadcrumbs} 
            RightSideButton=
            {<DownloadBtn filename={[sampleFiles.placement]} />}
            >
            <Upload 
                    {...this.props} endPoint={API.PLACEMENT_RESULT_UPLOAD} 
                    afterUploadSuccess={this.refreshTable}
                    message={<UploderMessage message={`Drop the Placement files here to Upload`}/>} 
                />
            <PlacementTable 
                showBatchDelete={true}
                 {...this.props} 
                 batchDeleteEndPoint={API.PLACEMENT_BATCH_DELETE} 
                 endPoint={this.EndPoint} 
                pageFor={PageType.PLACEMENT} />

            </Template>
        )
    }
}

export default connect(mapStateToProps, mapDispatchToProps)(List)