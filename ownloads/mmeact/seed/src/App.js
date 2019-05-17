import React from 'react';
import { Switch, Route } from 'react-router-dom';
import { Router } from "react-router";
import Home from '../src/components/home'
import LandingSection from '../src/components/landingsection'
import ChildhoodLanding from '../src/components/childhoodLanding'
import aboutUs from '../src/components/aboutUs'
import contactUs from '../src/components/contactUs'
import PrivacyPolicy from '../src/components/privacyPolicy'
import carrer from '../src/components/carrer'
import SiteMap from '../src/components/siteMap'
import TermOfUse from '../src/components/termOfUse'
import questionone from '../src/components/questionAnswers/questionone'
import questionsecond from '../src/components/questionAnswers/questionsecond'
import thirdquestion from '../src/components/questionAnswers/thirdquestion'
import questionLanding from '../src/components/questionLanding'
import checkout from '../src/layouts/checkOut'
import parentLanding from '../src/components/parentLanding'
import CollegeOverview from '../src/components/collegeOverview'
import { loadReCaptcha } from 'react-recaptcha-google'
import QuestionBanner from '../src/layouts/questionBanner'
import ProfessionalProgrammes from '../src/components/ProfessionalProgrammes'
import ParentsCollegue from '../src/components/ParentsCollegue'
import AnimatedIcon from '../src/layouts/AnimatedIcon'

//require('owl.carousel/src/scss/owl.carousel.scss');


const App = ({history}) => {
    
 const componentDidMount = () =>{
    loadReCaptcha();
 }


  return (
      <div className="wrapper">
          <Router history={history}>
            <Switch>
                <Route exact={true} path='/'  component={Home} />
                <Route exact={true} path='/landingsection/' component={LandingSection} />
                <Route exact={true} path='/childhoodLanding' component={ChildhoodLanding}></Route>
                <Route exact={true} path='/parentLanding' component={parentLanding}></Route>
                <Route exact={true} path='/aboutUs' component={aboutUs}></Route>
                <Route exact={true} path='/contactUs' component={contactUs}></Route>
                <Route exact={true} path='/privacyPolicy' component={PrivacyPolicy}></Route>
                <Route exact={true} path='/carrer' component={carrer}></Route>
                <Route exact={true} path='/siteMap' component={SiteMap}></Route>
                <Route exact={true} path='/termsOfUse' component={TermOfUse}></Route>
                <Route exact={true} path='/questionone' component={questionone}></Route>
                <Route exact={true} path='/questionsecond' component={questionsecond}></Route>
                <Route exact={true} path='/thirdquestion' component={thirdquestion}></Route>                
                <Route exact={true} path='/questionLanding' component={questionLanding}></Route>
                <Route exact={true} path='/checkout' component={checkout}></Route>
                <Route exact={true} path='/CollegeOverview' component={CollegeOverview}></Route>
                <Route exact={true} path='/questionBanner' component={QuestionBanner}></Route>
                <Route exact={true} path='/ProfessionalProgrammes' component={ProfessionalProgrammes}></Route>
                <Route exact={true} path='/ParentsCollegue' component={ParentsCollegue}></Route>
                <Route exact={true} path="/AnimatedIcon" component={AnimatedIcon}></Route>
            </Switch>
          </Router>
      </div>
      );
    };

export default App;
