import * as React from 'react';
import * as ReactDom from 'react-dom';
import {Props, mapDispatchToProps, mapStateToProps, ServerResponse} from '../../features/root-props';
import Template from '../layout/Template';
import  {connect } from 'react-redux';
import * as bs from 'react-bootstrap';
import SendEmailFrom, {SendEmailFormData, SendEmailFormProps} from './SendEmailForm';
import {SubmissionError, FormErrors } from 'redux-form';
import API from '../../aep';
import { actions as RootAction} from '../../features/root-action';
import moment from 'moment';
const Parser = require('html-react-parser');


interface SendEmailState {

    shouldUpdate: boolean;
    preview: boolean;
}

interface SendEmailProps extends Props {

    status: 'Confirmed' |  'Cancelled' | 'Reminder';
}

class SendEmail extends React.Component <SendEmailProps, SendEmailState> {

    private PlacementId: Array<string>;
    private queryStringObject: { [key: string]: string } | null;

    constructor(props: SendEmailProps) {
       
        super(props);
        this.onSubmit = this.onSubmit.bind(this);
        const params  = this.props.params();
        this.togglePreview = this.togglePreview.bind(this);
        this.previewBtnTitle = this.previewBtnTitle.bind(this);
        this.PlacementId = [params[params.length-3]];        

        this.queryStringObject = this.props.history.location.search ? this.props.helper.queryStringToObject(this.props.history.location.search) : null;

        if(this.queryStringObject && this.queryStringObject.placement_id){
            this.PlacementId = this.queryStringObject.placement_id.split(',');
        }
        this.state = {
            shouldUpdate: false,
            preview: false
        }
    }
    onSubmit(value: SendEmailFormData, dispatch: any, props: SendEmailFormProps) {

        // console.log('Sumitted');
        // console.log(value);
        
        //const keys = Object.keys(value);
        var formdata = new FormData();

        formdata.append('to', value.to ? value.to: '');
        formdata.append('cc', value.cc ? value.cc: '');
        formdata.append('body', value.body ? value.body: '');
        formdata.append('subject', value.subject ? value.subject: '');
        this.PlacementId.map((placement_id) => {
            formdata.append('placement_id[]', placement_id);
        })
        formdata.append('status', this.props.status);
        
        value.attachments && value.attachments.map(v => {
            formdata.append('attachments[]', v);
        });

        let endPoint = {...API.PLACEMENT_CH_STUS_SEND_EMAIL};      

        return this.props.callApi(endPoint, formdata )
        .then((response: ServerResponse) => {

            const msg = this.props.status == 'Reminder' ? 'Reminder has been sent' : 'Status has been Changed';
            this.props.swal.success(msg, () => {
                
                this.props.swal.close();

                if(this.queryStringObject){
                    this.props.history.push(this.queryStringObject.fromPage === 'PlacemnetList' ? '/placement':  '/course-run/'+ this.queryStringObject.courseRunId);
                }
                else {
                    this.props.history.push('/dashboard');
                }
                
            });

         }).catch((response: ServerResponse) => {

             throw new SubmissionError({...response.errors, _error: response.message ? response.message : '' });
         })
    }

    componentWillMount() {

        this.props.dispatch(RootAction.formAction.destroy('send_change_status_email') );

        this.props.callApi(API.PLACEMENT_EMAIL, {
            placement_id: this.PlacementId,
            status: this.props.status
        })
        .then( (response: ServerResponse) => {            

            this.props.dispatch(RootAction.formAction.initialize('send_change_status_email', response.data));
        }).catch((response: ServerResponse) => {  

            this.props.swal.error('Invalid Selection. The selected placement(s) may have de-conflict, deleted or already in confirmed status.', () => {

                this.props.swal.close();
                this.props.history.goBack();
            })
        })
    }

    togglePreview() {

        this.setState({
            shouldUpdate: !this.state.shouldUpdate,
            preview: !this.state.preview
        });
    }
    previewBtnTitle() {

        return this.state.preview? 'Back to Edit': 'Preview';
    }

    /**
     * Prepare the breadcrumb on base of from which page user is comming on this page
     */
    makeBreadCrumb(): Array< {title: string, url: string}> {
        
        var breadcrumbs: Array< {title: string, url: string}> = [];

        if(this.queryStringObject) {

            var ccourseRunDetailUri = this.queryStringObject.whereFrom === 'maintain-list' ? 'maintain-course-run' : 'course-run';
            breadcrumbs.push({
                title: this.queryStringObject.fromPage === 'PlacemnetList' ? 'Upload Placement Data':  'Course Run Detail',
                url: this.queryStringObject.fromPage === 'PlacemnetList' ? '/placement':  '/'+ccourseRunDetailUri+'/'+ this.queryStringObject.courseRunId
            });
        }

        breadcrumbs.push({title: 'Change Placement Status', url: '/placement/'+ this.PlacementId+'/change-status/'+this.props.status.toLowerCase()});

        return breadcrumbs;
    }

    render() {

       const realtimedata:{[key: string]: any} = this.props.helper.deepFind(this.props.rootState.form , 'send_change_status_email.values', {});

       console.log('Data............');
       console.log(realtimedata);

        const isFetching = this.props.helper.deepFind(this.props.rootState.server, API.PLACEMENT_EMAIL.sectionName+'.isFetching', true);
        const emailData = this.props.helper.deepFind(this.props.rootState.server, API.PLACEMENT_EMAIL.sectionName+'.response.data', {});
        console.log('emailData');
        console.log(emailData);

        return (
            <Template {...this.props} breadcrumb={this.makeBreadCrumb()} >
                <div className="lineframe email-template-edit-form">
            		<div className="lineframe-inner">
                    <button className="btn btn-primary mg-top-btn pull-right" onClick={this.togglePreview}>{this.previewBtnTitle()}</button>
                    <div className="clearfix"></div>
                        {this.state.preview ? <div className="email_template_preview">
                            <table className="table table-bordered">
                            <tbody>
                                <tr>
                                    <td>To </td>
                                    <td>{realtimedata.to}</td>
                                </tr>
                                <tr>
                                    <td>CC </td>
                                    <td>{realtimedata.cc}</td>
                                </tr>
                                <tr>
                                    <td>Subject </td>
                                    <td>{realtimedata.subject}</td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td>{Parser(realtimedata.body) }</td>    
                                </tr>
                            </tbody>
                            </table>                           
                        
                        </div> :
                            <SendEmailFrom onSubmit={this.onSubmit} isFetching={isFetching} />
                        }
                        
            		</div>
                </div>
            </Template>
        );
    }
}

export default connect(mapStateToProps, mapDispatchToProps)(SendEmail)