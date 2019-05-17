import * as React from 'react';
import { Provider } from 'react-redux';
import { Store } from 'redux';
import { ConnectedRouter } from 'react-router-redux';
import { Route, Switch } from 'react-router-dom';
import { History } from 'history';
import 'react-loading-bar/dist/index.css';
import 'react-datepicker/dist/react-datepicker.css';
import 'font-awesome/css/font-awesome.css';
import 'bootstrap/dist/css/bootstrap.css';

import './App.css';
import './scss/index.scss';

import Login from 'Components/auth/Login';
import MyProfile from 'Components/auth/MyProfile';
import Dashboard from 'Components/dashboard';
import CourseList from 'Components/course/List';
import CourseListForViewer from 'Components/course/ListForViewer';
import CourseDetail from 'Components/course/Detail';
import UserList from 'Components/user/List';
import EmailTemplateEdit from 'Components/email_template/Edit';

import CourseRunList  from 'Components/course_run/CourseRunList';
import CourseRunChangeStatusList  from 'Components/course_run/CourseRunChangeStatusList';
import MaintainCourseRunList  from 'Components/course_run/MaintainCourseRunList';
import CourseRunDetail from 'Components/course_run/Detail';
import SummaryList  from 'Components/course_run/SummaryList';

import PostCourseRunDataList from 'Components/placement/PostCourseRunDataList';
// import PlacementList from 'Components/placement/PlacementList';
//import PlacementDataReportList from 'Components/placement/PlacementDataReportList';
import PlacementDataReportList from 'Components/report/CoursePlacementDataReport';

import MyPlacementList from 'Components/placement/MyPlacementList';
import MySubordinatePlacementList from 'Components/placement/MySubordinatePlacementList';

import {PageType as PlacementPageType} from 'Components/placement/PlacementTable';
import PlacementSendEmail from 'Components/placement/SendEmail';
//import CourseRunReport from 'Components/course_run/ReportList';
import CourseRunReport from 'Components/report/CourseRunReport';
import CourseRunListForViewer from 'Components/course_run/CourseRunListForViewer';
import AttributeList from 'Components/AttributeList';

import API from './aep';


import SweetAlert from './plugins/Swal';
import {InitialProps as SwalProps} from './reducers/swal-reducer';

import helper from './plugins/Helper';

export interface Props {
  store: Store<any>;
  history: History;
}

export class App extends React.Component<Props, {}> {
  render() {
    
    const { store, history } = this.props;
    const initialProps = {
      history: history,
      dispatch: store.dispatch,
      rootState: store.getState(),
      helper: new helper(),
      callApi: ( ) =>{ return Promise.resolve('ok') }
    }
    return (
      <Provider store={store}>
        <ConnectedRouter history={history}>
          <>
          <SweetAlert {...SwalProps} />
          <Switch>
          <Route exact={true} path="/" render={ () => <Login  {...initialProps} whoViewed='Admin' /> } />
          <Route exact={true} path="/my-profile" render={ () => <MyProfile  {...initialProps} whoViewed='Admin' /> } />
          {/* Evnet Route -> List, Create , Edit */}
          <Route exact={true} path="/dashboard" render={ () => <Dashboard  {...initialProps} whoViewed='Admin' /> } />         
          <Route exact={true} path="/course" render={ () => <CourseList  {...initialProps} whoViewed='Admin' /> } />
          
          <Route exact={true} path="/course/:id" render={ () => <CourseDetail  {...initialProps} whoViewed='Admin' /> } />

          <Route exact={true} path="/course-run" render={ () => <CourseRunList  {...initialProps} whoViewed='Admin' /> } />         
          <Route exact={true} path="/maintain-course-run" render={ () => <MaintainCourseRunList  {...initialProps}  uploadEndPoint={API.COURSE_RUN_UPDATE} whoViewed='Admin' /> } />
          
          <Route exact={true} path="/course-run-change-status" render={ () => <CourseRunChangeStatusList  {...initialProps}  whoViewed='Admin' /> } /> 
          <Route exact={true} path="/course-run/:id" render={ () => <CourseRunDetail  {...initialProps} whoViewed='Admin' /> } />
          <Route exact={true} path="/maintain-course-run/:id" render={ () => <CourseRunDetail  {...initialProps} whoViewed='Admin' whereFrom="maintain-list" /> } />

          <Route exact={true} path="/course-run-summary" render={ () => <SummaryList  {...initialProps} whoViewed='Admin'  /> } />         
          <Route exact={true} path="/post-course-run-data" render={ () => <PostCourseRunDataList  {...initialProps} whoViewed='Admin' /> } />

          {/* <Route exact={true} path="/placement" render={ () => <PlacementList  {...initialProps} whoViewed='Admin' /> } /> */}
          
          <Route exact={true} path="/placement-data-report" render={ () => 
          <PlacementDataReportList  {...initialProps} whoViewed='Admin' /> } />

          <Route exact={true} path="/user" render={ () => <UserList  {...initialProps} whoViewed='Admin' /> } />
        
          {/* Email Template Route */}

          <Route exact={true} path="/email-template/1" render={ () => <EmailTemplateEdit  {...initialProps}  /> } />
          <Route exact={true} path="/email-template/2" render={ () => <EmailTemplateEdit  {...initialProps}  /> } />
          <Route exact={true} path="/email-template/3" render={ () => <EmailTemplateEdit  {...initialProps}  /> } />
          
          {/**Chnage Placemet Status**/}          
          <Route exact={true} path="/placement/:id/change-status/confirmed" render={ () => <PlacementSendEmail  {...initialProps} status="Confirmed"  /> } />
          <Route exact={true} path="/placement/:id/change-status/cancelled" render={ () => <PlacementSendEmail  {...initialProps} status="Cancelled"  /> } />
          <Route exact={true} path="/placement/:id/change-status/reminder" render={ () => <PlacementSendEmail  {...initialProps} status="Reminder"  /> } />
          {/* Reports */}
          
          <Route exact={true} path="/course-run-report" render={ () => <CourseRunReport  {...initialProps}  /> } />

          {/* Viewer User Routes */}

          <Route exact={true} path="/all-course" render={ () => 
            <CourseListForViewer  {...initialProps} whoViewed="Viewer" /> } 
          />

          <Route exact={true} path="/all-course-run" render={ () => 
            <CourseRunListForViewer  {...initialProps}  /> } 
          />

          <Route exact={true} path="/my-placement" render={ () => 
            <MyPlacementList  {...initialProps} whoViewed="Viewer"/> } 
          />
          <Route exact={true} path="/subordinate-placement" render={ () => 
            <MySubordinatePlacementList  {...initialProps} whoViewed="Viewer"/> } 
          />

          <Route exact={true} path="/attribute-list" render={ () => 
            <AttributeList  {...initialProps} whoViewed='Admin' /> } 
          />

          <Route            
            render={() => (
                <div>404 </div>
            )}
          />
          </Switch>
          </>
        </ConnectedRouter>
      </Provider>
    );
  }
}