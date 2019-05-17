import * as React from 'react';
import {Props, mapDispatchToProps, mapStateToProps, ServerResponse} from '../features/root-props';
import Template from './layout/Template';
import  {connect } from 'react-redux';
import * as bs from 'react-bootstrap';
import {SubmissionError, FormErrors } from 'redux-form';
import {actions as RootActions} from '../features/root-action';
import moment from 'moment';


class List extends React.Component <Props> {


    constructor(props:Props) {
       
        super(props);
    }   
    
    generateList(data:Array<{[key: string]: any}>, dataField: string  ) {


        return data.map((v, k) => {

            return (
                <li key={`list_${dataField}_${k}`} className="list-group-item">{ v[dataField]}</li>
            )
        })
    }

    makeOptionList(data:Array<{[key: string]: any}>, sectionName: string, dataField: string, isFetching: boolean ) {

        if(this.props.helper.isArray(data) && data.length > 0) {

            return (
                <ul className="option_main_list list-group">
                    <li> <span className="list-group-item active">{sectionName}</span> 
                        <ul>
                            {this.generateList(data, dataField)}
                        </ul>
                    </li>
                </ul>
            )
        }
        else {

            return (
                <ul className="option_main_list list-group not_found">
                    <li> <span className="list-group-item active">{sectionName}</span> 
                        <ul>
                            <li className="list-group-item ">  {isFetching ? 'Loading...': 'Not Found' } </li>
                        </ul>
                    </li>
                </ul>
            )
        }
    }

    render() {


        const breadcrumbs = [
            {title:'List Data', url: '/attribute-list'}
        ];
        
        const attributes: {[key: string]: Array<{[key: string]: any}>} = this.props.helper.deepFind(this.props.rootState.server,'options.response.data',{});
        const isFetching = this.props.helper.deepFind(this.props.rootState.server,'options.isFetching', false);

        //const absent_reason = attributes.absent_reason;
        // const assessment_types = attributes.assessment_types;
        // const assessment_types = attributes.assessment_types;
        // const assessment_types = attributes.assessment_types;
        // const assessment_types = attributes.assessment_types;

        return (
            <Template {...this.props} breadcrumb={breadcrumbs} >
                <div className="lineframe email-template-edit-form">
            		<div className="lineframe-inner">
                        {this.makeOptionList(attributes.absent_reason, 'Absent Reason', 'absent_reason', isFetching)}
                        {this.makeOptionList(attributes.assessment_types, 'Assessment Type', 'assessment_type_name', isFetching)}
                        {/*{this.makeOptionList(attributes.course_provider, 'Course Provider', 'provider_name', isFetching)}*/}
                        {this.makeOptionList(attributes.departments, 'Department', 'dept_name', isFetching)}
                        {this.makeOptionList(attributes.failure_reason, 'Failure Reason', 'failure_reason', isFetching)}
                        {this.makeOptionList(attributes.programe_category, 'Programme Category', 'prog_category_name', isFetching)}
                        {this.makeOptionList(attributes.programme_type, 'Programme Type', 'prog_type_name', isFetching)}
                        {this.makeOptionList(attributes.training_location, 'Location', 'location', isFetching)}
                        {this.makeOptionList(attributes.delivery_methods, 'Delivery Method', 'name', isFetching)}
                        <div className="clearfix"></div>
            		</div>
                </div>
            </Template>
        )
    }
}

export default connect(mapStateToProps, mapDispatchToProps)(List)
