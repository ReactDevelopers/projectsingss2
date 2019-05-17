import React, { Component } from 'react'
import * as bs from 'react-bootstrap'
import Header from '../layouts/header'
import Footer from '../layouts/footer'
import MobileHeader from '../layouts/mobileHeader'
import AnimatedIcon from '../layouts/AnimatedIcon'
import {NavLink} from 'react-router-dom'
import seedInstitute from '../assets/images/seed-institute.png'
import orgChart from '../assets/images/org-chart.png'
import pdfIcon from '../assets/images/pdf-icon.png'
import shareIcon from '../assets/images/share-icon.png'
import Tabs from 'react-responsive-tabs';
import 'react-responsive-tabs/styles.css';

export default class aboutUs extends Component {

				componentDidMount(){
					window.scrollTo(0,0);
				}
	
  render() {

	const presidents = [
		{
			name: 'Campus and facilities', 
			biography: 
			<div className="tabing-blocks bg-white">
				<div className="tab-content parallel-block d-flex flex-v-center">
					<div className="img-block">
						<img src={seedInstitute} alt="Seed Institute"></img>
					</div>
					<div className="desc-block p-l-20">
						<h2 className="heading main lg fontbold c-darkGray">SEED Institute</h2>
						<span className="c-darkGray">(City Campus)</span>
						<p>The campus is purpose-designed to enhance students’ learning journey with us. It has 9 classrooms all equipped with state-of-the-art facilities, including LCD projectors, Computers and AV systems. All classrooms can accommodate up to 45 students.</p>
					</div>
				</div>
				<p className="p-t-40">One of our greatest assets that benefit our students extensively is the SEED Institute Library. It is well-known for its extensive collection of books in the field of early childhood care and education. Updated regularly to keep up with the latest developments, it offers students access to more than 8,000 titles comprising textbooks, manuals, resource books, current periodicals, regional publications, journals, online publications and children’s literature</p>
			</div>
		},
		{
			name: 'Academic & Examination Board', 
			biography: 
			<div className="tabing-blocks md bg-white">
				<div className="tab-content">
					<div className="p-r-30 p-l-30">
						<h2 className="heading main md fontbold c-darkGray">Academic Review Board</h2>
						<div>
							<h4 className="heading main">CHAIRMAN</h4>
							<ul className="inline-list list-50 clearfix">
								<li>
									<div className="list-decs">
										<h4>Mr John Ang </h4>
										<p>(Senior Fellow Department of Social Work, National University of Singapore)</p>
									</div>
								</li>
							</ul>
						</div>
						<div>
							<h4 className="heading main">MEMBERS</h4>
							<ul className="inline-list list-50 clearfix">
								<li>
									<div className="list-decs">
										<h4>Dr Khoo Kim Choo</h4>
										<p>(Consultant, Children and Families / Director, Preschool for Multiple Intelligences)</p>
									</div>
								</li>
								<li>
									<div className="list-decs">
										<h4>Dr Khoo Kim Choo</h4>
										<p>(Educational Consultant, National Institute of Education / NIE(I) / NIE(CTL))</p>
									</div>
								</li>
								<li>
									<div className="list-decs">
										<h4>Ms Ho Yin Fong</h4>
										<p>(Academic Director, Office of Academic Affairs (SEED Institute), Chief Early Childhood Education Officer, NTUC First Campus)</p>
									</div>
								</li>
							</ul>
						</div>
					</div>
				</div>
				<div className="tab-content">
					<div className="p-r-30 p-l-30">
						<h2 className="heading main md fontbold c-darkGray">SEED Assessment & Examination Committee</h2>
						<div>
							<h4 className="heading main">MEMBERS</h4>
							<ul className="inline-list list-50 clearfix">
								<li>
									<div className="list-decs">
										<h4>Ho Yin Fong</h4>
										<p>(Academic Director, (SEED Institute) & Chief Early Childhood Education Officer (NTUC First Campus)</p>
									</div>
								</li>
								<li>
									<div className="list-decs">
										<h4>Dr Kok Siat Yeow </h4>
										<p>(Deputy Director, Programmes)</p>
									</div>
								</li>
								<li>
									<div className="list-decs">
										<h4>Low Siew Hong </h4>
										<p>(Head, Faculty Management and Academic Qualifications)</p>
									</div>
								</li>
								<li>
									<div className="list-decs">
										<h4>Millicent Nargis Bawany </h4>
										<p>(Senior Lecturer)</p>
									</div>
								</li>
								<li>
									<div className="list-decs">
										<h4>Ng Quee Hiang Juliana </h4>
										<p>(Senior Lecturer)</p>
									</div>
								</li>
								<li>
									<div className="list-decs">
										<h4>Suraya Binte Saidon </h4>
										<p>(Senior Lecturer)</p>
									</div>
								</li>
								<li>
									<div className="list-decs">
										<h4>Tan Choo Neo Mona </h4>
										<p>(Head, Faculty Management and Academic Qualifications)</p>
									</div>
								</li>
								<li>
									<div className="list-decs">
										<h4>Jenny Wong </h4>
										<p>(Head, Business & Parenting)</p>
									</div>
								</li>
								<li>
									<div className="list-decs">
										<h4>Lim Sze Hui</h4>
										<p>(Manager, Faculty Management)</p>
									</div>
								</li>
								<li>
									<div className="list-decs">
										<h4>Pamela Han</h4>
										<p>(Senior Manager, Programme Management)</p>
									</div>
								</li>
							</ul>
						</div>
					</div>
				</div>
			</div>
		},
		{
			name : 'Organisational Structure',
			biography: 
			<div className="tabing-blocks md bg-white">
				<div className="tab-content">
					<div className="p-r-30 p-l-30">
						<h2 className="heading main x-lg fontbold c-darkGray">SEED Organisation Chart</h2>
						<div className="text-center p-b-40">
							<img src={orgChart} alt="Seed Organization Chart"></img>
						</div>
						<div>
							<h4 className="heading md main">Managers of Private Education Institution (PEI)</h4>
							<h4 className="heading main">
								Mr Chan Tee Seng<br/> 
								Chief Executive Officer <br/>
								NTUC First Campus Co-operative Ltd
							</h4>
							<div className="desc-text">
								<p>Mr Chan has been the Chief Executive Officer of NTUC First Campus Co-operative since January 2009. He has served in various capacities within the National Trades Union Congress (NTUC) and its social enterprises in his more than 20 years with the Labour Movement. He is also chairman of the Singapore National Co-operative Federation (SNCF). Prior to his joining the NTUC, Mr Chan was with the Singapore Administrative Service from 1990 to 1991. At the NTUC, he held key appointments in NTUC Healthcare, NTUC Membership Department, NTUC Skills Development Department, NTUC Income, and the Health Corporation of Singapore Staff Union (HCSSU). Mr Chan was awarded a Colombo Plan Scholarship and graduated with an Honours Degree in Economics at the University of Western Australia in 1988.</p>
							</div>
							
						</div>
					</div>
				</div>
				<div className="tab-content">
					<div className="p-r-30 p-l-30">
						<div>
							<h4 className="heading main">
								Ms Ho Yin Fong Chief <br/>  
								Early Childhood Education Officer / Academic Director <br/>  
								NTUC First Campus Co-operative Ltd / SEED Institute <br/> 
							</h4>
							<div className="desc-text">
								<p>HO Yin Fong (MSc) is the Academic Director of SEED Institute. She holds a Masters of Science in Child Development and Early Childhood Education, and has had over 25 years of experience in the early childhood education field. Providing consultations on the expansion of programmes offered by SEED Institute; she reviews and strengthens partnerships with tertiary institutions, creates frameworks for research activities, and develops both professional qualifications and continual development programmes for principals and teachers. She also leads and oversees the work of Heads of Departments and faculty members of SEED Institute. </p>
								<p>As NTUC First Campus’ former Deputy CEO, and the Chief Early Childhood Education Officer of NTUC First Campus (NFC), she leads the early childhood professional teams supporting the network of centres. She oversees the organisation’s policies and initiatives for corporate quality services, staff professional development, and curriculum development and implementation, and support for at risk children and children with learning needs. She has also conducted teacher training sessions for NTUC First Campus, foreign educators under the Singapore Co-operation Programme (SCP) of the Ministry of Foreign Affairs as well as pre-services and in-service teachers in Mathematics and Science for Pre-schoolers at the Diploma and Degree level at SEED Institute.</p>
							</div>
							
						</div>
					</div>
				</div>
				<div className="tab-content">
					<div className="p-r-30 p-l-30">
						<div>
							<h4 className="heading main">
								Ms Hor Fong Lin <br/> 
								Chief Financial Officer <br/> 
								NTUC First Campus Co-operative Ltd <br/>  
							</h4>
							<div className="desc-text">
								<p>Fong Lin joined NTUC First Campus Co-operative Limited in 2012 as the Chief Financial Officer of the NTUC First Campus Group of companies. She graduated from the National University of Singapore with a Bachelor of Accountancy degree in 1985, is a fellow of both the Institute of Singapore Chartered Accountants (ISCA) and CPA Australia. She has more than 30 years of experience in managing the Accounting, Financial, Strategic Business Planning, Information Technology, Risk and Governance functions. Before joining NTUC First Campus she has worked in a wide spectrum of organisations in the same capacity in childcare services, international school, trading & retail, food & beverage and multi-national high tech industry.</p>
							</div>
							
						</div>
					</div>
				</div>
			</div>
		},
		{
			name : 'Faculty',
			biography: 
			<div className="tabing-blocks space-none bg-white">
				<div className="tab-content set-space">
					<div className="p-r-30 p-l-30">
						{/* <div className="text-center p-b-40">
							<div className="youtube-link bg-Gray"></div>
						</div> */}
						<div>
							<h4 className="heading p-b-20">Learning from the Very Best</h4>
							<div className="desc-text">
								<p>High quality training must begin with high quality trainers, and SEED Institute offers the advantage of the largest multi-disciplinary team of full time trainers, each backed by years of early childhood experience garnered through working with children, parents and the community. We handpicked each and every one of our trainers, choosing only those who are highly qualified. Each trainer is either a Masters or Ph.D. holder. </p>
								<p>Our teaching faculty's professional experience spans teaching and supervision in early childhood programmes, including specialised areas such as special needs education, parent education and social work. Placing top priority on the welfare of our students, they teach with a holistic, student-centered approach that emphasises caring for the well-being of each and every student.</p>
							</div>
							
						</div>
					</div>
				</div>
				<div className="tab-content set-space-around bg-Gray b-t-0 p-t-40">
					<div className="p-r-30 p-l-30">
						<div>
							<h2 className="heading main md">Full Time Faculty Members</h2>
							<ul className="inline-list list-33 clearfix">
								<li>
									<div className="list-decs">
										<div className="pdf-icon">
											{/* <NavLink to={process.env.PUBLIC_URL + '/myfile.pdf'}>
												<img src={pdfIcon} alt="Master Programme PDF"/>
											</NavLink> */}
										</div>
										<p>Academic Directors</p>
									</div>
								</li>
							</ul>
						</div>
					</div>
				</div>
				<div className="tab-content set-space-around bg-Gray b-t-0">
					<div className="p-r-30 p-l-30 p-t-40 b-t-lightGray">
						<div>
							<h2 className="heading main md">List of SEED Trainers</h2>
							<ul className="inline-list list-33 clearfix">
								<li>
									<div className="list-decs">
										<div className="pdf-icon">
											<img src={pdfIcon} alt="Certificate and Diploma Programmes PDF"></img>
										</div>
										<div>
											<p className="fontsemibold">List of Trainers -activities </p>
											<p>Certificate and Diploma Programmes</p>
										</div>
									</div>
								</li>
								<li>
									<div className="list-decs">
										<div className="pdf-icon">
											<img src={pdfIcon} alt="Degree Programme PDF"></img>
										</div>
										<div>
											<p className="fontsemibold">List of Trainers -Accountants </p>
											<p>Degree Programme</p>
										</div>
									</div>
								</li>
								<li>
									<div className="list-decs">
										<div className="pdf-icon">
											<img src={pdfIcon} alt="Master Programme PDF"></img>
										</div>
										<div>
											<p className="fontsemibold">List of Trainers -</p>
											<p>Master Programme</p>
										</div>
									</div>
								</li>
							</ul>
						</div>
					</div>
				</div>
			</div>
		}
	  ];
	  
	  function getTabs() {
		return presidents.map(president => ({
		  tabClassName: 'tab-head', // Optional
		  panelClassName: 'panel-content', // Optional
		  title: president.name,
		  content: president.biography,
		}));
	  }

    return (
      	<div className="relative">
		  	<MobileHeader/>
			<Header/>
			<AnimatedIcon/>
			<div className="relative mobile-heading b-t-lightGray">
				<div className="d-flex flex-v-center flex-sps-between">
					<h2 className="visible-xs hidden">About Us</h2>
					<NavLink to="" className="share-icon show-in-xs"><img src={shareIcon} alt="share"/></NavLink>
				</div>
			</div>
			<section className="banner-section">
				<div className="banner-image about-us-banner">
				</div>
			</section>
          	<div className="sections-wrapper bg-Gray">
				<div className="container">
					<div className="d-flex flex-v-center flex-sps-between hidden-xs p-b-40">
						<h2 className="heading lg c-darkGray">About Us</h2>
						<NavLink to="" className="share-icon"><img src={shareIcon} alt="share"/></NavLink>
					</div>
					<div className="desc-text c-darkGray">
						<p>
							<strong>SEED Institute - Shaping the Growth of Future Generations </strong>
						</p>
						<p>
							SEED Institute was built upon a vision to give children the best head-start possible, by grooming early childhood professionals to provide the best care and education to young children. 
						</p>
						<p>
						Our single-minded belief in the importance of early education is expressed in a saying that is shared with every student in the Institute: “All the flowers of all the tomorrows are in the seeds of today.” This is why we spare no effort to uphold the highest training standards, with our students enjoying the finest teaching and support from leading experts in early childhood education.
						</p>
					</div>
					<div className="about-blocks">
						<bs.Row>
							<bs.Col md={6} sm={6} xs={12}>
								<div className="static-box d-flex flex-v-center global-shadow bg-white">
									<div>
										<h2 className="heading lg p-b-5 fontbold c-darkGray">Vision </h2>
										<p>A joyful and inspiring early learning experience for all, that fulfills the promise of each child.</p>
									</div>
								</div>
							</bs.Col>
							<bs.Col md={6} sm={6} xs={12} className="m-t-20-xs">
								<div className="static-box d-flex flex-v-center global-shadow bg-white">
									<div>
										<h2 className="heading lg p-b-5 fontbold c-darkGray">Mission </h2>
										<p>We inspire early childhood professionals to excel in practice and achieve their best for each child.</p>
									</div>
								</div>
							</bs.Col>
						</bs.Row>
					</div>
					<div className="static-list p-l-30">
						<h4 className="c-darkGray fontbold">We do this by:</h4>
						<ul className="bullet-list">
						<li>Setting the highest standards for the development of ECCE practitioners.</li>
						<li>Teaching, mentoring and coaching ECCE professionals.</li>
						<li>Leading, developing and encouraging best practices in ECCE.</li>
						<li>Supporting continuous learning among ECCE practitioners.</li> 
						</ul>
					</div>
				</div>
				<section className="about-us-tabing tabing-section p-t-40">
					<div className="container">
						<Tabs items={getTabs()} />
					</div>
				</section>
          	</div>
          <Footer/>
      </div>
    )
  }
}
