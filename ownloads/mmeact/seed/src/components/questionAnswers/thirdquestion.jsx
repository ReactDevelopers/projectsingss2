import React, { Component } from 'react'
import Header from '../../layouts/header'
import Footer from '../../layouts/footer'
import AnimatedIcon from '../../layouts/AnimatedIcon'
import QuestionOverview from '../../layouts/questionOverview'
import QuestionBanner from '../../layouts/questionBanner'
import {NavLink} from 'react-router-dom'
import MobileHeader from '../../layouts/mobileHeader'

export default class fifthquestion extends Component {
    
  

  render() {
    return (
        <div className="relative">
            <MobileHeader/>
            <Header/>
            <AnimatedIcon/>
            <QuestionBanner/>
            <div className="hidden-xs">
                <QuestionOverview/>
            </div>
            <div className="show-in-xs">
                <div className="row pd-30">
                    <div className="col-md-6 col-sm-6 col-xs-6 p-r-0">
                        <h2 class="heading lg c-pink fontbold">Programme Fee</h2>
                    </div>
                    <div className="col-md-6 col-sm-6 col-xs-6 text-right">
                        <NavLink to="" className="c-darkGray text-underline">
                            <p className="hidden-xs">Back</p>
								<NavLink className="c-darkGray text-underline" to="/questionone">Restart search</NavLink>
                        </NavLink>
                    </div>
                </div>
            </div>
            <section className="question-block">
                <div className="container pd-xs-0">
                    <div className="questionWrapper set-space">
                        <div className="desc-text c-darkGray hidden-xs">
                            <h4 className="sm p-b-20">
                                <strong>Find out how much is the programme fee and subsidy is by using our programme fee calulator.</strong>
                            </h4>
                            <p>
                                Maecenas dapibus dolor a vulputate gravida. Nam vitae rutrum nisi. Phasellus ultricies a dolor eget lacinia. Aliquam auctor commodo nisl ut fermentum.
                            </p>
                        </div>
                        <div className="question-one">  
                            <div className="row p-b-30">
                                <div className="col-md-6 col-sm-6 col-xs-12">
                                    <p class="c-darkGray fontbold">Salary Range?</p>
                                </div>
                                <div className="col-md-6 col-sm-6 col-xs-12 text-right hidden-xs">
                                    <NavLink to="" className="c-darkGray text-underline">
                                        <p>Restart your search</p>
                                    </NavLink>
                                </div>
                            </div>
                            <div className="row">
                                <div className="col-md-6 col-sm-6 col-xs-12">
                                    <NavLink to="/checkout" className="static-box sm global-shadow bg-white text-center d-block">
                                        <span className="label-text md c-darkGray">$2,000 and below</span>
                                    </NavLink>
                                </div>
                                <div className="col-md-6 col-sm-6 col-xs-12 m-t-20-xs">
                                    <NavLink to="/checkout" className="static-box sm global-shadow bg-white text-center d-block">
                                        <span className="label-text md c-darkGray">Above $2,000</span>
                                    </NavLink>
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
