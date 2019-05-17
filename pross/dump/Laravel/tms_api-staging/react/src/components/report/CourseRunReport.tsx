import * as React from 'react';
import {Props, mapDispatchToProps, mapStateToProps, ServerResponse} from '../../features/root-props';
import Template from '../layout/Template';
import  {connect } from 'react-redux';
import * as bs from 'react-bootstrap';
import From, {CourseRunReportFormData, CourseRunReportProps} from './CourseRunReportForm';
import {SubmissionError, FormErrors } from 'redux-form';
import API from '../../aep';
import {actions as RootActions} from '../../features/root-action';
import moment from 'moment';

var Parser = require('html-react-parser');


class CourseRunReport extends React.Component <Props> {


    constructor(props:Props) {
       
        super(props);
        this.onSubmit = this.onSubmit.bind(this);
    }

    
    // shouldComponentUpdate(nextProps: Props, nextState: CourseRunReportState) {

    //     return this.props.helper.shouldUpdate(nextProps, this.props, [API.EMAIL_TEMPLATE_DETAIL.sectionName]);
    // }

    onSubmit(value: CourseRunReportFormData, dispatch: any, props: CourseRunReportProps) {
        
        let endPoint = {...API.COURSE_RUN_REPORT_LIST};
        endPoint.shouldResponseStore = false;
        endPoint.saveRequest = false;

       var fName = 'Course Run DATA.xlsx';

       var filterData: { [s in keyof CourseRunReportFormData ]: any } = {};

       this.props.helper.isObject(value) &&  Object.keys(value).map((fieldName: keyof CourseRunReportFormData ) => {
            
            if(fieldName === 'date_range') {

                filterData[fieldName] = {
                    value :  {start_date: value.date_range ? value.date_range[0]: null, end_date: value.date_range ? value.date_range[1] : null  },
                    comparator: 'date-range'
                }
            }
            else {

                filterData[fieldName] = {
                    value : value[fieldName] !== undefined ? value[fieldName] : null,
                    comparator: '='
                }
            }
       })
       
       this.props.callApi(endPoint, {export: true, customFilters: filterData })
         .then((blob: any) => {

            if(window.navigator.msSaveOrOpenBlob) {
                
                window.navigator.msSaveBlob(blob, fName);
            }
            else{

                var downloadLink = window.document.createElement('a');
                downloadLink.href = window.URL.createObjectURL(new Blob([blob], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet; charset=UTF-8' }));
                downloadLink.download = fName;
                document.body.appendChild(downloadLink);
                downloadLink.click();
                document.body.removeChild(downloadLink);

            }
         });  
    }

    render() {


        const breadcrumbs = [
            {title:'Course Run Report', url: '/course-run-report'}
        ];
        return (
            <Template {...this.props} breadcrumb={breadcrumbs} >
                <div className="lineframe email-template-edit-form">
            		<div className="lineframe-inner">
                        <From onSubmit={this.onSubmit} />
            		</div>
                </div>
            </Template>
        )
    }
}

export default connect(mapStateToProps, mapDispatchToProps)(CourseRunReport)