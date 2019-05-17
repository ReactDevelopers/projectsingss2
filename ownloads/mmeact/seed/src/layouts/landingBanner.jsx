import React, { Component } from 'react'
import * as bs from 'react-bootstrap'
import {NavLink} from 'react-router-dom'
import index from '../assets/sass/index.scss'
import logo from '../assets/images/logo.png'
import shape1 from '../assets/images/shape1.png'
import shape2 from '../assets/images/shape2.png'
import shape3 from '../assets/images/shape3.png'
import shape4 from '../assets/images/shape4.png'
import shape5 from '../assets/images/shape5.png'
import shape7 from '../assets/images/shape7.png'
import mosueobject from '../assets/images/mosueobject.png'

class LandingBanner extends React.Component {

  render() {
    return (
      <section className="banner-Section">
            <div className="container">
                <div className="landing-banner-wrapper">
                    <div className="logo">
                      <img src={logo}  alt="logo"/>
                    </div>
                    <div className="banner-text">
                      <h2>An Exceptional Journey Awaits</h2>
                        <NavLink class="btn find-out-more text-uppercase" to="/landingsection">
                        Find out more
                        </NavLink>
                    </div>
                </div>
            </div>
            <div className="shape-block">
                <ul className="shape-block-listing">
                    <li className="common-anime shape-1">
                      <img src={shape1}  alt="logo"/>
                    </li>
                    <li className="common-anime shape-2">
                        <img src={shape2}  alt="logo"/>
                    </li>
                    <li className="common-anime shape-3">
                        <img src={shape3}  alt="logo"/> 
                    </li>
                    <li className="common-anime shape-4">
                        <img src={shape4}  alt="logo"/>
                    </li>
                    <li className="common-anime shape-5">
                         <img src={shape5}  alt="logo"/>
                    </li>
                    <li className="common-anime shape-7">
                         <img src={shape7}  alt="logo"/>
                    </li>
                    <li className="common-anime shape-8">
                         <img src={mosueobject}  alt="logo"/>
                    </li>
                </ul>
            </div>
      </section>
    )
  }
}

export default LandingBanner