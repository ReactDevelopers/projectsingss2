import React, { Component } from 'react'
import {NavLink} from 'react-router-dom'
import imageBlock from '../assets/images/imageBlock3.png'
import imageBlock2 from '../assets/images/imageBlock2.png'
import imageBlock3 from '../assets/images/imageBlock.png'
import assesment from '../assets/images/assesment.png'
import assesmentRequire from '../assets/images/assesmentRequire.png'

export default class questionOverview extends Component {
  render() {
    return (
      <div>
        <section className="overview-section bg-Gray">
            <div className="container pd-xs-0">
                <div className="overview-wrapper sections-wrapper p-b-20">
                    <div className="overview-block">
                        <h2 className="overview-heading c-pink bold">Overview</h2>
                        <p className="mentor"><strong>Mentor teachers to enhance their daily practice</strong></p>
                        <p className="c-darkGray">Contribute to fostering a culture of continuous learning (Senior Pre-school Teacher)</p>
                        <h3 className="topic-head">Topics</h3>
                        <div className="topic-bloic">
                            <ul className="clearfix">
                                <li>
                                    <div className="topic-block">
                                        <div className="image">
                                            <img src={imageBlock}/>
                                        </div>
                                        <div className="content">
                                            <h4 className="c-darkGray">
                                            Develop an orientation program
                                            </h4>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div className="topic-block">
                                        <div className="image">
                                            <img src={imageBlock2}/>
                                        </div>
                                        <div className="content">
                                            <h4 className="c-darkGray">
                                            Co-ordinate and mentor novice teacher’s progress
                                            </h4>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div className="topic-block">
                                        <div className="image">
                                        <img src={imageBlock3}/>
                                        </div>
                                        <div className="content">
                                            <h4 className="c-darkGray">
                                            Evaluate, assess and report on novice teacher’s progress
                                            </h4>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                        <span className="note c-darkGray hidden-xs">Note: Participants are required to bring along the Handbook for Mentors for every session.</span>
                    </div>
                </div>  
                <div className="assesment-wrapper">
                    <div className="row m-custom">
                        <div className="col-md-6 col-sm-6 col-xs-12 p-custom">
                            <div className="assesmnet-block bg-white">
                                <h3 className="c-pink fontbold">Assessment</h3>
                                    <div className="blockWrapper">
                                        <div className="blockImage">
                                            <img src={assesment}/>
                                        </div>
                                        <div className="blockContent">
                                            <h3 className="c-md-gray">Written Assignment</h3>
                                            <ul className="bullet-list c-darkGray">
                                                <li>Guidebook</li>
                                                <li>Reflective Journaling</li>
                                            </ul>
                                        </div>
                                    </div>
                            </div>
                        </div>
                        <div className="col-md-6 col-sm-6 col-xs-12 p-custom m-t-20-xs">
                            <div className="assesmnet-block entry-requiremnt bg-white">
                                <h3 className="c-pink fontbold">Entry Requirement</h3>
                                <div className="blockWrapper">
                                    <div className="blockImage">
                                        <img src={assesmentRequire}/>
                                    </div>
                                    <div className="blockContent">
                                        <h3 className="c-md-gray">Applicants are recommended to possess the following: </h3>
                                        <p className="c-darkGray">Completed accredited course (WSQ PDECCE, WSQ PDECCE (CC) and/or WSQ SDECCE) with at least 3 years of teaching experience.</p>
                                    </div>
                                </div>
                            </div> 
                        </div>
                    </div>
                </div>
                <div className="program-buttons">
                    <ul>
                        <li>
                            <NavLink to="/questionone" className="btn c-pink fontbold md-radius global-shadow">Programme Fee</NavLink>
                        </li>
                        <li>
                            <NavLink to="/" className="btn c-pink fontbold md-radius global-shadow">Register Now </NavLink>
                        </li>
                    </ul>
                </div>
            </div>
        </section>  
      </div>
    )
  }
}
