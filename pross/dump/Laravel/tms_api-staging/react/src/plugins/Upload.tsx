import * as React from 'react';
import Dropzone from 'react-dropzone';
import { Props, 
    ServerResponse, 
    ServerResponseListData, 
    ListRequest, 
    mapDispatchToProps, 
    mapStateToProps 
} from "../features/root-props";
import {Panel, Button, Row, Col } from 'react-bootstrap';

import API, {ApiEndPointI} from '../aep';

interface UploadState {
    
    files: Array<{
        uploading: boolean,
        file: File,
        result?: ServerResponse<{[key: string]: any}, {[key: string]: any} | Array<ErrorsState>>;
        isError: boolean;
    }>;
    shouldUpdate: boolean;
}

interface UploadProps extends Props {
    
    endPoint: ApiEndPointI;
    afterUploadSuccess?: Function;
    message?: string | React.ReactElement<any> 
}

interface ErrorsState  {
    data: {[key: string]: any};
    errors: {[key: string]: Array<string>};
    row_no: number;
}

class Upload extends React.Component<UploadProps, UploadState> {
    
    constructor(props: UploadProps) {
        super(props);

        this.state = {
            files: [],
            shouldUpdate: false
        }
    }

    closeAlert(index: number) {
      
        var stateFile = this.state.files;
        stateFile.splice(index, 1);
        this.setState({files: stateFile, shouldUpdate: !this.state.shouldUpdate});
    }

    uploadAgain(index: number) {

        var stateFile = this.state.files;
        var file_data = stateFile[index];
        this.closeAlert(index);
        var data = file_data.result ? file_data.result.data : {};
        var skippedData = Object.keys(data);

        this.onDrop([file_data.file], null, null, true, skippedData);

    }
    shouldComponentUpdate(nextProps: Props, nextState: UploadState) {

        return (nextState.shouldUpdate !== this.state.shouldUpdate);
    }

    onDrop(slectedFiles: Array<File>, ddata?: any, arg3?: any, foreceUpload?: boolean, skippedData?: Array<any>) {

        var files = this.state.files;

        slectedFiles.map( (file: File) => {

            files.push({uploading: true, file: file, isError: false});
            var endPoint = {...this.props.endPoint};
            endPoint.file = true;

            var formData = new FormData();
            formData.append('file', file);
            foreceUpload ? formData.append('forceUpload', 'yes') : null;
            if(skippedData && skippedData.length){

                skippedData.map(function(sd){
                    formData.append('skippedData[]', sd); 
                })
            }
            const fileLenght = files.length;

            this.props.callApi(endPoint, formData)
                .then((data: ServerResponse<{[key: string]: any}, Array<ErrorsState>>) => {

                    var stateFile = this.state.files;
                    stateFile[fileLenght-1].result = data;
                    stateFile[fileLenght-1].uploading = false;
                    stateFile[fileLenght-1].isError = data.data.skipped ? true : false;
                   
                    this.setState({files: stateFile, shouldUpdate: !this.state.shouldUpdate});
                    this.props.afterUploadSuccess ? this.props.afterUploadSuccess() : null;

                }).catch( (data: ServerResponse<{[key: string]: any}, {[key: string]: any}>) => {

                    if(data.error_code === 500)  {

                        this.props.swal.error(data.message ? data.message : 'Server Error.', () => {
                            this.closeAlert(fileLenght-1);
                            this.props.swal.close();
                        } );
                    }
                    else {

                        var stateFile = this.state.files;
                        stateFile[fileLenght-1].result = data;
                        stateFile[fileLenght-1].uploading = false;
                        stateFile[fileLenght-1].isError = true;
                        this.setState({files: stateFile, shouldUpdate: !this.state.shouldUpdate});
                    }
                });
        })
        this.setState({files: files, shouldUpdate: !this.state.shouldUpdate});
    }

    printErrorMessage(errors: Array<ErrorsState>  ) {        

        return (

            <>
                
                <ul className="upload-error-list">
                    {errors.map( (error) => {

                        const errorskeys = Object.keys(error.errors);

                       return (
                            <>
                            <li><p>System got the following errors in the row {error.row_no+1}</p>
                            <ol>
                                {errorskeys.map((key) => {
                                    return error.errors[key].map(v => <li>{v}</li> )
                                }) }
                            </ol>
                            </li>
                            </>
                       )
                    }) }
                </ul>
            </>
        )
    }

