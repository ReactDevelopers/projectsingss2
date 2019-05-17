import React, { Component } from 'react'
import * as bs from 'react-bootstrap'
import fb from '../assets/images/fb.png'
// import { Formik, Form, Field, ErrorMessage } from 'formik';
import {NavLink} from 'react-router-dom'
    
export default class footer extends Component {

    emailSubs(){
        console.log(window.location.href);    
    }

  render() {
    return (
        <div>
            <div className="logo-wrapper text-center">
                <ul className="bottom-logo">
                    <li>
                    </li>   
                    <li className="newlogo">
                    </li>
                </ul>
            </div>
            <footer className="footer-section">
                <div className="container">
                    <bs.Row className="p-b-10">
                        <bs.Col md={8} sm={8} xs={12}>
                            <div className="footer-left-wrapper">
                                <bs.Row>
                                    <bs.Col md={4} sm={3} xs={12}>
                                        <div className="address-block">
                                            <p>SEED Institute Pte Ltd Mountbatten Square 229 Mountbatten Road #02-42 Singapore 398007</p>
                                        </div>
                                        <div className="mail-box hidden visible-xs">
                                            <span className="mail-icon">info@seedinstitute.edu.sg</span>
                                        </div>
                                    </bs.Col>
                                    <bs.Col md={8} sm={9} xs={12}>
                                        <div className="subscription-form">
                                            {/* <Formik initialValues={{ email : ''}}>
                                                validate = {
                                                    values => {
                                                        let errors = {};
                                                        if (!values.email) {
                                                            errors.email = 'Required';
                                                        }
                                                        else if(!/^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,}$/i.test(values.email)){
                                                            errors.email = 'Invalid email address';
                                                        }
                                                        return errors;
                                                    }}
                                                    {({ isSubmitting }) => (
                                                        <Form className="form-group">
                                                            <Field type="email" name="email" />
                                                            <ErrorMessage name="email" component="div" />
                                                            <button type="submit" className="btn" disabled={isSubmitting}>
                                                                Submit
                                                            </button>
                                                        </Form>
                                                    )}
                                            </Formik> */}
                                            <div className="form-group">
                                                <input type="email" class="form-control" placeholder="Join our newsletter"/>
                                                <button className="btn" onClick={this.emailSubs.bind(this)}></button>
                                            </div>
                                        </div>
                                    </bs.Col>
                                </bs.Row>
                                <bs.Row className="p-t-30">
                                    <bs.Col md={4} sm={12} xs={12}>
                                        <div className="mail-box hidden-xs">
                                            <span className="mail-icon">info@seedinstitute.edu.sg</span>
                                        </div>
                                    </bs.Col>
                                    <bs.Col md={8} sm={12} xs={12}>
                                    <div class="checkboxBtn">
                                        <input type="checkbox" id="checkbox02" name="checkbox"/>
                                        <label for="checkbox02"><span></span>Yes, I accept the <NavLink className="termscondition" to="/termsOfUse">Terms & Conditions</NavLink></label>
                                    </div>
                                    </bs.Col>
                                </bs.Row>
                            </div>
                        </bs.Col>
                        <bs.Col md={4} sm={4} xs={12}>
                            <div className="footer-right-wrapper">
                                <bs.Row>
                                    <bs.Col md={6} sm={6} xs={12}>
                                        <div className="footer-list">
                                            <ul>
                                                <li><NavLink to="/aboutUs">About Us</NavLink></li>
                                                <li><NavLink to="/contactUs">Contact Us</NavLink></li>
                                                <li><NavLink to="/carrer">Career</NavLink></li>
                                                <li className="hidden-xs p-t-10">
                                                    <img src={fb} alt="fb-logo"/>
                                                </li>
                                            </ul>
                                        </div>
                                    </bs.Col>
                                    <bs.Col md={6} sm={6} xs={12}>
                                        <div className="footer-list">
                                            <ul>
                                                <li><NavLink to="/siteMap">Sitemap</NavLink></li>
                                                <li><NavLink to="/termsOfUse">Terms of Use</NavLink></li>
                                                <li><NavLink to="/PrivacyPolicy">Privacy Policy</NavLink></li>
                                                <li className="hidden visible-xs p-t-10">
                                                    <img src={fb} alt="fb-logo"/>
                                                </li>
                                            </ul>
                                        </div>
                                    </bs.Col>
                                </bs.Row>
                            </div>
                        </bs.Col>
                    </bs.Row>
                </div>      
                <div className="bottom-footer">
                    <div className="container">
                        <bs.Row>
                            <bs.Col md={4} sm={6} xs={12}>
                                <div className="copyright-text text-left">
                                    <p>COPYRIGHT 2014 SEED INSTITUTE.</p>
                                </div>
                            </bs.Col>
                            <bs.Col md={8} sm={6} xs={12}>
                            <div className="copyright-text text-right">
                                    <p>CPE REGISTRATION NO.: 199504758Z | PERIOD OF REGISTRATION: 25.02.2015 - 24.02.2019.</p>
                                </div>
                            </bs.Col>
                        </bs.Row>
                    </div>
                </div>    
            </footer> 
        </div>
     )
  }
}

