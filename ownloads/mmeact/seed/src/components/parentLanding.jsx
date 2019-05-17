import React, { Component } from 'react'
import Header from '../layouts/header'
import Footer from '../layouts/footer'
import blockImage from '../assets/images/blockImage.png'
import CourseImage from '../assets/images/CourseImage.png'
import courseBlocksecond from '../assets/images/CourseImage.png'
import {NavLink} from 'react-router-dom'
import MobileHeader from '../layouts/mobileHeader'
import shareIcon from '../assets/images/share-icon.png'
import parentbanner from '../assets/images/parentbanner.png'

export default class parentLanding extends Component {

  componentDidMount(){
      window.scrollTo(0,0);
  }

  render() {
    return (
      <main className="childhoodLanding">
        <Header/>
        <MobileHeader/>
        <div class="relative mobile-heading b-t-lightGray">
            <div class="d-flex flex-v-center flex-sps-between">
                <h2 class="c-darkGray visible-xs hidden">I want to grow my parenting skills</h2>
                <NavLink to="/" className="share-icon hidden visible-xs">
                    <img src={shareIcon} alt="share"/>
                </NavLink>
            </div>
        </div>
        <section className="childhood-professional-section parent-landing-banner">
            <div className="banner-image banner-bg">
            </div>
            <div className="container">
                <div className="parent-middle-banner hidden visible-xs">
                    <img src={parentbanner} alt="Parent Banner"/>
                </div>
                <div className="course-wrapper p-t-40">
                    <div className="d-flex flex-v-center flex-sps-between hidden-xs">
                        <h2 className="c-darkGray heading">I want to grow my parenting skills</h2>
                        <NavLink to="/" className="share-icon hidden-xs">
                            <img src={shareIcon} alt="share"/>
                        </NavLink>    
                    </div>
                    <div className="childhoodprofeesional-block w-100">
                        <div>
                            <p>
                            Aenean venenatis pharetra nibh, blandit vestibulum magna pellentesque ac. Mauris a porta risus. Suspendisse potenti. Integer iaculis posuere lorem, at tristique velit porttitor vel. Curabitur aliquet augue ex, et accumsan nunc tincidunt et. Donec tristique maximus enim sit amet suscipit. Vivamus pretium tempus quam at porta. Interdum et malesuada fames ac ante ipsum primis in faucibus. In lobortis pharetra tortor, nec condimentum elit lacinia sed. Duis nunc massa, fringilla sit amet nisi sit amet, pellentesque.
                            </p>
                        </div>
                    </div>
                    <div className="parent-middle-banner hidden-xs">
                        <img src={parentbanner} alt="Parent Banner"/>
                    </div>
                    <div className="row m-custom">
                        <div className="col-md-6 col-sm-6 col-xs-12 full-width p-custom">
                            <div className="course-wrapper-block">
                                    <NavLink to="/CollegeOverview" className="anchor-link-black">
                                        <div className="courseheader commonspace">
                                        <h2>
                                        WSQ Adopt the Early Years Development Framework
                                        </h2>
                                        </div>
                                        <div className="courseimage">
                                        <img src={CourseImage}/>
                                        </div>
                                        <div className="courseContent commonspace">
                                        <p>The WSQ Adopt the Early Years Development Framework was launched by the Ministry of Social and Family Development (MSF) on 30 September 2011. The aim is to enhance the quality of care and development of infants, toddlers and nursery children aged 2 months through 3 years.</p>                                       
                                        </div>
                                    </NavLink>
                            </div>
                        </div>
                        <div className="col-md-6 col-sm-6 col-xs-12 full-width p-custom">
                            <div className="course-wrapper-block">
                                    <NavLink to="/CollegeOverview" className="anchor-link-black">
                                        <div className="courseheader commonspace">
                                        <h2>
                                        婴幼儿培育框架培训课程

                                        </h2>
                                        </div>
                                        <div className="courseimage">
                                        <img src={courseBlocksecond}/>
                                        </div>
                                        <div className="courseContent commonspace">
                                        <p>前新加坡青年、社会及家庭发展部（现在的社会及家庭发展部）于2011年9月29日推行了婴幼儿培育框架。该框架的目的在于增强对2个月到3岁婴儿、学步儿及幼儿培育与发展的质量。 婴幼儿培育框架是一个能够帮助托儿中心建立适当培育与发展实践，以及为三岁以下的幼儿创设培育环境的一个工具。该框架是通过与早期教育专家、业者、及家长的深入讨论，两年多参考了各方面的资料和信息及研究开发而成的。</p>
                                        </div>
                                    </NavLink>
                            </div>
                        </div>
                        <div className="col-md-6 col-sm-6 col-xs-12 full-width p-custom">
                            <div className="course-wrapper-block">
                                    <NavLink to="/CollegeOverview" className="anchor-link-black">
                                        <div className="courseheader commonspace">
                                        <h2>
                                        WSQ Relief Staff Programme (Early Childhood Care and Education)
                                        </h2>
                                        </div>
                                        <div className="courseimage">
                                        <img src={CourseImage}/>
                                        </div>
                                        <div className="courseContent commonspace">
                                        <p>The WSQ Relief Staff Programme is a training programme developed by SEED Institute, supported by the Early Childhood Development Agency (ECDA) and the SkillsFuture SG (SSG) [formerly known as Singapore Workforce Development Agency (WDA)]. Candidates will be able to have a better understanding of developmental characteristics of children, learn ways to relate with them and apply appropriate practices while taking care of them.</p>
                                        </div>
                                    </NavLink>
                            </div>
                        </div>
                        <div className="col-md-6 col-sm-6 col-xs-12 full-width p-custom">
                            <div className="course-wrapper-block">
                                    <NavLink to="/CollegeOverview" className="anchor-link-black">
                                        <div className="courseheader commonspace">
                                        <h2>
                                        WSQ Advanced Certificate in Early Years
                                        </h2>
                                        </div>
                                        <div className="courseimage">
                                        <img src={CourseImage}/>
                                        </div>
                                        <div className="courseContent commonspace">
                                        <p>Provide educarers with an overview of the history and theories behind early childhood care and education, as well as an understanding of the various domains and stages of development for children from birth to 3 years. These must be portrayed in the context of the work experience of local educarers to help them reflect, assess and improve their current practices.</p>
                                        </div>
                                    </NavLink>
                            </div>
                        </div>
                        <div className="col-md-6 col-sm-6 col-xs-12 full-width p-custom">
                            <div className="course-wrapper-block">
                                    <NavLink to="/CollegeOverview" className="anchor-link-black">
                                        <div className="courseheader commonspace">
                                        <h2>
                                        WSQ Advanced Certificate in Early Childhood Care and Education
                                        </h2>
                                        </div>
                                        <div className="courseimage">
                                        <img src={CourseImage}/>
                                        </div>
                                        <div className="courseContent commonspace">
                                        <p>This course is equivalent to the Certificate in Early Childhood Care and Education under Early Childhood Training Accreditation Committee (ECTAC) guidelines. The WSQ Advanced Certificate in Early Childhood Care and Education course is an 800-hour programme specially designed for staff working with pre-schoolers to expand their specific teaching skills and knowledge required for both kindergartens and childcare settings. Upon successful completion of the programme, graduates will be awarded with a certificate from SEED Institute. They will also be awarded the WSQ Advanced Certificate in Early Childhood Care & Education.</p>
                                        </div>
                                    </NavLink>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <Footer/>
      </main>
    )
  }
}
