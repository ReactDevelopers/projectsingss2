import React, { Component } from 'react'
import OwlCarousel from 'react-owl-carousel';
import LandingBanner from '../layouts/landingBanner'
import Header from '../layouts/header'
import Footer from '../layouts/footer'
import carouselimage1 from '../assets/images/carouselimage1.png'
import carouselimage2 from '../assets/images/carouselimage2.png'
import carouselimage3 from '../assets/images/carouselimage3.png'
import compass from '../assets/images/compass.png'
import schedule from '../assets/images/schedule.png'
import MobileHeader from '../layouts/mobileHeader'
import {NavLink} from 'react-router-dom'

const options = {
    items : 3,
    dots:false,
    responsive:{
        0:{
            items:1,
            dots:true
        },
        600:{
            items:2,
            dots:true
        },
        768:{
            dots:false
        },
        1000:{
            items:3
        }
    }
};



export default class Home extends React.Component {

    componentDidMount() {
        window.scrollTo(0,0);
    }

    
    render() {
        return (
        <div className="home-section">
            <MobileHeader/>
            <LandingBanner/>
            <Header/>
            <section className="landing-banner-section">
                <div className="owl-carousel-section">
                    <div className="container">
                        <div className="banner-head">
                            <h2>Latest News</h2>
                        </div>
                        <OwlCarousel className="owl-theme"  margin={16} {...options}>
                            <div class="item">
                                <div className="block-wraper">
                                    <div className="image-block">
                                        <img src={carouselimage1} alt="Carousel"></img>
                                    </div>
                                    <div className="news-content">
                                        <h3>Chinese? We are NOT </h3>
                                        <div  className="slot-available">
                                            <h4>PARENT. COLLEGUE</h4>
                                            <ul>
                                                <li>6 October 2018 - Day 1 </li>
                                                <li>13 October 2018 - Day 2</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="item">
                                <div className="block-wraper">
                                    <div className="image-block">
                                        <img src={carouselimage2} alt="Carousel"></img>
                                    </div>
                                    <div className="news-content">
                                        <h3>幼儿保育和教</h3>
                                        <div  className="slot-available">
                                            <h4>PROFESSIONAL PROGRAMMES </h4>
                                            <ul>
                                                <li> Last intake for 2018! </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="item">
                                <div className="block-wraper">
                                <div className="image-block">
                                        <img src={carouselimage3} alt="Carousel"></img>
                                    </div>
                                    <div className="news-content">
                                        <h3>WSQ Advanced</h3>
                                        <div  className="slot-available">
                                            <h4>PROFESSIONAL PROGRAMMES </h4>
                                            <ul>
                                                <li> 2018年最后一班！ </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="item">
                                <div className="block-wraper">
                                    <div className="image-block">
                                        <img src={carouselimage1} alt="Carousel"></img>
                                    </div>
                                    <div className="news-content">
                                        <h3>Chinese? We are NOT </h3>
                                        <div  className="slot-available">
                                            <h4>PARENT. COLLEGUE</h4>
                                            <ul>
                                                <li>6 October 2018 - Day 1 </li>
                                                <li>13 October 2018 - Day 2</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="item">
                                <div className="block-wraper">
                                    <div className="image-block">
                                        <img src={carouselimage2} alt="Carousel"></img>
                                    </div>
                                    <div className="news-content">
                                        <h3>幼儿保育和教</h3>
                                        <div  className="slot-available">
                                            <h4>PROFESSIONAL PROGRAMMES </h4>
                                            <ul>
                                                <li> Last intake for 2018! </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="item">
                                <div className="block-wraper">
                                <div className="image-block">
                                        <img src={carouselimage3} alt="Carousel"></img>
                                    </div>
                                    <div className="news-content">
                                        <h3>WSQ Advanced</h3>
                                        <div  className="slot-available">
                                            <h4>PROFESSIONAL PROGRAMMES </h4>
                                            <ul>
                                                <li> 2018年最后一班！ </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>  
                            
                        </OwlCarousel>
                    </div>
                </div>
                <div className="shape-block">
                <ul className="shape-block-listing">
                    <li className="common-anime shape-1">
                        <img src={compass} alt="Compass"/>
                    </li>
                    <li className="common-anime shape-2">
                        <img src={schedule} alt="schedule"/>
                    </li>
                </ul>
            </div>
            </section>
            <section className="childhood-section">
                <div className="flex-wrapper">
                    <div className="earlychildood">
                        <NavLink to="/childhoodLanding" className="white">
                            <div className="childhoodcontent">
                                <div className="content-wrapper">
                                    <h2>your</h2>
                                    <h2>early childhood</h2>
                                </div>
                            </div>
                        </NavLink>
                    </div>
                    <div className="earlychildood yourchildhood">
                        <NavLink to="/parentLanding" className="white">
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
        </div>
        )
    }
}
