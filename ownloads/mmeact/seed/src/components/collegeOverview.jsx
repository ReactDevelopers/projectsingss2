import React, { Component } from 'react'
import {NavLink} from 'react-router-dom'
import imageBlock from '../assets/images/imageBlock.png'
import imageBlock2 from '../assets/images/imageBlock2.png'
import imageBlock3 from '../assets/images/imageBlock3.png'
import assesment from '../assets/images/assesment.png'
import assesmentRequire from '../assets/images/assesmentRequire.png'
import Header from '../layouts/header';
import Footer from '../layouts/footer';
import MobileHeader from '../layouts/mobileHeader'
import shareIcon from '../assets/images/share-icon.png'

export default class collegeOverview extends Component {
  render() {
    return (
      <div>
        <MobileHeader/>
        <Header/>
        <section className="overview-section bg-Gray">
            <section className="parent-banner banner2">
                    <div className="banner-image banner-bg">
                    </div>
            </section>
            <div className="container">
            
                <div className="Q-A-banner college-overview overview-wrapper sections-wrapper">
                <div class="d-flex flex-v-center flex-sps-between">
                    <h2 class="c-darkGray heading main">Social Emotional Development (The Parenting Years)</h2>
                    <a class="share-icon active" aria-current="page" href="/"><img src={shareIcon} alt="share"/>
                    </a>
                </div>
                    <div className="overview-block">
                        <h2 className="overview-heading c-pink fontbold">Overview</h2>
                        <h3>Do you find these questions familiar?</h3>
                        <div className="topic-bloic">
                            <ul className="clearfix">
                                <li>
                                    <div className="topic-block">
                                        <div className="image">
                                            <img src={imageBlock}/>
                                        </div>
                                        <div className="content">
                                            <h4>
                                            "Why is he/she throwing a tantrum!?"
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
                                            <h4>
                                            "Why doesn't he/ she share?"
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
                                            <h4>
                                            Will he/she have friends?"
                                            </h4>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                        <div className="content-wrapper p-t-40">
                            <p>Each child has his own temperament, our role as parents is to learn our child’s needs and develop r easonable expectations. </p>
                             <p className="p-t-20">By understanding their temperament, we can help our child develop social and emotional skills. Research shows that the first six years of a child’s life is the foundation of social and emotional skills, so skilled parenting is instrumental in these vital years.</p>
                        </div>
                        <p className="p-t-20 p-b-20 fontbold">Learn practical skills using real-life examples through our workshops today! </p>
                        <h3 className="c-pink p-t-20 p-b-10">Social and Emotional Development </h3>
                        <ul>
                            <li>Social and Emotional Skills</li>
                            <li>Help Me Behave Well</li>
                            <li>Help Me Manage My Inappropriate Behaviour</li>
                        </ul>
                    </div>
                    <div className="assesment-block bg-white pd-30 m-t-30">
                            <h2 className="c-pink p-b-40">Assessment</h2>
                            <div className="row">
                                <div className="col-md-2 col-sm-4 col-xs-12">
                                    <label>Date</label>
                                </div>
                                <div className="col-md-9 col-sm-8 col-xs-12">
                                    <div className="schedule-time p-b-20">
                                        <ul>
                                            <li className="c-pink">22 September - 6 October 2018 | 3x Saturdays</li>
                                            <li>22 September 2018</li>
                                            <li>29 September 2018</li>
                                            <li>6 October 2018</li>
                                        </ul>     
                                    </div>                         
                                </div>  
                                <div className="col-md-2 col-sm-4 col-xs-12">
                                    <div className="">
                                        <label>Time</label>
                                    </div>
                                </div>
                                <div className="col-md-9 col-sm-8 col-xs-12">
                                    <div className="common-height">
                                         <label className="c-pink">10am - 12pm</label>
                                    </div>
                                </div>
                                <div className="col-md-2 col-sm-4 col-xs-12">
                                    <div className="">
                                         <label>Venue</label>
                                    </div>
                                </div>
                                <div className="col-md-9 col-sm-8 col-xs-12">
                                    <div className="common-height">
                                        <label className="address c-pink p-b-20">
                                        NTUC Trade Union House 73 Bras Basah Road, #07-01 Singapore 189556
                                        </label>
                                    </div>
                                </div>
                                <div className="col-md-2 col-sm-4 col-xs-12">
                                    <div className="">
                                        <label>Workshop Fee</label>
                                    </div>
                                </div>
                                <div className="col-md-9 col-sm-8 col-xs-12">
                                    <div className="common-height">
                                        <label className="c-pink">$140 $97* (Key in promo code “NFC” at point of registration”)</label>
                                    </div>
                                </div>
                            </div>
                    </div>
                </div>  
                </div>

               
            </section>  
            <Footer/>
      </div>
    )
  }
}
