import React, { Component } from 'react'
import * as bs from 'react-bootstrap'
import Header from '../layouts/header'
import Footer from '../layouts/footer'
import MobileHeader from '../layouts/mobileHeader'
import {NavLink} from 'react-router-dom'

export default class Carrer extends Component {

	componentDidMount(){
			window.scrollTo(0,0);
	}

  render() {	
    return (
		<div>
			<MobileHeader/>
			<Header/>
			<div className="relative mobile-heading b-t-lightGray">
				<h2 className="c-darkGray visible-xs hidden">Career</h2>
			</div>
			<section className="term-of-use-banner">
				<div className="banner-image banner-bg">
				</div>
			</section>
			<div className="sections-wrapper career-page terms-page bg-Gray">
				<div className="container m-t-20-xs">
					<h2 className="c-darkGray heading main hidden-xs">Career</h2>
					<div className="c-darkGray">
						<p className="p-b-20">SEED Institute is hiring! </p>
						<p>Drop us an email with your resume to <NavLink to="mailto:info@seedinstitute.edu.sg" className=" c-pink text-underline">info@seedinstitute.edu.sg.</NavLink></p>
						<p>Shortlisted candidates will be contacted for interview via email or mobile.</p>
					</div>
				</div>
				<div className="accordion-section">
					<div className="container pd-xs-0">
						<bs.PanelGroup accordion id="accordion1">
							<bs.Panel eventKey="1">
								<bs.Panel.Heading>
									<bs.Panel.Title toggle className="down-arrow">Senior Executive, Partnerships</bs.Panel.Title>
								</bs.Panel.Heading>
								<bs.Panel.Body collapsible>
								<div className="static-content space-around bg-white c-darkGray">
									<ul className="list-p-b-40">
										<li>
											<h4>Responsibilities:</h4>
											<ul className="bullet-list">
												<li>Strategic planning of partnerships to drive marketing performance metrics and to increase visitorship and engagement of Kidzmatters mobile site, training programmes and workshops.</li>
												<li>Lead generation, partner negotiation, building of partner pitch decks, closing partnership deals and management of partner accounts. </li>
												<li>Project management of partner supported activities and campaigns, including execution and post analysis tracking of partnership campaigns.</li>
												<li>Identifying future partnerships which can driving positive marketing performance from the insights.</li>
												<li>Corporate engagement and account servicing of corporate operators and centre partnerships.</li>
												<li>Working closely with team to close partnership deals, being fully responsible for the marketing components of the deal .</li>
												<li>Overall partner account management.</li>
												<li>Able to work closely and collaboratively with multiple stakeholders, both internal and external.</li>
											</ul>
										</li>
										<li>
											<h4>Requirements:</h4>
											<ul className="bullet-list">
												<li>Charismatic presenter who can sell marketing ideas as the solution to business problems.</li>
												<li>Ability to negotiate and demonstrate clear logical thinking of what can be beneficial in a partnership to our business.</li>
												<li>Ability to communicate effectively along with excellent writing skills.</li>
												<li>Good knowledge of Windows Office programmes especially in Powerpoint and Excel.</li>
												<li>Comfortable with data and analytics in order to monitor partner and affiliate performance.</li>
												<li>Experience in a partnerships, sales or account management.</li>
												<li>Effective project management skills.</li>
												<li>Excellent administration and organizational skills and being meticulous.</li>
												<li>Minimum 2 years in in partnerships, marketing, advertising sales, advertising agency and/or consulting roles.</li>
												<li>Independent and result-driven mindset.</li>
											</ul>
										</li>
									</ul>
								</div>
								</bs.Panel.Body>
							</bs.Panel>
							<bs.Panel eventKey="2">
								<bs.Panel.Heading>
									<bs.Panel.Title toggle className="down-arrow">Executive, Sales &amp; Marketing</bs.Panel.Title>
								</bs.Panel.Heading>
								<bs.Panel.Body collapsible>
									<div className="static-content space-around bg-white c-darkGray">
										Executive, Sales &amp; Marketing
									</div>
								</bs.Panel.Body>
							</bs.Panel>
							<bs.Panel eventKey="3">
								<bs.Panel.Heading>
									<bs.Panel.Title toggle className="down-arrow">Copywriter</bs.Panel.Title>
								</bs.Panel.Heading>
								<bs.Panel.Body collapsible>
									<div className="static-content space-around bg-white c-darkGray">
										Copywriter Conent
									</div>
								</bs.Panel.Body>
							</bs.Panel>
						</bs.PanelGroup>
					</div>
				</div>
			</div>
			<Footer/>  
		</div>
    )
  }
}
