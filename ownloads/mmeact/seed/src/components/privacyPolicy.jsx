import React, { Component } from 'react'
import Header from '../layouts/header'
import Footer from '../layouts/footer'
import MobileHeader from '../layouts/mobileHeader'
import {NavLink} from 'react-router-dom'

export default class PrivacyPolicy extends Component {

	componentDidMount(){
        window.scrollTo(0,0);
    }

	
  render() {
    return (
		<div>
			<MobileHeader/>
			<Header/>
			<div className="relative mobile-heading b-t-lightGray">
				<h2 className="c-darkGray visible-xs hidden">Privacy Policy</h2>
        	</div>
			<section className="term-of-use-banner">
				<div className="banner-image banner-bg">
				</div>
			</section>
			<div className="sections-wrapper terms-page bg-Gray">
				<div className="container pd-xs-0 hidden-xs">
					<h2 className="c-darkGray heading main">Privacy Policy</h2>
				</div>
				<section className="terms-content">
					<div className="container pd-xs-0">
						<div className="static-content space-around bg-white c-darkGray">
							<ul className="list-p-b-40">
								<li>
									<p>
									Here at SEED Institute, we are dedicated to protecting your personally identifiable information and respect the privacy of all our students and business contacts. Please read this DATA Protection to learn more about the ways in which we collect, use and protect your personal information.</p>
								</li>
								<li>
									<h4 className="heading main">1	If you:</h4>
									<ul className="bullet-list">
										<li>have any questions or feedback relating to your Personal Data or our Data Protection Policy;</li>
										<li>would like to withdraw your consent to any use of your Personal Data as set out in this Data Protection Policy; or</li>
										<li>would like to obtain access and make corrections to your Personal Data records.</li>
									</ul>
									<p className="p-t-30 p-b-30"><strong>You can approach us via the following channels:</strong></p>
									<ul className="bullet-list">
										<li>Email us at <NavLink to="mailto:info@seedinstitute.edu.sg" className=" c-pink text-underline">info@seedinstitute.edu.sg</NavLink></li>
										<li>Call our hotline at <strong>(+65) 6332 0668</strong></li>
										<li><strong>Approach our staff at the City Campus</strong></li>
									</ul>
								</li>
								<li>
									<p className="heading main">    2 You may also write to our Data Protection Officer as follows:</p>
									<p>
										<ul className="fontbold">
											<li>Data Protection Officer</li>
											<li>SEED Institute</li>
											<li>73 Bras Basah Road</li>
											<li>#07-01 NTUC Trade Union House</li>
											<li>Singapore 189556</li>
										</ul>
									</p>
								</li>
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
