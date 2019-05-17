import React, { Component } from 'react'
import * as bs from 'react-bootstrap'
import coloredlogo from '../assets/images/coloredlogo.png'
import {NavLink} from 'react-router-dom'

export default class Header extends Component {
  render() {
    return (
        <header className="header-section">
            <bs.Navbar>
              <bs.Navbar.Header>
                    <bs.Navbar.Brand>
                      <NavLink to="/">
                        <img src={coloredlogo} alt="colored-logo"/>
                      </NavLink>
                    </bs.Navbar.Brand>
                </bs.Navbar.Header>
                <bs.Nav className="inner-navbar">
						<bs.NavItem eventKey={1} href="#" className="prof-link">
							<NavLink to="/ProfessionalProgrammes">
								PROFESSIONAL SERVICES
							</NavLink>
						</bs.NavItem>
                      <bs.NavItem eventKey={2} href="#">
							<NavLink to="/ParentsCollegue">
								PARENTS COLLEGE
							</NavLink>
						</bs.NavItem>
                </bs.Nav>
            </bs.Navbar>
        </header>
     )
  }
}

