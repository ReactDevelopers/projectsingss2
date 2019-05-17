import React, { Component } from 'react'
import {NavLink} from 'react-router-dom'
import imageBlock from '../assets/images/imageBlock.png'
import imageBlock2 from '../assets/images/imageBlock2.png'
import imageBlock3 from '../assets/images/imageBlock3.png'
import assesment from '../assets/images/assesment.png'
import shareIcon from '../assets/images/share-icon.png'
import assesmentRequire from '../assets/images/assesmentRequire.png'

export default class QuestionBanner extends Component {
  render() {
    return (
        <div>
        <div className="relative mobile-heading b-t-lightGray">
            <div className="d-flex flex-v-center flex-sps-between">
                <h2 className="c-darkGray visible-xs hidden">(WSQ) Mentoring Novice Teachers</h2>
                <NavLink to="" className="share-icon show-in-xs"><img src={shareIcon} alt="share"/></NavLink>
            </div>
           
        </div>
        <section className="Q-A-banner bg-Gray">
            <div className="banner-image banner-bg">
            </div>
            <div className="container pd-xs-0 hidden-xs">
                <div className="d-flex flex-v-center flex-sps-between">
                    <h2 className="c-darkGray heading main">(WSQ) Mentoring Novice Teachers</h2>
                    <NavLink to="" className="share-icon"><img src={shareIcon} alt="share"/></NavLink>
                </div>
            </div>
        </section>  
    </div>
    )
  }
}