    printDuplicateErrorMessage(data: {[key: string]: Array<{[key: string]: any}> }  ) {        
        
        var skippedData = Object.keys(data);

        return (

            <ul className="upload-error-list">
                {
                    skippedData.map((run_id_per_id) => {
                        
                        return <li> System got {data[run_id_per_id].length} entry for this per id : {data[run_id_per_id][0].per_id} and course run id: {data[run_id_per_id][0].course_run_id} </li>
                    })
                }
           </ul>
        )
    }

    render() {
        const { files } =  this.state;
        const {message, endPoint} = this.props;
        return (
            <section>
            {files && files.map(( v, k ) => {                
                if(!v.uploading){
                   return (
                   <div className="panel-sections">
                    <Panel key={`uploader-error-panel-${k}`} id={`uploader-error-panel-${k}`} defaultExpanded={false} className={v.isError? 'has-error': 'no-error'} >
                        <Panel.Heading key={`uploader-error-panel-heading-${k}`}>
                            <Row key={`uploader-error-panel-heading-row-${k}`}>
                                <Col md={11} sm={11} xs={11}>
                                    <Panel.Title toggle={v.isError}>
                                    <ul className="upload-result-points clearfix">
                                        <li>File: {v.file.name}</li>
                                        {v.result  && v.result.error_code ==='duplicate' ? 
                                            <>
                                            <li className="btn-duplicate-msg"><i>Click here</i> to find the duplicate entries. Click "Yes" to skip duplicate entry and continue with rest data and click "Cancel" to reject the request.</li>
                                            <li className="btn-duplicate-btn-yes"><Button className="btn-primary" onClick={() => { this.uploadAgain(k) }} >Yes</Button></li>
                                            <li className="btn-duplicate-btn-cancel"><Button className="btn-danger" onClick={() => {this.closeAlert(k)}} >Cancel</Button></li>
                                            </>
                                         : <>
                                            <li>Total: {v.result && v.result.data.total ? v.result.data.total : 0 }</li>
                                            <li>Created: {v.result && v.result.data.inserted ? v.result.data.inserted : 0 }</li>
                                            <li>Updated: {v.result && v.result.data.updated ? v.result.data.updated : 0 }</li>
                                            <li>Skipped: {v.result && v.result.data.skipped ? v.result.data.skipped : 0 }</li></>
                                        }
                                    </ul>
                                    
                                    <div className="clearfix"></div>
                                    </Panel.Title>
                                    
                                </Col>
                                <Col md={1} sm={1} xs={1} className="text-right">
                                    <Button onClick={() => {this.closeAlert(k)}} className="close-btn"></Button>
                                </Col>    
                            </Row>
                        </Panel.Heading>
                        <Panel.Collapse>
                            <Panel.Body>
                            {v.isError && v.result && this.props.helper.isObject(v.result.errors) ?  v.result.errors.file[0] : null}

                            {
                               v.isError && v.result && this.props.helper.isArray(v.result.errors)   ?
                                    this. printErrorMessage(v.result.errors ) :  null                                
                            }
                            {
                                v.result && v.result.error_code == 'duplicate' ? this.printDuplicateErrorMessage(v.result.data) :  null
                            }

                            {v.result  && v.result.error_code ==='FHNM' ? 
                                <span>File Header does not match with required, It should be in the sequence of <b> {v.result.data.join(', ')}</b></span> 
                            : null}

                            </Panel.Body>
                        </Panel.Collapse>
                    </Panel>
                        </div>
                   )
                }else {

                    return null;
                }               
                
            })}    
            
            <div className="dropzone">
              <Dropzone onDrop={this.onDrop.bind(this)}>
               <Row className={`upload-file-row ${files.length ? 'has-file' : 'no-file'}`} >
                    {!files.length ? (message ? message : <p>Try dropping some files here, or click to select files to upload.</p>) : null }

                {files.map((v, k) => {

                    return (
                            <Col sm={1} key={`file-${k}`} title={v.file.name}  className={v.uploading ? 'uploading': 'uploaded'}>
                                <div className="upload-item-wrapper">
                                    <span>{v.file.size/1024} KB</span>
                                    {v.uploading ? 
                                    <div className="progress">
                                        <div className="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100">
                                        </div>
                                    </div> : <div className="success-check"></div>
                                    }

                                </div>
                                {/* {v.file.name} */}
                            </Col>
                    );
                })}
                </Row>
              </Dropzone>
            </div>
          </section>
        )
    }
}

export default Upload;