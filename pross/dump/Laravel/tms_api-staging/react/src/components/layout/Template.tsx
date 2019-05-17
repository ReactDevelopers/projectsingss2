import * as React from 'react';
import {Props} from '../../features/root-props';
import Header from './Header';
import Footer from './Footer';
import * as bs from 'react-bootstrap';
import Loading from './LoaderBar';
import Breadcrumb from './Breadcrumb';
import { ReactElement } from 'react';
import {actions} from '../../features/root-action';
import API from '../../aep';
import {AuthI} from '../../models/Auth';

interface TemplateProps extends Props {

    RightSideButton?: ReactElement<any>;
}

export default class Template extends React.Component <TemplateProps> {

    componentWillMount() {

        const currentPath: string = this.props.history.location.pathname;
        
        const viewerRoute = [
            /\/all-course$/,
            /\/all-course-run$/,
            /\/my-placement$/,
            /\/subordinate-placement$/,
            /\/dashboard$/,
            /\/my-profile$/,
            /\/course\/[0-9]+$/,
            /\/course-run\/[0-9]+$/
        ];


        /**
         * Redirect to Home Screen when AUth User not found.
         */
        if(!this.props.helper.isLogin()) {

            this.props.dispatch(actions.auth.logoutSuccess(API.AUTH_USER));
        }

       const auth_user = this.props.helper.deepFind(this.props.rootState.server,'auth_user.response.data', {});

       if(auth_user.view_as === 'Viewer'  && this.props.helper.strMatchInArray(currentPath, viewerRoute) === -1){

            this.props.history.push('/dashboard');
       }
        
        
    }
    render () {
        
        const props = this.props;
        
        return (
            
            <div className="tms_theme">
                <Loading {...props} />
                <Header {...props} />  
                <Breadcrumb {...props} />
                
                <bs.Grid fluid={true} className="container_wrap">
                    <bs.Grid>
                        {this.props.children}
                    </bs.Grid>
                </bs.Grid>
                <Footer/>
            </div>
        )
    }
}