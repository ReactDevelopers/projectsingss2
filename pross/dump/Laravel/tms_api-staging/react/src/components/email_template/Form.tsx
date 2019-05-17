import * as React from 'react';
import { Field, reduxForm, getFormSubmitErrors, FormErrors, ConfigProps } from 'redux-form';
import {connect} from 'react-redux';
import * as bs from 'react-bootstrap';
import {getFieldError, CustomFormProps} from '../../plugins/form/FormInit';
import TextInput from '../../plugins/form/layout/FormGroupText';
import RichText from '../../plugins/form/layout/FormGroupRichTextEditor';
import Message from '../layout/Message';
import API from '../../aep';
import { RootState } from 'Features/root-reducer';

export interface EmailTemplateFormData {
    body?: string;
    subject?: string;
}
const labelPossition: 'Top' = 'Top';

export interface EmailTemplateProps extends CustomFormProps<EmailTemplateFormData> {}

const EmailTemplate: React.StatelessComponent<EmailTemplateProps> = ({ handleSubmit, 
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
                name="subject" 
                component={TextInput} 
                serverError={getFieldError(submitErrors,'subject')} 
                placeholder="Email Subject" 
                label="Email Subject"
                labelPossition={labelPossition}
                isFetching={isFetching}
                type="text" />
            <Field serverError={getFieldError(submitErrors,'password')} 
                name="body" 
                label="Email Body"   
                isFetching={isFetching}                 
                component={RichText}
                labelPossition={labelPossition}
                placeholder="Email Body"  />            
             
            <div className="form-group">
                <div className="text-right col-sm-8 col-sm-offset-4">
                <button type="submit" disabled={submitting} className="submit-btn btn btn-default">Create</button>
                </div>
            </div>
            <Message isError={submitFailed} message={error} /> 
            </div>
        </bs.Form>
        
);


const EForm =  reduxForm({
    form: 'email_template',  // a unique identifier for this form,
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
        submitErrors: getFormSubmitErrors('email_template')(state),
        //initialValues: fromData
        // dirty: isDirty('myForm')(state),
        // pristine: isPristine('myForm')(state),
        // valid: isValid('myForm')(state),
        // invalid: isInvalid('myForm')(state)
        }
      }
)(EForm);