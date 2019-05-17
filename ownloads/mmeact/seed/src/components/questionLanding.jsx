import React, { Component } from 'react'
import Header from '../layouts/header'
import Footer from '../layouts/footer'
import AnimatedIcon from '../layouts/AnimatedIcon'
import QuestionOverview from '../layouts/questionOverview'
import {NavLink} from 'react-router-dom'
import MobileHeader from '../layouts/mobileHeader'
import QuestionBanner from '../layouts/questionBanner'

export default class questionLanding extends Component {
  componentDidMount()
  {
    window.scrollTo(0,0);
  }

  render() {

    return (
      <div className="relative question-pages">
          <MobileHeader/>
          <Header/>
          <AnimatedIcon/>
          <QuestionBanner/>
          <QuestionOverview/>
          <Footer/>
      </div>
    )
    
  }
}
