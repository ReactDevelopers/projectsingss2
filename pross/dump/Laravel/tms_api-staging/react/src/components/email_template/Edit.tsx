import * as React from 'react';
import {Props, mapDispatchToProps, mapStateToProps, ServerResponse} from '../../features/root-props';
import Template from '../layout/Template';
import  {connect } from 'react-redux';
import * as bs from 'react-bootstrap';
import EmailTemplateForm, {EmailTemplateFormData, EmailTemplateProps} from './Form';
import {SubmissionError, FormErrors } from 'redux-form';
import API from '../../aep';
import {actions as RootActions} from '../../features/root-action';
import moment from 'moment';

var Parser = require('html-react-parser');

interface EditEmailTemplateState {
    shouldUpdate: boolean;
    preview: boolean;
}
class EditEmailTemplate extends React.Component <Props, EditEmailTemplateState> {

    private EmailTemplateID: string;

    constructor(props:Props) {
       
        super(props);
        this.onSubmit = this.onSubmit.bind(this);
        const params  = this.props.params();

        this.EmailTemplateID = params[params.length-1];

        this.state = {
            shouldUpdate: false,
            preview: false
        }
    }

    componentWillMount() {

        let endPoint = {...API.EMAIL_TEMPLATE_DETAIL};
        endPoint.url += '/'+this.EmailTemplateID;
        
        this.props.dispatch(RootActions.formAction.destroy('email_template'));
        
        this.props.callApi(endPoint)
            .then((res) => {

                this.props.dispatch(RootActions.formAction.initialize('email_template', res.data));
            })
            .catch(() => {

                this.props.swal.error('Email Template not found', () => {
                    this.props.history.push('/dashboard');
                    this.props.swal.close();
                });
            })

    }
    shouldComponentUpdate(nextProps: Props, nextState: EditEmailTemplateState) {

        return this.props.helper.shouldUpdate(nextProps, this.props, [API.EMAIL_TEMPLATE_DETAIL.sectionName]);
    }

    onSubmit(value: EmailTemplateFormData, dispatch: any, props: EmailTemplateProps) {
        
        const IsError = Object.keys(props.syncErrors).length;
        console.log('submitting...');
        let endPoint = {...API.EMAIL_TEMPLATE_ACTION};
        endPoint.method = 'PUT';
        endPoint.url += '/'+this.EmailTemplateID;
        // value.recipient =  value.recipient ? value.recipient : null;       
        // value.role_id =  value.role_id ? value.role_id : null;
        var data = {
            subject: value.subject ? value.subject : null,
            body: value.body ? value.body : null
        }

        if(!IsError) {
            
           return  this.props.callApi(endPoint, data)
            .then((response: ServerResponse) => {

               this.props.swal.success('Email Template has been saved successfully.', () => {
                   this.props.history.push('/');
                   this.props.swal.close();
               });

            }).catch((response: ServerResponse) => {

                throw new SubmissionError({...response.errors, _error: response.message ? response.message : '' });
            })

        }else {

            throw new SubmissionError({...props.syncErrors, _error: 'Invalid data'});
        }
    }

    /**
     * This component is using to edit the tempalte for Group, Event and Initial Event
     */
    getTitle() {

        switch (this.EmailTemplateID) {
            case "1":
                return "Email Content For Placement Confirm";
            case "2":
                return "Email Content For Placement Cancel";

            case "3":
                return "Email Content For Placement Reminder";
            default:
               return ""
        }
    }

  
    render() {

        const title = this.getTitle();        

        const breadcrumbs = [
            {title: title, url: '/email-template/'+this.EmailTemplateID}
        ];

        const _email_template_detail = this.props.rootState.server[API.EMAIL_TEMPLATE_DETAIL.sectionName];
        const isFetching = _email_template_detail && _email_template_detail.isFetching ? _email_template_detail.isFetching : false;
        
        const template: string = this.props.helper.deepFind(_email_template_detail,'response.data.template');         

        return (
            <Template {...this.props} breadcrumb={breadcrumbs} >
                <div className="lineframe email-template-edit-form">
            		<div className="lineframe-inner">
                        <EmailTemplateForm onSubmit={this.onSubmit} isFetching={isFetching}  />
                        
            		</div>
                </div>
            </Template>
        )
    }
}

export default connect(mapStateToProps, mapDispatchToProps)(EditEmailTemplate)
