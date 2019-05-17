import React, { Component } from 'react'
import coloredlogo from '../assets/images/coloredlogo.png'
import {NavLink} from 'react-router-dom'
import shape1 from '../../src/assets/images/shape1.png'
import shape2 from '../../src/assets/images/shape2.png'
import shape3 from '../../src/assets/images/shape3.png'
import AnimatedIcon from '../layouts/AnimatedIcon'
import bottomWear from '../assets/images/bottom-wear.png'
import book from '../assets/images/book.png'
import puzzle from '../assets/images/puzzle.png'


class mobileHeader extends React.Component{


    constructor(props){
        super(props)    
        this.state = {
            condition : false
        }        
    }
    toggle(){
        this.setState({
            condition: !this.state.condition
        });
    }
    untoggled(){
        this.setState({
            condition : false
        });
    }
    
    render() {
        return (
            <div>
          
            <div className="top-header hidden visible-xs">
              
                <div className="container">
                    <div class="navbar-header inline-block pull-left">
                        <NavLink to="/" className="navbar-brand h-auto">
                             <img src={coloredlogo} alt="colored-logo"/>
                        </NavLink>
                    </div>
                    <div className="toggle-icon inline-block pull-right">
                        <button className="btn" onClick={this.toggle.bind(this)}>
                            <span></span>
                            <span></span>
                            <span></span>
                        </button>
                    </div>
                </div>
                <div className={ this.state.condition ? "sidebar-block show" : "sidebar-block" }>                
                    <button className="cross-link" onClick={this.untoggled.bind(this)}>
                        <span></span>
                        <span></span>
                    </button>
                    <div className="mobile-animated-icon">
                        <AnimatedIcon/>
                    </div>
                    <div className="logo">
                        <NavLink  to="/">
                            <img src={coloredlogo}/>
                        </NavLink>
                    </div>
                    <div className="menu-list">
                        <ul>
                            <li>
                                <NavLink to="/aboutUs">
                                  About Us
                                </NavLink>
                                </li>
                            <li> <NavLink to="/ProfessionalProgrammes">
                                   Professional Services
                                </NavLink>
                            </li>
                            <li>
                                <NavLink to="/ParentsCollegue">
                                    Parents College
                                </NavLink>
                            </li>
                            <li>
                                <NavLink to="/contactUs">
                                    Contact Us
                                </NavLink>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            </div>
        );
    }
}

export default mobileHeader;