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

import Message from '../layout/Message';
import API from '../../aep';
import { RootState } from 'Features/root-reducer';
import Helper from '../../plugins/Helper';
import { OptionComponentProps} from 'react-select';

const helper = new Helper();


export interface PlacementReportFormData {
    prog_category_name?: [string];
    course_code?: [string];
    date_range?: [ string, string ];
    prog_type_name?: [string];
    percipient_name?: Array<string>;
    attendance?: string;
    assessment_results?: string;
}
const labelPossition: 'Top' = 'Top';
const DatePickerPossition: 'right' = 'right';

export class CourseOptionListRendor extends React.Component<OptionComponentProps> {

    onClick(val: any) {

        this.props.selectValue ? this.props.selectValue(val) : null;
    }
    render() {

        const { option, optionIndex, isFocused, isSelected } = this.props;
            
        return <div onClick={() => { this.onClick(option) }}  className="Select-menu" id={`react-select-${optionIndex}--list`} role="listbox" tabIndex={-1}>
            <div className="Select-option" role="option" id={`react-select-${optionIndex}--option-0`}>
                {option.course_title} <span className="course_code_option">({option.course_code})</span>
            </div>
        </div>
    }
}

export interface PlacementReportProps extends CustomFormProps<PlacementReportFormData> {
    programe_category: Array<{[key: string]: any }>;
    programme_type: Array<{[key: string]: any }>;
    users: Array<{[key: string]: any }>;
}

const CourseRunReportFrom: React.StatelessComponent<PlacementReportProps> = ({ handleSubmit, 
    submitErrors, 
    submitting, 
    pristine, 
    submitFailed,
    isFetching,
    valid,
    users,
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
                valueKey="id"
                optionComponent={CourseOptionListRendor}
                serverError={getFieldError(submitErrors,'course_code')} 
                placeholder="Course Name" 
                label="Course Name"
                filterInkeys={['course_code']}
                labelPossition={labelPossition}
                isFetching={isFetching}
            />
            </div>
            <div className="col-md-4">
                <Field 
                    name="percipient_name" 
                    component={SelectInput}
                    options={users}
                    pageSize={10}
                    labelKey="name"
                    valueKey="id"
                    serverError={getFieldError(submitErrors,'percipient_name')} 
                    placeholder="Participants" 
                    label="Participants"
                    multi={true}
                    labelPossition={labelPossition}
                    isFetching={isFetching}
                />
            </div>
            </div>
            <div className="row">

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
                <Field 
                    name="attendance" 
                    component={SelectInput}
                    options={[{value: 'Present', label: 'Present'}, {value: 'Absent', label: 'Absent'}]}
                    labelKey="label"
                    valueKey="value"
                    serverError={getFieldError(submitErrors,'attendance')} 
                    placeholder="Attendance" 
                    label="Attendance"
                    labelPossition={labelPossition}
                    isFetching={isFetching}
                />
            </div>

            <div className="col-md-2">
                <Field 
                    name="assessment_results" 
                    component={SelectInput}
                    options={[{value: 'Fail', label: 'Fail'},{value: 'Pass', label: 'Pass'}]}
                    labelKey="label"
                    valueKey="value"
                    serverError={getFieldError(submitErrors,'assessment_results')} 
                    placeholder="Assesment Result" 
                    label="Assesment Result"
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
    form: 'placement_report',  // a unique identifier for this form,
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
        submitErrors: getFormSubmitErrors('placement_report')(state),
        programe_category:  helper.deepFind(state.server,'options.response.data.programe_category',[]),
        programme_type:  helper.deepFind(state.server,'options.response.data.programme_type',[]),
        users:  helper.deepFind(state.server,'options.response.data.users',[])
        //initialValues: fromData
        // dirty: isDirty('myForm')(state),
        // pristine: isPristine('myForm')(state),
        // valid: isValid('myForm')(state),
        // invalid: isInvalid('myForm')(state)
        }
      }
)(EForm);