import * as React from 'react';
import { Field, reduxForm, getFormSubmitErrors, FormErrors, ConfigProps } from 'redux-form';
import {connect} from 'react-redux';
import * as bs from 'react-bootstrap';
import {getFieldError, CustomFormProps} from '../../plugins/form/FormInit';
import TextInput from '../../plugins/form/layout/FormGroupText';
import RichText from '../../plugins/form/layout/FormGroupRichTextEditor';
import FileInput from '../../plugins/form/layout/FormGroupFile';
import Message from '../layout/Message';
import API from '../../aep';
import { RootState } from 'Features/root-reducer';

export interface SendEmailFormData {
    body?: string;
    subject?: string;
    to?: string;
    cc?: string;
    attachments?: Array<File>;
}
const labelPossition: 'Top' = 'Top';

export interface SendEmailFormProps extends CustomFormProps<SendEmailFormData> {}

const EmailTemplate: React.StatelessComponent<SendEmailFormProps> = ({ handleSubmit, 
    submitErrors, 
    submitting, 
    pristine, 
    submitFailed,
    isFetching,
    valid,
    error 
        }) => (
        
        <bs.Form horizontal={true} onSubmit={handleSubmit}>
            <div className="container inner-form">
            <Field 
                name="to" 
                component={TextInput} 
                serverError={getFieldError(submitErrors,'to')} 
                placeholder="TO" 
                label="TO"
                labelPossition={labelPossition}
                isFetching={isFetching}
                type="text" />
            <Field 
                name="cc" 
                component={TextInput} 
                serverError={getFieldError(submitErrors,'cc')} 
                placeholder="CC" 
                label="CC"
                labelPossition={labelPossition}
                isFetching={isFetching}
                type="text" />
            <Field 
                name="attachments" 
                component={FileInput} 
                serverError={getFieldError(submitErrors,'attachments')} 
                label="Attachment"
                multiple={true}
                maxSelectedFiles={10}
                acceptedExt={['pdf']}
                note="Select only pdf file."
                labelPossition={labelPossition}
                isFetching={isFetching} />
            <Field 
                name="subject" 
                component={TextInput} 
                serverError={getFieldError(submitErrors,'subject')} 
                placeholder="Email Subject" 
                label="Email Subject"
                labelPossition={labelPossition}
                isFetching={isFetching}
                type="text" />
            <Field serverError={getFieldError(submitErrors,'body')} 
                name="body" 
                label="Email Body"   
                isFetching={isFetching}                 
                component={RichText}
                labelPossition={labelPossition}
                placeholder="Email Body"  />            
             
            <div className="form-group">
                <div className="text-right col-sm-8 col-sm-offset-4">
                <button type="submit" disabled={submitting} title="Change Status & Send Email" className="submit-btn btn btn-default submit-btn save_send_email">Create</button>
                </div>
            </div>
            <Message isError={submitFailed} message={error} /> 
            </div>
        </bs.Form>
        
);


const EForm =  reduxForm({
    form: 'send_change_status_email',  // a unique identifier for this form,
    //validate: validate,
    destroyOnUnmount: false,
    //enableReinitialize: true,
    // keepDirtyOnReinitialize : true,
})(EmailTemplate);

export default connect(
    (state: RootState) => {

        return {
        // values: getFormValues('myForm')(state),
        // syncErrors: getFormSyncErrors('myForm')(state),
        submitErrors: getFormSubmitErrors('send_change_status_email')(state),
        //initialValues: fromData
        // dirty: isDirty('myForm')(state),
        // pristine: isPristine('myForm')(state),
        // valid: isValid('myForm')(state),
        // invalid: isInvalid('myForm')(state)
        }
      }
)(EForm);