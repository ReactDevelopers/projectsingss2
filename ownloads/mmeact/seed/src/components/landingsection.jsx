import React, { Component } from 'react'
import Header from '../layouts/header'
import Footer from '../layouts/footer'
import MobileHeader from '../layouts/mobileHeader'
import {NavLink} from 'react-router-dom'


export default class LandingSection extends Component {

    componentDidMount(){
        window.scrollTo(0,0);
    }

      
  render() {
        return(
            <main className="landing-banner">   
                <MobileHeader/>
                <Header/>
                <section className="childhood-section">
                    <div className="flex-wrapper">
                        <div className="earlychildood">
                            <NavLink to="/childhoodLanding" className="white anchor-link">
                            <div className="childhoodcontent">
                                    <div className="content-wrapper">
                                        <h2>your</h2>
                                        <h2>early childhood</h2>
                                    </div>
                                </div>
                            </NavLink>
                        </div>
                        <div className="earlychildood yourchildhood">
                            <NavLink to="/parentLanding" className="white anchor-link">
                                <div className="childhoodcontent">
                                    <div className="content-wrapper parent-wrapper">
                                        <h2>your</h2>
                                        <h2>parenting skills</h2>
                                    </div>
                                </div>
                            </NavLink>
                        </div>
                    </div>
                </section>
                <Footer/>
            </main>
        )
  }
}

