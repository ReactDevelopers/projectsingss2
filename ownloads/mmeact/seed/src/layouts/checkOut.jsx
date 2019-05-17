import React, { Component } from 'react'
import Header from '../../src/layouts/header'
import Footer from '../../src/layouts/footer'
import AnimatedIcon from '../../src/layouts/AnimatedIcon'
import QuestionOverview from '../layouts/questionOverview'
import QuestionBanner from '../../src/layouts/questionBanner'
import {NavLink} from 'react-router-dom'
import MobileHeader from '../../src/layouts/mobileHeader'

export default class checkout extends Component {
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
							<p>Restart search</p>
						</NavLink>
					</div>
				</div>
			</div>
			<div className="question-block">
				<div className="container pd-xs-0">
					<div className="questionWrapper summary-block set-space">
						<div className="desc-text c-darkGray hidden-xs">
							<h4 className="sm p-b-20">
								<strong>Find out how much is the programme fee and subsidy is by using our programme fee calulator.</strong>
							</h4>
							<p>
								Maecenas dapibus dolor a vulputate gravida. Nam vitae rutrum nisi. Phasellus ultricies a dolor eget lacinia. Aliquam auctor commodo nisl ut fermentum.
							</p>
						</div>
						<div className="set-block-space row p-b-30">
							<div className="col-md-6 col-sm-6 col-xs-12">
								<h2 class="heading md c-darkGray fontbold hidden-xs">Summary</h2>
								<h2 class="heading md c-darkGray fontbold show-in-xs">Result</h2>
							</div>
							<div className="col-md-6 col-sm-6 col-xs-12 text-right hidden-xs">
							
								<NavLink className="c-darkGray text-underline" to="/questionone">Restart search</NavLink>
							
							</div>
						</div>
						<div className="summary-list set-block-space c-darkGray bg-Gray">
							<p className="p-b-20"><strong>Programme Fee</strong></p>
							<h2 className="heading md main">SSG 90% (SME)</h2>
							<div className="row">
								<div className="col-md-6 col-sm-6 col-xs-12">
									<ul>
										<li>
											<p><strong>Full course fee</strong></p>
											<p>$350.00</p>
										</li>
										<li>
											<p><strong>SSG Grant</strong></p>
											<p>$0.00</p>
										</li>
										<li>
											<p><strong>Nett course fee</strong></p>
											<p>$350.00</p>
										</li>
									</ul>
								</div>
								<div className="col-md-6 col-sm-6 col-xs-12 ">
									<ul>
										<li>
											<p><strong>7% GST on nett course fee</strong></p>
											<p><strong> $24.50</strong></p>
										</li>
										<li>
											<p><strong>Total nett course fee payable, including GST</strong></p>
											<p><strong>$374.50</strong></p>
										</li>
										<li>
											<p><strong>Additional funding</strong></p>
											<p><strong>$315.00</strong></p>
										</li>
									</ul>
								</div>
							</div>
							<div className="pd-40 bg-white m-t-10">
								<p className="p-b-10"><strong>Total nett course fee payable, including GST, after additional funding</strong></p>
								<div className="d-flex flex-v-center flex-sps-between">
									<h2 className="heading x-lg fontRegular">$374.50</h2>
									<NavLink to="" className="btn md global-shadow hidden-xs">Pay Now</NavLink>
									<NavLink to="" className="btn md global-shadow show-in-xs m-auto">Register Now</NavLink>
								</div>
							</div>
						</div>
					</div>
				</div>
          	</div>
          	<Footer/>
      </div>
    )
  }
}
