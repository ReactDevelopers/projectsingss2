import * as React from 'react';
import { Row, Col } from 'react-bootstrap';
import { NavLink } from 'react-router-dom';
var FAS = require('react-fontawesome');
import {Props} from '../../features/root-props';

export default class Nav extends React.Component<Props>{

    render( ) {
        
        const auth_user = this.props.helper.deepFind(this.props.rootState.server,'auth_user.response.data', {})

        return (
            <Row className="dashboard-menu-center">
                <Col md={12} sm={12} xs={12}>
                	<div className="course-list">
						<div className="course-list-inner">
							<h2>ACTIONS</h2>
                            <ul className="course-listings"> 
                                <li><NavLink to="/all-course"><FAS name=""  className="list-view-icon"/>Show All Courses</NavLink></li>
                                <li><NavLink to="/all-course-run"><FAS name=""  className="list-view-icon"/>Show All Course Runs</NavLink></li>
                                <li><NavLink to="/my-placement"><FAS name=""  className="list-view-icon"/>My Placements</NavLink></li>
                                {auth_user.is_supervisor ? 
                                <li><NavLink to="/subordinate-placement"><FAS name=""  className="list-view-icon"/>Subordinates Placements</NavLink></li>
                                : null }
                            </ul>
						</div>
					</div>
                </Col>
                {/* <Col md={6} sm={6} xs={12}>
                	<div className="course-list">
						<div className="course-list-inner">
							<h2>Menu</h2>
                            <ul className="course-listings"> 
                            <li><NavLink to="/subordinate-placement"><FAS name=""  className="list-view-icon"/>Subordinates Placements</NavLink></li>
                            </ul>
						</div>
					</div>
                </Col> */}
            </Row>
        )
    }
}