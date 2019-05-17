import * as React from 'react';
import { Row, Col } from 'react-bootstrap';
import { NavLink } from 'react-router-dom';
var FAS = require('react-fontawesome');
import {Props} from '../../features/root-props';

export default class Nav extends React.Component<Props>{

    render( ) {

        return (
            <Row>
                <Col md={6} sm={6} xs={12}>
                	<div className="course-list">
						<div className="course-list-inner">
							<h2>Course ACTIONS</h2>
							<ul className="course-listings">
                                <li><NavLink to="/course-run"><FAS name=""  className="list-view-icon"/>Create/Update Course Runs</NavLink></li>
                                <li><NavLink to="/maintain-course-run"><FAS name=""  className="list-view-icon"/>Maintain Course Runs</NavLink></li>
                                <li><NavLink to="/post-course-run-data"><FAS name=""  className="list-view-icon"/>Submit/Update Post Course Run Data</NavLink></li>
                                <li><NavLink to="/course-run-summary"><FAS name=""  className="list-view-icon"/>Submit/Update Post Course Summary Data</NavLink></li>    
				    		</ul>			
                            <h2>Reports</h2>
                            <ul className="course-listings">
                                <li><NavLink to="/placement-data-report"><FAS name=""  className="list-view-icon"/>Course Placement Data Report</NavLink></li>
                                <li><NavLink to="/course-run-report"><FAS name=""  className="list-view-icon"/>Course Run Data Report</NavLink></li>    
				    		</ul>			
						</div>
					</div>
                </Col>
                <Col md={6} sm={6} xs={12}>
                	<div className="course-list">
						<div className="course-list-inner">
							<h2>Admin Actions</h2>
                            <ul className="course-listings">
                            <li><NavLink to="/user"><FAS name=""  className="list-view-icon"/>Staff Database</NavLink></li>
                                <li><NavLink to="/course"><FAS name=""  className="list-view-icon"/>Maintain Course Directory</NavLink></li>
                                <li><NavLink to="/course-run-change-status"><FAS name=""  className="list-view-icon"/>Edit Course Run Status</NavLink></li>
                                {/* <li><NavLink to="/placement"><FAS name=""  className="list-view-icon"/>Submit/Upload Placement Data</NavLink></li> */}
                                <li><NavLink to="/email-template/1"><FAS name=""  className="list-view-icon"/>Email Content For Placement Confirm</NavLink></li>
                                <li><NavLink to="/email-template/2"><FAS name=""  className="list-view-icon"/>Email Content For Placement Cancel</NavLink></li>
                                <li><NavLink to="/email-template/3"><FAS name=""  className="list-view-icon"/>Email Content For Placement Reminder</NavLink></li>

                                <li><NavLink to="/attribute-list"><FAS name=""  className="list-view-icon"/>List Data</NavLink></li>
                            </ul>
						</div>
					</div>
                </Col>
            </Row>
        )
    }
}