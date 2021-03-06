import * as React from 'react';
import { Field, reduxForm, getFormSubmitErrors, FormErrors, ConfigProps } from 'redux-form';
import {connect} from 'react-redux';
import * as bs from 'react-bootstrap';
import {getFieldError, CustomFormProps} from '../../plugins/form/FormInit';
// import TextInput from '../../plugins/form/layout/FormGroupText';
// import RichText from '../../plugins/form/layout/FormGroupRichTextEditor';
// import FileInput from '../../plugins/form/layout/FormGroupFile';
import SelectInput from '../../plugins/form/layout/FormGroupSelect';
import DateRangeInput from '../../plugins/form/layout/FormGroupRangeDatePicker';
import {CourseOptionListRendor} from './PlacementReportForm';

import Message from '../layout/Message';
import API from '../../aep';
import { RootState } from 'Features/root-reducer';
import Helper from '../../plugins/Helper';
const helper = new Helper();

export interface CourseRunReportFormData {
    prog_category_name?: [string];
    course_code?: [string];
    date_range?: [ string, string ];
    prog_type_name?: [string];
}
const labelPossition: 'Top' = 'Top';
const DatePickerPossition: 'left' = 'left';

export interface CourseRunReportProps extends CustomFormProps<CourseRunReportFormData> {
    programe_category: Array<{[key: string]: any }>;
    programme_type: Array<{[key: string]: any }>;
}

const CourseRunReportFrom: React.StatelessComponent<CourseRunReportProps> = ({ handleSubmit, 
    submitErrors, 
    submitting, 
    pristine, 
    submitFailed,
    isFetching,
    valid,
    programe_category,
    programme_type,
    error 
        }) => (
        
        <bs.Form horizontal={false} onSubmit={handleSubmit}>
            <div className="row">
            <div className="col-md-3">

            <Field 
                name="prog_type_name" 
                component={SelectInput}
                options={programme_type}
                labelKey="prog_type_name"
                valueKey="id"
                serverError={getFieldError(submitErrors,'prog_type_name')} 
                placeholder="Programme Type" 
                label="Programme Type"
                filterInkeys={['prog_type_code']}
                multi={true}
                labelPossition={labelPossition}
                isFetching={isFetching}
            />
            </div>
            <div className="col-md-2">
            <Field 
                name="prog_category_name" 
                component={SelectInput}
                options={programe_category}
                labelKey="prog_category_name"
                valueKey="id"
                serverError={getFieldError(submitErrors,'prog_category_name')} 
                placeholder="Program Category" 
                label="Program Category"
                multi={true}
                labelPossition={labelPossition}
                isFetching={isFetching}
            />
            </div>
            <div className="col-md-3">

            <Field 
                name="course_code" 
                component={SelectInput}
                endPoint={API.COURSE_LIST}
                isAsync={true}
                labelKey="course_title"
                multi={true}
                valueKey="course_code"
                serverError={getFieldError(submitErrors,'course_code')} 
                placeholder="Course Name" 
                label="Course Name"
                optionComponent={CourseOptionListRendor}
                filterInkeys={['course_code']}
                labelPossition={labelPossition}
                isFetching={isFetching}
            />
            </div>
            
            <div className="col-md-2">
            <Field 
                name="date_range" 
                opens={DatePickerPossition}
                component={DateRangeInput}
                serverError={getFieldError(submitErrors,'date_range')} 
                placeholder="Start date & end date" 
                label="Start date & end date"
                labelPossition={labelPossition}
                isFetching={isFetching}
            />
            </div>

            <div className="col-md-2">
             
            <div className="form-group">
                <div className="text-right col-sm-8 col-sm-offset-4">
                <button type="submit" disabled={submitting} className=" btn btn-primary rpt-btn">Export All</button>
                </div>
            </div>
            </div>
            <Message isError={submitFailed} message={error} /> 
            </div>
        </bs.Form>
        
);


const EForm =  reduxForm({
    form: 'course_run_report',  // a unique identifier for this form,
    //validate: validate,
    destroyOnUnmount: true,
    //enableReinitialize: true,
    // keepDirtyOnReinitialize : true,
})(CourseRunReportFrom);

export default connect(
    (state: RootState) => {
        return {
        // values: getFormValues('myForm')(state),
        // syncErrors: getFormSyncErrors('myForm')(state),
        submitErrors: getFormSubmitErrors('course_run_report')(state),
        programe_category:  helper.deepFind(state.server,'options.response.data.programe_category',[]),
        programme_type:  helper.deepFind(state.server,'options.response.data.programme_type',[])
        //initialValues: fromData
        // dirty: isDirty('myForm')(state),
        // pristine: isPristine('myForm')(state),
        // valid: isValid('myForm')(state),
        // invalid: isInvalid('myForm')(state)
        }
      }
)(EForm);