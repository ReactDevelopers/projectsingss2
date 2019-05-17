import React, { Component } from 'react'
import Header from '../layouts/header'
import Footer from '../layouts/footer'
import MobileHeader from '../layouts/mobileHeader'
import {NavLink} from 'react-router-dom'

export default class siteMap extends Component {

	componentDidMount(){
		window.scrollTo(0,0);
}


  render() {
    return (
      	<div>
			<MobileHeader/>
			<Header/>
			<div className="relative mobile-heading b-t-lightGray">
				<h2 className="c-darkGray visible-xs hidden">Sitemap</h2>
        	</div>
			<section className="term-of-use-banner">
				<div className="banner-image banner-bg">
				</div>
			</section>
			<div className="sections-wrapper terms-page bg-Gray">
				<div className="container pd-xs-0 hidden-xs">
					<h2 className="c-darkGray heading main">Sitemap</h2>
				</div>
				<section className="terms-content">
					<div className="container pd-xs-0">
						<div className="static-content space-around bg-white c-darkGray">
							<ul className="bullet-list md list-p-b-20 fontsemibold">
								<li><NavLink to="/aboutUs">About Us</NavLink></li>
								<li><NavLink to="/ProfessionalProgrammes">Professional Services</NavLink></li>
								<li><NavLink to="/ParentsCollegue">Parents College</NavLink></li>
								<li><NavLink to="/contactUs">Contact Us</NavLink></li>
							</ul>
						</div>
					</div>
				</section>
			</div>
			<Footer/>  
		</div>
    )
  }
}
