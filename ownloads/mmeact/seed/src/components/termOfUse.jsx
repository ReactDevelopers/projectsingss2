import React, { Component } from 'react'
import Header from '../layouts/header'
import Footer from '../layouts/footer'
import MobileHeader from '../layouts/mobileHeader'
import {NavLink} from 'react-router-dom'

export default class TermOfUse extends Component {

	componentDidMount(){
        window.scrollTo(0,0);
    }

	
  render() {
    return (
		<div>
			<MobileHeader/>
			<Header/>
			<div className="relative mobile-heading b-t-lightGray">
				<h2 className="c-darkGray visible-xs hidden">Terms of Use</h2>
        	</div>
			<section className="term-of-use-banner">
				<div className="banner-image banner-bg">
				</div>
			</section>
			<div className="sections-wrapper terms-page bg-Gray">
				<div className="container pd-xs-0 hidden-xs">
					<h2 className="c-darkGray heading main">Terms of Use</h2>
				</div>
				<section className="terms-content">
					<div className="container pd-xs-0">
						<div className="static-content space-around bg-white c-darkGray">
							<ul className="list-p-b-40">
								<li>
									<h4>1. INTELLECTUAL PROPERTY SEED </h4>
									<p>Institute reserves all intellectual property rights to this Web Site and the Services, including international copyright and trade mark rights. All other names, products and marks mentioned in this Web Site are the intellectual property of their respective owners. No materials provided through this Web Site, including text, graphics, code, audio, video and/or software may be reproduced, modified, adapted, distributed, published, displayed, uploaded, posted, transmitted, hyperlinked or dealt with in any manner and in any form whether in whole or in part without the express, prior written approval of SEED Institute and the respective copyright and trade mark holders. You shall not create any derivative work of such materials. The Services and materials provided through this Web Site are for the user's personal consumption only and the user may not engage in any dealings with other parties with such services and contents. Such dealings include dealings which will adversely affect the commercial value of the Services provided by SEED Institute . You may not also insert a hyperlink to this Web Site (or any part of this Web Site) on any other web site or mirror any of the contents contained on this Web Site on any other server. Nothing herein shall be construed as granting any right or any license to any trademarks or content on this Web Site without the written permission of SEED Institute or the respective intellectual property owners. In the event that you use the Services and/or materials in violation of the terms herein, whether for your own benefit or otherwise, you agree to hold SEED Institute fully indemnified against all losses and all actions, claims, proceedings, costs and damages and all legal costs or other expenses arising out of any such use or out of any claim by a third party based on your non-authorised use of such Services and/or materials.  </p>
								</li>
								<li>
									<h4 className="c-darkGray">2. PRIVACY</h4>
									<p>Internet user privacy is of paramount importance to SEED Institute and our clients. We support the protection of client and consumers' privacy rights as a fundamental element of our business. The following document thoroughly explains our privacy policy. Please read it carefully. If you have any questions, please contact info@seedinstitute.edu.sg</p>
								</li>
								<li>
									<h4 className="c-darkGray">Information Collection And Use</h4>
									<p> SEED Institute is the sole owner of the information collected on this site. We will not sell, share, or rent this information to others in ways different from what is disclosed in this statement. SEED Institute collects information from our users at several different points on our website. You agree that all information and/or particulars sent or submitted by you to SEED Institute in relation to the access of this website is non-confidential and non-proprietary unless otherwise expressly indicated by you. You further undertake not to submit any information and/or other materials which are or may be offensive, illegal or which may not be lawfully disseminated under any applicable law. </p>
								</li>
								<li>
									<h4 className="c-darkGray">Sharing </h4>
									<p>We may share aggregated demographic information with our partners. This is not linked to any personal information that can identify any individual person.</p>
								</li>
								<li>
									<h4 className="c-darkGray">Links</h4>
									<p>We read every message sent in and try to reply promptly. We use the information collected to respond directly to your questions or comments. We may also file user comments to improve the site and program, or review and discard the information. Users' personal information is only shared with third parties to fulfill service requests made by the user and in the situations described above. This web site contains links to other sites. Please be aware that SEED Institute is not responsible for the privacy practices of these other sites. We encourage our users to be aware when they leave our site and to read the privacy statements of each and every web site that collects personally identifiable information. This privacy statement applies solely to information collected by SEED Institute.</p>
								</li>
								<li>
									<h4 className="c-darkGray">Security</h4>
									<p>We do not request sensitive information (such as credit card number and/or social security number) from our users. SEED Institute takes every precaution to protect our users' information both online and offline. Periodically, our employees are notified and/or reminded about the importance we place on privacy, and what they can do to ensure our customers' information is protected. The servers on which we store personally identifiable information are kept in a secure environment, behind a locked cage. If you have any questions about the security at our website, you can send an email to info@seedinstitute.edu.sg. The transmission of data, information or content (including any transmissions over the Internet, through SMS, or other publicly accessible networks) is not secure, and is therefore subject to possible loss, interception or alteration while in transit. SEED Institute does not assume liability for any loss or damage you may suffer as a result of transmissions over the Internet, via SMS, or other publicly accessible networks.</p>
								</li>
								<li>
									<h4 className="c-darkGray">Site and Service Updates </h4>
									<p>We communicate with the user to provide requested services and respond to information requests via email or phone. </p>
								</li>
								<li>
									<h4 className="c-darkGray">Correction/Updating Personal Information </h4>
									<p>If a user's personally identifiable information changes (such as address), or if a user no longer desires our service, we will endeavor to provide a way to correct, update or remove that user's personal data provided to us.</p>
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
