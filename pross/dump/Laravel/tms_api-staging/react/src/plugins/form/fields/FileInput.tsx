import { 
    Field, 
    Fields, 
    reduxForm, 
    ValidateCallback, 
    ConfigProps,  
    getFormSubmitErrors, 
    InjectedFormProps, 
    FormErrors, 
    FormAction, 
    FieldsProps, 
    WrappedFieldProps
} from 'redux-form';

import  {connect } from 'react-redux';
import * as React from 'react';
import {FieldGeneralProps} from '../FormInit';
//import {mapDispatchToProps, mapStateToProps, Props} from '../../../features/root-props';
import {actions as RootActions} from '../../../features/root-action';
import store from '../../../store';

export type WrappedFileFieldProps = {

    placeholder?: string,
    serverError?: string | undefined,
    acceptedExt?: Array<string>,
    maxSize?: number,
    multiple?: boolean,
    maxSelectedFiles?: number,
    note?: string | React.ReactHTMLElement<any>;

} & WrappedFieldProps & FieldGeneralProps;

import * as bs from 'react-bootstrap';
import { Col } from 'react-bootstrap';
import { RECEIVED_API_RESPONSE } from '../../../aep';
interface FileElementStates {
    shooldUpdate: boolean;
}
export default class FileElement extends React.Component<WrappedFileFieldProps, FileElementStates> {
    
    protected files: HTMLInputElement;

    constructor(props: WrappedFileFieldProps) {
        
        super(props);
        this.state = {
            shooldUpdate: false
        }
        //this.deleteConfirmation = this.deleteConfirmation.bind(this);
        //this.handleChangeFile = this.handleChangeFile.bind(this);
    }


    shouldComponentUpdate(nextProps: WrappedFileFieldProps, nextState: FileElementStates) {
       
        console.log('GGGGGGGGGGGGGGGGGGGGG');
        console.log(nextProps);
        console.log(this.props);
        if( this.props.input.value !== nextProps.input.value) {

            return true;
        }
        else if( !this.props.input.value && nextProps.input.value) {

            return true;
        }
        else if(this.props.input.value && !nextProps.input.value) {

            return true;
        }
        else if(this.props.input.value && nextProps.input.value && this.props.input.value.length !== nextProps.input.value.length ){

            return true;
        }
        else if(nextState.shooldUpdate != this.state.shooldUpdate) {

            return true;
        }

        return false;
    }

    handleChangeFile(event: FileList, target: any) {
        
        const {multiple, maxSelectedFiles, acceptedExt, maxSize, input} = this.props;
        console.log('111111111111');
        var files: Array<File> = Object.values(input.value ? {...input.value} : {});;
        const selectedFile = files.length;       
        const length  = event.length;   
        

       // Check for : user can select only one file
       if(!multiple && selectedFile) {
            //console.log('22222222222222222222');
            $(target).val('');
            store.dispatch( RootActions.swal.error("The system does not allow to select the more than one file.") );
            return;
       }

       // Check for : user can not select more the given number of files
       if(maxSelectedFiles && (selectedFile+length) >  maxSelectedFiles) {
            //console.log('33333333333333333333333');
            $(target).val('');
            store.dispatch( RootActions.swal.error("The system does not allow to select the more than " + maxSelectedFiles + ' file(s)') );
            return;
       }
       var errorMessage: string = '';

       for(var i=0; i < length; i++) {
            
            var file = event[i];
            // Check file extension
            const fileNameArr = file.name.split('.');
            var fileExt =  fileNameArr[fileNameArr.length-1].toLowerCase();

            if(acceptedExt && acceptedExt.indexOf(fileExt) === -1 ){

                errorMessage += 'The system does not allow the File ('+file.name+'). <br>';
            }
            else if(maxSize && file.size > maxSize){

                errorMessage += 'The File ('+file.name+') size is more than '+Math.round(file.size/1025).toFixed(2)+' KB. <br>';
            }
            else {

                files.push(file);
            }
       }

       console.log('Check on chnages');
       console.log(files);
       
       if(errorMessage){
           
            store.dispatch( RootActions.swal.error(errorMessage));
       }

       this.setState({shooldUpdate: !this.state.shooldUpdate});
       $(target).val('');
       this.props.input.onChange(files);
    }

    deleteConfirmation(e: any, index: number) {
        
        store.dispatch(RootActions.swal.confirm("Are you sure to delete this file?", () => {

            const  files: Array<File> = this.props.input.value ? this.props.input.value : [];
            console.log('files Before...');
            console.log(files);
            files.splice(index, 1);
            console.log('Files after s');
            console.log(files);
            this.props.input.onChange(files);
            this.setState({shooldUpdate: !this.state.shooldUpdate});
            store.dispatch( RootActions.swal.close() );
        }));
    }

    render() {
        const {placeholder, multiple, note, meta: { touched , error, submitting}, serverError } = this.props;
        const  files: Array<File> = this.props.input.value ? this.props.input.value : [];
        const hasError = !!error && !!touched || !!serverError;
        //console.log('$$$$$$$$$$$$$$$$$$###################FFFFFFFFFFFFFFFFFF');
        return (
            <>
                <bs.ControlLabel  htmlFor="custom-files" className="custom-files-upload">
                    {placeholder ? placeholder : 'Browse' }                    
                    <input id="custom-files" type="file" multiple={ multiple !== undefined ? multiple : false } 
                    ref ={(input: HTMLInputElement) => { this.files = input; } } 
                    onChange={ (e: any) => this.handleChangeFile(e.target.files, e.target) } />                     
                </bs.ControlLabel>
                {note ? <bs.HelpBlock>{note}</bs.HelpBlock> : null }
                {hasError && error ? <span className="help-block">{error.toString()}</span>: null}
                {files && files.length ?
                <bs.ListGroup componentClass ="ul" className="attachments-list">
                    {files.map((file: File, index) =>{
                            
                        return (
                            <bs.ListGroupItem key={`attachment_k_${index}`} data-id={index}>
                                <bs.FormGroup >
                                <Col sm={12} >
                                    <bs.Button key={`Attach_dlt_btn_${index}`} 
                                        onClick={(e:any) => {this.deleteConfirmation(e,index)}} 
                                        className="btn-addDelete btn-remove">
                                    Delete
                                    </bs.Button>
                                    {file.name} 
                                    ({ Math.round(file.size/1025).toFixed(2) } KB)
                                    {/* (Type: {file.type}) */}
                                    <bs.HelpBlock>
                                        {hasError && <span className="help-block">{serverError && serverError[index] != undefined ? serverError : null }</span>}
                                    </bs.HelpBlock>
                                </Col> 
                                </bs.FormGroup>
                                <bs.Clearfix />
                            </bs.ListGroupItem>
                        )
                    }) }
                </bs.ListGroup>: null}
            </>
        )
    }
}